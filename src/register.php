<?php
session_start();
include('quiz_app.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username already exists
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username already exists
        $error_message = "Username already taken. Please choose a different username.";
    } else {
        // Insert new user into users table
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $success_message = "Registration successful. <a href='login.php'>Login here</a>.";
        } else {
            $error_message = "Error registering user: " . $stmt->error;
        }
    }
    header("Location: login.php");
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($success_message)) echo "<p style='color: green;'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
