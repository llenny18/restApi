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

        // Encode the student data to JSON and then to Base64
        $json_data = json_encode($student_data);
        $encoded_data = base64_encode($json_data);

        // Path to the Node.js script
        $node_path = 'C:\\Program Files\\nodejs\\node.exe';  // Adjust the path to node executable
        $decisionweak_script = 'C:\\path\\to\\decisionweak.js';  // Adjust to the correct path

        // Command to run the Node.js script with the encoded data
        $command = escapeshellcmd("{$node_path} {$decisionweak_script} {$encoded_data}");

        // Execute the command and capture the output
        $output = shell_exec($command);

        // Decode the JSON output from the Node.js script
        $predictions = json_decode($output, true);

        // Error handling in case the output is not valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding the output from decisionweak.js.";
            exit();
        }
    } else {
        echo "No data found for the given student ID.";
        exit();
    }
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weakness Detection Result</title>
</head>
<body>
    <h1>Student Weakness Detection</h1>

    <h2>Weakness Predictions</h2>
    <p><strong>0 = Not Weak, 1 = Weak</strong></p>

    <table border="1">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Weakness Prediction</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // List the subjects and their corresponding weakness predictions
            $subjects = array_keys($student_data[0]); // Get the subjects from the first record
            foreach ($subjects as $index => $subject) {
                $prediction = $predictions[$index]; // Get the prediction for each subject
                echo "<tr><td>{$subject}</td><td>{$prediction}</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h2>Exam Attempts</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Attempt</th>
                <th>Financial Accounting and Reporting</th>
                <th>Advanced Financial Accounting and Reporting</th>
                <th>Management Services</th>
                <th>Auditing</th>
                <th>Taxation</th>
                <th>Regulatory Framework</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display the scores for each attempt
            foreach ($student_data as $index => $scores) {
                echo "<tr><td>{$labels[$index]}</td>";
                foreach ($scores as $score) {
                    echo "<td>{$score}</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
