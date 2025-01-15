<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <a href="add_question.php" style="margin-bottom: 20px; display: inline-block; text-decoration: none; color: white; background-color: green; padding: 10px; border-radius: 5px;">
        Add New Question
    </a>

</head>
<body>
    <h1>Quiz Results</h1>
    <p>You scored: <?php echo $score; ?></p>
    <a href="index.php">Take the Quiz Again</a>
    <a href="logout.php" >Logout</a>
</body>
</html>
