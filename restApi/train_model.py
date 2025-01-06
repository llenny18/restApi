import mysql.connector
import pandas as pd
import pickle
from sklearn.ensemble import RandomForestClassifier  # Use RandomForestClassifier instead of DecisionTreeClassifier

# Define the thresholds for each subject (these are the cut-off marks you defined in app.py)
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
        host='localhost',  # e.g., 'localhost'
        database='cpa',  # Replace with your DB name
        user='root',  # Replace with your DB username
        password=''  # Replace with your DB password
    )
    return connection

# Fetch historical data from the database for training
def fetch_historical_data():
    connection = get_db_connection()
    cursor = connection.cursor()

    # SQL query to fetch the latest exam results
    query = """
    SELECT 
        student_id, 
        Financial_Score, 
        Adv_Score, 
        Mng_Score, 
        Auditing_Score, 
        Taxation_Score, 
        Framework_Score
    FROM score_history 
    ORDER BY exam_date DESC
    """

    cursor.execute(query)
    rows = cursor.fetchall()

    # Prepare the data to be used in the machine learning model
    student_data = []
    for row in rows:
        student_data.append({
            'Financial Accounting and Reporting': row[1],
            'Advanced Financial Accounting and Reporting': row[2],
            'Management Services': row[3],
            'Auditing': row[4],
            'Taxation': row[5],
            'Regulatory Framework for Business Transaction': row[6]
        })

    cursor.close()
    connection.close()

    # Convert data to DataFrame
    df = pd.DataFrame(student_data)
    return df

# Generate labels based on the thresholds (if score < threshold, label as 'Weak' (1), else 'Not Weak' (0))
def generate_labels(df):
    labels = []
    for subject, scores in df.items():
        threshold = thresholds.get(subject)
        labels.append([1 if score < threshold else 0 for score in scores])

    # Convert labels to DataFrame (transpose to match the original data structure)
    labels_df = pd.DataFrame(labels).transpose()
    return labels_df

# Train the model using historical data from the database
def train_model():
    # Fetch historical data from the database
    df = fetch_historical_data()

    # Generate labels based on the thresholds
    labels_df = generate_labels(df)

    # Load previous model if it exists
    try:
        with open('model.pkl', 'rb') as file:
            model = pickle.load(file)
        print("Model loaded successfully.")
    except FileNotFoundError:
        print("No previous model found, training a new one.")
        model = RandomForestClassifier(random_state=42)  # Using RandomForestClassifier here

    # Train or retrain the model using RandomForestClassifier
    model.fit(df, labels_df)

    # Save the updated model to a .pkl file
    with open('model.pkl', 'wb') as file:
        pickle.dump(model, file)

    print("Model trained and saved as 'model.pkl'.")

# Run the training function
if __name__ == '__main__':
    train_model()
