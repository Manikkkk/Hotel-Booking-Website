<?php
include '../config.php';

// Check if the price column exists
$check_price = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'price'");
if(mysqli_num_rows($check_price) == 0) {
    // Add price column
    $alter_price = mysqli_query($conn, "ALTER TABLE room ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0");
    if($alter_price) {
        echo "Price column added successfully<br>";
    } else {
        echo "Failed to add price column: " . mysqli_error($conn) . "<br>";
    }
}

// Check if the room_no column exists
$check_room_no = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'room_no'");
if(mysqli_num_rows($check_room_no) == 0) {
    // Add room_no column
    $alter_room_no = mysqli_query($conn, "ALTER TABLE room ADD COLUMN room_no VARCHAR(10) NOT NULL DEFAULT ''");
    if($alter_room_no) {
        echo "Room number column added successfully<br>";
    } else {
        echo "Failed to add room number column: " . mysqli_error($conn) . "<br>";
    }
}

echo "Database update completed";
?>