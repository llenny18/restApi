<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get unique topics and count their occurrences
$sql = "SELECT topic, COUNT(*) AS topic_count FROM reg GROUP BY topic ORDER BY topic";
$result = $conn->query($sql);

// Check if query was successful
if ($result->num_rows > 0) {
    // Display topics and their counts
    echo "<h1>regulatory Count</h1>";
    echo "<table border='1' style='width: 50%; margin: 20px auto; text-align: left;'>";
    echo "<tr><th>Topic</th><th>Count</th></tr>";

    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['topic'] . "</td><td>" . $row['topic_count'] . "</td></tr>";
    }

    echo "</table>";
} else {
    echo "No topics found.";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topic Counter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 30px;
            color: #12234e;
        }
        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #12234e;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

</body>
</html>
