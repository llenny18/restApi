<?php
session_start();

// Check if the user is logged in as a student
if (isset($_SESSION['student']) && $_SESSION['student'] === 'student' && isset($_SESSION['username']) && isset($_SESSION['ID'])) {
    // If the user is a student, fetch the necessary session data
    $username = $_SESSION['username']; // Username of the logged-in student
    $student_Id = $_SESSION['ID'];     // ID of the logged-in student

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Connect to the MySQL database
    $conn = new mysqli('localhost', 'root', '', 'cpa');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM score_history WHERE student_id = ? ORDER BY exam_date DESC LIMIT 3");
    $stmt->bind_param("s", $username);  // "s" means string type
    $stmt->execute();
    $result = $stmt->get_result();

    // If data is found
    if ($result && $result->num_rows > 0) {
        $student_data = [];
        $labels = []; // To hold exam labels (e.g., "Attempt 1", "Attempt 2", "Attempt 3")

        // Collect the 3 latest quiz scores for each subject
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Accounting and Reporting' => $row['Financial_Score'],
                'Advanced Financial Accounting and Reporting' => $row['Adv_Score'],
                'Management Services' => $row['Mng_score'],
                'Auditing' => $row['Auditing_Score'],
                'Taxation' => $row['Taxation_Score'],
                'Regulatory Framework for Business Transaction' => $row['Framework_score']
            );
            $labels[] = "Attempt " . (count($student_data)); // Add labels like "Attempt 1", "Attempt 2"
        }

        // Calculate the average score for each subject over the 3 latest quizzes
        $averaged_scores = [];
        foreach ($student_data[0] as $subject => $score) {
            $total = 0;
            foreach ($student_data as $quiz) {
                $total += $quiz[$subject];
            }
            $averaged_scores[$subject] = $total / count($student_data);  // Store the average score
        }

        // Send the averaged scores to the Python script
        $input_json = json_encode($averaged_scores);  // Sending averaged scores
        $encoded_json = base64_encode($input_json);

        // Full path to Python executable on Windows
        $python_path = 'C:\\Users\\Msi\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';  // Replace with your Python path

        // Path to hybrid.py script
        $hybrid_script = 'C:\\xampp\\htdocs\\CPA\\hybrid.py';  // Replace with the full path to hybrid.py

        // Call hybrid.py to generate the study plan
        $command = escapeshellcmd($python_path . ' ' . $hybrid_script . ' ' . escapeshellarg($encoded_json)) . ' 2>&1';
        $hybrid_output = shell_exec($command);
        $hybrid_output = trim($hybrid_output);  // Remove any extra whitespace

        // Check if the Python script executed successfully
        if ($hybrid_output === null) {
            echo "Error executing Python script. Please check the paths and script.";
            exit();
        }

        // Decode the combined JSON output from hybrid.py
        $decoded_output = json_decode($hybrid_output, true);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error: " . json_last_error_msg() . "<br>";
            echo "Output received from hybrid.py: " . htmlspecialchars($hybrid_output);
        } else {
            // Extract the study plan from the decoded output
            $study_plan = $decoded_output['study_plan'];  // This will be an associative array of subjects with study resources

            // Display the username and study recommendations
            echo "<h1>Student ID: $username</h1>";
            echo "<h3>Username: $username</h3>";  // Display the username
            echo "<h3>Study Recommendations:</h3>";
            echo "<table border='1'><tr><th>Subject</th><th>Recommendation</th></tr>";

            // Loop through the study plan and display recommendations
            foreach ($study_plan as $subject => $recommendations) {
                // Combine all recommendations for the same subject into one row
                $recommendation_list = implode("<br>", $recommendations);
                echo "<tr><td>{$subject}</td><td>{$recommendation_list}</td></tr>";
            }

            echo "</table>";

            // Prepare the data for the line chart
            $line_chart_data = [];
            $subjects = array_keys($student_data[0]); // Subjects to plot

            // Extract data for each subject (score progression over attempts)
            foreach ($subjects as $subject) {
                $scores = [];
                foreach ($student_data as $quiz) {
                    $scores[] = $quiz[$subject];
                }
                $line_chart_data[$subject] = $scores;
            }

            // Convert PHP arrays to JSON for JavaScript
            $line_chart_data_json = json_encode($line_chart_data);
            $labels_json = json_encode($labels);
        }
    } else {
        echo "No student data found.";
    }

    // Close the connection
    $conn->close();
} else {
    // Redirect to login page if the user is not logged in as a student
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        /* Title Styling */
        h2 {
            font-size: 28px;
            color: #12234e;
            margin: 20px 0;
        }

        /* Chart Container */
        #progressChart {
            margin-top: 30px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
        }

        /* Recommendation Table Styling */
        .recommendation-table {
            width: 80%;
            margin-top: 40px;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .recommendation-table th, .recommendation-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .recommendation-table th {
            background-color: #12234e;
            color: #fff;
        }

        .recommendation-table td {
            background-color: #f9f9f9;
        }

        .recommendation-table tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .recommendation-table {
                width: 95%;
            }

            h2 {
                font-size: 24px;
            }

            #progressChart {
                width: 90%;
                height: 250px;
            }
        }

        /* Container for better alignment */
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Main Container -->
<div class="container">
    <h2>Student Progress: Exam Attempts</h2>

    <!-- Chart Container -->
    <canvas id="progressChart" width="400" height="200"></canvas>

    <!-- Recommendations Section -->
    <div class="recommendation-section">
            <!-- Recommendations will be injected here -->
        </table>
    </div>
</div>

<script>
    // Parse the PHP variables into JavaScript
    const lineChartData = <?php echo $line_chart_data_json; ?>;
    const labels = <?php echo $labels_json; ?>;

    // Prepare the datasets for the line chart
    const datasets = Object.keys(lineChartData).map(subject => ({
        label: subject,
        data: lineChartData[subject],
        borderColor: getRandomColor(),
        fill: false,
    }));

    // Create the line chart
    new Chart(document.getElementById('progressChart'), {
        type: 'line',
        data: {
            labels: labels, // Attempts
            datasets: datasets, // Scores for each subject
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Exam Attempt',
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: 'Score',
                    },
                    suggestedMin: 0,
                    suggestedMax: 100,
                },
            },
        },
    });

    // Utility function to generate random color for each subject line
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>

</body>
</html>