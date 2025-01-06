<?php
$conn = new mysqli('localhost', 'root', '', 'cpa');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET method: Retrieve data for editing
    if (!isset($_GET["id"])) {
        header("location: q2.php");
        exit;
    }

    $id = $_GET["id"];

    // Query the database for the selected question
    $sql = "SELECT * FROM adv WHERE adv_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error executing query: " . $conn->error);
    }

    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: q2.php");
        exit;
    }

    // Assign variables
    $question = $row["question"];
    $opt1 = $row["opt1"];
    $opt2 = $row["opt2"];
    $opt3 = $row["opt3"];
    $opt4 = $row["opt4"];
    $answer = $row["answer"];
    $topic = $row["topic"];
    $imagePath = $row["image"]; // Retrieve the existing image path

    $stmt->close();
} else {
    // POST method: Update the data
    $id = $_POST['id'];
    $question = $_POST['question'];
    $opt1 = $_POST['opt1'];
    $opt2 = $_POST['opt2'];
    $opt3 = $_POST['opt3'];
    $opt4 = $_POST['opt4'];
    $answer = $_POST['answer'];
    $topic = $_POST['topic'];
    $imagePath = $_POST['existingImage']; // Retain the current image path

    // Check if "Remove Image" is selected
    if (isset($_POST['removeImage']) && $_POST['removeImage'] == "1") {
        $imagePath = ''; // Remove the image
        $question = preg_replace('/\[Image:\s*(.+?)\]/', '', $question); // Remove image reference
    }

    // Handle image replacement
    if (isset($_FILES['questionImage']) && $_FILES['questionImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['questionImage']['tmp_name'];
        $imageName = basename($_FILES['questionImage']['name']);
        $uploadDir = 'uploads/';
        $newImagePath = $uploadDir . $imageName;

        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        if (move_uploaded_file($imageTmpPath, $newImagePath)) {
            $imagePath = $newImagePath; // Update with the new image path
            $question = preg_replace('/\[Image:\s*(.+?)\]/', '', $question); // Remove old image reference
            $question .= " [Image: " . $newImagePath . "]"; // Add new image reference
        }
    }

    // Validate input
    if (empty($id) || empty($question) || empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4) || empty($answer) || empty($topic)) {
        die("All fields are required.");
    }

    // SQL query to update the data
    $sql = "UPDATE adv SET 
                question = ?, 
                opt1 = ?, 
                opt2 = ?, 
                opt3 = ?, 
                opt4 = ?, 
                answer = ?, 
                topic = ?, 
                image = ? 
            WHERE adv_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $question, $opt1, $opt2, $opt3, $opt4, $answer, $topic, $imagePath, $id);

    if ($stmt->execute()) {
        header("location: q2.php");
        exit;
    } else {
        die("Error updating record: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
