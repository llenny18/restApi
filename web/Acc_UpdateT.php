<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);
	
// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the updated Account_ID, password, and ID from the form
if (isset($_POST['account_ID']) && isset($_POST['password']) && isset($_POST['ID'])) {
    $accountId = mysqli_real_escape_string($connection, $_POST['account_ID']);
    $newPassword = mysqli_real_escape_string($connection, $_POST['password']);
    $id = mysqli_real_escape_string($connection, $_POST['ID']); // Assuming ID is passed in the form

    // Get the old Account_ID from the database
    $oldAccountIdQuery = "SELECT Account_ID FROM accounts WHERE ID = '$id'";
    $oldAccountIdResult = $connection->query($oldAccountIdQuery);
    if ($oldAccountIdResult && $oldAccountIdResult->num_rows > 0) {
        $oldAccountIdRow = $oldAccountIdResult->fetch_assoc();
        $oldAccountId = $oldAccountIdRow['Account_ID'];
    } else {
        die("Error: Account_ID not found");
    }

    // Start a transaction
    $connection->begin_transaction();

    try {
        // Update the account details in the 'accounts' table
        $updateAccountSQL = "UPDATE accounts SET password = '$newPassword', Account_ID = '$accountId' WHERE ID = '$id'";
        if ($connection->query($updateAccountSQL) !== TRUE) {
            throw new Exception("Error updating account: " . $connection->error);
        }

        // Now, update all occurrences of student_id that match the old Account_ID ($oldAccountId) in the score_history table
        $updateScoreHistorySQL = "UPDATE score_history SET student_id = '$accountId' WHERE student_id = '$oldAccountId'";
        if ($connection->query($updateScoreHistorySQL) !== TRUE) {
            throw new Exception("Error updating score_history: " . $connection->error);
        }

        // Now, update all occurrences of student_id that match the old Account_ID ($oldAccountId) in the topic_score table
        $updateTopicScoreSQL = "UPDATE topic_score SET student_id = '$accountId' WHERE student_id = '$oldAccountId'";
        if ($connection->query($updateTopicScoreSQL) !== TRUE) {
            throw new Exception("Error updating topic_score: " . $connection->error);
        }

        // Commit the transaction
        $connection->commit();

        // Redirect back to the account management page after update
        header("Location: teacherHP.php");

    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $connection->rollback();
        echo "Failed to update: " . $e->getMessage();
    }
}

// Close the connection
$connection->close();
?>
