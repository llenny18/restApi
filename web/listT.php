<?php
session_start();

// Debug: Output all cookies (Remove or comment this out)

// Check if the 'user_category' cookie exists and is set to 'teacher'
if (!isset($_COOKIE['user_category']) || $_COOKIE['user_category'] !== 'teacher') {
  header("Location: http://127.0.0.1:5000");
    exit();
}

// Retrieve the username from the 'username' cookie
if (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username'];
} else {
    exit();
}

// Debug: Output the username (Remove or comment this out)

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'cpa');
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT Categories FROM accounts WHERE Account_ID = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt_result = $stmt->get_result();

if ($stmt_result->num_rows > 0) {
    $data = $stmt_result->fetch_assoc();

    // Debug: Output the fetched data (Remove or comment this out)

    // Double-check if the category is still 'teacher'
    if ($data['Categories'] != 'teacher') {
        exit();
    }
} else {
    exit();
}
$stmt->close();
$conn->close();
// If everything is correct, display the teacher page
?>

<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Subject</title>
    <link rel="icon" type="image/x-icon" href="Style/cpace.ico">
    <link href="Style/list.css" rel="stylesheet" type="text/css">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="logo-left">
        <img src="Style/logo.png" alt="School Logo" class="logo-img">
    </div>

    <div class="logo-center">
        <img src="Style/cpace1.png" alt="CPAce Logo" class="logo-center-img">
    </div>

    <div class="school-info">
        <h1>Colegio de San Juan de Letran Calamba</h1>
        <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
    </div>
</header>

<div class="taskbar">
    <div class="logo"></div>
    <div class="taskbar-links">
        <a href="listT.php" class="taskbar-button">Test</a>
        <a href="teacherHP.php" class="taskbar-button">Account</a>
    </div>
    <a href="http://127.0.0.1:5000" class="taskbar-button logout">Logout</a>
    
    
</div>
</body>
</html>

<div class="container my-5">
<div class="section">
    <h2>List of Quiz</h2>
<br>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Items</th>
            <th>Action</th>
</tr>
</thead>
<tbody>
    
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM financial";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>1</td>
      <td>Financial Accounting and Reporting</td>
      <td>$total_items</td>
      <td>
        <a href='q1t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM adv";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>2</td>
      <td>Advanced Financial Accounting and Reporting</td>
      <td>$total_items</td>
      <td>
        <a href='q2t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM mng";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>3</td>
      <td>Management Services</td>
      <td>$total_items</td>
      <td>
        <a href='q3t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM aud";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>4</td>
      <td>Auditing</td>
      <td>$total_items</td>
      <td>
        <a href='q4t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM tax";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>5</td>
      <td>Taxation</td>
      <td>$total_items</td>
      <td>
        <a href='q5t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "cpa";

$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection -> connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_items FROM reg";
$summary_result = $connection->query($query);
$row_summary = $summary_result->fetch_assoc();
$total_items = $row_summary['total_items'];

  echo "
    <tr>
      <td>6</td>
      <td>Regulatory Framework for Business Transaction</td>
      <td>$total_items</td>
      <td>
        <a href='q6t.php?'>
          <button class='edit-button'>Edit</button>
        </a>
      </td>
    </tr>
  ";

?>
      </tbody>
</table>

</div>
</body>
</html>
