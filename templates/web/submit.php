<?php
// Start session to ensure the user is logged in
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student ID from session (using username)
$student_id = $_SESSION['username'];

// Initialize scores for each topic
$scores = [
    'Fin_State' => 0,
    'Fin_Rep' => 0,
    'Key_Accounting' => 0,
    'Other' => 0,
    'Specialized' => 0,
    'Part' => 0,
    'Corporate' => 0,
    'Joint' => 0,
    'Revenue' => 0,
    'Home_Office' => 0,
    'Combination' => 0,
    'Consolidated' => 0,
    'Derivatives' => 0,
    'Translation' => 0,
    'no_profit' => 0,
    'cost' => 0,
    'special' => 0,
    'Man_Acc' => 0,
    'Fin_Man' => 0,
    'Eco' => 0,
    'Fundamentals' => 0,
    'Risk_based' => 0,
    'Understanding' => 0,
    'Audit_Evidence' => 0,
    'Audit_Completion' => 0,
    'CIS' => 0,
    'Attestation' => 0,
    'Governance' => 0,
    'Risk_Response' => 0,
    'Principles' => 0,
    'Remedies' => 0,
    'Income_Tax' => 0,
    'Transfer_Tax' => 0,
    'Business_Tax' => 0,
    'Doc_Stamp' => 0,
    'Excise_Tax' => 0,
    'Gov_Tax' => 0,
    'Prefer_Tax' => 0,
    'LBT' => 0,
    'Bouncing' => 0,
    'Consumer' => 0,
    'Rehabilitation' => 0,
    'PHCA' => 0,
    'Procurement' => 0,
    'LBO' => 0,
    'LOBT' => 0,
    'Security_Law' => 0,
    'Doing_Business' => 0
];

// Retrieve submitted answers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answers'])) {
    $answers = $_POST['answers'];

    // Debug: Check if answers are correctly passed
    // Uncomment the next line to check the answers array
    // var_dump($answers);

    // Loop through submitted answers and check correctness
    foreach ($answers as $questionId => $answer) {
        // Determine the table and primary key based on questionId format
        list($tablePrefix, $id) = explode('_', $questionId, 2);

        $table = null;
        $primaryKey = null;
        
        switch ($tablePrefix) {
            case 'financial':
                $table = 'financial';
                $primaryKey = 'fin_id';
                break;
            case 'tax':
                $table = 'tax';
                $primaryKey = 'tax_id';
                break;
            case 'adv':
                $table = 'adv';
                $primaryKey = 'adv_id';
                break;
            case 'mng':
                $table = 'mng';
                $primaryKey = 'mng_id';
                break;
            case 'aud':
                $table = 'aud';
                $primaryKey = 'aud_id';
                break;
            case 'reg':
                $table = 'reg';
                $primaryKey = 'reg_id';
                break;
            default:
                continue; // Unknown question ID format
        }

        // Fetch the correct answer and topic from the database
        if ($table && $primaryKey) {
    $sql = "SELECT answer, topic FROM $table WHERE $primaryKey = '$id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correctAnswer = $row['answer'];
        $topic = $row['topic'];

        // Replace spaces and hyphens with underscores in topic name to match score keys
        $formattedTopic = str_replace([' ', '-'], '_', $topic);

        // Check if the user answered correctly and update scores
        if ($answer == $correctAnswer) {
            if (isset($scores[$formattedTopic])) {
                $scores[$formattedTopic]++;
            } else {
                error_log("Unexpected topic: $formattedTopic"); // Debug unknown topics
            }
        }
    }
}
}


    // Prepare columns and values for insertion
    $columns = "`student_id`, `exam_date`";
    $values = "'$student_id', '" . date('Y-m-d H:i:s') . "'";

    // Append the scores to columns and values
    foreach ($scores as $topic => $score) {
        $columns .= ", `$topic`";
        $values .= ", $score";
    }

    // Debug: Check if columns and values are correct
    // Uncomment the next line to see the generated SQL
    // echo $insertSql = "INSERT INTO topic_score ($columns) VALUES ($values)"; 

    // Insert the topic scores into the database
    $insertSql = "INSERT INTO topic_score ($columns) VALUES ($values)";
    if ($conn->query($insertSql) === TRUE) {
        // Redirect to the next script for score history insertion
        header("Location: total_score.php");
        exit();
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
} else {
    echo "No answers were submitted.";
}


// Close the database connection
$conn->close();
?>
