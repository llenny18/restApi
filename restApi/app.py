import subprocess
from flask import Flask, request, jsonify
import requests
import mysql.connector
import pandas as pd
import pickle
from sklearn.ensemble import RandomForestClassifier

app = Flask(__name__)

# Automatically start recommend.py
recommend_process = subprocess.Popen(["python", "recommend.py"])

# Define the thresholds for each subject
thresholds = {
    'Financial Accounting and Reporting': 0.75 * 10,
    'Advanced Financial Accounting and Reporting': 0.75 * 12,
    'Management Services': 0.75 * 9,
    'Auditing': 0.75 * 9,
    'Taxation': 0.75 * 18,
    'Regulatory Framework for Business Transaction': 0.75 * 10
}

# MySQL database connection
def get_db_connection():
    connection = mysql.connector.connect(
        host='localhost',
        database='cpa',
        user='root',
        password=''
    )
    return connection

# Fetch historical data for a particular student from MySQL
def load_student_data_from_mysql(student_id):
    connection = get_db_connection()
    cursor = connection.cursor()

    query = """
    SELECT 
        Financial_Score, Adv_Score, Mng_Score, Auditing_Score, 
        Taxation_Score, Framework_Score 
    FROM score_history 
    WHERE student_id = %s 
    ORDER BY exam_date DESC LIMIT 3
    """
    cursor.execute(query, (student_id,))
    rows = cursor.fetchall()

    student_data = []
    for row in rows:
        student_data.append({
            'Financial Accounting and Reporting': row[0],
            'Advanced Financial Accounting and Reporting': row[1],
            'Management Services': row[2],
            'Auditing': row[3],
            'Taxation': row[4],
            'Regulatory Framework for Business Transaction': row[5]
        })

    cursor.close()
    connection.close()

    df = pd.DataFrame(student_data)
    return df

# Load the pre-trained Random Forest model from file
def load_model():
    with open('model.pkl', 'rb') as file:
        model = pickle.load(file)
    return model

# Load the trained Random Forest model globally
model = load_model()

@app.route('/predict/<student_id>', methods=['POST'])
def predict(student_id):
    student_df = load_student_data_from_mysql(student_id)

    # Make a prediction using the trained Random Forest model
    student_predictions = model.predict(student_df)

    # Map predictions to "Weak" or "Not Weak"
    predictions_dict = {
        subject: "Weak" if prediction == 1 else "Not Weak"
        for subject, prediction in zip(student_df.columns, student_predictions[0])
    }

    # Send data to recommend.py for recommendations
    try:
        response = requests.post(
            "http://127.0.0.1:5001/generate_recommendation",
            json=predictions_dict
        )
        recommendations = response.json()
    except requests.exceptions.RequestException as e:
        return jsonify({"error": f"Failed to connect to recommendation service: {e}"})

    # Combine predictions and recommendations
    combined_response = {
        "predictions": predictions_dict,
        "recommendations": recommendations
    }

    return jsonify(combined_response)

if __name__ == '__main__':
    try:
        app.run(debug=True)
    finally:
        # Ensure recommend.py is terminated when app.py exits
        recommend_process.terminate()
