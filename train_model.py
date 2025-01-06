import mysql.connector 
import pandas as pd 
import pickle
from sklearn.ensemble import RandomForestClassifier 
from sklearn.model_selection import train_test_split 
from sklearn.metrics import accuracy_score, classification_report
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)

# Define the thresholds for each subject
thresholds = {
    'Financial Accounting and Reporting': 0.75 * 10,
    'Advanced Financial Accounting and Reporting': 0.75 * 12,
    'Management Services': 0.75 * 9,
    'Auditing': 0.75 * 9,
    'Taxation': 0.75 * 18,
    'Regulatory Framework for Business Transaction': 0.75 * 10
}

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

# Fetch historical data from the database for training
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

# Generate labels based on the thresholds
def generate_labels(df):
    try:
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

# Train the model
def train_model():
    try:
        # Fetch historical data
        df = fetch_historical_data()
        if df.empty:
            logging.error("No data available for training. Exiting.")
            return

        # Generate labels
        labels_df = generate_labels(df)

        # Train-test split
        X_train, X_test, y_train, y_test = train_test_split(df, labels_df, test_size=0.2, random_state=42)

        # Initialize model
        model = RandomForestClassifier(random_state=42)

        # Train the model
        model.fit(X_train, y_train)
        logging.info("Model training completed.")

        # Evaluate the model
        y_pred = model.predict(X_test)
        accuracy = accuracy_score(y_test, y_pred)
        logging.info(f"Model accuracy: {accuracy * 100:.2f}%")

        # Detailed classification report
        logging.info("Classification Report:\n" + classification_report(y_test, y_pred))

        # Feature importance
        feature_importances = model.feature_importances_
        feature_importance_df = pd.DataFrame({
            'Feature': df.columns,
            'Importance': feature_importances
        }).sort_values(by='Importance', ascending=False)
        logging.info(f"Feature Importances:\n{feature_importance_df}")

        # Save the model
        with open('subject_model.pkl', 'wb') as file:
            pickle.dump(model, file)
        logging.info("Model saved as 'subject_model.pkl'.")
    except Exception as e:
        logging.error(f"Error during training: {e}")
        raise

# Run the training function
if __name__ == '__main__':
    train_model()
