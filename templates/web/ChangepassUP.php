<?php
session_start();
if (isset($_POST['ID'], $_POST['current-password'], $_POST['new-password'])) {
    $ID = $_POST['ID'];
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'cpa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the current password from the database
    $query = "SELECT password FROM accounts WHERE ID = '$ID'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password']; // Password from database

        // Check if the entered current password matches the one in the database
        if ($currentPassword === $storedPassword) {
            // Proceed to change the password
            $updateQuery = "UPDATE accounts SET password = '$newPassword' WHERE ID = '$ID'";

            if ($conn->query($updateQuery) === TRUE) {
                // Password updated successfully
                header('location: studenthp.php');

                // Now, update all occurrences of student_id that match the old Account_ID in the score_history and topic_score tables
                $oldAccountId = $ID; // Assuming $oldAccountId is the ID that needs to be replaced
                $updateScoreHistorySQL = "UPDATE score_history SET student_id = '$ID' WHERE student_id = '$oldAccountId'";
                if ($conn->query($updateScoreHistorySQL) !== TRUE) {
                    throw new Exception("Error updating score_history: " . $conn->error);
                }

                // Now, update all occurrences of student_id that match the old Account_ID in the topic_score table
                $updateTopicScoreSQL = "UPDATE topic_score SET student_id = '$ID' WHERE student_id = '$oldAccountId'";
                if ($conn->query($updateTopicScoreSQL) !== TRUE) {
                    throw new Exception("Error updating topic_score: " . $conn->error);
                }

            } else {
                echo "<script>alert('Error updating password.');</script>";
            }
        } else {
            echo "<script>alert('Current password does not match!');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
}
?>
