<?php
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = "Guest"; // Default value if no session exists
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPA Board Exam Reviewer</title>
    <link rel="stylesheet" href="Style/homepage.css">
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">

</head>
<body>
    <div class="header-top">
        <div class="school-info">
            <img src="Style/logo.png" alt="School Logo"> 
            <div>
                <h1>Colegio de San Juan de Letran Calamba</h1>
                <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
            </div>
        </div>

        <div class="logo-container">
            <img src="Style/cpace1.png" alt="CPAce Logo" class="logo">
        </div>
        
        <div class="user-info">
            <span class="username" onclick="toggleDropdown()"><?php echo htmlspecialchars($username); ?></span>
            <div id="user-dropdown" class="dropdown hidden">
                <ul>
                    <li><a href="index.php">Log Out</a></li>
                    <li><a href="changepass.html">Change Password</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            console.log("Dropdown toggled");  // Debugging line
            dropdown.classList.toggle('hidden');
        }
    </script>
</body>

<!-- Navigation Bar with custom options -->
<nav>
    <ul>
        <li><a href="exam.php">Start Exam</a></li>
        <li><a href="algoResult.php">Results & Recommendations</a></li>
    </ul>
</nav>

<div class="school-section">
    <h1>SCHOOL OF BUSINESS, MANAGEMENT, AND ACCOUNTANCY</h1>
    <div class="sbma-logo">
        <img src="Style/SBMA_logo.png" alt="SBMA Logo" />
    </div>

    <h2>Vision</h2>
    <p>
        To be a recognized premier academic institution dedicated to educational distinction and producers of exceptional
        business professionals ready to meet the challenges of a domestic and globalized business environment grounded by
        the Dominican values and culture of conscience, discipline, and excellence.
    </p>

    <h2>Mission</h2>
    <p>
        The School of Business, Management and Accountancy commits to develop responsive and productive professionals and
        entrepreneurs by providing quality business curricular programs that are imbued with Filipino, Dominican, and Christian
        attitudes and principles.
    </p>

    <h2>About the School</h2>
    <p>
        The School of Business, Management, and Accountancy shall provide quality and relevant business education to its
        students and eventually produce graduates more responsive and industry-ready in both local and international settings.
    </p>

    <h2>Quality Objectives</h2>
    <ul>
        <li>To continually deliver quality business education through outcomes-based instruction and quality departmental services to the stakeholders.</li>
        <li>To continually elevate the accreditation level of the business education curricular programs.</li>
        <li>To strengthen the technical and professional competencies of administrators, faculty members, and support personnel.</li>
    </ul>
</div>
<h3 class="highlight">Bachelor of Science in Accountancy</h3>

   <h2 class="program-objectives-heading">Program Educational Objectives</h2>
<ul class="program-objectives">
    <li>Develop and maintain an attitude of learning to learn in order to maintain their competence;</li>
    <li>Develop knowledge-based problem solving skills and ethical values;</li>
    <li>Support the various stakeholders in taking strategic and operating decisions through the presentation and analysis of financial data and information arising from business transactions;</li>
    <li>Practice as accounting professionals engaged in services such as, but not limited to, audit and assurance, tax consultancy and management advisory anchored on professional ethics and moral values;</li>
    <li>Objectively design, develop and evaluate accounting systems;</li>
    <li>Conduct accounting and finance related research in organizing people and resources to create value;</li>
    <li>Contribute to the economic development as responsible partners of the government and stakeholders through managing and/or establishing income generating centers where they portray the roles of strategic business partners and change agents and/or consultants, as entrepreneurs, or managers in government agencies such as those in internal revenues, finance, and trade and industries;</li>
    <li>Impart and share expertise to those in the academe and those pursuing continuing professional development.</li>
</ul>
</div>

    <div class="about-cpace">
        <h2>About CPAce</h2>
        <p>
            CPAce is an innovative and adaptive learning platform designed to help students prepare for the CPA Board Exam. 
            Using a combination of decision tree algorithms and hybrid methods, CPAce offers personalized recommendations
            that cater to each student’s strengths and weaknesses, providing a more effective and efficient way to study.
            With real-time progress tracking and a comprehensive question bank, CPAce aims to empower students to excel in their 
            exams and become proficient professionals in the field of accountancy.
        </p>
        <p>
            This platform, CPAce (CPA Exam Adaptive Learning System), was developed by a team of passionate and dedicated Computer Science students from Colegio de San Juan de Letran Calamba. The team consists of three individuals who are committed to advancing technology in education. Their goal is to provide an innovative solution to help students efficiently prepare for the CPA Board Exam through adaptive learning.
        </p>
        <p>
        Kenji Denise Lawas, a skilled software developer with a strong interest in machine learning and artificial intelligence, contributed significantly to developing the system's core algorithms. His expertise in data structures and algorithm optimization played a crucial role in building an adaptive platform that personalizes the learning experience for each student.
        </p>
        <p>
        Luis Daniel Laserna, a driven individual passionate about user experience (UX) and front-end development, was responsible for creating an intuitive and visually appealing user interface. His focus on user-centric design ensured that the platform is functional and easy to navigate, providing an optimal learning environment for students.
        </p>
        <p>
        Jowell Andrei Dayan, the leader who guided the team with a perfect balance of technical expertise and emotional intelligence, fostering collaboration and innovation. His leadership empowered each member to excel and contributed greatly to the success of CPAce.
        </p>
    </div>
</div>

<footer>
    <p>CPAce Reviewer &copy; 2024. All rights reserved.</p>
</footer>


</body>
</html>
