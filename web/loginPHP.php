<?php
session_start();
$username = $_POST['username'];
$password = $_POST['password'];

// Connect to the database
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

        // Check if the password matches
        if ($data['password'] == $password) {
            setcookie("user_category", $data['Categories'], time() + (86400 * 7), "/", "", false, true);
            setcookie("username", $username, time() + (86400 * 7), "/", "", false, true);
            // Unset any previous session variables
            session_unset();

            // Set common session variable
            $_SESSION['username'] = $username;

            // Check if the user is a teacher
            if ($data["Categories"] == "teacher") {
                // Set a cookie for the teacher category
                setcookie(
                    "username",                      // Cookie name
                    $username,                       // Cookie value (from the login form)
                    time() + (86400 * 7),            // Expiration time (7 days)
                    "/",                             // Path (available across the domain)
                    "",                              // Domain (optional)
                    false,                           // Secure flag (true for HTTPS only; false for HTTP)
                    true                             // HttpOnly flag (not accessible via JavaScript)
                );

                $_SESSION['teacher'] = $data['Categories'];
                header("Location: teacherHP.php");
                exit(); // Always use exit after header redirection
            } elseif ($data["Categories"] == "student") {
                $_SESSION['student'] = $data['Categories'];
                $_SESSION['ID'] = $data['ID'];
                $_SESSION['password'] = $password;
                header("Location: hp.php");
                exit();
            } elseif ($data["Categories"] == "admin") {
                setcookie(
                    "username",                      // Cookie name
                    $username,                       // Cookie value (from the login form)
                    time() + (86400 * 7),            // Expiration time (7 days)
                    "/",                             // Path (available across the domain)
                    "",                              // Domain (optional)
                    false,                           // Secure flag (true for HTTPS only; false for HTTP)
                    true                             // HttpOnly flag (not accessible via JavaScript)
                );
                $_SESSION['admin'] = $data['Categories'];
                header("Location: adminHP.php");
                exit();
            }
        } else {
            // Invalid password
            echo "<h2>Invalid username or password</h2>";
            header("Location: http://127.0.0.1:5000");
            exit();
        }
    } else {
        // Invalid username
        echo "<h2>Invalid username or password</h2>";
        header("Location: http://127.0.0.1:5000");
        exit();
    }
}
?>
