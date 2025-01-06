<?php
// Start session
session_start();

// Check if the ID parameter is set in the URL
if (isset($_GET['id'])) {
    $accountId = $_GET['id'];  // Get the Account_ID from the URL
    
    // Store Account_ID in session for later use
    $_SESSION['account_id'] = $accountId; // Store Account_ID in session

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'cpa');
    if ($conn->connect_error) {
        die("Failed to connect: " . $conn->connect_error);
    } else {
        // Prepare SQL query to fetch the Account_ID and username
        $stmt = $conn->prepare("SELECT * FROM accounts WHERE Account_ID = ?");
        $stmt->bind_param("s", $accountId); // Bind the Account_ID to the SQL statement
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Fetch user data from the database
            $data = $result->fetch_assoc();
            $_SESSION['username'] = $data['Account_ID'];  // Assuming 'Account_ID' is the username field
        } else {
            // Handle case where Account_ID is not found
            $_SESSION['error_message'] = "Account not found.";
            header("Location: algoResultT.php");
            exit();
        }

        $stmt->close();
        $conn->close();
    }

    // Redirect to algoResultA.php
    header("Location: algoResultT.php");
    exit(); // Ensure no further code is executed after the redirect
} else {
    // If ID is not provided, redirect to algoResultA.php
    $_SESSION['error_message'] = "No student ID provided.";
    header("Location: algoResultT.php");
    exit(); // Ensure that the script stops here
}
?>
