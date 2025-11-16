<?php
session_start();
include '../config.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Invalid staff ID',
            confirmButtonColor: '#4361ee'
        }).then(() => {
            window.location.href = 'staff.php';
        });
    </script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Delete staff from database
$delete_sql = "DELETE FROM staff WHERE id = '$id'";
$result = mysqli_query($conn, $delete_sql);

if($result) {
    header("Location: staff.php");
    exit;
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete staff member: " . mysqli_error($conn) . "',
            confirmButtonColor: '#4361ee'
        }).then(() => {
            window.location.href = 'staff.php';
        });
    </script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deleting Staff...</title>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        // Redirect to staff page if JavaScript is enabled and no PHP redirect happened
        window.location.href = 'staff.php';
    </script>
</body>
</html>
