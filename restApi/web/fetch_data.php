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

        // Store the data in the session so it can be accessed in the next script
        $_SESSION['student_data'] = $student_data;

// Redirect to fetch_and_print.php after storing data in session
header("Location: fetch_and_print.php");
exit();

    } else {
        // If no records found
        echo json_encode(['error' => 'No data found']);
    }

} else {
    // If the student is not logged in
    echo json_encode(['error' => 'Not logged in']);
}
?>
