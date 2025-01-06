<?php
session_start();

// Ensure the user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['account_id'])) {  // Changed from $_SESSION['ID'] to $_SESSION['account_id']
    $username = $_SESSION['username']; // Logged-in student's username
    $student_Id = $_SESSION['account_id'];  // Corrected to $_SESSION['account_id']

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $conn = new mysqli('localhost', 'root', '', 'cpa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM score_history WHERE student_id = ? ORDER BY exam_date DESC LIMIT 5");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $recommendations = [];
    $grouped_recommendations = [];
    $error = null;

    if ($result && $result->num_rows > 0) {
        $student_data = [];
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'exam_date' => $row['exam_date'],
                'Financial Accounting and Reporting' => $row['Financial_Score'],
                'Advanced Financial Accounting and Reporting' => $row['Adv_Score'],
                'Management Services' => $row['Mng_score'],
                'Auditing' => $row['Auditing_Score'],
                'Taxation' => $row['Taxation_Score'],
                'Regulatory Framework for Business Transaction' => $row['Framework_score']
            );
        }

        $ch = curl_init("http://localhost:5000/predict/$username");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($student_data[0]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);
        if ($response === false) {
            $error = "cURL Error: " . curl_error($ch);
        } else {
            $predictions = json_decode($response, true);
            if (isset($predictions['recommendations'])) {
                $recommendations = $predictions['recommendations'];
                // Group recommendations by subject
                foreach ($recommendations as $rec) {
                    $subject = $rec['subject'];
                    if (!isset($grouped_recommendations[$subject])) {
                        $grouped_recommendations[$subject] = [];
                    }
                    $grouped_recommendations[$subject][] = [
                        'topic' => $rec['topic'],
                        'recommendation' => $rec['recommendation']
                    ];
                }
            } else {
                $error = "Error: Invalid data received from the API.";
            }
        }
        curl_close($ch);
    } else {
        $error = "No records found for the student.";
    }
} else {
    $error = "Unauthorized access.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Recommendations and Scores</title>
    <link href="Style/results.css" rel="stylesheet" type="text/css">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<header class="header">
        <div class="container">
            <div class="logo">
                <img src="http://localhost/restApi/web/Style/logo.png" alt="Logo" class="logo-img">
                <div class="school-info">
                    <h1>Colegio de San Juan de Letran Calamba</h1>
                    <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
                </div>
            </div>
        </div>
    </header>
<body>
<a href="BackT.php" class="back-button">Back</a>
<h1> Student ID: <?php echo htmlspecialchars($username); ?> </h1>
<div class="container">
    
    <h1>Student Recommendations and Score History</h1>
    <canvas id="scoreChart" width="400" height="200"></canvas>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($grouped_recommendations)): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Recommendation</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped_recommendations as $subject => $recs): ?>
                <?php foreach ($recs as $index => $rec): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td rowspan="<?= count($recs) ?>"><?= htmlspecialchars($subject) ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($rec['recommendation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        
        <script>
            const ctx = document.getElementById('scoreChart').getContext('2d');

            // Subject names
            const labels = <?= json_encode(array_column($student_data, 'exam_date')); ?>; // Dates of the exams

            // Data for each subject
            const data = {
                labels: labels,
                datasets: [
                    {
                        label: 'Financial Accounting and Reporting',
                        data: <?= json_encode(array_column($student_data, 'Financial Accounting and Reporting')); ?>,
                        fill: false,
                        borderColor: 'rgba(255, 99, 132, 1)', // Red
                        tension: 0.1
                    },
                    {
                        label: 'Advanced Financial Accounting and Reporting',
                        data: <?= json_encode(array_column($student_data, 'Advanced Financial Accounting and Reporting')); ?>,
                        fill: false,
                        borderColor: 'rgba(54, 162, 235, 1)', // Blue
                        tension: 0.1
                    },
                    {
                        label: 'Management Services',
                        data: <?= json_encode(array_column($student_data, 'Management Services')); ?>,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)', // Green
                        tension: 0.1
                    },
                    {
                        label: 'Auditing',
                        data: <?= json_encode(array_column($student_data, 'Auditing')); ?>,
                        fill: false,
                        borderColor: 'rgba(153, 102, 255, 1)', // Purple
                        tension: 0.1
                    },
                    {
                        label: 'Taxation',
                        data: <?= json_encode(array_column($student_data, 'Taxation')); ?>,
                        fill: false,
                        borderColor: 'rgba(255, 159, 64, 1)', // Orange
                        tension: 0.1
                    },
                    {
                        label: 'Regulatory Framework for Business Transactions',
                        data: <?= json_encode(array_column($student_data, 'Regulatory Framework for Business Transaction')); ?>,
                        fill: false,
                        borderColor: 'rgba(201, 203, 207, 1)', // Gray
                        tension: 0.1
                    }
                ]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Score History by Subject'

                        }
                    }
                }
            };

            const scoreChart = new Chart(ctx, config);
        </script>
    <?php else: ?>
        <p class="error">No recommendations available.</p>
    <?php endif; ?>
</div>
</body>
</html>