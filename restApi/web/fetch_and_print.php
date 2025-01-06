<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Exam Results</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS file -->
    <style>
        /* General styles for the body */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container for the main content */
        .container {
            width: 80%;
            max-width: 1200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }

        /* Header styling */
        h2 {
            font-size: 24px;
            color: #12234e; /* Dark blue */
            margin-bottom: 20px;
            text-align: center;
        }

        /* Style for individual result boxes */
        .result-box {
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Label for the exam */
        .result-box h3 {
            font-size: 20px;
            color: #12234e;
        }

        /* Style for displaying exam results */
        .result-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        /* Styling for each result block */
        .result-item {
            width: 48%; /* Two items per row */
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* For error message (if no data available) */
        .no-data {
            color: #d9534f; /* Red */
            font-size: 18px;
            text-align: center;
        }

        /* Button styling for possible actions */
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #12234e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .button:hover {
            background-color: #0d1b33;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    if (isset($_SESSION['student_data'])) {
        $student_data = $_SESSION['student_data'];

        // Output the data in a structured format
        echo "<h2>Processed Student Exam Data:</h2>";

        // Create a box for each student's exam attempt
        foreach ($student_data as $index => $attempt) {
            echo "<div class='result-box'>";
            echo "<h3>Attempt " . ($index + 1) . "</h3>";
            echo "<div class='result-content'>";
            foreach ($attempt as $subject => $score) {
                echo "<div class='result-item'>";
                echo "<strong>$subject:</strong> $score";
                echo "</div>";
            }
            echo "</div>"; // Close result-content
            echo "</div>"; // Close result-box
        }

        // Optionally clear the session data
        unset($_SESSION['student_data']);
    } else {
        echo "<p class='no-data'>No student data available.</p>";
    }
    ?>

    <!-- Optional: Add a button to go back to the previous page or other actions -->
    <a href="index.php" class="button">Go Back to Dashboard</a>
</div>

</body>
</html>
