<?php
// Database Connection
$host = 'localhost';
$dbname = 'mynewdb';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

// Function to check if the user has taken the quiz
function has_user_taken_quiz($conn, $user_id) {
    $query = "SELECT quiz_taken FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($quiz_taken);
    $stmt->fetch();
    $stmt->close();
    return $quiz_taken == 1; // Returns true if quiz_taken is 1
}

// Function to update the quiz status after the user takes the quiz
function mark_quiz_as_taken($conn, $user_id) {
    $query = "UPDATE users SET quiz_taken = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Function to fetch quiz questions
function get_questions($conn) {
    $query = "SELECT * FROM questions";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}
// Function to fetch user by username
function get_user($conn, $username) {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

?>
