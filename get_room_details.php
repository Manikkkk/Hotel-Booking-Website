<?php
session_start();
include 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if room ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Room ID is required']);
    exit;
}

$roomId = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch room details from database with explicit column selection including bed
$sql = "SELECT id, type, bed, price, room_no, image FROM room WHERE id = '$roomId'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        'error' => 'Database query failed: ' . mysqli_error($conn),
        'sql' => $sql
    ]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    $room = mysqli_fetch_assoc($result);
    
    // Prepare response with bed column
    $response = [
        'id' => $room['id'],
        'type' => $room['type'],
        'bed' => isset($room['bed']) ? $room['bed'] : 'Single', // Use bed column directly
        'price' => isset($room['price']) ? $room['price'] : getPriceByRoomType($room['type']),
        'room_no' => isset($room['room_no']) ? $room['room_no'] : '',
        'image' => !empty($room['image']) ? $room['image'] : 'images/rooms/placeholder.jpg'
    ];
    
    echo json_encode($response);
} else {
    echo json_encode([
        'error' => 'Room not found',
        'id' => $roomId
    ]);
}

// Function to get price by room type (fallback)
function getPriceByRoomType($type) {
    switch ($type) {
        case 'Superior Room':
            return 5000;
        case 'Deluxe Room':
            return 4000;
        case 'Guest House':
            return 3500;
        case 'Single Room':
            return 2500;
        default:
            return 3000;
    }
}
?>



