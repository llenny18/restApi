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

// Retrieve scores from topic_score table
$sql = "SELECT * FROM topic_score WHERE student_id = '$student_id' ORDER BY exam_date DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Calculate total scores for each subject
    $financial_score = $row['Fin_rep'] + $row['Fin_State'] + $row['Key_Accounting'] + $row['Other'] + $row['Specialized'];
    $adv_score = $row['Part'] + $row['Corporate'] + $row['Joint'] + $row['Revenue'] + $row['Home_Office'] + $row['Combination'] + $row['Consolidated'] + $row['Derivatives'] + $row['Translation'] + $row['no_profit'] + $row['cost'] + $row['special'];
    $mng_score = $row['Man_Acc'] + $row['Fin_Man'] + $row['Eco'];
    $reg_score = $row['LBT'] + $row['Bouncing'] + $row['Consumer'] + $row['Rehabilitation'] + $row['PHCA'] + $row['Procurement'] + $row['LBO'] + $row['LOBT'] + $row['Security_Law'] + $row['Doing_Business'];
    $tax_score = $row['Principles'] + $row['Remedies'] + $row['Income_Tax'] + $row['Transfer_Tax'] + $row['Business_Tax'] + $row['Doc_Stamp'] + $row['Excise_Tax'] + $row['Gov_Tax'] + $row['Prefer_Tax'];
    $aud_score = $row['Fundamentals'] + $row['Risk-based'] + $row['Understanding'] + $row['Audit_Evidence'] + $row['Audit_Completion'] + $row['CIS'] + $row['Attestation'] + $row['Governance'] + $row['Risk_Response'];

    // Insert the total scores into the score_history table
    $stmt = $conn->prepare("INSERT INTO score_history (student_id, Adv_Score, Auditing_Score, Financial_Score, Mng_Score, Taxation_Score, Framework_score, exam_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute statement
    $stmt->bind_param("iiiiiii", $student_id, $adv_score, $aud_score, $financial_score, $mng_score, $tax_score, $reg_score);
    if ($stmt->execute()) {
        header("Location: Studenthp.php");
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }

    $stmt->close();
} else {
    header('hp.php');
}

// Close the database connection
$conn->close();
?>
