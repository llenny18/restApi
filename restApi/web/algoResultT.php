<?php
session_start();

// Ensure the user is logged in
if (isset($_SESSION['student']) && $_SESSION['student'] === 'student' && isset($_SESSION['username']) && isset($_SESSION['ID'])) {
    $username = $_SESSION['username']; // Logged-in student's username
    $student_Id = $_SESSION['ID'];     // Student's unique ID

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'cpa');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch student data
    $stmt = $conn->prepare("SELECT * FROM score_history WHERE student_id = ? ORDER BY exam_date DESC LIMIT 3");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $student_data = [];

        // Prepare data for the Python API
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Accounting and Reporting' => $row['Financial_Score'],
                'Advanced Financial Accounting and Reporting' => $row['Adv_Score'],
                'Management Services' => $row['Mng_score'],
                'Auditing' => $row['Auditing_Score'],
                'Taxation' => $row['Taxation_Score'],
                'Regulatory Framework for Business Transaction' => $row['Framework_score']
            );
        }
        
        // Send data to Python Flask API
        $ch = curl_init("http://localhost:5000/predict/$username");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($student_data[0]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        if ($response === false) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            // Decode JSON response
            $predictions = json_decode($response, true);
            if (isset($predictions['predictions']) && isset($predictions['recommendations'])) {
                foreach ($predictions['predictions'] as $subject => $status) {
                }
                foreach ($predictions['recommendations'] as $subject => $recommendation) {
                }
            } else {
                echo "Error: Invalid data received from the API.";
            }
        }

        curl_close($ch);
    } else {
        echo "No records found for the student.";
    }
} else {
    echo "Unauthorized access.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Prediction and Recommendations</title>
    <link href="Style/algo.css" rel="stylesheet" type="text/css">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
</head>
<body>
<div class="header-top">
        <div class="school-info">
            <img src="Style/logo.png" alt="School Logo"> 
            <div>
                <h1>Colegio de San Juan de Letran Calamba</h1>
                <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
            </div>
        </div>

        <div class="logo-container">
            <img src="Style/cpace1.png" alt="CPAce Logo" class="logo">
        </div>
        
        <div class="user-info">
            <span class="username" onclick="toggleDropdown()"><?php echo htmlspecialchars($username); ?></span>
            <div id="user-dropdown" class="dropdown hidden">
                <ul>
                    <li><a href="index.php">Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            console.log("Dropdown toggled");  // Debugging line
            dropdown.classList.toggle('hidden');
        }
    </script>

    <div class="container">
        <h3>CPAce Weakness Prediction</h3>
        <?php if (isset($predictions['predictions']) && isset($predictions['recommendations'])): ?>
            <div class="section">
                <h4>Predictions:</h4>
                <div class="result">
                    <?php foreach ($predictions['predictions'] as $subject => $status): ?>
                        <p><span class="subject"><?= $subject ?></span>: <span class="status"><?= $status ?></span></p>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="section">
                <h4>Recommendations:</h4>
                <div class="result">
                    <?php foreach ($predictions['recommendations'] as $subject => $recommendation): ?>
                        <p><span class="subject"><?= $subject ?></span>: <?= $recommendation ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="error">Error: Invalid data received from the API.</p>
        <?php endif; ?>
    </div>
</body>
</html>
