<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Auditing Question Config</title>
  <link href='Style/questions.css' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="taskbar">
    <div class="logo"></div>
    <a href="index.php" class="taskbar-button">Logout</a>
    <a href="list.php" class="taskbar-button">Test</a>
    <a href="admin.php" class="taskbar-button">Account</a>
</div>
<a href="list.php" class="back-button">&#8592;</a>

<form class="section" action="q4add.php" method="post">
  <h1>Auditing</h1>
  <input class="btn" type="submit" value="Add Questions" />
</form>
<br/>

<div class="section">
  <h2>List of questions:</h2>
  
  <!-- Search Form -->
  <form method="get" action="">
    <input type="text" name="search" placeholder="Search for a question..." 
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
    <input type="submit" value="Search" />
  </form>
  <br>

  <?php
  // Database connection details
  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "cpa";

  // Create connection
  $connection = new mysqli($servername, $username, $password, $database);

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

  <table id="table1" class="table">
    <thead>
      <tr>
        <th>No.</th>
        <th>Scenario</th>
        <th>Question</th>
        <th>Image</th>
        <th>Option 1</th>
        <th>Option 2</th>
        <th>Option 3</th>
        <th>Option 4</th>
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

      // Loop through each row and display it
      while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>{$counter}</td>";

          // Scenario handling: Extract image URL if exists
          $scenario_text = $row['scenario'];
          $image_url = "";

          // Check if scenario contains an image URL in markdown format (e.g., [Image: uploads/...])
          if (preg_match('/\[\s*Image:\s*(.*?)\s*\]/', $scenario_text, $matches)) {
              $image_url = $matches[1];  // The image URL is captured in $matches[1]
              // Remove the image URL from the scenario text to leave only the text
              $scenario_text = preg_replace('/\[\s*Image:\s*.*?\s*\]/', '', $scenario_text);
          }

          // Display the scenario text
          echo "<td>";
          echo "<p>{$scenario_text}</p>";  // Display the text

          // If an image is found, display it
          if (!empty($image_url)) {
              echo "<img src='{$image_url}' alt='Scenario Image' style='max-width: 100px; margin-top: 5px;'>";
          }
          echo "</td>";

          // Other columns
          echo "<td>{$row['question']}</td>";
          echo "<td>";
          if (!empty($row['image'])) {
              echo "<img src='{$row['image']}' alt='Question Image' style='max-width: 100px; margin-top: 5px;'>";
          } else {
              echo "No image";
          }
          echo "</td>";
          echo "<td>{$row['opt1']}</td>";
          echo "<td>{$row['opt2']}</td>";
          echo "<td>{$row['opt3']}</td>";
          echo "<td>{$row['opt4']}</td>";
          echo "<td>{$row['answer']}</td>";
          echo "<td>{$row['topic']}</td>";
          echo "<td>
                <a href='/CPA/q4Edit.php?id={$row['aud_id']}'>
                  <button class='edit-button'>Edit</button>
                </a>
                <a href='/CPA/q4Delete.php?id={$row['aud_id']}'>
                  <button class='delete-button'>Delete</button>
                </a>
                </td>";
          echo "</tr>";
          $counter++;
      }

      // Close connection
      $connection->close();
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
