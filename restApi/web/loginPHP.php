<?php
session_start();
$username = $_POST['username'];
$password = $_POST['password'];

$conn = new mysqli('localhost', 'root', '', 'cpa');
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
} else {
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE Account_ID = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows > 0) {
        $data = $stmt_result->fetch_assoc();
        if ($data['password'] == $password) {
            
            // Unset any previous session variables
            session_unset();
            
            // Set the correct session data based on the user category
            $_SESSION['username'] = $username; // Set the common session variable
            
            if ($data["Categories"] == "teacher") {
                $_SESSION['teacher'] = $data['Categories'];
                header("Location: teacherHP.php");
                exit(); // Always use exit after header redirection
            } elseif ($data["Categories"] == "student") {
                $_SESSION['student'] = $data['Categories'];
                $_SESSION['ID'] = $data['ID'];
                $_SESSION['password'] = $password;
                header("Location: Studenthp.php");
                exit();
            } elseif ($data["Categories"] == "admin") {
                $_SESSION['admin'] = $data['Categories'];
                header("Location: adminHP.php");
                exit();
            }
        } else {
            echo "<h2>Invalid user or password</h2>";
            header("Location: index.php");
            exit();
        }
    } else {
        echo "<h2>Invalid username or password</h2>";
        header("Location: index.php");
        exit();
    }
}
?>
