<?php
// Establish connection to the database
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $password = $_POST['password'];
    $selectedRole = $_POST['role']; // Retrieve selected role from the dropdown
    $accountID = $_POST['account_ID']; // Retrieve account ID directly

    // Check if the account ID already exists in the accounts table
    $checkStmt = $conn->prepare("SELECT * FROM accounts WHERE Account_ID = ?");
    $checkStmt->bind_param("i", $accountID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Account ID already exists
        header("Location: admin.php?error=exists");
        exit(); // Ensure no further code is executed after redirect
    } else {
        // Prepare SQL statement for insertion into the accounts table
        $stmt = $conn->prepare("INSERT INTO accounts (Account_ID, password, Categories) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $accountID, $password, $selectedRole); // Bind parameters

        // Execute the prepared statement
        $stmt->execute();

        // Check if the insertion was successful
        if ($stmt->affected_rows > 0) {
            header("Location: teacherHP.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }

    // Close the check statement
    $checkStmt->close();
}

// Close connection
$conn->close();
?>
