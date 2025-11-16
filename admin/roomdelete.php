<?php
include '../config.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('Invalid room ID');
        window.location.href = 'room.php';
    </script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get room info to delete image if exists
$get_room = mysqli_query($conn, "SELECT image FROM room WHERE id = '$id'");
if(mysqli_num_rows($get_room) > 0) {
    $room = mysqli_fetch_assoc($get_room);
    
    // Delete image file if it exists
    if(!empty($room['image']) && file_exists('../' . $room['image'])) {
        unlink('../' . $room['image']);
    }
}

// Delete room from database
$delete_sql = "DELETE FROM room WHERE id = '$id'";
$result = mysqli_query($conn, $delete_sql);

if($result) {
    echo "<script>
        alert('Room deleted successfully');
        window.location.href = 'room.php';
    </script>";
} else {
    echo "<script>
        alert('Failed to delete room: " . mysqli_error($conn) . "');
        window.location.href = 'room.php';
    </script>";
}
?>
