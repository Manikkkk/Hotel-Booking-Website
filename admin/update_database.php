<?php
include 'config.php';

// Check if password columns need to be updated
$check_signup_password = mysqli_query($conn, "SHOW COLUMNS FROM signup LIKE 'Password'");
$signup_password_column = mysqli_fetch_assoc($check_signup_password);

// If column exists but is too small for encrypted passwords
if ($signup_password_column && $signup_password_column['Type'] != 'TEXT') {
    $alter_signup_password = mysqli_query($conn, "ALTER TABLE signup MODIFY Password TEXT NOT NULL");
    if ($alter_signup_password) {
        echo "<p>Updated signup table password column to TEXT</p>";
    }
}

// Do the same for emp_login table
$check_emp_password = mysqli_query($conn, "SHOW COLUMNS FROM emp_login LIKE 'Emp_Password'");
$emp_password_column = mysqli_fetch_assoc($check_emp_password);

if ($emp_password_column && $emp_password_column['Type'] != 'TEXT') {
    $alter_emp_password = mysqli_query($conn, "ALTER TABLE emp_login MODIFY Emp_Password TEXT NOT NULL");
    if ($alter_emp_password) {
        echo "<p>Updated emp_login table password column to TEXT</p>";
    }
}

echo "<p>Database update completed</p>";
?>

