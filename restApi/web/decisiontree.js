// Sample input: Array of student data for the first 3 attempts
let studentData = [
    {
        'Financial_Score': 65,
        'Adv_Score': 70,
        'Mng_score': 75,
        'Auditing_Score': 80,
        'Taxation_Score': 85,
        'Framework_score': 90
    },
    {
        'Financial_Score': 50,
        'Adv_Score': 60,
        'Mng_score': 65,
        'Auditing_Score': 50,
        'Taxation_Score': 55,
        'Framework_score': 60
    }
];

// Decision tree thresholds (adjust based on what you consider weak)
const weakThreshold = 60;

// Function to check if the student is weak in any subject
function predictWeakness(data) {
    let predictions = {};
    
    // Loop through all subjects
    for (let subject in data) {
        let weakCount = 0;
        
        // Check how many times the student is below the weak threshold
        for (let i = 0; i < data[subject].length; i++) {
            if (data[subject][i] < weakThreshold) {
                weakCount++;
            }
        }
        
        // If 2 or more attempts are weak, predict weakness in the subject
        predictions[subject] = weakCount >= 2 ? "Weak" : "Not Weak";
    }

    return predictions;
}

// Prepare data for each subject (for first 3 attempts)
let formattedData = studentData.map(student => ({
    Financial_Score: [student.Financial_Score, student.Adv_Score, student.Mng_score],
    Adv_Score: [student.Adv_Score, student.Financial_Score, student.Auditing_Score],
    Mng_score: [student.Mng_score, student.Taxation_Score, student.Framework_score],
    Auditing_Score: [student.Auditing_Score, student.Taxation_Score, student.Mng_score],
    Taxation_Score: [student.Taxation_Score, student.Financial_Score, student.Adv_Score],
    Framework_score: [student.Framework_score, student.Auditing_Score, student.Taxation_Score]
}));

// Now apply the decision tree prediction on formatted data
formattedData.forEach(student => {
    console.log(predictWeakness(student));
});
