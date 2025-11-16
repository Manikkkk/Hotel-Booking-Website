<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['usermail'])) {
    header("Location: login.php");
    exit;
}

$usermail = $_SESSION['usermail'];

// Fetch messages sent by this user
$sqlq = "SELECT * FROM messages WHERE email = '$usermail' ORDER BY time DESC";
$result = mysqli_query($conn, $sqlq);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages </title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/modern-style.css">
    
    <style>
        body {
            background-color: var(--body-bg);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .message-card {
            border-radius: var(--radius-lg);
            background-color: white;
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .message-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .message-header {
            background-color: var(--primary-light);
            padding: 1.25rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .message-content {
            padding: 1.5rem;
        }
        
        .message-text {
            background-color: var(--gray-100);
            padding: 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            position: relative;
            border-left: 4px solid var(--primary);
        }
        
        .reply-section {
            background-color: var(--gray-100);
            padding: 1.25rem;
            border-radius: var(--radius);
            border-left: 4px solid var(--secondary);
        }
        
        .message-time {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-replied {
            background-color: var(--secondary-light);
            color: var(--secondary);
        }
        
        .status-pending {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            background-color: var(--gray-100);
            margin-right: 0.75rem;
        }
        
        .btn-back:hover {
            background-color: var(--gray-200);
            color: var(--gray-900);
        }
        
        .btn-back i {
            margin-right: 0.5rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container py-4">
        <div class="page-header">
            <div>
                <h2 class="fw-bold mb-1">My Messages</h2>
                <p class="text-muted mb-0">View your communication history with our team</p>
            </div>
            <div class="action-buttons">
                <a href="home.php" class="btn-back">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <!-- <a href="send_message.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Message
                </a> -->
            </div>
        </div>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="message-card">
                    <div class="message-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-semibold">Message </h5>
                            <div class="message-time">
                                <i class="far fa-clock me-1"></i>
                                <?= date('M d, Y - h:i A', strtotime($row['time'])) ?>
                            </div>
                        </div>
                        <?php if(!empty($row['bot_response'])): ?>
                            <span class="status-badge status-replied">
                                <i class="fas fa-check-circle me-1"></i>Replied
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock me-1"></i>Pending
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="message-content">
                        <div class="mb-3">
                            <label class="form-label text-muted mb-2">
                                <i class="fas fa-paper-plane me-2"></i>Your Message
                            </label>
                            <div class="message-text">
                                <?= nl2br(htmlspecialchars(decrypt_message($row['user_message']))) ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-muted mb-2">
                                <i class="fas fa-reply me-2"></i>Admin Response
                            </label>
                            <?php if(!empty($row['bot_response'])): ?>
                                <div class="reply-section">
                                    <?= nl2br(htmlspecialchars(decrypt_message($row['bot_response']))) ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light text-center py-3">
                                    <i class="fas fa-hourglass-half me-2 text-muted"></i>
                                    <em class="text-muted">Waiting for admin response...</em>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-comment-dots"></i>
                </div>
                <h3 class="fw-bold mb-3">No Messages Yet</h3>
                <p class="text-muted mb-4">You haven't sent any messages to our team yet.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="home.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                    <a href="send_message.php" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Your First Message
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
