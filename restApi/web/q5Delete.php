<?php
if ( isset($_GET["id"])){
    $id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "cpa";

    //Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $connection->prepare("DELETE FROM tax WHERE tax_id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Redirect to admin page after successful deletion
        header("Location: q5.php");
        exit;
    } else {
        // Handle execution errors
        echo "Error deleting record: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $connection->close();
} else {
    echo "Error: ID parameter missing.";
}
?>