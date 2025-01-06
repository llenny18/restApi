<?php
// Start session to ensure the user is logged in
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch a limited number of questions per topic from all tables
function fetchQuestions($conn) {
    $questions = [];

    // Define the tables and number of questions per topic
    $tableConfigs = [
        'financial' => 2,
        'aud' => 1,
        'tax' => 2,
        'mng' => 3,
        'adv' => 1,   // Table name in the database
        'reg' => 1
    ];

    // Define primary key for each table
    $primaryKeys = [
        'financial' => 'fin_id',
        'aud' => 'aud_id',
        'tax' => 'tax_id',
        'mng' => 'mng_id',
        'adv' => 'adv_id',   // Corresponding to the "adv" table
        'reg' => 'reg_id'
    ];

    foreach ($tableConfigs as $table => $limit) {
        if ($limit > 0) {
            // Get distinct topics from each table
            $topicsResult = $conn->query("SELECT DISTINCT topic FROM $table");
            if ($topicsResult) {
                while ($row = $topicsResult->fetch_assoc()) {
                    $topic = $row['topic'];

                    // Fetch limited number of questions per topic
                    $topicSafe = $conn->real_escape_string($topic);
                    $primaryKey = $primaryKeys[$table]; // Get the primary key for the table

                    $sql = "SELECT 
                                question, 
                                scenario, 
                                image, 
                                opt1, 
                                opt2, 
                                opt3, 
                                opt4, 
                                answer, 
                                $primaryKey AS question_id 
                            FROM $table 
                            WHERE topic = '$topicSafe' 
                            ORDER BY RAND() 
                            LIMIT $limit";
                    $result = $conn->query($sql);
                    if ($result) {
                        while ($question = $result->fetch_assoc()) {
                            $question['question_id'] = "{$table}_{$question['question_id']}";
                            $questions[$table][] = $question;  // Group questions by table (topic)
                        }
                    }
                }
            }
        }
    }

    return $questions;
}

// Fetch questions
$allQuestions = fetchQuestions($conn);

// Group questions by scenario within each topic
$groupedQuestionsByTopic = [];
foreach ($allQuestions as $topic => $questions) {
    foreach ($questions as $question) {
        $scenarioKey = !empty($question['scenario']) ? $question['scenario'] : 'no_scenario';
        $groupedQuestionsByTopic[$topic][$scenarioKey][] = $question;
    }
}

