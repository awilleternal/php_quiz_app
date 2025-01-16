<?php
session_start();
include('quiz_app.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];//$_SESSION is a global array used to store session data.
var_dump($_SESSION);
$quiz_taken = has_user_taken_quiz($conn, $user_id);
//var_dump($GLOBALS);
if ($quiz_taken) {
    echo "<h1>You have already taken the quiz!</h1>";
    echo '<a href="logout.php">Logout</a>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $questions = get_questions($conn);

    foreach ($_POST['answers'] as $question_id => $answer) {
        var_dump($_POST);
        $query = "SELECT correct_answer FROM questions WHERE id = ?";//?is for attribure or info that we dont have to execute 
        $stmt = $conn->prepare($query);                              //wwe can use bind_parameter to put the info in ?
        echo "fff";
        var_dump($stmt);
        $stmt->bind_param("i", $question_id);//flow :- prepare queery->bind parameter->execute>bindresultto variable >fetch >close
        $stmt->execute();
        $stmt->bind_result($correct_answer);
        $stmt->fetch();

        if ($answer === $correct_answer) {
            $score++;
        }

        $stmt->close();
    }

    mark_quiz_as_taken($conn, $user_id);
    $_SESSION['score'] = $score;
    header("Location: results.php");
    exit();
}

$questions = get_questions($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MCQ Quiz</title>
</head>
<body>
    <h1> MCQ Quiz</h1>
    <a href="add_question.php" style="margin-bottom: 20px; display: inline-block; text-decoration: none; color: white; background-color: green; padding: 10px; border-radius: 5px;">Add New Question</a>
    <a href="logout.php">Logout</a>
    <form method="POST">
        <?php foreach ($questions as $question): ?>
            <div>
                <p><?php echo $question['question_text']; ?></p>
                <?php var_dump($question);?>
                <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A" required> <?php echo $question['option_a']; ?></label><br>
                <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B"> <?php echo $question['option_b']; ?></label><br>
                <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C"> <?php echo $question['option_c']; ?></label><br>
                <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D"> <?php echo $question['option_d']; ?></label><br>
            </div>
        <?php endforeach; ?>
        <button type="submit">Submit Answers</button>
    </form>
</body>
</html>
