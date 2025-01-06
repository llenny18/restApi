<?php
// Check if the 'id' parameter is set in the GET request
if (isset($_GET['id'])) {
    // Get the student ID from the GET request
    $student_id = $_GET['id'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'cpa');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement with a parameter placeholder
    $stmt = $conn->prepare("SELECT * FROM score_history WHERE student_id = ?");
    
    // Bind the parameter to the placeholder
    $stmt->bind_param("s", $student_id); // Assuming student_id is a string. Use "i" for integer

    // Execute the prepared statement
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            echo "Student ID: " . $row['student_id'] . "<br>";
            echo "Score: " . $row['score'] . "<br>";
            echo "Date: " . $row['date'] . "<br>";
            echo "<hr>";
        }
    } else {
        echo "No records found for Student ID: " . htmlspecialchars($student_id);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "No student ID provided.";
}
?>
