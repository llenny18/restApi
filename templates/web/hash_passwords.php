<?php
// Database connection

$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all rows from the 'accounts' table
$sql = "SELECT ID, password FROM accounts";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['ID'];
        $plainPassword = $row['password'];

        // Check if the password is already hashed
        if (password_get_info($plainPassword)['algo'] == 0) { // Not hashed
            // Hash the plain-text password
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            // Update the hashed password in the database
            $updateSql = "UPDATE accounts SET password='$hashedPassword' WHERE ID=$id";
            if ($conn->query($updateSql) === TRUE) {
                echo "Password hashed successfully for ID: $id<br>";
            } else {
                echo "Error updating password for ID: $id - " . $conn->error . "<br>";
            }
        } else {
            echo "Password for ID: $id is already hashed. Skipping...<br>";
        }
    }
} else {
    echo "No records found in the accounts table!";
}

$conn->close();
?>
