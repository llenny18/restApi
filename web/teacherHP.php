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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers' Account Management</title>
    <link href="Style/admin.css" rel="stylesheet" type="text/css"> <!-- Link to external CSS -->
        <link rel="icon" type="image/x-icon" href="Style/cpace.ico">
        <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
</head>
<body>

<!-- Header Section with School Info and Logo -->
<div class="header-top">
    <div class="header-left">
        <div class="logo"></div>
        <div class="school-info">
            <h1>Colegio de San Juan de Letran Calamba</h1>
            <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
        </div>
           <div class="header-center">
        <img src="Style/cpace1.png" alt="CPAce Icon" class="header-icon" />
    </div>
    </div>
    <div class="taskbar">
        <div class="taskbar-center">
            <a href="listT.php" class="taskbar-button">Test</a>
            <a href="student_score.php" class="taskbar-button">Students Progress</a>
        </div>
        <div class="user-info">
     <div class="username-container" onclick="toggleDropdown()">
        <span class="username"><?php echo htmlspecialchars($username); ?></span>
    </div>
            <div id="user-dropdown" class="dropdown hidden">
                <ul>
                    <li><a href="http://127.0.0.1:5000">Log Out</a></li>
                    <li><a href="changepass.html">Change Password</a></li>
                </ul>
            </div>
        </div>
            <script>
      // Toggle dropdown visibility
function toggleDropdown() {
    const dropdown = document.getElementById("user-dropdown");
    dropdown.classList.toggle("hidden");
}

// Close dropdown if clicked outside
document.addEventListener("click", function (event) {
    const dropdown = document.getElementById("user-dropdown");
    const usernameContainer = document.querySelector(".username-container");
    if (!usernameContainer.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add("hidden");
    }
});

    </script>
    </div>
</div>
</div>
<form class="section" action="AccountingT.php" method="post">
    <h1>Teachers' Account Management</h1>
    <label>ID</label>
    <input id="name"  required maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="account_ID" type="text" placeholder="ID" autofocus />
    <label>Password</label>
    <input id="skill" name="password" type="text" placeholder="Password" />
    <select id="title" name="role" hidden>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select>
    <br /><br />
    <input class="btn" type="submit" value="Add Account" />
</form>

<!-- Search Form -->
<div class="section">
    <h2>Search for Account:</h2>
    <form method="get" action="">
        <input type="text" name="search" placeholder="Enter Account ID" />
        <input type="submit" value="Search" />
    </form>
</div>

<!-- Account List Section -->
<div class="section">
    <h2>Student Account List:</h2>

    <!-- Table 1: Students -->
    <table id="table1" class="table">
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Attempts</th> <!-- Column header for attempts -->
          <th>Recorded Date</th> <!-- Column header for recorded_date -->
          <th>Actions</th>
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
          if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
          }

          // Default query to fetch all student records
          $sql = "
          SELECT accounts.ID, accounts.Account_ID, accounts.password, 
                 COUNT(score_history.student_id) AS attempts, 
                 MAX(score_history.recorded_date) AS latest_recorded_date
          FROM accounts
          LEFT JOIN score_history ON accounts.Account_ID = score_history.student_id
          WHERE accounts.categories = 'student' AND accounts.Account_ID != 3210048
           ";

          // If a search term is entered, modify the query to filter by Account_ID
          if (isset($_GET['search']) && !empty($_GET['search'])) {
            $searchTerm = mysqli_real_escape_string($connection, $_GET['search']);
            $sql .= " AND accounts.Account_ID LIKE '%$searchTerm%'";
          }

          $sql .= " GROUP BY accounts.Account_ID";  // Ensure we group by Account_ID

          $result = $connection->query($sql);

          if (!$result) {
            die("Invalid query: " . $connection->error);
          }

          // Display student records
          while ($row = $result->fetch_assoc()) {
            echo "
              <tr>
                <td>{$row['Account_ID']}</td>

                <td>{$row['attempts']}</td>  <!-- Displaying the number of attempts -->
                <td>{$row['latest_recorded_date']}</td>  <!-- Displaying the latest recorded_date -->
                <td>
                  <a href='ViewProgressT.php?id={$row['Account_ID']}'><button type='button' style='background-color: #4CAF50;'>View Progress</button></a>
                  </a>
                  </aT
                </td>
              </tr>
            ";
          }

          // Close the connection
          $connection->close();
        ?>
      </tbody>
    </table>
</div>

</body>
</html>
