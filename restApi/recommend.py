from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Recommendations for each subject
recommendations = {
    'Financial Accounting and Reporting': "Focus on mastering financial statement preparation and analysis.",
    'Advanced Financial Accounting and Reporting': "Review complex accounting standards and consolidation techniques.",
    'Management Services': "Practice problem-solving for management decision-making scenarios.",
    'Auditing': "Study auditing standards and practical applications in auditing financial statements.",
    'Taxation': "Familiarize yourself with current tax laws and computation techniques.",
    'Regulatory Framework for Business Transaction': "Understand legal frameworks and business law applications."
}

@app.route('/generate_recommendation', methods=['POST'])
def generate_recommendation():
    data = request.json  # Receive data from app.py

    # Generate recommendations based on the predictions
    recommendation_dict = {}
    for subject, status in data.items():
        if status == "Weak":
            recommendation_dict[subject] = recommendations.get(subject, "General improvement needed.")
        else:
            recommendation_dict[subject] = "Keep up the good work!"

    return jsonify(recommendation_dict)

if __name__ == '__main__':
    app.run(port=5001, debug=True)
