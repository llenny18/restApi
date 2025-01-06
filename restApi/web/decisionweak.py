from flask import Flask, request, jsonify
import pandas as pd
from sklearn.tree import DecisionTreeClassifier

app = Flask(__name__)

# Define the thresholds for each subject
thresholds = {
    'Financial Accounting and Reporting': 0.75 * 70,
    'Advanced Financial Accounting and Reporting': 0.75 * 72,
    'Management Services': 0.75 * 72,
    'Auditing': 0.75 * 72,
    'Taxation': 0.75 * 72,
    'Regulatory Framework for Business Transaction': 0.75 * 100
}

# Load historical data (or use your own training data)
data = {
    'Financial Accounting and Reporting': [65, 72, 50, 80, 90],
    'Advanced Financial Accounting and Reporting': [70, 68, 50, 85, 76],
    'Management Services': [72, 85, 50, 80, 65],
    'Auditing': [65, 72, 45, 90, 88],
    'Taxation': [74, 60, 45, 85, 82],
    'Regulatory Framework for Business Transaction': [80, 85, 90, 70, 60]
}

df = pd.DataFrame(data)

# Generate labels based on thresholds
labels = []
for subject, scores in df.items():
    threshold = thresholds.get(subject)
    labels.append([1 if score < threshold else 0 for score in scores])

labels_df = pd.DataFrame(labels).transpose()

# Train the Decision Tree model
X = df
y = labels_df
model = DecisionTreeClassifier(random_state=42)
model.fit(X, y)

@app.route('/predict', methods=['POST'])
def predict():
    student_data = request.json  # Get the student data from the request

    # Convert the student data to a DataFrame for prediction
    student_df = pd.DataFrame([student_data])
    
    # Make prediction using the trained model
    student_predictions = model.predict(student_df)

    # Return the predictions as JSON
    return jsonify(student_predictions.tolist())

if __name__ == '__main__':
    app.run(debug=True)
