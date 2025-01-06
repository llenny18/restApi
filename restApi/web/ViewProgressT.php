<?php
// Start session
session_start();

// Check if the ID parameter is set in the URL
if (isset($_GET['id'])) {
    $accountId = $_GET['id'];  // Get the Account_ID from the URL
    
    // Store Account_ID in session for later use
    $_SESSION['account_id'] = $accountId; // Store Account_ID in session
    
    // Redirect to decision.php
    header("Location: decision.php");
    exit(); // Ensure no further code is executed after the redirect
} else {
    // If ID is not provided, redirect to decision.php
    $_SESSION['error_message'] = "No student ID provided.";
    header("Location: decision.php");
    exit(); // Ensure that the script stops here
}
?>
