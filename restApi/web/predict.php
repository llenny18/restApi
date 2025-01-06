<?php
session_start();

// Check if the user is logged in as a student
if (isset($_SESSION['student']) && $_SESSION['student'] === 'student' && isset($_SESSION['username']) && isset($_SESSION['ID'])) {
    // If the user is a student, fetch the necessary session data
    $username = $_SESSION['username']; // Username of the logged-in student
    $student_Id = $_SESSION['ID'];     // ID of the logged-in student

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Connect to the MySQL database
    $conn = new mysqli('localhost', 'root', '', 'cpa');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM score_history WHERE student_id = ? ORDER BY exam_date DESC LIMIT 3");
    $stmt->bind_param("s", $username);  // "s" means string type
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result as an associative array
    $scores = [];
    while ($row = $result->fetch_assoc()) {
        $scores[] = $row;
    }

    // Close the connection
    $conn->close();

    // Encode the data in base64 for JavaScript use
    $jsonData = json_encode($scores);
    $base64Data = base64_encode($jsonData);
} else {
    // If not logged in as a student, redirect to login page or show an error
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Weakness Prediction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .results {
            margin-top: 20px;
        }
        .subject {
            font-weight: bold;
        }
        .weak {
            color: red;
        }
        .not-weak {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Subject Weakness Prediction</h1>

    <!-- Section where results will be displayed -->
    <div class="results" id="predictionResults"></div>

    <script>
        // Pass the base64 encoded data from PHP to JavaScript
        const inputData = "<?php echo $base64Data; ?>";

        // Function to decode a base64 string
        function decodeBase64(input) {
            // Decode the base64 string to a JSON string
            const decodedData = atob(input);
            // Parse the decoded JSON string into a JavaScript object
            return JSON.parse(decodedData);
        }

        // Define the high scores for each category (adjusted for your requirements)
        const highScores = {
            'Financial Accounting and Reporting': 70,
            'Advanced Financial Accounting and Reporting': 72,
            'Management Services': 72,
            'Auditing': 72,
            'Taxation': 72,
            'Regulatory Framework for Business Transaction': 100
        };

        // Define the threshold for weakness (score below threshold is considered weak)
        const weaknessThreshold = 0.75;  // This represents 75% of the high score

        // Function to detect weaknesses based on the student's data
        function detectWeaknesses(studentData) {
            const predictions = [];
            
            for (const subject in studentData) {
                const score = parseInt(studentData[subject], 10); // Convert the score to an integer
                const maxScore = highScores[subject] || 100; // Default to 100 if the subject is not found
                
                // Calculate the threshold as 75% of the max score
                const thresholdScore = maxScore * weaknessThreshold;

                // If the student's score is below the threshold, mark it as weak
                if (score < thresholdScore) {
                    predictions.push(1);  // Weak
                } else {
                    predictions.push(0);  // Not weak
                }
            }

            return predictions;
        }

        // Decode the base64 input data to a JSON object
        const studentData = decodeBase64(inputData);

        // Prepare data for each subject (for first 3 attempts)
        let formattedData = studentData.map(student => ({
            Financial_Score: [student.Financial_Score, student.Adv_Score, student.Mng_score],
            Adv_Score: [student.Adv_Score, student.Financial_Score, student.Auditing_Score],
            Mng_score: [student.Mng_score, student.Taxation_Score, student.Framework_score],
            Auditing_Score: [student.Auditing_Score, student.Taxation_Score, student.Mng_score],
            Taxation_Score: [student.Taxation_Score, student.Financial_Score, student.Adv_Score],
            Framework_score: [student.Framework_score, student.Auditing_Score, student.Taxation_Score]
        }));

        // Function to display the predictions on the webpage
        function displayResults() {
            let resultsDiv = document.getElementById('predictionResults');
            resultsDiv.innerHTML = ''; // Clear previous results

            // Loop through formatted data and display the predictions
            formattedData.forEach((student, index) => {
                let studentResults = detectWeaknesses(student);
                let studentDiv = document.createElement('div');
                studentDiv.classList.add('student');

                // Display the results for this student
                studentDiv.innerHTML = `<h3>Student ${index + 1} Prediction:</h3>`;
                
                for (let subject in studentResults) {
                    let resultClass = studentResults[subject] === 1 ? 'weak' : 'not-weak';
                    studentDiv.innerHTML += `
                        <p class="subject ${resultClass}">${subject}: ${studentResults[subject] === 1 ? 'Weak' : 'Not Weak'}</p>
                    `;
                }

                resultsDiv.appendChild(studentDiv);
            });
        }

        // Display results when the page loads
        window.onload = displayResults;
    </script>
</body>
</html>