// Shuffle questions within each topic and scenario
$shuffledQuestions = [];
foreach ($groupedQuestionsByTopic as $topic => $scenarios) {
    $shuffledQuestions[$topic] = [];
    $scenarioKeys = array_keys($scenarios);
    shuffle($scenarioKeys); // Shuffle the scenario groups within each topic

    foreach ($scenarioKeys as $scenario) {
        $questions = $scenarios[$scenario];
        shuffle($questions); // Shuffle questions within each scenario group
        $shuffledQuestions[$topic][$scenario] = $questions;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <header>
    
<div class="header-top">
    <div class="school-info">
        <img src="Style/logo.png" alt="School Logo"> 
        <div>
            <h1>Colegio de San Juan de Letran Calamba</h1>
            <p>Bucal, Calamba City, Laguna, Philippines • 4027</p>
        </div>
    </div>
 <div id="timer">Time Remaining: 3h 0m 1s</div>
    <div class="logo-container">
        <img src="Style/cpace1.png" alt="CPAce Logo" class="logo">
    </div>
</div>
</header>

    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam</title>
    <link href="Style/exam.css" rel="stylesheet" type="text/css"> <!-- Link to external CSS -->
    <link href="https://db.onlinewebfonts.com/c/be6ee7dae05b1862ef6f63d5e2145706?family=Monotype+Old+English+Text+W01" rel="stylesheet">
    <script type="text/javascript">
        // Countdown timer (3 hours)
        var timeLeft = 3 * 60 * 60; // 16 minutes in seconds

  function updateTimer() {
    var hours = Math.floor(timeLeft / 3600); // Get hours
    var minutes = Math.floor((timeLeft % 3600) / 60); // Get minutes
    var seconds = timeLeft % 60; // Get seconds

    // Display the time in "HH:mm:ss" format
document.getElementById("timer").innerHTML = "Time Remaining: " + hours + "h " + minutes + "m " + seconds + "s";


            // Notify when 10 seconds are left
            if (timeLeft === 9000) {
                alert("2 hours and 30 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 7200) {
                alert("2 hours left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 5400) {
                alert("1 hour and 30 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 3600) {
                alert("1 hour left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 1800) {
                alert("30 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 900) {
                alert("15 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 600) {
                alert("10 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 300) {
                alert("5 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft === 120) {
                alert("2 minutes left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }    
            if (timeLeft === 60) {
                alert("1 minute left! Take a break — the timer is paused. Click 'OK' when you're ready to continue");
            }
            if (timeLeft <= 0) {
                // Time is up, submit the form after a short delay
                alert("TIME'S UP!");
                setTimeout(function() {
                    document.getElementById("examForm").submit();
                }, 500); // Delay to ensure form submission happens correctly
            } else {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }

        // Prevent page refresh and submit automatically when leaving
        window.onbeforeunload = function(event) {
            console.log("User is trying to leave the page.");
            document.getElementById("examForm").submit();
            return "Are you sure you want to leave? Your progress will be lost."; // Some browsers may show this message.
        };

        window.onload = function() {
            updateTimer();  // Start the timer when the page loads
        };
    </script>
</head>
<body>

<div class="container">
    <h2>Online Exam</h2>
    
    <form id="examForm" action="submit.php" method="post">
        <div id="question-container">
            <?php
            // Define the order of topics with display names
            $examTopics = [
                'financial' => 'Financial',
                'adv' => 'Advance Financial',  // Display name for "adv"
                'tax' => 'Taxation',
                'mng' => 'Management',
                'aud' => 'Auditing',
                'reg' => 'Regulatory Framework'
            ];

            // Display all questions grouped by topic and scenario with aligned numbering
            $questionNumber = 1;

            // Loop through each topic in the defined order
            foreach ($examTopics as $topicTable => $topicDisplayName) {
                if (isset($shuffledQuestions[$topicTable])) {
                    echo "<h3>" . htmlspecialchars($topicDisplayName) . " Questions</h3>";  // Display topic title

                    foreach ($shuffledQuestions[$topicTable] as $scenario => $questions) {
                        echo '<div class="scenario-container">';

                        // Display scenario if available
                        if ($scenario !== 'no_scenario') {
                            $scenarioText = preg_replace_callback(
                                '/\[Image:\s*(.+?)\]/i',
                                function ($matches) {
                                    $imagePath = htmlspecialchars($matches[1]);
                                    return "<br><img src='$imagePath' alt='Scenario Image' style='max-width: 100%; margin: 10px 0;'><br>";
                                },
                                htmlspecialchars($scenario)
                            );
                            echo "<p><strong>Scenario:</strong> $scenarioText</p>";
                        }

                        // Loop through each question within this scenario group
                        foreach ($questions as $question) {
                            $questionId = $question['question_id']; // Use a unique identifier for each question

                            echo '<div class="question-box">';

                            // Display question text with a proper question number
                            echo "<p><strong>Question $questionNumber:</strong> " . htmlspecialchars($question['question']) . "</p>";

                            // Display image (if available)
                            if (!empty($question['image'])) {
                                echo "<img src='" . htmlspecialchars($question['image']) . "' alt='Question Image' style='max-width: 100%; margin: 10px 0;'><br>";
                            }

                            // Display answer options
                            $options = [
                                'A' => $question['opt1'], 
                                'B' => $question['opt2'], 
                                'C' => $question['opt3'], 
                                'D' => $question['opt4']
                            ];
                            $correctAnswer = $question['answer'];  // Correct answer (A, B, C, D)

                            // Shuffle the options array
                            $optionKeys = array_keys($options);
                            shuffle($optionKeys); // Shuffle the answer options

                            // Display the shuffled options
                            foreach ($optionKeys as $key) {
                                $value = $options[$key]; // Get the answer text from the shuffled key
                                echo "<label>
                                        <input type='radio' name='answers[$questionId]' value='$key' required>
                                        " . htmlspecialchars($value) . "
                                    </label><br>";
                            }

                            echo '</div><br>';
                            $questionNumber++;
                        }

                        echo '</div><br>';
                    }
                }
            }
            ?>
        </div>

        <!-- Submit button -->
        <button type="submit">Submit Exam</button>
    </form>
</div>

</body>
</html>
