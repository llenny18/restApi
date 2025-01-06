import sys
import json
import base64

# Predefined study recommendations
predefined_recommendations = {
    'Financial Accounting': {
        'Fin_rep': 'Review key financial reporting standards (e.g., IFRS, GAAP) and practice preparing financial statements. Focus on balance sheets, income statements, and cash flow statements.',
        'Fin_State': 'Revisit the structure and elements of financial statements. Practice identifying mistakes and making corrections in sample statements.',
        'Key_Accounting': 'Focus on the key principles of accounting (e.g., revenue recognition, matching principle, and accruals). Practice with basic accounting journal entries and ledgers.',
        'Other': 'Broaden knowledge in miscellaneous financial topics such as financial analysis techniques, ratios, and forecasting.',
        'Specialized': 'If the user struggles with niche areas such as international finance, banking, or specific regulatory topics, suggest focused materials.',
    },
    'Advanced Financial Accounting': {
        'Part': 'Review foundational financial accounting concepts like journal entries and trial balances. Practice with questions covering financial accounting basics.',
        'Corporate': 'Study corporate accounting topics such as equity, dividends, and shareholder transactions.',
        'Joint': 'Understand joint venture structures, accounting for joint operations, and consolidation.',
        'Revenue': 'Review revenue recognition principles (especially ASC 606). Practice revenue recognition in different scenarios.',
        'Home_Office': 'Focus on cost allocation, budgeting, and financial reporting for home office expenses in corporate structures.',
        'Combination': 'Study the methods of combining businesses (e.g., mergers and acquisitions). Understand the accounting treatments involved.',
        'Consolidated': 'Practice consolidating financial statements for a group of companies, including eliminating intercompany transactions.',
        'Derivatives': 'Focus on the accounting for financial instruments like options and futures. Practice questions related to the valuation and reporting of derivatives.',
        'Translation': 'Study the translation of foreign currency transactions and financial statements for multinational companies.',
        'no_profit': 'Review financial accounting for non-profit organizations, focusing on funds, grants, and revenue recognition.',
        'cost': 'Study cost accounting principles, such as job-order costing, process costing, and overhead allocation.',
        'special': 'Cover specialized financial accounting topics like environmental accounting, or fair value measurement.',
    },
    'Management Services': {
        'Man_Acc': 'Focus on managerial decision-making tools such as cost-volume-profit analysis, break-even analysis, and budgeting.',
        'Fin_Man': 'Study concepts in financial management such as capital budgeting, risk analysis, and financial planning.',
        'Eco': 'Review key economic principles such as supply and demand, market structures, and economic indicators.',
    },
    'Auditing': {
        'Fundamentals': 'Review the basic principles of auditing, including planning, risk assessment, and audit evidence.',
        'Risk-based': 'Focus on identifying audit risks and planning audit procedures based on risk assessments.',
        'Understanding': 'Strengthen knowledge of auditing standards and ethical guidelines for auditors.',
        'Audit_Evidence': 'Study how to gather and evaluate audit evidence, including internal controls and substantive testing.',
        'Audit_Completion': 'Review audit procedures for completing audits, preparing final reports, and communicating findings.',
        'CIS': 'Review the role of technology in auditing and the assessment of computer systems in audits.',
        'Attestation': 'Study different types of attestation engagements, including reviews, compilations, and agreed-upon procedures.',
        'Governance': 'Focus on corporate governance, including the role of auditors in governance processes.',
        'Risk_Response': 'Learn how auditors respond to identified risks and handle audit findings.',
    },
    'Taxation': {
        'Principles': 'Focus on basic taxation principles such as income taxation, tax deductions, and credits.',
        'Remedies': 'Study the legal remedies available to taxpayers, such as tax disputes, appeals, and penalties.',
        'Income_Tax': 'Deep dive into income tax laws and practice filing tax returns for individuals and businesses.',
        'Transfer_Tax': 'Focus on taxation of property transfers, including estate and gift taxes.',
        'Business_Tax': 'Study taxes on businesses, including corporate tax rates, tax credits, and deductions.',
        'Doc_Stamp': 'Study documentary stamp taxes on various documents like contracts and deeds.',
        'Excise_Tax': 'Review excise tax laws related to goods and services like tobacco and alcohol.',
        'Gov_Tax': 'Study government taxes and how to file tax returns related to government services.',
        'Prefer_Tax': 'Focus on preferential tax rates for specific industries or types of income.',
    },
    'Regulatory Framework for Business Transaction': {
        'LBT': 'Study the different taxes applicable to businesses operating locally, including business tax filing procedures.',
        'Bouncing': 'Review the laws and penalties surrounding bounced checks, including its legal implications.',
        'Consumer': 'Focus on consumer protection laws, including product safety, false advertising, and consumer rights.',
        'Rehabilitation': 'Study the laws related to business rehabilitation and bankruptcy. - Resource: Problem-solving exercises on rehabilitation processes.',
        'PHCA': 'Learn about the anti-competitive practices regulated under the Philippine Competition Act.',
        'Procurement': 'Review the Government Procurement Law, focusing on procurement procedures, bidding processes, and compliance.',
        'LBO': 'Study business organization laws, including corporation formation, governance, and dissolution.',
        'LOBT': 'Review laws covering business contracts, partnerships, and other business transactions.',
        'Security_Law': 'Focus on securities law, particularly the regulations around stocks, bonds, and securities exchanges.',
        'Doing_Business': 'Study the Ease of Doing Business Act and its impact on regulatory processes.',
    }
}


def generate_study_plan(student_data):
    study_plan = {}

    for subject, score in student_data.items():
        # Only add predefined recommendations for weak subjects
        if score < 70:  # For example, subject is weak if score < 70
            if subject in predefined_recommendations:
                study_plan[subject] = predefined_recommendations[subject]
    
    return study_plan

try:
    # Get JSON-encoded string from PHP (base64 format)
    encoded_input = sys.argv[1]
    decoded_data = base64.b64decode(encoded_input).decode('utf-8')
    student_data = json.loads(decoded_data)

    # Generate a study plan based on the student's weak subjects
    study_plan = generate_study_plan(student_data)

    # Return the study plan as JSON
    print(json.dumps({"study_plan": study_plan}))

except Exception as e:
    # Print any errors as JSON to ensure structured output
    error_output = {"error": str(e)}
    print(json.dumps(error_output))
