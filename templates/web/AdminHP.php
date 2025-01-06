<?php
session_start();

// Debug: Output all cookies (Remove or comment this out)

// Check if the 'user_category' cookie exists and is set to 'teacher'
if (!isset($_COOKIE['user_category']) || $_COOKIE['user_category'] !== 'admin') {
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
    if ($data['Categories'] != 'admin') {
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
    <title>Admin | CPAce</title>
    <link href="Style/admin.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/x-icon" href="Style/cpace.ico">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
    <script>
        // JavaScript for editing rows inline in Table 1 (Students)
        function editRowTable1(row) {
            var cells = row.getElementsByTagName("td");
            if (cells[0].getElementsByTagName("input").length > 0) {
                // If already in edit mode, save the changes for Table 1
                var accountId = cells[0].getElementsByTagName("input")[0].value;
                var password = cells[1].getElementsByTagName("input")[0].value;
                var form = row.getElementsByTagName("form")[0];

                form.elements['account_ID'].value = accountId;
                form.elements['password'].value = password;
                form.submit(); // Submit the form to update the database
            } else {
                // If not in edit mode, switch to edit mode for Table 1
                cells[0].innerHTML = `<input type="text" value="${cells[0].innerText}" />`;
                cells[1].innerHTML = `<input type="text" value="${cells[1].innerText}" />`;

                // Hide "Edit" and "Delete" buttons, show "Save" button
                cells[4].innerHTML = `
                    <button type="button" onclick="saveRowTable1(this.closest('tr'))" style="background-color: #4CAF50;">Save</button>
                `;
            }
        }

        // JavaScript for saving row in Table 1 (Students)
        function saveRowTable1(row) {
            var cells = row.getElementsByTagName("td");
            var accountId = cells[0].getElementsByTagName("input")[0].value;
            var password = cells[1].getElementsByTagName("input")[0].value;
            var form = row.getElementsByTagName("form")[0];

            form.elements['account_ID'].value = accountId;
            form.elements['password'].value = password;
            form.submit(); // Submit the form to update the database
        }

        // JavaScript for editing rows inline in Table 2 (Teachers)
        function editRowTable2(row) {
            var cells = row.getElementsByTagName("td");
            if (cells[0].getElementsByTagName("input").length > 0) {
                // If already in edit mode, save the changes for Table 2
                var accountId = cells[0].getElementsByTagName("input")[0].value;
                var password = cells[1].getElementsByTagName("input")[0].value;
                var form = row.getElementsByTagName("form")[0];

                form.elements['account_ID'].value = accountId;
                form.elements['password'].value = password;
                form.submit(); // Submit the form to update the database
            } else {
                // If not in edit mode, switch to edit mode for Table 2
                cells[0].innerHTML = `<input type="text" value="${cells[0].innerText}" />`;
                cells[1].innerHTML = `<input type="text" value="${cells[1].innerText}" />`;

                // Hide "Edit" and "Delete" buttons, show "Save" button
                cells[2].innerHTML = `
                    <button type="button" onclick="saveRowTable2(this.closest('tr'))" style="background-color: #4CAF50;">Save</button>
                `;
            }
        }

        // JavaScript for saving row in Table 2 (Teachers)
        function saveRowTable2(row) {
            var cells = row.getElementsByTagName("td");
            var accountId = cells[0].getElementsByTagName("input")[0].value;
            var password = cells[1].getElementsByTagName("input")[0].value;
            var form = row.getElementsByTagName("form")[0];

            form.elements['account_ID'].value = accountId;
            form.elements['password'].value = password;
            form.submit(); // Submit the form to update the database
        }

        // Function to toggle visibility of tables
        function toggleTable(tableId) {
            var table1 = document.getElementById('table1');
            var table2 = document.getElementById('table2');
            if (tableId === 'table1') {
                table1.classList.remove('hidden');
                table2.classList.add('hidden');
            } else {
                table2.classList.remove('hidden');
                table1.classList.add('hidden');
            }
        }
    </script>
</head>
<body>

<!-- Header Section -->
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
            <a href="listA.php" class="taskbar-button">Test</a>
            <a href="AdminHP.php" class="taskbar-button">Account</a>
            <a href="student_score.php" class="taskbar-button">Students Progress</a>
        </div>
        <div class="taskbar-right">
            <a href="http://127.0.0.1:5000" class="taskbar-button logout">Logout</a>
        </div>
    </div>
</div>
</div>

<!-- Account Management Form -->
<form class="section" action="AccountingA.php" method="post">
    <h1>CPAce Admin Account</h1>
    <label>ID</label>
    <input id="name"  required maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="account_ID" type="text" placeholder="ID" autofocus />
    <label>Password</label>
    <input id="skill" name="password" type="text" placeholder="Password" />
    <select id="title" name="role">
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
    <h2>Account List:</h2>
    <button class="toggle-btn" onclick="toggleTable('table1')">Student</button>
    <button class="toggle-btn" onclick="toggleTable('table2')">Teacher</button>

    <!-- Table 1: Students -->
    <table id="table1" class="table">
        <thead>
            <tr>
                <th>Student_ID</th>
                <th>Password</th>
                <th>Attempts</th>
                <th>Recorded Date</th>
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
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                $sql = "
                SELECT accounts.ID, accounts.Account_ID, accounts.password, 
                       COUNT(score_history.student_id) AS attempts, 
                       MAX(score_history.recorded_date) AS latest_recorded_date
                FROM accounts
                LEFT JOIN score_history ON accounts.Account_ID = score_history.student_id
                WHERE accounts.categories = 'student' AND accounts.Account_ID != 3210048
                 ";
            

                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $searchTerm = mysqli_real_escape_string($connection, $_GET['search']);
                    $sql .= " AND accounts.Account_ID LIKE '%$searchTerm%'";
                }

                $sql .= " GROUP BY accounts.Account_ID";  

                $result = $connection->query($sql);
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                    <form method='POST' action='Acc_UpdateA.php'>
                        <td>{$row['Account_ID']}</td>
                        <td>{$row['password']}</td>
                        <td>{$row['attempts']}</td>
                        <td>{$row['latest_recorded_date']}</td>
                        <td>
                            <button type='button' onclick='editRowTable1(this.closest(\"tr\"))' style='background-color: #20c997;'>Edit</button>
                            <a href='ViewProgressA.php?id={$row['Account_ID']}'><button type='button' style='background-color: #4CAF50;'>View Progress</button></a>
                            <a href='DeleteA.php?id={$row['Account_ID']}'><button type='button' style='background-color: #f44336;'>Delete</button></a>
                        </td>
                        <input type='hidden' name='account_ID' value='{$row['Account_ID']}' />
                        <input type='hidden' name='password' value='{$row['password']}' />
                        <input type='hidden' name='ID' value='{$row['ID']}' />
                    </form>
                </tr>
                    ";
                }

                $connection->close();
            ?>
        </tbody>
    </table>

    <!-- Table 2: Teachers -->
    <table id="table2" class="table hidden">
        <thead>
            <tr>
                <th>Teacher_ID</th>
                <th>Password</th>
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

                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                $sql = "
                  SELECT accounts.ID, accounts.Account_ID, accounts.password, 
                         COUNT(score_history.student_id) AS attempts, 
                         MAX(score_history.recorded_date) AS latest_recorded_date
                  FROM accounts
                  LEFT JOIN score_history ON accounts.Account_ID = score_history.student_id
                  WHERE accounts.categories = 'teacher'
                ";

                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $searchTerm = mysqli_real_escape_string($connection, $_GET['search']);
                    $sql .= " AND accounts.Account_ID LIKE '%$searchTerm%'";
                }

                $sql .= " GROUP BY accounts.Account_ID";

                $result = $connection->query($sql);
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <form method='POST' action='Acc_UpdateA.php'>
                            <td>{$row['Account_ID']}</td>
                            <td>{$row['password']}</td>
                            <td>
                                <button type='button' onclick='editRowTable2(this.closest(\"tr\"))' style='background-color: #20c997;'>Edit</button>
                                <a href='DeleteA.php?id={$row['Account_ID']}'><button type='button' style='background-color: #f44336;'>Delete</button></a>
                            </td>
                            <input type='hidden' name='account_ID' value='{$row['Account_ID']}' />
                            <input type='hidden' name='password' value='{$row['password']}' />
                            <input type='hidden' name='ID' value='{$row['ID']}' />
                        </form>
                    </tr>
                    ";
                }

                $connection->close();
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
