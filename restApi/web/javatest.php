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

    // If data is found
    if ($result && $result->num_rows > 0) {
        $student_data = [];
        $labels = []; // To hold exam labels (e.g., "Attempt 1", "Attempt 2", "Attempt 3")

        // Collect the 3 latest quiz scores for each subject
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Accounting and Reporting' => $row['Financial_Score'],
                'Advanced Financial Accounting and Reporting' => $row['Adv_Score'],
                'Management Services' => $row['Mng_score'],
                'Auditing' => $row['Auditing_Score'],
                'Taxation' => $row['Taxation_Score'],
                'Regulatory Framework for Business Transaction' => $row['Framework_score']
            );
            $labels[] = "Attempt " . (count($student_data)); // Add labels like "Attempt 1", "Attempt 2"
        }

        // Calculate the average score for each subject over the 3 latest quizzes
        $averaged_scores = [];
        foreach ($student_data[0] as $subject => $score) {
            $total = 0;
            foreach ($student_data as $quiz) {
                $total += $quiz[$subject];
            }
            $averaged_scores[$subject] = $total / count($student_data);  // Store the average score
        }

        // Send the averaged scores to decisionweak.js and hybrid.js via command line
        $input_json = json_encode($averaged_scores);  // Sending averaged scores
        $encoded_json = base64_encode($input_json);

        // Path to decisionweak.js and hybrid.js
        $node_path = 'C:\\Program Files\\nodejs\\node.exe';  // Update path if needed
        $decisionweak_script = 'C:\\path\\to\\decisionweak.js';  // Full path to decisionweak.js
        $hybrid_script = 'C:\\path\\to\\hybrid.js';  // Full path to hybrid.js

        // Execute decisionweak.js (weakness detection)
        $command_weakness = escapeshellcmd($node_path . ' ' . $decisionweak_script . ' ' . escapeshellarg($encoded_json)) . ' 2>&1';
        $weakness_output = shell_exec($command_weakness);
        $weakness_output = trim($weakness_output);  // Remove extra whitespace

        if ($weakness_output === null) {
            echo "Error executing decisionweak.js.";
            exit();
        }

        // Log the weakness output for debugging
        file_put_contents('debug_output_weakness.txt', $weakness_output);

        // Decode the weakness predictions
        $weakness_predictions = json_decode($weakness_output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error in Weakness Detection: " . json_last_error_msg();
            exit();
        }

        // Execute hybrid.js (study plan generation)
        $command_study_plan = escapeshellcmd($node_path . ' ' . $hybrid_script . ' ' . escapeshellarg($encoded_json)) . ' 2>&1';
        $study_plan_output = shell_exec($command_study_plan);
        $study_plan_output = trim($study_plan_output);  // Remove extra whitespace

        if ($study_plan_output === null) {
            echo "Error executing hybrid.js.";
            exit();
        }

        // Log the study plan output for debugging
        file_put_contents('debug_output_study_plan.txt', $study_plan_output);

        // Decode the study plan from hybrid.js
        $decoded_study_plan = json_decode($study_plan_output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error in Study Plan Generation: " . json_last_error_msg();
            exit();
        }

        // Extract study plan from the decoded output
        $study_plan = $decoded_study_plan['study_plan'];

        // Display the study plan and weakness detection results
        echo "<h1>Student Progress</h1>";
        echo "<h3>Weakness Detection (1 = Weak, 0 = Not Weak):</h3>";
        echo "<pre>" . print_r($weakness_predictions, true) . "</pre>";

        echo "<h3>Study Recommendations:</h3>";
        echo "<table border='1'><tr><th>Subject</th><th>Recommendations</th></tr>";

        foreach ($study_plan as $subject => $recommendations) {
            // Combine all recommendations for the same subject into one row
            $recommendation_list = implode("<br>", $recommendations);
            echo "<tr><td>{$subject}</td><td>{$recommendation_list}</td></tr>";
        }

        echo "</table>";

    } else {
        echo "No student data found.";
    }

    // Close the connection
    $conn->close();
} else {
    // Redirect to login page if the user is not logged in as a student
    header("Location: index.php");
    exit();
}
?>
