import subprocess
from flask import Flask, request, render_template, jsonify
import requests
import mysql.connector
import pandas as pd
import pickle
import logging
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
from sklearn.model_selection import train_test_split

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Automatically start recommend.py (port 5003)
recommend_process = subprocess.Popen(["python", "topic_score.py"])
recommend_process = subprocess.Popen(["python", "recommend.py"])

# Database connection function
def get_db_connection():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            database='cpa',
            user='root',
            password='',
            charset="utf8mb4",
            collation="utf8mb4_general_ci"
        )
        return connection
    except mysql.connector.Error as err:
        logging.error(f"Database connection failed: {err}")
        raise

# Function to load historical data for a student from MySQL
def load_student_data_from_mysql(student_id):
    try:
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

        if not rows:
            logging.warning(f"No historical data found for student ID: {student_id}")
            return pd.DataFrame()

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
        logging.info(f"Loaded historical data for student ID: {student_id}")
        return df
    except Exception as e:
        logging.error(f"Error loading student data for student ID {student_id}: {e}")
        raise

# Load the pre-trained Random Forest model globally

def fetch_historical_data():
    try:
        connection = get_db_connection()
        cursor = connection.cursor()

        query = """
        SELECT 
            Financial_Score, 
            Adv_Score, 
            Mng_Score, 
            Auditing_Score, 
            Taxation_Score, 
            Framework_Score
        FROM score_history
        """
        cursor.execute(query)
        rows = cursor.fetchall()

        if not rows:
            logging.warning("No data found in the database.")
            return pd.DataFrame()

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
        logging.info(f"Loaded {len(df)} rows of data.")
        return df
    except Exception as e:
        logging.error(f"Error fetching data: {e}")
        raise


def generate_labels(df):
    try:
        thresholds = {
            'Financial Accounting and Reporting': 0.75 * 10,
            'Advanced Financial Accounting and Reporting': 0.75 * 12,
            'Management Services': 0.75 * 9,
            'Auditing': 0.75 * 9,
            'Taxation': 0.75 * 18,
            'Regulatory Framework for Business Transaction': 0.75 * 10
        }

        labels = pd.DataFrame()
        for subject in df.columns:
            if subject not in thresholds:
                logging.warning(f"No threshold defined for {subject}. Skipping.")
                continue
            labels[subject] = df[subject].apply(lambda x: 1 if x < thresholds[subject] else 0)
        logging.info("Labels generated successfully.")
        return labels
    except Exception as e:
        logging.error(f"Error generating labels: {e}")
        raise


def load_model():
    try:
        with open('subject_model.pkl', 'rb') as file:
            model = pickle.load(file)
        logging.info("Model loaded successfully.")

        # Fetch historical data
        df = fetch_historical_data()
        if df.empty:
            logging.warning("No data available to calculate accuracy.")
        else:
            # Generate labels
            labels_df = generate_labels(df)

            # Train-test split (80-20 split)
            X_train, X_test, y_train, y_test = train_test_split(df, labels_df, test_size=0.2, random_state=42)

            # Predict on test data
            y_pred = model.predict(X_test)

            # Calculate accuracy
            accuracy = accuracy_score(y_test.values.argmax(axis=1), y_pred.argmax(axis=1))
            logging.info(f"Model accuracy: {accuracy * 100:.2f}%")

        return model
    except FileNotFoundError:
        logging.error("Model file not found. Ensure 'subject_model.pkl' is present.")
        raise

model = load_model()

@app.route('/')
def index():
    return render_template('index.html')  # Render the HTML file

@app.route('/predict/<student_id>', methods=['POST'])
def predict(student_id):
    try:
        logging.info(f"Received request for student ID: {student_id}")

        # Load student data from MySQL
        student_df = load_student_data_from_mysql(student_id)
        if student_df.empty:
            return jsonify({"error": f"No data found for student ID {student_id}"}), 404

        # Make predictions using the pre-trained model
        student_predictions = model.predict(student_df)
        predictions_dict = {
            subject: "Weak" if prediction == 1 else "Not Weak"
            for subject, prediction in zip(student_df.columns, student_predictions[0])
        }
        logging.info(f"Predictions for student ID {student_id}: {predictions_dict}")

        try:
            recommend_response_5002 = requests.post(
                "http://127.0.0.1:5002/generate_fields",
                json={"student_id": student_id}
            )
            if recommend_response_5002.status_code == 200:
                fields = recommend_response_5002.json().get("fields", {})
            else:
                fields = {}
                logging.warning(f"Failed to fetch fields from 5002 for student ID {student_id}")
        except requests.exceptions.RequestException as e:
            logging.error(f"Error connecting to 5002 service: {e}")
            fields = {}

        # Fetch recommendations from recommend.py (Port 5003)
        try:
            recommend_response = requests.post(
                "http://127.0.0.1:5003/generate_recommendations",
                json={"student_id": student_id, "fields": fields, "topic_data": predictions_dict}
            )
            recommendations = recommend_response.json() if recommend_response.status_code == 200 else {"error": "Failed to fetch recommendations"}
            logging.info(f"Recommendations for student ID {student_id}: {recommendations}")
        except requests.exceptions.RequestException as e:
            logging.error(f"Error connecting to recommend service: {e}")
            recommendations = {"error": "Failed to connect to recommend service"}


        # Combine predictions and recommendations for PHP
        parsed_recommendations = []
        if "recommendations" in recommendations:
            for subject, recs in recommendations["recommendations"].items():
                for rec in recs:
                    parsed_recommendations.append({
                        "subject": subject,
                        "topic": rec.get("topic"),
                        "recommendation": rec.get("recommendation")
                    })

        combined_response = {
            "predictions": predictions_dict,
            "recommendations": parsed_recommendations
        }
        logging.info(f"Combined response for student ID {student_id}: {combined_response}")

        return jsonify(combined_response)

    except Exception as e:
        logging.error(f"Error in prediction for student ID {student_id}: {e}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    try:
        if __name__ == "__main__":
            app.run(host="127.0.0.1", port=5005, debug=True)

    finally:
        recommend_process.terminate()
