from flask import Flask, request, jsonify
import mysql.connector
import pandas as pd
import logging
from sklearn.ensemble import RandomForestClassifier
import pickle
import requests

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Database connection function
def get_db_connection():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            database='cpa',
            user='root',
            password=''
        )
        return connection
    except mysql.connector.Error as err:
        logging.error(f"Database connection failed: {err}")
        raise

# Fetch student data from the database
def load_student_data_from_mysql(student_id):
    try:
        connection = get_db_connection()
        cursor = connection.cursor()

        query = """
        SELECT 
            Fin_rep, Fin_State, Key_Accounting, Other, Specialized,
            Part, Corporate, Joint, Revenue, Home_Office, Combination, Consolidated, Derivatives, Translation, no_profit, cost, special,
            Man_Acc, Fin_Man, Eco,
            Fundamentals, Risk_based, Understanding, Audit_Evidence, Audit_Completion, CIS, Attestation, Governance, Risk_Response,
            Principles, Remedies, Income_Tax, Transfer_Tax, Business_Tax, Doc_Stamp, Excise_Tax, Gov_Tax, Prefer_Tax,
            LBT, Bouncing, Consumer, Rehabilitation, PHCA, Procurement, LBO, LOBT, Security_Law, Doing_Business
        FROM topic_score 
        WHERE student_id = %s 
        ORDER BY exam_date DESC LIMIT 5
        """
        cursor.execute(query, (student_id,))
        rows = cursor.fetchall()

        if not rows:
            logging.warning(f"No data found for student ID: {student_id}")
            return pd.DataFrame()

        student_data = []
        for row in rows:
            student_data.append({
                'Fin_rep': row[0],
                'Fin_State': row[1],
                'Key_Accounting': row[2],
                'Other': row[3],
                'Specialized': row[4],
                'Part': row[5],
                'Corporate': row[6],
                'Joint': row[7],
                'Revenue': row[8],
                'Home_Office': row[9],
                'Combination': row[10],
                'Consolidated': row[11],
                'Derivatives': row[12],
                'Translation': row[13],
                'no_profit': row[14],
                'cost': row[15],
                'special': row[16],
                'Man_Acc': row[17],
                'Fin_Man': row[18],
                'Eco': row[19],
                'Fundamentals': row[20],
                'Risk_based': row[21],
                'Understanding': row[22],
                'Audit_Evidence': row[23],
                'Audit_Completion': row[24],
                'CIS': row[25],
                'Attestation': row[26],
                'Governance': row[27],
                'Risk_Response': row[28],
                'Principles': row[29],
                'Remedies': row[30],
                'Income_Tax': row[31],
                'Transfer_Tax': row[32],
                'Business_Tax': row[33],
                'Doc_Stamp': row[34],
                'Excise_Tax': row[35],
                'Gov_Tax': row[36],
                'Prefer_Tax': row[37],
                'LBT': row[38],
                'Bouncing': row[39],
                'Consumer': row[40],
                'Rehabilitation': row[41],
                'PHCA': row[42],
                'Procurement': row[43],
                'LBO': row[44],
                'LOBT': row[45],
                'Security_Law': row[46],
                'Doing_Business': row[47]
            })

        cursor.close()
        connection.close()

        df = pd.DataFrame(student_data)
        logging.info(f"Loaded data for student ID: {student_id}")
        return df
    except Exception as e:
        logging.error(f"Error loading student data: {e}")
        raise

# Define thresholds
thresholds = {
    'Fin_rep': 0.5 * 2,
    'Fin_State': 0.5 * 2,
    'Key_Accounting': 0.5 * 2,
    'Other': 0.5 * 2,
    'Specialized': 0.5 * 2,

    'Part': 1 * 1,
    'Corporate': 1 * 1,
    'Joint': 1 * 1,
    'Revenue': 1 * 1,
    'Home_Office': 1 * 1,
    'Combination': 1 * 1,
    'Consolidated': 1 * 1,
    'Derivatives': 1 * 1,
    'Translation': 1 * 1,
    'no_profit': 1 * 1,
    'cost': 1 * 1,
    'special': 1 * 1,
    
    'Man_Acc': 0.6 * 3,
    'Fin_Man': 0.6 * 3,
    'Eco': 0.6 * 3,
    
    'Fundamentals': 1 * 1,
    'Risk_based': 1 * 1,
    'Understanding': 1 * 1,
    'Audit_Evidence': 1 * 1,
    'Audit_Completion': 1 * 1,
    'CIS': 1 * 1,
    'Attestation': 1 * 1,
    'Governance': 1 * 1,
    'Risk_Response': 1 * 1,
    
    'Principles': 0.5 * 2,
    'Remedies': 0.5 * 2,
    'Income_Tax': 0.5 * 2,
    'Transfer_Tax': 0.5 * 2,
    'Business_Tax': 0.5 * 2,
    'Doc_Stamp': 0.5 * 2,
    'Excise_Tax': 0.5 * 2,
    'Gov_Tax': 0.5 * 2,
    'Prefer_Tax': 0.5 * 2,
    
    'LBT': 1 * 1,
    'Bouncing': 1 * 1,
    'Consumer': 1 * 1,
    'Rehabilitation': 1 * 1,
    'PHCA': 1 * 1,
    'Procurement': 1 * 1,
    'LBO': 1 * 1,
    'LOBT': 1 * 1,
    'Security_Law': 1 * 1,
    'Doing_Business': 1 * 1
}

@app.route('/generate_fields', methods=['POST'])
def generate_fields():
    try:
        data = request.json
        student_id = data.get("student_id")

        if not student_id:
            return jsonify({"error": "Student ID is required"}), 400

        logging.info(f"Generating fields for student ID: {student_id}")

        # Load student data
        student_df = load_student_data_from_mysql(student_id)
        if student_df.empty:
            return jsonify({"error": "No data found for student ID"}), 404

        # Assess topics against thresholds
        fields = {}
        for column in student_df.columns:
            topic_scores = student_df[column]
            status = "Weak" if topic_scores.mean() < thresholds[column] else "Not Weak"
            fields[column] = status

        logging.info(f"Fields for student ID {student_id}: {fields}")

        return jsonify({
            "student_id": student_id,
            "fields": fields
        }), 200

    except Exception as e:
        logging.error(f"Error generating fields: {e}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5002, debug=True)
