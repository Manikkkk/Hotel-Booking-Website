<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['usermail']) || empty($_SESSION['usermail'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get JSON data from request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validate required fields
if (!isset($data['user_message']) || !isset($data['bot_response']) || !isset($data['time'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Sanitize inputs
$username = isset($data['username']) ? mysqli_real_escape_string($conn, $data['username']) : $_SESSION['usermail'];
$email = mysqli_real_escape_string($conn, $_SESSION['usermail']);

// Encrypt the messages before storing
$user_message = mysqli_real_escape_string($conn, encrypt_message($data['user_message']));
$bot_response = mysqli_real_escape_string($conn, encrypt_message($data['bot_response']));
$time = mysqli_real_escape_string($conn, $data['time']);

// Insert message into database
$sql = "INSERT INTO messages (username, email, user_message, bot_response, time) 
        VALUES ('$username', '$email', '$user_message', '$bot_response', '$time')";

if (mysqli_query($conn, $sql)) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}
?>
