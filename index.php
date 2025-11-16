<?php
// Move all PHP processing to the top of the file
include 'config.php';
session_start();

// Add this at the top of your PHP section to track which form to display
$showSignup = false;
$usernameError = false;
$emailError = false;
$passwordError = false;
$confirmPasswordError = false;

// Add variables for login errors
$loginError = false;
$loginErrorMessage = "";

// Add this at the top of your PHP section
$signupSuccess = false;
$signupEmail = "";
$signupUsername = "";

// Check if there's a signup success message in the session
if (isset($_SESSION['signup_success']) && $_SESSION['signup_success'] === true) {
    $signupSuccess = true;
    $signupEmail = isset($_SESSION['signup_email']) ? $_SESSION['signup_email'] : "";
    $signupUsername = isset($_SESSION['signup_username']) ? $_SESSION['signup_username'] : "";
    
    // Clear the session variables
    unset($_SESSION['signup_success']);
    unset($_SESSION['signup_email']);
    unset($_SESSION['signup_username']);
}

// Process signup form
if (isset($_POST['user_signup_submit'])) {
    $showSignup = true; // Stay on signup form if there are errors
    $Username = mysqli_real_escape_string($conn, $_POST['Username']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $Password = $_POST['Password'];
    $CPassword = $_POST['CPassword'];

    // Validate email format
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $emailError = true;
    }
    // Validate username (at least 3 characters)
    else if (strlen($Username) < 3) {
        $usernameError = true;
    }
    // Validate password strength
    else if (
        empty(trim($Password)) ||
        strlen($Password) < 8 ||
        !preg_match('/[A-Z]/', $Password) ||
        !preg_match('/[a-z]/', $Password) ||
        !preg_match('/[0-9]/', $Password) ||
        !preg_match('/[\W]/', $Password)
    ) {
        $passwordError = true;
    }
    else if ($Password != $CPassword) {
        $confirmPasswordError = true;
    }
    else {
        $sql = "SELECT * FROM signup WHERE Email = '$Email'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {
            $emailError = true;
        } else {
            // Encrypt password before storing
            // $encryptedPassword = encrypt_message($Password);
            
            $sql = "INSERT INTO signup (Username,Email,Password) VALUES ('$Username', '$Email', '$Password')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Set success message in session
                $_SESSION['signup_success'] = true;
                $_SESSION['signup_email'] = $Email;
                $_SESSION['signup_username'] = $Username;
                
                // Redirect to login page
                ob_start();
                header("Location: index.php");
                ob_end_flush();
                exit();
            }
        }
    }
}

