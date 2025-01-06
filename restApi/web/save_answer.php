<?php
// Start session to access session data
session_start();

// Check if the necessary data (question_id and answer) is sent via POST
if (isset($_POST['answers']) && !empty($_POST['answers'])) {
    // Loop through each question and save the selected answer to the session
    foreach ($_POST['answers'] as $questionId => $answer) {
        $_SESSION['answers'][$questionId] = $answer;
    }

    // If the "save_draft" button is clicked, save the progress and redirect
    if (isset($_POST['save_draft'])) {
        echo 'Draft saved successfully!';
    } else {
        header('location: StudentHP.php');
    }
} else {
    // If no answers are posted, respond with an error
    echo 'No answers selected';
}
?>
