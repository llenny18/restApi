<?php
session_start();

// Check if the user is logged in as a student
if (isset($_SESSION['student']) && $_SESSION['student'] === 'student') {
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

    // Query to get the latest 3 quiz attempts for the student, sorted by exam_date
    $sql = "SELECT * FROM topic_score WHERE student_id = $username ORDER BY exam_date DESC LIMIT 3";
    $result = $conn->query($sql);

    // If data is found
    if ($result && $result->num_rows > 0) {
        $student_data = [];

        // Collect the 3 latest quiz scores for each subject and topic
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Accounting and Reporting' => [
                    $row['Fin_rep'],
                    $row['Fin_State'],
                    $row['Key_Accounting'],
                    $row['Other'],
                    $row['Specialized']
                ],
                'Advanced Financial Accounting and Reporting' => [
                    $row['Part'],
                    $row['Corporate'],
                    $row['Joint'],
                    $row['Revenue'],
                    $row['Home_Office'],
                    $row['Combination'],
                    $row['Consolidated'],
                    $row['Derivatives'],
                    $row['Translation'],
                    $row['no_profit'],
                    $row['cost'],
                    $row['special']
                ],
                'Management Services' => [
                    $row['Man_Acc'],
                    $row['Fin_Man'],
                    $row['Eco']
                ],
                'Auditing' => [
                    $row['Fundamentals'],
                    $row['Risk-based'],
                    $row['Understanding'],
                    $row['Audit_Evidence'],
                    $row['Audit_Completion'],
                    $row['CIS'],
                    $row['Attestation'],
                    $row['Governance'],
                    $row['Risk_Response']
                ],
                'Regulatory Framework for Business Transaction' => [
                    $row['LBT'],
                    $row['Bouncing'],
                    $row['Consumer'],
                    $row['Rehabilitation'],
                    $row['PHCA'],
                    $row['Procurement'],
                    $row['LBO'],
                    $row['LOBT'],
                    $row['Security_Law'],
                    $row['Doing_Business']
                ],
                'Taxation' => [
                    $row['Principles'],
                    $row['Remedies'],
                    $row['Income_Tax'],
                    $row['Transfer_Tax'],
                    $row['Business_Tax'],
                    $row['Doc_Stamp'],
                    $row['Excise_Tax'],
                    $row['Gov_Tax'],
                    $row['Prefer_Tax']
                ]
            );
        }

        // Send the data to the Python script
        $input_json = json_encode($student_data);  // Sending the student data
        $encoded_json = base64_encode($input_json);

        // Full path to Python executable on Windows
        $python_path = 'C:\\Users\\Msi\\AppData\\Local\\Microsoft\\WindowsApps\\python3.exe';  // Replace with your Python path

        // Path to hybrid.py script
        $hybrid_script = 'C:\\xampp\\htdocs\\CPA\\hybrid.py';  // Replace with the full path to hybrid.py

        // Call hybrid.py to evaluate weaknesses and generate study plan
        $command = escapeshellcmd($python_path . ' ' . $hybrid_script . ' ' . escapeshellarg($encoded_json)) . ' 2>&1';
        $hybrid_output = shell_exec($command);
        $hybrid_output = trim($hybrid_output);

        // Log the command and output for debugging
        echo "Executing command: " . $command . "<br>";
        echo "Output from hybrid.py: " . htmlspecialchars($hybrid_output) . "<br>";

        // Decode the combined JSON output from hybrid.py
        $decoded_output = json_decode($hybrid_output, true);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error: " . json_last_error_msg() . "<br>";
        } else {
            // Only output the weaknesses and study plan as JSON
            $output = [
                'weaknesses' => $decoded_output['weaknesses'],
                'study_plan' => $decoded_output['study_plan']
            ];

            // Output the JSON
            echo json_encode($output);
        }
    } else {
        echo json_encode(['error' => 'No student data found.']);
    }
} else {
    // Redirect to login page if the user is not logged in as a student
    header("Location: index.php");
    exit();
}

// Close the connection
$conn->close();
?>
