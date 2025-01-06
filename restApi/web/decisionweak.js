const base64 = require('base-64');

try {
    // Read the base64 encoded JSON string from the command line
    const input_data = process.argv[2];

    if (!input_data) {
        throw new Error("No input data provided");
    }

    // Decode the base64 string to JSON format
    const decoded_data = base64.decode(input_data);

    // Parse the JSON string into a JavaScript object
    const student_data = JSON.parse(decoded_data);

    // Define the high scores for each category (adjusted for your requirements)
    const high_scores = {
        'Financial Accounting and Reporting': 70,
        'Advanced Financial Accounting and Reporting': 72,
        'Management Services': 72,
        'Auditing': 72,
        'Taxation': 72,
        'Regulatory Framework for Business Transaction': 100
    };

    // Define the threshold for weakness (score below threshold is considered weak)
    const weakness_threshold = 0.75;  // This represents 75% of the high score

    // Generate predictions based on the threshold and high score for each subject
    function detect_weaknesses(student_data) {
        const predictions = [];
        for (const [subject, score] of Object.entries(student_data)) {
            // Get the high score for the current subject
            const max_score = high_scores[subject] || 100;  // Default to 100 if the subject is not found
            // Calculate the threshold as 75% of the max score
            const threshold_score = max_score * weakness_threshold;
            const int_score = parseInt(score);  // Convert the score to an integer
            // If the student's score is below the threshold, mark it as weak
            if (int_score < threshold_score) {
                predictions.push(1);  // Weak
            } else {
                predictions.push(0);  // Not weak
            }
        }
        return predictions;
    }

    // Get predictions for the student's scores
    const predictions = detect_weaknesses(student_data);

    // Output the predictions as JSON
    console.log(JSON.stringify(predictions));
} catch (error) {
    console.error("Error in decisionweak.js:", error.message);
    // Output a JSON error response
    console.log(JSON.stringify({ error: "An error occurred in decisionweak.js", message: error.message }));
}
