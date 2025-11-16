<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

$usermail = $_SESSION['usermail'];

// Fetch username (optional)
$get_user = mysqli_query($conn, "SELECT username FROM signup WHERE Email = '$usermail'");
$user = mysqli_fetch_assoc($get_user);
$username = $user ? $user['username'] : 'User';

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_messages'])) {
    $message = mysqli_real_escape_string($conn, $_POST['user_messages']);
    $time = date("Y-m-d H:i:s");

    $sql = "INSERT INTO messages (username, usermail, user_message, time) VALUES ('$username', '$usermail', '$message', '$time')";
    if (mysqli_query($conn, $sql)) {
        $success = "Message sent successfully!";
    } else {
        $error = "Failed to send message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Send a Message to Admin</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="user_message" class="form-label">Your Message</label>
                <textarea name="user_message" id="user_message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>

        <div class="mt-3">
            <a href="user_messages.php" class="btn btn-outline-secondary">View My Messages</a>
        </div>
    </div>
</body>
</html>
