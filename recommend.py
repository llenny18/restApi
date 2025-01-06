from flask import Flask, request, jsonify
import logging
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Predefined recommendations for topics
predefined_recommendations = {
    'Fin_rep': 'Focus on understanding IFRS and GAAP standards. Practice creating financial reports.',
    'Fin_State': 'Review financial statement structures and elements. Correct errors in practice statements.',
    'Key_Accounting': 'Study key accounting principles, such as accruals and revenue recognition.',
    'Other': 'Explore advanced financial topics like forecasting and financial analysis.',
    'Specialized': 'Dive deeper into niche areas like international finance or environmental accounting.',
    'Part': 'Master the basics of journal entries and trial balances.',
    'Corporate': 'Study corporate equity, dividends, and transactions related to shareholders.',
    'Joint': 'Understand joint ventures and operations, including consolidation techniques.',
    'Revenue': 'Practice revenue recognition scenarios under ASC 606.',
    'Home_Office': 'Learn cost allocation and budgeting for corporate home office expenses.',
    'Combination': 'Explore accounting treatments for mergers and acquisitions.',
    'Consolidated': 'Practice consolidating financial statements and eliminating intercompany transactions.',
    'Derivatives': 'Understand valuation and reporting for financial derivatives like options and futures.',
    'Translation': 'Study foreign currency transaction translation and multinational reporting.',
    'no_profit': 'Learn about fund accounting and revenue recognition for non-profits.',
    'cost': 'Review cost accounting techniques like job-order costing and process costing.',
    'special': 'Focus on specialized topics like environmental accounting and fair value measurements.',
    'Man_Acc': 'Practice decision-making tools like cost-volume-profit analysis.',
    'Fin_Man': 'Study capital budgeting and risk management techniques.',
    'Eco': 'Review micro and macroeconomic principles and their business applications.',
    'Fundamentals': 'Understand the core principles of auditing, including risk assessment.',
    'Risk_based': 'Learn how to tailor audit procedures based on risk.',
    'Understanding': 'Strengthen knowledge of ethical guidelines and auditing standards.',
    'Audit_Evidence': 'Study evidence collection and evaluation methods.',
    'Audit_Completion': 'Focus on audit completion procedures and reporting findings.',
    'CIS': 'Learn about auditing computer systems and IT controls.',
    'Attestation': 'Understand attestation engagements like reviews and compilations.',
    'Governance': 'Study corporate governance principles and auditor roles.',
    'Risk_Response': 'Review strategies for responding to audit risks and issues.',
    'Principles': 'Master basic taxation principles like income tax computation.',
    'Remedies': 'Study legal remedies available to taxpayers.',
    'Income_Tax': 'Focus on income tax preparation for individuals and businesses.',
    'Transfer_Tax': 'Review estate and gift tax calculations.',
    'Business_Tax': 'Study corporate taxes, deductions, and credits.',
    'Doc_Stamp': 'Understand documentary stamp taxes on contracts and deeds.',
    'Excise_Tax': 'Review excise tax laws on goods like tobacco and alcohol.',
    'Gov_Tax': 'Learn about taxes specific to government transactions.',
    'Prefer_Tax': 'Explore preferential tax rates and conditions.',
    'LBT': 'Review local business taxes and their filing procedures.',
    'Bouncing': 'Study legal implications and penalties of bounced checks.',
    'Consumer': 'Understand consumer protection laws.',
    'Rehabilitation': 'Focus on business rehabilitation and bankruptcy processes.',
    'PHCA': 'Review anti-competitive practices under the Philippine Competition Act.',
    'Procurement': 'Learn government procurement laws and bidding processes.',
    'LBO': 'Study corporate formation, governance, and dissolution laws.',
    'LOBT': 'Understand laws covering partnerships and business transactions.',
    'Security_Law': 'Review regulations for securities and exchanges.',
    'Doing_Business': 'Learn about the Ease of Doing Business Act and compliance.'
}

# Subject-to-topics mapping
subject_to_topics_mapping = {
    "Financial Accounting and Reporting": [
        "Fin_rep", "Fin_State", "Key_Accounting", "Other", "Specialized"
    ],
    "Advanced Financial Accounting and Reporting": [
        "Part", "Corporate", "Joint", "Revenue", "Home_Office", "Combination",
        "Consolidated", "Derivatives", "Translation", "no_profit", "cost", "special"
    ],
    "Management Services": [
        "Man_Acc", "Fin_Man", "Eco"
    ],
    "Auditing": [
        "Fundamentals", "Risk_based", "Understanding", "Audit_Evidence", "Audit_Completion",
        "CIS", "Attestation", "Governance", "Risk_Response"
    ],
    "Taxation": [
        "Principles", "Remedies", "Income_Tax", "Transfer_Tax", "Business_Tax",
        "Doc_Stamp", "Excise_Tax", "Gov_Tax", "Prefer_Tax"
    ],
    "Regulatory Framework for Business Transaction": [
        "LBT", "Bouncing", "Consumer", "Rehabilitation", "PHCA", "Procurement",
        "LBO", "LOBT", "Security_Law", "Doing_Business"
    ]
}

@app.route('/generate_recommendations', methods=['POST'])
def generate_recommendations():
    try:
        data = request.json
        student_id = data.get("student_id")
        fields = data.get("fields", {})
        topic_data = data.get("topic_data", {})

        if not student_id or not fields or not topic_data:
            return jsonify({"error": "Invalid input"}), 400

        logging.info(f"Generating recommendations for student ID: {student_id}")

        # Generate recommendations grouped by subject using content-based filtering
        recommendations = {}
        tfidf_vectorizer = TfidfVectorizer()

        # Preprocess predefined recommendations into TF-IDF vectors
        topics = list(predefined_recommendations.keys())
        descriptions = list(predefined_recommendations.values())
        tfidf_matrix = tfidf_vectorizer.fit_transform(descriptions)

        for subject, topics_list in subject_to_topics_mapping.items():
            # Check if the subject is weak in topic_data
            if topic_data.get(subject, "").lower() == "weak":
                subject_recommendations = []
                for topic in topics_list:
                    # Include recommendations only for weak topics within weak subjects
                    if fields.get(topic, "").lower() == "weak":
                        if topic in predefined_recommendations:
                            # Compute similarity between topic description and all predefined topics
                            topic_description = predefined_recommendations[topic]
                            topic_vector = tfidf_vectorizer.transform([topic_description])
                            similarities = cosine_similarity(topic_vector, tfidf_matrix).flatten()

                            # Get the most similar recommendation
                            most_similar_index = similarities.argmax()
                            most_similar_recommendation = descriptions[most_similar_index]

                            subject_recommendations.append({
                                "recommendation": most_similar_recommendation  # Include the most relevant recommendation
                            })

                if subject_recommendations:
                    recommendations[subject] = subject_recommendations

        return jsonify({
            "student_id": student_id,
            "recommendations": recommendations
        }), 200

    except Exception as e:
        logging.error(f"Error generating recommendations: {e}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5003, debug=True)