// Process user login form
if (isset($_POST['user_login_submit'])) {
    $Username = mysqli_real_escape_string($conn, $_POST['Username']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $Password = $_POST['Password']; // Don't escape password before verification

    // First verify the email exists
    $check_email = "SELECT * FROM signup WHERE Email = '$Email'";
    $email_result = mysqli_query($conn, $check_email);
    
    if ($email_result->num_rows > 0) {
        // Email exists, now check username and password
        $user = mysqli_fetch_assoc($email_result);
        
        if ($user['Username'] === $Username && $user['Password'] === $Password) {
            // Login successful
            $_SESSION['usermail'] = $Email;
            $_SESSION['username'] = $Username;
            $_SESSION['userid'] = $user['UserID'];
            
            // Use output buffering to prevent "headers already sent" error
            ob_start();
            header("Location: home.php");
            ob_end_flush();
            exit();
        } else {
            // Username or password incorrect
            $loginError = true;
            if ($user['Username'] !== $Username) {
                $loginErrorMessage = "Incorrect username. Please try again.";
            } else {
                $loginErrorMessage = "Incorrect password. Please try again.";
            }
        }
    } else {
        // Email doesn't exist
        $loginError = true;
        $loginErrorMessage = "Email not found. Please check your email or sign up.";
    }
}

// Process employee login form
if (isset($_POST['Emp_login_submit'])) {
    $Email = $_POST['Emp_Email'];
    $Password = $_POST['Emp_Password'];
    
    $sql = "SELECT * FROM emp_login WHERE Emp_Email = '$Email'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $emp = mysqli_fetch_assoc($result);
        if ($emp['Emp_Password'] === $Password) {
            $_SESSION['usermail'] = $Email;
            $Email = "";
            $Password = "";
            
            // Use output buffering to prevent "headers already sent" error
            ob_start();
            header("Location: admin/admin.php");
            ob_end_flush();
            exit();
        } else {
            // Password incorrect
            $loginError = true;
            $loginErrorMessage = "Incorrect staff password. Please try again.";
        }
    } else {
        // Email doesn't exist
        $loginError = true;
        $loginErrorMessage = "Staff email not found. Please check your email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juju Homestay - Welcome</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Merienda:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #475569;
            --light: #f1f5f9;
            --dark: #0f172a;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            min-height: 100vh;
            overflow-x: hidden;
            background-color: #e2e8f0; /* Slightly darker background */
        }
        
        .brand-font {
            font-family: 'Merienda', cursive;
        }
        
        .auth-container {
            display: flex;
            min-height: 100vh;
        }
        
        .carousel-side {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: none;
        }
        
        .carousel-image {
            position: absolute;
            top: 0;
            left: 0; 
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 1s ease;
        }
        
        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.8)); /* Darker overlay */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 3rem;
            color: white;
        }
        
        .carousel-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .carousel-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 80%;
        }
        
        .auth-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: #e2e8f0; /* Light gray background */
            background-image: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); /* Subtle gradient */
        }
        
        .auth-box {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12); /* Stronger shadow */
            background-color: white;
            border: 1px solid #e5e7eb; /* Subtle border */
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo h1 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .auth-logo p {
            color: var(--secondary);
            font-size: 1rem;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 0.75rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--secondary);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .auth-tab.active {
            color: var(--primary);
        }
        
        .auth-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary);
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1; /* Darker border */
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8fafc; /* Very light gray background */
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
            background-color: white;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
            background: none;
            border: none;
            padding: 0;
        }
        
        .auth-btn {
            width: 100%;
            padding: 0.85rem; /* Slightly taller button */
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle button shadow */
        }
        
        .auth-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--secondary);
        }
        
        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }
        
        .auth-link:hover {
            text-decoration: underline;
        }
        
        .error-field {
            border-color: var(--danger) !important;
            background-color: rgba(239, 68, 68, 0.05) !important;
        }
        
        .password-requirements {
            font-size: 0.8rem;
            color: var(--secondary);
            margin-top: 0.5rem;
            background-color: #f1f5f9;
            padding: 0.75rem;
            border-radius: 6px;
        }
        
        .password-requirements ul {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }
        
        .password-requirements li {
            margin-bottom: 0.25rem;
        }
        
        .user-type-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .user-type-tab {
            flex: 1;
            text-align: center;
            padding: 0.75rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--secondary);
            transition: all 0.3s ease;
            background-color: #f1f5f9;
        }
        
        .user-type-tab.active {
            color: white;
            background-color: var(--primary);
        }
        
        /* Responsive styles */
        @media (min-width: 992px) {
            .carousel-side {
                display: block;
            }
            
            .auth-box {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            }
        }
        
        /* Animation for carousel */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 1s ease forwards;
        }
        
        /* Mobile improvements */
        @media (max-width: 768px) {
            .auth-side {
                padding: 1rem;
            }
            
            .auth-box {
                padding: 1.5rem;
                border-radius: 10px;
            }
            
            /* Add a subtle pattern background for mobile */
            body {
                background-image: linear-gradient(135deg, #e2e8f0 25%, #cbd5e1 25%, #cbd5e1 50%, #e2e8f0 50%, #e2e8f0 75%, #cbd5e1 75%, #cbd5e1 100%);
                background-size: 20px 20px;
            }
        }
        
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .alert i {
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Carousel Side -->
        <div class="carousel-side">
            <div id="carouselImages">
                <img src="./images/carousel/1.jpg" class="carousel-image fade-in" style="opacity: 1;">
                <img src="./images/carousel/2.jpg" class="carousel-image" style="opacity: 0;">
                <img src="./images/carousel/back.jpg" class="carousel-image" style="opacity: 0;">
                <img src="./images/carousel/market.jpg" class="carousel-image" style="opacity: 0;">
            </div>
            <div class="carousel-overlay">
                <div class="carousel-content">
                    <h1 class="brand-font">Welcome to Juju Homestay</h1>
                    <p>Experience comfort and luxury in our carefully designed accommodations. Your perfect getaway awaits.</p>
                </div>
            </div>
        </div>
        
        <!-- Auth Side -->
        <div class="auth-side">
            <div class="auth-box">
                <div class="auth-logo">
                    <h1 class="brand-font">Juju Homestay</h1>
                    <p>Your home away from home</p>
                </div>
                
                <!-- Auth Tabs -->
                <div class="auth-tabs">
                    <div class="auth-tab active" id="login-tab">Login</div>
                    <div class="auth-tab" id="signup-tab">Sign Up</div>
                </div>
                
                <!-- Login Form -->
                <div class="auth-form active" id="login-form">
                    <div class="user-type-tabs">
                        <div class="user-type-tab active" id="user-login-tab">User</div>
                        <div class="user-type-tab" id="staff-login-tab">Staff</div>
                    </div>
                    
                    <!-- User Login Form -->
                    <form class="user-login-form active" id="user-login-form" action="" method="POST">
                        <?php if($signupSuccess): ?>
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            Account created successfully! Please login with your credentials.
                        </div>
                        <?php endif; ?>
                        
                        <?php if($loginError && !isset($_POST['Emp_login_submit'])): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $loginErrorMessage; ?>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="form-label" for="login-username">Username</label>
                            <input type="text" class="form-control" id="login-username" name="Username" 
                                   value="<?php echo $signupSuccess ? htmlspecialchars($signupUsername) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="login-email">Email</label>
                            <input type="email" class="form-control" id="login-email" name="Email" 
                                   value="<?php echo $signupSuccess ? htmlspecialchars($signupEmail) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="login-password">Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="login-password" name="Password" required>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('login-password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="auth-btn" name="user_login_submit">Login</button>
                        <div class="auth-footer">
                            <p id="login-footer">Don't have an account? <a class="auth-link" id="show-signup">Sign up</a></p>
                        </div>
                    </form>
                    
                    <!-- Staff Login Form -->
                    <form class="user-login-form" id="staff-login-form" action="" method="POST" style="display: none;">
                        <?php if($loginError && isset($_POST['Emp_login_submit'])): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $loginErrorMessage; ?>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="form-label" for="staff-email">Email</label>
                            <input type="email" class="form-control" id="staff-email" name="Emp_Email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="staff-password">Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="staff-password" name="Emp_Password" required>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('staff-password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="auth-btn" name="Emp_login_submit">Login as Staff</button>
                    </form>
                </div>
                
                <!-- Signup Form -->
                <div class="auth-form" id="signup-form" style="display: none;">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="signup-username">Username</label>
                            <input type="text" class="form-control <?php echo $usernameError ? 'error-field' : ''; ?>" id="signup-username" name="Username" required>
                            <!-- <small class="text-muted">Must be at least 3 characters</small> -->
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="signup-email">Email</label>
                            <input type="email" class="form-control <?php echo $emailError ? 'error-field' : ''; ?>" id="signup-email" name="Email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="signup-password">Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control <?php echo $passwordError ? 'error-field' : ''; ?>" id="signup-password" name="Password" required>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('signup-password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <small>Password must contain:</small>
                                <ul>
                                    <li>At least 8 characters</li>
                                    <li>At least one uppercase letter (A-Z)</li>
                                    <li>At least one lowercase letter (a-z)</li>
                                    <li>At least one number (0-9)</li>
                                    <li>At least one special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="signup-confirm-password">Confirm Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control <?php echo $confirmPasswordError ? 'error-field' : ''; ?>" id="signup-confirm-password" name="CPassword" required>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('signup-confirm-password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="auth-btn" name="user_signup_submit">Create Account</button>
                    </form>
                </div>
                
                <div class="auth-footer">
                    
                    <p id="signup-footer" style="display: none;">Already have an account? <a class="auth-link" id="show-login">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle password visibility
        function togglePasswordVisibility(inputId, button) {
            const passwordInput = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
        
        // Tab switching
        document.addEventListener('DOMContentLoaded', function() {
            // Auth tabs (Login/Signup)
            const loginTab = document.getElementById('login-tab');
            const signupTab = document.getElementById('signup-tab');
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');
            const loginFooter = document.getElementById('login-footer');
            const signupFooter = document.getElementById('signup-footer');
            
            function showLogin() {
                loginTab.classList.add('active');
                signupTab.classList.remove('active');
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
                loginFooter.style.display = 'block';
                signupFooter.style.display = 'none';
            }
            
            function showSignup() {
                signupTab.classList.add('active');
                loginTab.classList.remove('active');
                signupForm.style.display = 'block';
                loginForm.style.display = 'none';
                signupFooter.style.display = 'block';
                loginFooter.style.display = 'none';
            }
            
            loginTab.addEventListener('click', showLogin);
            signupTab.addEventListener('click', showSignup);
            document.getElementById('show-signup').addEventListener('click', showSignup);
            document.getElementById('show-login').addEventListener('click', showLogin);
            
            // User type tabs (User/Staff)
            const userLoginTab = document.getElementById('user-login-tab');
            const staffLoginTab = document.getElementById('staff-login-tab');
            const userLoginForm = document.getElementById('user-login-form');
            const staffLoginForm = document.getElementById('staff-login-form');
            
            userLoginTab.addEventListener('click', function() {
                userLoginTab.classList.add('active');
                staffLoginTab.classList.remove('active');
                userLoginForm.style.display = 'block';
                staffLoginForm.style.display = 'none';
            });
            
            staffLoginTab.addEventListener('click', function() {
                staffLoginTab.classList.add('active');
                userLoginTab.classList.remove('active');
                staffLoginForm.style.display = 'block';
                userLoginForm.style.display = 'none';
            });
            
            // Image carousel
            const images = document.querySelectorAll('.carousel-image');
            let currentImageIndex = 0;
            
            function cycleImages() {
                // Hide all images
                images.forEach(img => {
                    img.style.opacity = 0;
                });
                
                // Show next image
                currentImageIndex = (currentImageIndex + 1) % images.length;
                images[currentImageIndex].style.opacity = 1;
                
                // Schedule next cycle
                setTimeout(cycleImages, 5000);
            }
            
            // Start carousel
            if (images.length > 1) {
                setTimeout(cycleImages, 5000);
            }
            
            // Check if we need to show signup form (PHP variable)
            <?php if($showSignup && !$signupSuccess): ?>
            showSignup();
            <?php endif; ?>
            
            // If there was a successful signup, make sure login tab is active
            <?php if($signupSuccess): ?>
            showLogin();
            // Focus on the password field since username and email are already filled
            document.getElementById('login-password').focus();
            <?php endif; ?>
        });
    </script>
</body>
</html>
