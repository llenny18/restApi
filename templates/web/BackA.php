<?php
// Start session
session_start();

// Check if the 'username' cookie is set
if (isset($_COOKIE['username'])) {
    // Retrieve the username from the cookie
    $_SESSION['username'] = $_COOKIE['username'];
    
    // Optionally, you can store other session data or perform additional operations

    // Redirect to another page after setting the session variable
    header("Location: adminHP.php");  // Redirect to a different page
    exit();
} else {
    // If the cookie is not set, redirect to login page or handle error
    header("Location: login.php");  // Redirect to login page
    exit();
}
?>
