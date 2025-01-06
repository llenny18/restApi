<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if Save Draft is clicked (from exam.php)
if (isset($_POST['save_draft']) && $_POST['save_draft'] == '1') {
    // Save the answers to session (draft)
    $_SESSION['draft_answers'] = $_POST['answers'];

    // Save the questions being displayed (this can be done using an array of question IDs or full question data)
    if (isset($_POST['displayed_questions'])) {
        // Saving displayed questions to session
        $_SESSION['draft_questions'] = $_POST['displayed_questions']; // This stores the question IDs or full question data
    }

    // Save the number of questions being displayed
    if (isset($_POST['question_count'])) {
        $_SESSION['draft_question_count'] = $_POST['question_count']; // Save the number of questions
    }

    // Feedback to the user
    echo "<script>alert('Your draft has been saved!'); window.location.href='studenthp.php';</script>";
} else {
    echo "<script>alert('No draft to save!'); window.location.href='exam.php';</script>";
}
?>
