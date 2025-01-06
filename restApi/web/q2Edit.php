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
    $sql = "SELECT * FROM adv WHERE adv_id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: q2.php");
        exit;
    }

    $question = $row["question"];
    $opt1 = $row["opt1"];
    $opt2 = $row["opt2"];
    $opt3 = $row["opt3"];
    $opt4 = $row["opt4"];
    $answer = $row["answer"];
    $topic = $row["topic"];
    $image = $row["image"]; // Fetch the image from the database
    $scenario = $row['scenario']; // Corrected this line

} else {
    // POST method: Update the question
    $id = $_POST['id'];
    $question = $_POST['question'];
    $opt1 = $_POST['opt1'];
    $opt2 = $_POST['opt2'];
    $opt3 = $_POST['opt3'];
    $opt4 = $_POST['opt4'];
    $answer = $_POST['answer'];
    $topic = $_POST['topic'];
    $image = $_POST['existingImage']; // Retain the existing image path

    // Handle image upload
    if (isset($_FILES['questionImage']) && $_FILES['questionImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['questionImage']['tmp_name'];
        $imageName = basename($_FILES['questionImage']['name']);
        $uploadDir = 'uploads/';
        $image = $uploadDir . $imageName;

        // Create the uploads directory if not existing
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        move_uploaded_file($imageTmpPath, $image);
    }

    // Check if the "Remove Image" option is selected
    if (isset($_POST['removeImage']) && $_POST['removeImage'] == '1') {
        $image = ''; // Remove the image
    }

    // SQL query to update the data
    $stmt = $conn->prepare("UPDATE adv SET 
        question = ?, 
        opt1 = ?, 
        opt2 = ?, 
        opt3 = ?, 
        opt4 = ?, 
        answer = ?, 
        topic = ?, 
        image = ? 
        WHERE adv_id = ?");
    $stmt->bind_param("ssssssssi", $question, $opt1, $opt2, $opt3, $opt4, $answer, $topic, $image, $id);

    if ($stmt->execute()) {
        header("location: q2.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Question</title>
  <link href='Style/QAdd.css' rel='stylesheet' type='text/css'>
  <script>
    // JavaScript to preview the uploaded image and add an "X" button to remove it
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.innerHTML = ''; // Clear previous content

        const file = event.target.files[0];

        if (file) {
            const imgWrapper = document.createElement('div');
            imgWrapper.style.position = 'relative';
            imgWrapper.style.display = 'inline-block';

            const imgElement = document.createElement('img');
            imgElement.src = URL.createObjectURL(file);
            imgElement.style.maxWidth = '200px'; // Set max width for the preview
            imgElement.style.marginTop = '10px';
            imgElement.style.border = '1px solid #ccc';
            imgElement.style.borderRadius = '5px';

            const removeButton = document.createElement('span');
            removeButton.textContent = '×';
            removeButton.style.position = 'absolute';
            removeButton.style.top = '5px';
            removeButton.style.right = '5px';
            removeButton.style.background = '#ff0000';
            removeButton.style.color = '#fff';
            removeButton.style.borderRadius = '50%';
            removeButton.style.width = '20px';
            removeButton.style.height = '20px';
            removeButton.style.display = 'flex';
            removeButton.style.alignItems = 'center';
            removeButton.style.justifyContent = 'center';
            removeButton.style.cursor = 'pointer';
            removeButton.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';

            // Event listener to remove the image preview
            removeButton.addEventListener('click', () => {
                imagePreview.innerHTML = '<p>No image selected.</p>';
                document.querySelector('input[name="questionImage"]').value = ''; // Clear the file input
                document.getElementById('existingImage').value = ''; // Clear the existing image path
            });

            imgWrapper.appendChild(imgElement);
            imgWrapper.appendChild(removeButton);
            imagePreview.appendChild(imgWrapper);
        } else {
            imagePreview.innerHTML = '<p>No image selected.</p>';
        }
    }
    
    function updateCharacterCount() {
        const textarea = document.getElementById('question');
        const charCountSpan = document.getElementById('charCount');
        
        const currentLength = textarea.value.length;
        const maxLength = textarea.getAttribute('maxlength');
        
        charCountSpan.textContent = `${currentLength}/${maxLength}`;
    }
  </script>
</head>
<body>
<div class="taskbar">
    <div class="logo"></div>
    <a href="index.php" class="taskbar-button">Logout</a>
    <a href="list.php" class="taskbar-button">Test</a>
    <a href="admin.php" class="taskbar-button">Account</a>
</div>
<a href="q2.php" class="back-button">&#8592;</a>
<form class="section" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" id="existingImage" name="existingImage" value="<?php echo htmlspecialchars($image); ?>">
    <h1>Edit Question</h1>
    
    <h3>Scenario:</h3>
    <div id="textQuestionInput">
    <?php
    // Scenario text
    $scenario_text = htmlspecialchars($scenario);

    // Check if the scenario contains an image URL (e.g., [Image: uploads/abc.jpg])
    $image_url = "";
    if (preg_match('/\[\s*Image:\s*(.*?)\s*\]/', $scenario_text, $matches)) {
        $image_url = $matches[1];  // Capture the image URL
        $scenario_text = preg_replace('/\[\s*Image:\s*.*?\s*\]/', '', $scenario_text);  // Remove the image markdown from the text
    }

    // Display the scenario text
    echo "<p>{$scenario_text}</p>";

    // If an image URL was found, display it
    if (!empty($image_url)) {
        echo "<img src='{$image_url}' alt='Scenario Image' style='max-width: 200px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;'>";
    }
    ?>
    </div>

    <h3>Question:</h3>
        <div id="textQuestionInput">
            <textarea id="question" name="question" placeholder="Input your Question Here." autofocus maxlength="500" oninput="updateCharacterCount()"><?php echo htmlspecialchars($question);?></textarea>
        </div>
    <span id="charCount">0/500</span>

    <h3>Attached Image:</h3>
    <div id="imagePreview">
        <?php if (!empty($image)): ?>
            <div style="position: relative; display: inline-block;">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Question Image" style="max-width: 200px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;" />
                <span style="position: absolute; top: 5px; right: 5px; background: #ff0000; color: #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" onclick="document.getElementById('existingImage').value = ''; document.getElementById('imagePreview').innerHTML = '<p>No image selected.</p>';">×</span>
            </div>
        <?php else: ?>
            <p>No image selected.</p>
        <?php endif; ?>
    </div>

    <h3>Attach a New Image:</h3>
    <input type="file" name="questionImage" accept="image/*" onchange="previewImage(event)" />

    <h3>Choices:</h3>
    <input name="opt1" type="text" value="<?php echo htmlspecialchars($opt1); ?>" placeholder="A." />
    <input name="opt2" type="text" value="<?php echo htmlspecialchars($opt2); ?>" placeholder="B." />
    <input name="opt3" type="text" value="<?php echo htmlspecialchars($opt3); ?>" placeholder="C." />
    <input name="opt4" type="text" value="<?php echo htmlspecialchars($opt4); ?>" placeholder="D." />

    <h3>Correct Answer:</h3>
    <select name="answer">
        <option value="A" <?php echo $answer == 'A' ? 'selected' : ''; ?>>A</option>
        <option value="B" <?php echo $answer == 'B' ? 'selected' : ''; ?>>B</option>
        <option value="C" <?php echo $answer == 'C' ? 'selected' : ''; ?>>C</option>
        <option value="D" <?php echo $answer == 'D' ? 'selected' : ''; ?>>D</option>
    </select>

    <h3>Topic:</h3>
    <select name="topic">
        <option value="Part" <?php echo $topic == 'Part' ? 'selected' : ''; ?>>Partnership Accounting</option>
        <option value="Corporate"<?php echo $topic == 'Corporate' ? 'selected' : ''; ?>>Corporate Liquidation </option>
        <option value="Joint"<?php echo $topic == 'Joint' ? 'selected' : ''; ?>>Joint Arrangements (PFRS 11)</option>
        <option value="Revenue"<?php echo $topic == 'Revenue' ? 'selected' : ''; ?>>Revenue Recognition (PFRS 15) </option>
        <option value="Home Office"<?php echo $topic == 'Home Office' ? 'selected' : ''; ?>>Home Office, Branch, and Agency Transactions</option>
        <option value="Combination"<?php echo $topic == 'Combination' ? 'selected' : ''; ?>>Business Combination (PFRS 3) </option>
        <option value="Consolidated"<?php echo $topic == 'Consolidated' ? 'selected' : ''; ?>>Consolidated Financial Statements (PFRS 10) </option>
        <option value="Derivatives"<?php echo $topic == 'Derivatives' ? 'selected' : ''; ?>>Derivatives and Hedging (PFRS 9)</option>
        <option value="Translation"<?php echo $topic == 'Translation' ? 'selected' : ''; ?>>Translation of Foreign Currency Financial Statements (PAS 21/29)</option>
        <option value="no profit"<?php echo $topic == 'no profit' ? 'selected' : ''; ?>>Not-for-Profit and Government Accounting</option>
        <option value="cost"<?php echo $topic == 'cost' ? 'selected' : ''; ?>>Cost Accounting</option>
        <option value="special"<?php echo $topic == 'special' ? 'selected' : ''; ?>>Special Topics </option>
    </select>
  
    <br /><br/>
    <input class="btn" type="submit" value="Update Question" />
</form>
</body>
</html>
