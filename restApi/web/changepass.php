<?php
session_start();

// Ensure that session variables are set
if (isset($_SESSION['username'], $_SESSION['ID'], $_SESSION['password'])) {
    $username = $_SESSION['username'];
    $ID = $_SESSION['ID']; // Assuming you store the user ID in the session
    $password = $_SESSION['password']; // Assuming the session stores the current password
}

// Flag for password mismatch error
$passwordMismatchError = false;
$passwordUpdated = false; // To track if the password has been updated successfully

// Handle the password change process when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if the current password entered matches the session password
    if ($currentPassword !== $password) {
        $passwordMismatchError = true; // Set the error flag
    } elseif ($newPassword !== $confirmPassword) {
        // Check if new password and confirm password match
        echo "<script>alert('New password and confirm password do not match.');</script>";
    } else {
        // Update password in session (if session-based password is used)
        $_SESSION['password'] = $newPassword; // Update session with new password
        
        // Debug: Print the new password to ensure session is updated
        
        // Optionally, update the password in the database
        $conn = new mysqli('localhost', 'root', '', 'cpa');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the SQL query to update the password in the database
        $updatePasswordQuery = "UPDATE accounts SET password = ? WHERE ID = ?";
        $stmt = $conn->prepare($updatePasswordQuery);
        $stmt->bind_param("si", $newPassword, $ID);  // 's' for string, 'i' for integer
        if ($stmt->execute()) {
            // Successfully updated password in the database
            echo "<script>alert('Password has been updated');</script>";
            $passwordUpdated = true; // Set the flag to show success message
        } else {
            echo "<script>alert('Error updating password in the database.');</script>";
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="Style/change.css"> <!-- Link to the new change.css -->
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
    <style>
        /* Style for the error message */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: inline-block; /* Ensure it displays inline */
        }

        .success-message {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-top">
        <div class="school-info">
            <img src="Style/logo.png" alt="School Logo" class="school-logo"> <!-- Add a class for styling the logo -->
            <div>
                <h1>Colegio de San Juan de Letran Calamba</h1>
                <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
            </div>
        </div>

        <!-- Back Arrow Button (instead of the dropdown) -->
        <div class="back-button-container">
            <a href="StudentHP.php" class="back-button">&#8592;</a> <!-- Unicode left arrow -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="change-password-box">
            <h2>Change Your Password</h2>
            
            <?php if ($passwordUpdated): ?>
                <div class="success-message">Your password has been updated successfully!</div>
            <?php endif; ?>
            
            <form action="changepass.php" method="POST" class="change-password-form">
                <input type="hidden" name="ID" value="<?php echo $ID; ?>"> <!-- Pass user ID to the PHP script -->
                
                <div class="input-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" name="current-password" required>
                    <?php if ($passwordMismatchError): ?>
                        <span class="error-message" id="current-password-error">Current password does not match!</span>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" name="new-password" required>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>

                <button type="submit" class="submit-btn">Change Password</button>
            </form>
        </div>
    </div>

    <!-- Client-side validation -->
    <script>
        document.querySelector(".change-password-form").addEventListener("submit", function(event) {
            var newPassword = document.getElementById("new-password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var currentPassword = document.getElementById("current-password").value;

            var currentPasswordError = document.getElementById("current-password-error");

            // Reset error messages initially
            currentPasswordError.style.display = "none";

            // If current password does not match, show error and prevent form submission
            if (currentPassword !== "<?php echo $password; ?>") {
                currentPasswordError.style.display = "inline"; // Show the error message
                event.preventDefault(); // Prevent form submission
            }

            // Check if the new password and confirm password match
            if (newPassword !== confirmPassword) {
                alert("New password and confirm password do not match.");
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>
</html>
