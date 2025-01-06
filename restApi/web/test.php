<!DOCTYPE html>
<html>
<head>
    <title>Random Question</title>
</head>
<body>
    <h1>Random Question</h1>
    <?php
    // Array of questions and answers
    $questions = array(
        "What is the capital of France?" => "Paris",
        "Who wrote 'To Kill a Mockingbird'?" => "Harper Lee",
        "What is the chemical symbol for water?" => "H2O",
        "What year did the Titanic sink?" => "1912",
        "Who painted the Mona Lisa?" => "Leonardo da Vinci",
    );

    // Get a random question
    $random_question = array_rand($questions);

    // Get the answer for the random question
    $correct_answer = $questions[$random_question];

    // Display the random question
    echo "<p><strong>Question:</strong> $random_question</p>";

    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get user's answer
        $user_answer = isset($_POST['user_answer']) ? $_POST['user_answer'] : '';

        // Check if the user's answer is correct
        if (strcasecmp($user_answer, $correct_answer) === 0) {
            echo "<p style='color:green;'>Correct! The answer is $correct_answer</p>";
        } else {
            echo "<p style='color:red;'>Incorrect. The correct answer is $correct_answer</p>";
        }
    }
    ?>
    <form method="post">
        <label for="user_answer">Your Answer:</label>
        <input type="text" id="user_answer" name="user_answer" required>
        <br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
