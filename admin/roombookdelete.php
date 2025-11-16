<?php
include '../config.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('Invalid booking ID');
        window.location.href = 'roombook.php';
    </script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Delete booking from database
$delete_sql = "DELETE FROM roombook WHERE id = '$id'";
$result = mysqli_query($conn, $delete_sql);

// Also delete from payment table if exists
$payment_sql = "DELETE FROM payment WHERE id = '$id'";
mysqli_query($conn, $payment_sql);

if($result) {
    echo "<script>
        alert('Booking deleted successfully');
        window.location.href = 'roombook.php';
    </script>";
} else {
    echo "<script>
        alert('Failed to delete booking: " . mysqli_error($conn) . "');
        window.location.href = 'roombook.php';
    </script>";
}
?>
