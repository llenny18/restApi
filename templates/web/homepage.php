<?php
session_start();

if (isset($_SESSION['username'], $_SESSION['attempts'], $_SESSION['Date_Started'])) {
    $username = $_SESSION['username'];
    $attempts = $_SESSION['attempts'];
    $Date_Started = $_SESSION['Date_Started'];

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="Style/homepage.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chivo+Mono:ital,wght@0,100..900;1,100..900&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Teko:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
<div class="background-image"></div>
    <div class="background-image-right"></div>
<div class="wrapper fadeInDown">
    
    <div class="FadeIn container">
        <div class="header">
            <img src="Style/logo.png" alt="Left Image" class="header-image">
            <h2>Colegio De San Juan De Letran</h2>
            <img src="Style/sbma.png" alt="Right Image" class="header-image">
        </div>
        <h1><strong>Welcome to <br><span class="tab">CPA Reviewer!</span></strong></h1>
            <Button href="#" class="btn btn1" onclick="startExam()">Start Exam <span class="tab">→</span></button><br>
            <Button  class="btn btn2" onclick="result()">See Results<span class="tab2">→</span></button><br>
            <Button href="reco.html" class="btn btn3" onclick="recommendation()">Recommendations<span class="tab1">→</button></a>
            <p class="FadeIn custom-paragraph">Turning Challenges <br>into an Opportunities</p>
            <button class="button button1" onclick="toggleLogout()">
                <?php echo htmlspecialchars($username); ?>
            </button>
            <div class="logout-options" id="logoutOptions">
                <a href="index.php">Log Out</a>
                
            </div>
        </div>
    </div>
</div>

    <script>
        function startExam() {
            if (confirm("Are you sure you want to start the exam? You can not exit after this move and the timer will start when you click OK.")) {
                // If user clicks OK, redirect to exam page to start the exam
                window.location.href = "exam.php";
            }
        }

        function result() {
                window.location.href = "result.html";
        }

        function recommendation() {
                window.location.href = "reco.html";
        }

        function toggleLogout() {
            var logoutOptions = document.getElementById("logoutOptions");
            logoutOptions.classList.toggle("show");
        }
    </script>
</body>
</html>
