<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the latest 5 records for each unique student_id
$sql = "SELECT student_id, Financial_score, Adv_Score, Mng_score, Auditing_Score, Taxation_Score, Framework_score 
        FROM score_history
        ORDER BY student_id, history_id DESC";  // Assuming there's an 'id' column to order the records by most recent

// Execute the query
$result = $conn->query($sql);

// Debug: Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Debug: Check number of rows returned

$data = [];
$unique_students = [];
$total_scores = [
  'Financial_score' => 0,
  'Adv_Score' => 0,
  'Mng_score' => 0,
  'Auditing_Score' => 0,
  'Taxation_Score' => 0,
  'Framework_score' => 0,
];

$student_score_count = []; // To track how many scores we have for each student

// Fetch all data and track unique student_ids
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $student_id = $row['student_id'];
    if (!isset($data[$student_id])) {
      $data[$student_id] = [];
      $student_score_count[$student_id] = 0; // Initialize student score count
    }

    if ($student_score_count[$student_id] < 5) {
      // Store the latest 5 records for each student
      $data[$student_id][] = $row;
      $student_score_count[$student_id]++; // Increment the score count for the student
    }

    if (!in_array($student_id, $unique_students)) {
      $unique_students[] = $student_id;  // Track unique student IDs
    }
  }
} else {
  echo "0 results<br>";
}

// Calculate the total scores for each subject
foreach ($data as $student_id => $scores) {
  foreach ($scores as $score) {
    $total_scores['Financial_score'] += $score['Financial_score'];
    $total_scores['Adv_Score'] += $score['Adv_Score'];
    $total_scores['Mng_score'] += $score['Mng_score'];
    $total_scores['Auditing_Score'] += $score['Auditing_Score'];
    $total_scores['Taxation_Score'] += $score['Taxation_Score'];
    $total_scores['Framework_score'] += $score['Framework_score'];
  }
}

// Define the increment values for each subject
$increments = [
  'Financial_score' => 25,
  'Adv_Score' => 50,
  'Mng_score' => 35,
  'Auditing_Score' => 40,
  'Taxation_Score' => 45,
  'Framework_score' => 30
];

// Calculate the highest score for each subject based on the number of unique students
$highest_scores = [];
foreach ($increments as $subject => $increment) {
  $highest_scores[$subject] = count($unique_students) * $increment;
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

    <!-- Main Content Section -->
    <div class="main-content">
        <!-- Chart Section -->
        <div class="chart-container">
            <canvas id="scoreChart"></canvas>
        </div>

        <!-- Text Section -->
        <div class="text-container">
    <h3>Scores Overview</h3>
    <ul>
        <li>
            Financial: <span><?php echo $total_scores['Financial_score']; ?>/<?php echo $highest_scores['Financial_score']; ?></span>
        </li>
        <li>
            Advance Financial: <span><?php echo $total_scores['Adv_Score']; ?>/<?php echo $highest_scores['Adv_Score']; ?></span>
        </li>
        <li>
            Management: <span><?php echo $total_scores['Mng_score']; ?>/<?php echo $highest_scores['Mng_score']; ?></span>
        </li>
        <li>
            Auditing: <span><?php echo $total_scores['Auditing_Score']; ?>/<?php echo $highest_scores['Auditing_Score']; ?></span>
        </li>
        <li>
            Taxation: <span><?php echo $total_scores['Taxation_Score']; ?>/<?php echo $highest_scores['Taxation_Score']; ?></span>
        </li>
        <li>
            Framework: <span><?php echo $total_scores['Framework_score']; ?>/<?php echo $highest_scores['Framework_score']; ?></span>
        </li>
    </ul>
</div>
    </div>
</div>
  <script>
    // PHP data passed into JavaScript
    const totalScores = <?php echo json_encode($total_scores); ?>; // Pass aggregated scores
    const highestScores = <?php echo json_encode($highest_scores); ?>; // Pass highest scores for each subject

    const ctx = document.getElementById('scoreChart').getContext('2d');

    // Prepare dataset for the chart
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
            'rgba(255, 99, 132, 0.2)',  // Financial score color
            'rgba(54, 162, 235, 0.2)',  // Advance Financial score color
            'rgba(255, 206, 86, 0.2)',  // Management score color
            'rgba(75, 192, 192, 0.2)',  // Auditing score color
            'rgba(153, 102, 255, 0.2)', // Taxation score color
            'rgba(255, 159, 64, 0.2)'   // Framework score color
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',   // Financial score border color
            'rgba(54, 162, 235, 1)',   // Advance Financial score border color
            'rgba(255, 206, 86, 1)',   // Management score border color
            'rgba(75, 192, 192, 1)',   // Auditing score border color
            'rgba(153, 102, 255, 1)',  // Taxation score border color
            'rgba(255, 159, 64, 1)'    // Framework score border color
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: Math.max(...Object.values(highestScores)) // Dynamically set the max value
          }
        }
      }
    });
  </script>
</body>
</html>