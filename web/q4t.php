<?php
session_start();

// Check if the user is logged in and is a admin
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header("Location: http://127.0.0.1:5000/");
    exit();
}

$username = $_SESSION['username'];

// Connect to the database and retrieve the category
$conn = new mysqli('localhost', 'root', '', 'cpa');
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
}
$conn->set_charset('utf8'); // Ensure UTF-8 encoding

$stmt = $conn->prepare("SELECT Categories FROM accounts WHERE Account_ID = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt_result = $stmt->get_result();

if ($stmt_result->num_rows > 0) {
    $data = $stmt_result->fetch_assoc();
    
    // Check if the category is 'admin'
    if ($data['Categories'] != 'teacher') {
        // If not a admin, redirect to index.php or another appropriate page
        header("Location: http://127.0.0.1:5000/");
        exit();
    }
} else {
    // User not found, redirect to login page
    header("Location: http://127.0.0.1:5000/");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Auditing Question Configuration</title>
  <link rel="icon" type="image/x-icon" href="Style/cpace.ico">
  <link href='Style/questions.css' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="taskbar">
    <!-- Logo positioned on the far left -->
    <div class="logo"></div>

    <!-- Taskbar Links -->
    <div class="taskbar-center">
        <a href="listT.php" class="taskbar-button">Test</a>
        <a href="teacherHP.php" class="taskbar-button">Account</a>
    </div>

    <!-- Logout Button -->
    <a href="http://127.0.0.1:5000" class="logout">Logout</a>
</div>

<!-- Back Button Positioned Below the Logo -->
<a href="listT.php" class="back-button" onclick="window.history.back();">&#8592; Back</a>

<!-- Add Questions Form -->
<div class="section">
<h1>Auditing</h1>
  <div class="add-question-container">
    <form action="q4addt.php" method="post">
      <input class="btn" type="submit" value="Add Questions" />
    </form>
  </div>
</div>
<div class="section">
  <h2>List of questions:</h2>
  
 <!-- Search Form -->
  <form method="get" action="">
    <input type="text" name="search" placeholder="Search for a question..." 
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
    <input type="submit" value="Search" />
  </form>
  <br>

  <?php
  // Database connection details
  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "cpa";
  
  $connection = new mysqli($servername, $username, $password, $database);
  $connection->set_charset('utf8'); // Ensure UTF-8 encoding

  // Check connection
  if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
  }

  // Query to get the total number of questions
  $query = "SELECT COUNT(*) AS total_items FROM aud";
  $summary_result = $connection->query($query);
  $row_summary = $summary_result->fetch_assoc();
  $total_items = $row_summary['total_items'];
  ?>
  <!-- Display the total number of questions -->
  <div class="total-questions">Total number of Questions: <?php echo $total_items; ?></div>
  <br>
  <div class="table-container">
  <table id="table1" class="table">
    <thead>
      <tr>
        <th>No.</th>
        <th>Scenario</th>
        <th>Question</th>
        <th>Image</th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>Answer</th>
        <th>Topic</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Initialize the counter
      $counter = 1;

      // Check if a search query is provided
      $searchQuery = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';

      // Query to fetch filtered rows based on the search query
      if (!empty($searchQuery)) {
          $sql = "SELECT * FROM aud WHERE question LIKE '%$searchQuery%'";
      } else {
          $sql = "SELECT * FROM aud";
      }

      $result = $connection->query($sql);

      if (!$result) {
          die("Invalid query: " . $connection->error);
      }

      // Topic mapping
      $topic_mapping = [
        "Fundamentals" => "Fundamentals of auditing and assurance services",
        "Risk-based" => "Risk-based Financial Statement Audit",
        "Understanding" => "Understanding the Entity and Internal Control",
        "Audit Evidence" => "Audit Evidence and Documentation",
        "Audit Completion" => "Audit Completion and Reporting",
        "CIS" => "Auditing in CIS Environment",
        "Attestation" => "Attestation Services",
        "Governance" => "Governance, Ethics, and Quality Management",
        "Risk Response" => "Risk Response and Reporting"
    ];
    
    // Loop through each row and display it
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$counter}</td>";
    
        // Scenario handling
        $scenario_text = htmlspecialchars($row['scenario'], ENT_QUOTES, 'UTF-8');
        echo "<td>{$scenario_text}</td>";
    
        echo "<td>" . htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>";
        if (!empty($row['image'])) {
            echo "<img src='{$row['image']}' alt='Question Image' style='max-width: 100px; margin-top: 5px;'>";
        } else {
            echo "No image";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($row['opt1'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['opt2'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['opt3'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['opt4'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['answer'], ENT_QUOTES, 'UTF-8') . "</td>";
    
        // Map the topic to its display name
        $topic_display = isset($topic_mapping[$row['topic']]) ? $topic_mapping[$row['topic']] : $row['topic'];
        echo "<td>" . htmlspecialchars($topic_display, ENT_QUOTES, 'UTF-8') . "</td>";
    
        echo "<td>
                <a href='q4Editt.php?id={$row['aud_id']}'>
                  <button class='edit-button'>Edit</button>
                </a>
                <a href='q4Deletet.php?id={$row['aud_id']}'>
                  <button class='delete-button'>Delete</button>
                </a>
              </td>";
        echo "</tr>";
        $counter++;
    }

      $connection->close();
      ?>
    </tbody>
  </table>
    </div>
</div>

</body>
</html>
