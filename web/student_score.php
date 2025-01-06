<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the latest record for each unique student_id, excluding a specific student_id
$sql = "SELECT sh.student_id, sh.Financial_score, sh.Adv_Score, sh.Mng_score, 
               sh.Auditing_Score, sh.Taxation_Score, sh.Framework_score
        FROM score_history sh
        INNER JOIN (
            SELECT student_id, MAX(history_id) as max_id
            FROM score_history
            WHERE student_id != 3210048 -- Exclude specific student_id
            GROUP BY student_id
        ) latest
        ON sh.student_id = latest.student_id AND sh.history_id = latest.max_id";

// Execute the query
$result = $conn->query($sql);

// Check if query execution was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Initialize variables
$total_scores = [
    'Financial_score' => 0,
    'Adv_Score' => 0,
    'Mng_score' => 0,
    'Auditing_Score' => 0,
    'Taxation_Score' => 0,
    'Framework_score' => 0,
];

$unique_students = []; // To track unique student IDs

// Process the data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        $unique_students[] = $student_id;

        // Add scores only if they exist
        foreach ($total_scores as $subject => &$score) {
            $score += isset($row[$subject]) ? $row[$subject] : 0;
        }
    }
}

// Calculate the dynamic increments based on the number of unique students
if (!empty($unique_students)) {
    $unique_student_count = count(array_unique($unique_students));
    $increments = [
        'Financial_score' => $unique_student_count * 10,
        'Adv_Score' => $unique_student_count * 12,
        'Mng_score' => $unique_student_count * 9,
        'Auditing_Score' => $unique_student_count * 9,
        'Taxation_Score' => $unique_student_count * 18,
        'Framework_score' => $unique_student_count * 10,
    ];
} else {
    // If no data is available, set increments and scores to 0
    $unique_student_count = 0;
    $increments = [
        'Financial_score' => 0,
        'Adv_Score' => 0,
        'Mng_score' => 0,
        'Auditing_Score' => 0,
        'Taxation_Score' => 0,
        'Framework_score' => 0,
    ];
    $total_scores = [
        'Financial_score' => 0,
        'Adv_Score' => 0,
        'Mng_score' => 0,
        'Auditing_Score' => 0,
        'Taxation_Score' => 0,
        'Framework_score' => 0,
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Score Progress</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="Style/student_score.css" rel="stylesheet" type="text/css">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
</head>
<div class="header-top">
    <div class="header-left">
        <div class="logo"></div>
        <div class="school-info">
            <h1>Colegio de San Juan de Letran Calamba</h1>
            <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
        </div>
    </div>
    <div class="header-center">
        <img src="Style/cpace1.png" alt="CPAce Icon" class="header-icon" />
    </div>
    <a href="javascript:history.back()" class="back-btn">Back</a>
</div>
<body>
    <a href="adminHP.php" class="back-btn">Back</a>
    <div class="container">
        <h1>Students Score Progress</h1>
        <h2>Score Progress Chart</h2>

        <div class="main-content">
            <div class="chart-container">
                <canvas id="scoreChart"></canvas>
            </div>

            <div class="text-container">
                <?php if (!empty($unique_students)) { ?>
                    <h3>Scores Overview</h3>
                    <ul>
                        <li>Financial: <span><?php echo $total_scores['Financial_score'] ?: 'No data'; ?>/<?php echo $increments['Financial_score']; ?></span></li>
                        <li>Advance Financial: <span><?php echo $total_scores['Adv_Score'] ?: 'No data'; ?>/<?php echo $increments['Adv_Score']; ?></span></li>
                        <li>Management: <span><?php echo $total_scores['Mng_score'] ?: 'No data'; ?>/<?php echo $increments['Mng_score']; ?></span></li>
                        <li>Auditing: <span><?php echo $total_scores['Auditing_Score'] ?: 'No data'; ?>/<?php echo $increments['Auditing_Score']; ?></span></li>
                        <li>Taxation: <span><?php echo $total_scores['Taxation_Score'] ?: 'No data'; ?>/<?php echo $increments['Taxation_Score']; ?></span></li>
                        <li>Framework: <span><?php echo $total_scores['Framework_score'] ?: 'No data'; ?>/<?php echo $increments['Framework_score']; ?></span></li>
                    </ul>
                <?php } else { ?>
                    <p>No scores available. Please add student data.</p>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        const totalScores = <?php echo json_encode($total_scores); ?>;
        const highestScores = <?php echo json_encode($increments); ?>;

        const ctx = document.getElementById('scoreChart').getContext('2d');

        const labels = ['Financial', 'Advance Financial', 'Management', 'Auditing', 'Taxation', 'Framework'];
        const scores = [
            totalScores.Financial_score,
            totalScores.Adv_Score,
            totalScores.Mng_score,
            totalScores.Auditing_Score,
            totalScores.Taxation_Score,
            totalScores.Framework_score
        ];

        const scoreChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Score Progress',
                    data: scores,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...Object.values(highestScores))
                    }
                }
            }
        });
    </script>
</body>
</html>
