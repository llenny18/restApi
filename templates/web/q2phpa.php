<?php
// Establish connection to the database
$conn = new mysqli('localhost', 'root', '', 'cpa');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = isset($_POST['questionText']) ? $_POST['questionText'] : [];
    $opt1 = $_POST['opt1'];
    $opt2 = $_POST['opt2'];
    $opt3 = $_POST['opt3'];
    $opt4 = $_POST['opt4'];
    $answers = $_POST['answer'];
    $topics = $_POST['topic'];
    $images = $_FILES['questionImage'];
    $scenario = isset($_POST['scenario']) ? $_POST['scenario'] : '';

    // Handle scenario image upload
    $scenarioImagePath = '';
    if (isset($_FILES['scenarioImage']) && $_FILES['scenarioImage']['error'] === UPLOAD_ERR_OK) {
        $scenarioImageTmpPath = $_FILES['scenarioImage']['tmp_name'];
        $scenarioImageName = basename($_FILES['scenarioImage']['name']);
        $uploadDir = 'uploads/';
        $scenarioImagePath = $uploadDir . $scenarioImageName;

        // Create uploads directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($scenarioImageTmpPath, $scenarioImagePath);
    }

    // Combine scenario text and image path
    $scenarioData = $scenario . ($scenarioImagePath ? " [Image: $scenarioImagePath]" : '');

    foreach ($questions as $index => $question) {
        $questionText = $question;
        $opt1Text = $opt1[$index];
        $opt2Text = $opt2[$index];
        $opt3Text = $opt3[$index];
        $opt4Text = $opt4[$index];
        $answerText = $answers[$index];
        $topicText = $topics[$index];
        $image = '';

        if (isset($images['tmp_name'][$index]) && $images['error'][$index] === UPLOAD_ERR_OK) {
            $imageTmpPath = $images['tmp_name'][$index];
            $imageName = basename($images['name'][$index]);
            $image = $uploadDir . $imageName;

            move_uploaded_file($imageTmpPath, $image);
        }

        $stmt = $conn->prepare("INSERT INTO adv (question, opt1, opt2, opt3, opt4, answer, topic, image, scenario)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $questionText, $opt1Text, $opt2Text, $opt3Text, $opt4Text, $answerText, $topicText, $image, $scenarioData);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    header("Location: q2a.php");
    exit;
}
?>
