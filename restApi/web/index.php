<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPAce Board Exam Reviewer</title>
    <link rel="stylesheet" href="Style/logging.css"> <!-- Link to external CSS -->
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="Style/logo.png" alt="Logo" class="logo-img">
                <div class="school-info">
                    <h1>Colegio de San Juan de Letran Calamba</h1>
                    <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Background Section -->
    <div class="background">
        <div class="wrapper">
            <h2 class="active">Log In</h2>
            <div class="fadeIn first">
                <img src="Style/logo.png" alt="Logo">
                <img src="Style/SBMA_logo.png" alt="SBMA Logo">
                <h1>CPAce Board Exam Reviewer</h1>
            </div>
            <form class="login-container" action="loginPHP.php" method="post">
                <input type="text" name="username" placeholder="ID Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Log in">
            </form>
        </div>
    </div>

    <script>
        // JavaScript function to send data to Flask API
        function submitForm(event) {
            event.preventDefault();  // Prevent default form submission

            const username = document.querySelector('[name="username"]').value;
            const password = document.querySelector('[name="password"]').value;

            // Create data object to send to Flask
            const studentData = {
                username: username,
                password: password
            };

            // Send a POST request to Flask API (change URL if needed)
            fetch('http://127.0.0.1:5000/predict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(studentData)  // Convert data to JSON format
            })
            .then(response => response.json())  // Parse JSON response
            .then(data => {
                console.log(data);  // Log the result from Flask
            })
            .catch(error => console.error('Error:', error));  // Handle errors
        }
    </script>
</body>
</html>
