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

// Check if the Account_ID is set
if (isset($_GET['id'])) {
    $accountId = mysqli_real_escape_string($connection, $_GET['id']);
    
    // Start a transaction
    $connection->begin_transaction();

    try {
        // Step 1: Delete from score_history table where student_id matches Account_ID
        $deleteScoreHistorySQL = "DELETE FROM score_history WHERE student_id = '$accountId'";
        if ($connection->query($deleteScoreHistorySQL) !== TRUE) {
            throw new Exception("Error deleting from score_history: " . $connection->error);
        }

        // Step 2: Delete from topic_score table where student_id matches Account_ID
        $deleteTopicScoreSQL = "DELETE FROM topic_score WHERE student_id = '$accountId'";
        if ($connection->query($deleteTopicScoreSQL) !== TRUE) {
            throw new Exception("Error deleting from topic_score: " . $connection->error);
        }

        // Step 3: Delete from accounts table
        $deleteAccountSQL = "DELETE FROM accounts WHERE Account_ID = '$accountId'";
        if ($connection->query($deleteAccountSQL) !== TRUE) {
            throw new Exception("Error deleting from accounts: " . $connection->error);
        }

        // Commit the transaction if everything is successful
        $connection->commit();

        // Redirect back to the account management page after deletion
        header("Location: teacherHP.php");

    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $connection->rollback();
        echo "Failed to delete: " . $e->getMessage();
    }
}

// Close the connection
$connection->close();
?>
