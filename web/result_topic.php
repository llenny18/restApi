<?php
session_start();

// Check if the user is logged in as a student
if (isset($_SESSION['student']) && $_SESSION['student'] === 'student' && isset($_SESSION['username']) && isset($_SESSION['ID'])) {
    // Fetch the necessary session data
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
    $stmt = $conn->prepare("SELECT * FROM topic_score WHERE student_id = ? ORDER BY exam_date DESC LIMIT 3");
    $stmt->bind_param("s", $username);  // "s" means string type
    $stmt->execute();
    $result = $stmt->get_result();

    // If data is found
    if ($result && $result->num_rows > 0) {
        $student_data = [];
        
        // Collect the 3 latest topic scores for the student
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Reporting' => $row['Fin_rep'],
                'Financial Statement Analysis' => $row['Fin_State'],
                'Key Accounting Topics' => $row['Key_Accounting'],
                'Other Topics' => $row['Other'],
                'Specialized Accounting Topics' => $row['Specialized'],
                'Management Accounting' => $row['Man_Acc'],
                'Financial Management' => $row['Fin_Man'],
                'Economics' => $row['Eco'],
                'Auditing Fundamentals' => $row['Fundamentals'],
                'Risk based Auditing' => $row['Riskbased'],
                'Audit Evidence' => $row['Audit_Evidence'],
                'Audit Completion' => $row['Audit_Completion'],
                'CIS' => $row['CIS'],
                'Governance' => $row['Governance'],
                'Taxation' => $row['Principles'],
                'Business Transactions' => $row['LBT'],
                'Bouncing Checks' => $row['Bouncing'],
                'Consumer Protection' => $row['Consumer'],
                // Add other topics as needed...
            );
        }

        // Convert the student data to JSON format for easy transfer to Python
        $json_data = json_encode($student_data);

        // URL of the Python web server or API endpoint
        $python_url = 'http://localhost:5000/process_data'; // Assuming your Python server is running on localhost

        // Use cURL to send the data to the Python server (POST request)
        $ch = curl_init($python_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['student_data' => $json_data]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        // Execute cURL request and get the response from Python
        $response = curl_exec($ch);
        curl_close($ch);

        // Redirect to another page with the response
        header("Location: algoResult.php" . urlencode($response));
        exit();
    } else {
        echo "No data found for this student.";
    }
} else {
    echo "Unauthorized access.";
}
?>
