<?php
session_start();
include '../config.php';

// Handle admin reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'], $_POST['message_id'])) {
    $message_id = intval($_POST['message_id']);
    $reply = mysqli_real_escape_string($conn, encrypt_message($_POST['reply']));

    $update = "UPDATE messages SET bot_response='$reply' WHERE id=$message_id";
    mysqli_query($conn, $update);
    
    // Add success message to be shown
    $success_msg = "Reply sent successfully!";
}

// Fetch messages
$sqlq = "SELECT * FROM messages ORDER BY time DESC";
$result = mysqli_query($conn, $sqlq);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Messages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Modern color palette */
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #eef2ff;
            --secondary: #2ec4b6;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
            --info: #118ab2;
            
            /* Neutral colors */
            --dark: #1b263b;
            --light: #f8f9fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* UI elements */
            --body-bg: #f5f7fb;
            --card-bg: var(--white);
            --card-border: var(--gray-200);
            --card-radius: 16px;
            --input-radius: 10px;
            --btn-radius: 10px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Transitions */
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: var(--gray-800);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem 1.5rem;
        }
        
        .card-title {
            color: var(--gray-800);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            color: var(--primary);
            margin-right: 0.75rem;
        }
        
        /* Message card styling */
        .message-card {
            border-radius: var(--card-radius);
            background-color: var(--white);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .message-card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background-color: var(--primary-light);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 1rem;
        }
        
        .user-email {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .message-time {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .message-content {
            padding: 1.5rem;
            background-color: var(--white);
        }
        
        .message-text {
            background-color: var(--gray-50);
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            color: var(--gray-800);
            position: relative;
        }
        
        .message-text::before {
            content: '"';
            position: absolute;
            top: 0.5rem;
            left: 0.75rem;
            font-size: 2rem;
            color: var(--gray-300);
            font-family: serif;
            line-height: 1;
        }
        
        .reply-section {
            background-color: var(--gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .reply-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: var(--gray-700);
            font-weight: 500;
        }
        
        .reply-header i {
            margin-right: 0.5rem;
            color: var(--primary);
        }
        
        .reply-text {
            padding: 1rem;
            background-color: var(--white);
            border-radius: 8px;
            color: var(--gray-800);
            border-left: 4px solid var(--primary);
        }
        
        .no-reply {
            color: var(--gray-500);
            font-style: italic;
        }
        
        .reply-form {
            margin-top: 1.5rem;
        }
        
        .reply-form textarea {
            border-radius: var(--input-radius);
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            background-color: var(--white);
            color: var(--gray-800);
            font-size: 0.95rem;
            transition: var(--transition);
            resize: none;
        }
        
        .reply-form textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        /* Button styling */
        .btn {
            border-radius: var(--btn-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: var(--white);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }
        
        .empty-title {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            font-size: 1.5rem;
        }
        
        .empty-description {
            color: var(--gray-500);
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Search input styling */
        .search-container {
            position: relative;
            width: 280px;
        }
        
        .search-input {
            padding-left: 2.75rem;
            height: 45px;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            z-index: 10;
        }
        
        /* Status badge */
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
            background-color: var(--success);
            color: white;
        }
        
        .status-pending {
            background-color: var(--warning);
            color: var(--gray-800);
        }
        
        /* Alert styling */
        .alert {
            border-radius: var(--card-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(6, 214, 160, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        /* Back to dashboard button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--gray-600);
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            color: var(--primary);
        }
        
        .back-btn i {
            margin-right: 0.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .container {
                padding: 0 1rem;
            }
            
            .search-container {
                width: 220px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
            }
            
            .message-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .message-time {
                margin-top: 0.5rem;
            }
            
            .search-container {
                width: 100%;
                margin-top: 1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .card-title {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-comments"></i>User Messages
                </h5>
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="messageSearch" class="form-control search-input" placeholder="Search messages...">
                </div>
            </div>
        </div>
        
        <?php if(isset($success_msg)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
        </div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="message-card">
                    <div class="message-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                            </div>
                            <div class="user-details">
                                <span class="user-name"><?php echo htmlspecialchars($row['username']); ?></span>
                                <span class="user-email"><?php echo htmlspecialchars($row['email']); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php if(!empty($row['bot_response'])): ?>
                                <span class="status-badge status-replied me-3">Replied</span>
                            <?php else: ?>
                                <span class="status-badge status-pending me-3">Pending</span>
                            <?php endif; ?>
                            <span class="message-time">
                                <i class="far fa-clock me-1"></i>
                                <?php echo date('M d, Y - h:i A', strtotime($row['time'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="message-content">
                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars(decrypt_message($row['user_message']))); ?>
                        </div>
                        
                        <?php if(!empty($row['bot_response'])): ?>
                            <div class="reply-section">
                                <div class="reply-header">
                                    <i class="fas fa-reply"></i> Your Reply
                                </div>
                                <div class="reply-text">
                                    <?php echo nl2br(htmlspecialchars(decrypt_message($row['bot_response']))); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="reply-form">
                            <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                            <div class="mb-3">
                                <textarea name="reply" class="form-control" rows="3" placeholder="Type your reply here..." required><?php echo !empty($row['bot_response']) ? decrypt_message($row['bot_response']) : ''; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                <?php echo !empty($row['bot_response']) ? 'Update Reply' : 'Send Reply'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-comment-dots"></i>
                </div>
                <h3 class="empty-title">No Messages Yet</h3>
                <p class="empty-description">When users send messages, they will appear here.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add search functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('messageSearch');
            
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const messageCards = document.querySelectorAll('.message-card');
                    
                    messageCards.forEach(card => {
                        const userName = card.querySelector('.user-name').textContent.toLowerCase();
                        const userEmail = card.querySelector('.user-email').textContent.toLowerCase();
                        const messageText = card.querySelector('.message-text').textContent.toLowerCase();
                        const replyText = card.querySelector('.reply-text') ? 
                                         card.querySelector('.reply-text').textContent.toLowerCase() : '';
                        
                        // Check if any of the content matches the search term
                        if (userName.includes(searchTerm) || 
                            userEmail.includes(searchTerm) || 
                            messageText.includes(searchTerm) || 
                            replyText.includes(searchTerm)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Show a message if no results found
                    const visibleCards = document.querySelectorAll('.message-card[style="display: \'\'"], .message-card:not([style])');
                    const noResultsMsg = document.getElementById('noResultsMessage');
                    
                    if (visibleCards.length === 0 && searchTerm !== '') {
                        if (!noResultsMsg) {
                            const noResults = document.createElement('div');
                            noResults.id = 'noResultsMessage';
                            noResults.className = 'alert alert-info text-center my-4';
                            noResults.innerHTML = '<i class="fas fa-search me-2"></i>No messages match your search.';
                            
                            const container = document.querySelector('.container');
                            const lastCard = document.querySelector('.card.mb-4');
                            container.insertBefore(noResults, lastCard.nextSibling);
                        }
                    } else if (noResultsMsg) {
                        noResultsMsg.remove();
                    }
                });
            }
        });
    </script>
</body>
</html>

