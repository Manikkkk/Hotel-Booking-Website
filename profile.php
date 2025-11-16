<?php
include 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['usermail']) || empty($_SESSION['usermail'])) {
    header("location: index.php");
    exit();
}

$usermail = $_SESSION['usermail'];

// Fetch user data
$user_sql = "SELECT * FROM signup WHERE Email = '$usermail'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Handle profile update
$update_success = false;
$update_error = false;

if (isset($_POST['update_profile'])) {
    // Remove username from the update fields
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Update user data without changing username
    $update_sql = "UPDATE signup SET 
                    Phone = '$phone',
                    Address = '$address'
                    WHERE Email = '$usermail'";
    
    if (mysqli_query($conn, $update_sql)) {
        $update_success = true;
        
        // Refresh user data
        $user_result = mysqli_query($conn, $user_sql);
        $user = mysqli_fetch_assoc($user_result);
    } else {
        $update_error = true;
    }
}

// Handle password change
$password_success = false;
$password_error = false;
$password_error_msg = "";

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check_password = "SELECT Password FROM signup WHERE Email = '$usermail'";
    $password_result = mysqli_query($conn, $check_password);
    $password_row = mysqli_fetch_assoc($password_result);
    
    // Decrypt stored password for comparison
    $decrypted_stored_password = decrypt_message($password_row['Password']);
    
    if ($decrypted_stored_password != $current_password) {
        $password_error = true;
        $password_error_msg = "Current password is incorrect";
    } 
    // Validate password strength
    else if (
        empty(trim($new_password)) ||
        strlen($new_password) < 8 ||
        !preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/[0-9]/', $new_password) ||
        !preg_match('/[\W]/', $new_password)
    ) {
        $password_error = true;
        $password_error_msg = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character";
    }
    // Check if passwords match
    else if ($new_password != $confirm_password) {
        $password_error = true;
        $password_error_msg = "New passwords do not match";
    } 
    else {
        // Encrypt new password before updating
        // $encrypted_new_password = encrypt_message($new_password);
        
        // Update password
        $update_password_sql = "UPDATE signup SET Password = '$new_password' WHERE Email = '$usermail'";
        
        if (mysqli_query($conn, $update_password_sql)) {
            $password_success = true;
        } else {
            $password_error = true;
            $password_error_msg = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Juju Homestay</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        /* Simplified color scheme and styling */
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --border-radius: 6px;
            --box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        /* Simplified header with centered content */
        .profile-header {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
            text-align: center;
            border: 1px solid var(--border-color);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 35px;
            margin: 0 auto 15px;
        }
        
        .profile-header h2 {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1.8rem;
            color: var(--text-dark);
        }
        
        .profile-header p {
            color: var(--text-muted);
            margin-bottom: 15px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }
        
        .stat-item {
            background-color: var(--light-bg);
            padding: 5px 12px;
            border-radius: 15px;
            margin: 0 5px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        /* Simplified cards */
        .profile-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }
        
        .profile-card-header {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-weight: 500;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .profile-card-header i {
            margin-right: 10px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Form elements */
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 10px 12px;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            background-color: var(--card-bg);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        /* Alerts */
        .alert {
            border-radius: var(--border-radius);
            padding: 12px 15px;
            margin-bottom: 20px;
            border: none;
        }
        
        /* Password field styling */
        .password-field-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
            z-index: 10;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
        }
        
        .password-field-wrapper .form-control {
            padding-right: 35px;
        }
        
        /* List group styling */
        .list-group-item {
            padding: 12px 15px;
            border-color: var(--border-color);
        }
        
        .list-group-item:hover {
            background-color: var(--light-bg);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand me-5 fw-bold fs-3" href="home.php">
                <span class="text-primary">Juju</span> Homestay
            </a>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link me-2" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-2" href="booked.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-2" href="user_messages.php">Messages</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <!-- User Profile Dropdown -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-dark dropdown-toggle shadow-none" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : $_SESSION['usermail']; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userProfileDropdown">
                            <li><a class="dropdown-item active" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="booked.php"><i class="fas fa-list me-2"></i>My Bookings</a></li>
                            <li><a class="dropdown-item" href="user_messages.php"><i class="fas fa-envelope me-2"></i>Messages</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                    <a href="logout.php" class="btn btn-outline-dark shadow-none">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="profile-container">
            <?php if($update_success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Profile updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if($update_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> Failed to update profile. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if($password_success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Password changed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if($password_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $password_error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2><?php echo $user['Username']; ?></h2>
                <p><?php echo $user['Email']; ?></p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <i class="fas fa-calendar-check me-1"></i> Member since <?php echo date('M Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?>
                    </div>
                    <?php if(!empty($user['Phone'])): ?>
                    <div class="stat-item">
                        <i class="fas fa-phone me-1"></i> <?php echo $user['Phone']; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="profile-card mb-4">
                        <div class="profile-card-header">
                            <i class="fas fa-user-edit"></i>
                            <h4 class="mb-0">Edit Profile</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo $user['Username']; ?>" readonly>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="<?php echo $user['Email']; ?>" readonly>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                                <div class="mb-4">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo isset($user['Phone']) ? $user['Phone'] : ''; ?>">
                                </div>
                                <div class="mb-4">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo isset($user['Address']) ? $user['Address'] : ''; ?></textarea>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="profile-card mb-4">
                        <div class="profile-card-header">
                            <i class="fas fa-key"></i>
                            <h4 class="mb-0">Change Password</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-4 password-field-wrapper">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('current_password', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                    <span>
                                        <p>Required to change password</p>
                                    </span>
                                </div>
                                <div class="mb-4 password-field-wrapper">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                    <!-- <div class="password-requirements">
                                        <p class="mb-1">Password must contain:</p>
                                        <ul>
                                            <li>At least 8 characters</li>
                                            <li>At least one uppercase letter (A-Z)</li>
                                            <li>At least one lowercase letter (a-z)</li>
                                            <li>At least one number (0-9)</li>
                                            <li>At least one special character (!@#$%^&*)</li>
                                        </ul>
                                    </div> -->
                                    <span>
                                        <p>Enter New password</p>
                                    </span>
                                </div>
                                <div class="mb-4 password-field-wrapper">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                    <span>
                                        <p>Enter confirm password</p>
                                    </span>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Security card can be removed since we now have a dedicated password change section -->
                    
                    <!-- Quick Links card remains the same -->
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <i class="fas fa-bookmark"></i>
                            <h4 class="mb-0">Quick Links</h4>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="booked.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-list me-2"></i> My Bookings
                                </a>
                                <a href="user_messages.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-envelope me-2"></i> Messages
                                </a>
                                <a href="home.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-home me-2"></i> Back to Home
                                </a>
                                <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-white p-3 mt-5">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">© 2023 Juju Homestay. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Designed with <i class="fa-solid fa-heart text-danger"></i> by Juju Team</p>
            </div>
        </div>
    </div>
    
    <!-- Add JavaScript for password visibility toggle -->
    <script>
        function togglePasswordVisibility(inputId, button) {
            const passwordInput = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (passwordInput.type === "password") {
                // If password is currently hidden, make it visible
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                // If password is currently visible, hide it
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
            
            // Prevent form submission when clicking the button
            return false;
        }
    </script>
</body>
</html>






