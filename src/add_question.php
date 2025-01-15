<?php
session_start();
include('quiz_app.php');

if (!isset($_SESSION['user_id']) ) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    // Insert question into database
    $query = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);

    if ($stmt->execute()) {
        $success_message = "Question added successfully!";
    } else {
        $error_message = "Error adding question: " . $stmt->error;
    }

    $stmt->close();
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
</head>
<body>
    <h1>Add a New Question</h1>
    <a href="logout.php" >Logout</a>

    <?php if (isset($success_message)) echo "<p style='color: green;'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>

    <form method="POST">
        <label for="question_text">Question:</label><br>
        <textarea name="question_text" id="question_text" rows="4" cols="50" required></textarea><br><br>

        <label for="option_a">Option A:</label><br>
        <input type="text" name="option_a" id="option_a" required><br><br>

        <label for="option_b">Option B:</label><br>
        <input type="text" name="option_b" id="option_b" required><br><br>

        <label for="option_c">Option C:</label><br>
        <input type="text" name="option_c" id="option_c" required><br><br>

        <label for="option_d">Option D:</label><br>
        <input type="text" name="option_d" id="option_d" required><br><br>

        <label for="correct_answer">Correct Answer (A/B/C/D):</label><br>
        <select name="correct_answer" id="correct_answer" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select><br><br>

        <button type="submit">Add Question</button>
    </form>
</body>
</html>
