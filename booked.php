<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['usermail']) || empty($_SESSION['usermail'])) {
    header("location: index.php");
    exit();
}

$usermail = $_SESSION['usermail'];

// Check if booking ID is provided
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Always fetch the latest booking status from database if ID is provided
if ($booking_id) {
    // Make sure the booking belongs to the logged-in user
    $sql = "SELECT * FROM roombook WHERE id = '$booking_id' AND Email = '$usermail'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Map database status to display status
        $status = 'Pending';
        if ($row['stat'] == 'Confirm') {
            $status = 'Confirmed';
        } elseif ($row['stat'] == 'Cancel') {
            $status = 'Cancelled';
        }
        
        // Update or create session data with latest info from database
        $_SESSION['booked'] = [
            'id' => $row['id'],
            'name' => $row['Name'],
            'email' => $row['Email'],
            'phone' => isset($row['Phone']) ? $row['Phone'] : '',
            'roomType' => $row['RoomType'],
            'bed' => $row['Bed'],
            'checkIn' => $row['cin'],
            'checkOut' => $row['cout'],
            'nights' => $row['nodays'],
            'status' => $status,
            'roomNumber' => isset($row['room_no']) ? $row['room_no'] : '',
        ];
    } elseif (isset($_SESSION['booked']) && isset($_SESSION['booked']['id']) && $_SESSION['booked']['id'] == $booking_id) {
        // Keep existing session data if database query fails but session exists
    } else {
        // Clear session if no matching booking found
        unset($_SESSION['booked']);
        $_SESSION['booking_error'] = "Booking not found or does not belong to your account.";
        header("location: home.php");
        exit();
    }
} else {
    // If no booking ID provided, check if user has any bookings
    $recent_booking_sql = "SELECT * FROM roombook WHERE Email = '$usermail' ORDER BY id DESC LIMIT 1";
    $recent_booking_result = mysqli_query($conn, $recent_booking_sql);
    
    if ($recent_booking_result && mysqli_num_rows($recent_booking_result) > 0) {
        $recent_booking = mysqli_fetch_assoc($recent_booking_result);
        $booking_id = $recent_booking['id'];
        
        // Redirect to the booking details page with the ID
        header("location: booked.php?id=$booking_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Juju Homestay</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-hover: #3a56d4;
            --primary-light: rgba(67, 97, 238, 0.1);
            --secondary: #2ec4b6;
            --dark: #1e293b;
            --light: #f8fafc;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-500: #64748b;
            --gray-800: #1e293b;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gray-100);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .h-font {
            font-family: 'Merienda', cursive;
        }
        
        .booking-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 2rem;
            margin-bottom: 2rem;
            border: none;
        }
        
        .booking-header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem;
            position: relative;
        }
        
        .booking-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, transparent 49%, var(--primary) 50%);
            background-size: 20px 20px;
        }
        
        .booking-body {
            padding: 2rem;
        }
        
        .booking-info {
            margin-bottom: 1.5rem;
        }
        
        .booking-info-item {
            display: flex;
            margin-bottom: 1rem;
            align-items: center;
        }
        
        .booking-info-icon {
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .booking-info-content {
            flex-grow: 1;
        }
        
        .booking-info-label {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }
        
        .booking-info-value {
            font-weight: 500;
            color: var(--dark);
        }
        
        .booking-dates {
            display: flex;
            background-color: var(--gray-100);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .booking-date {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
        }
        
        .booking-date:first-child {
            border-right: 1px dashed var(--gray-300);
        }
        
        .booking-date-label {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }
        
        .booking-date-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .booking-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        
        .booking-status.pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .booking-status.confirmed {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .booking-status.cancelled {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .btn-back {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-back:hover {
            background-color: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            color: white;
        }
        
        .btn-back i {
            margin-right: 0.5rem;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .navbar-brand {
            font-family: 'Merienda', cursive;
            font-weight: 700;
            color: var(--primary);
        }
        
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }
        
        .no-booking {
            text-align: center;
            padding: 3rem;
        }
        
        .no-booking i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }
        
        .no-booking h3 {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .no-booking p {
            color: var(--gray-500);
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .booking-dates {
                flex-direction: column;
            }
            
            .booking-date:first-child {
                border-right: none;
                border-bottom: 1px dashed var(--gray-300);
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
    <!-- Auto refresh script to keep status updated -->
    <script>
        // Refresh the page every 30 seconds to update booking status
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                Juju Homestay
            </a>
            <div class="ms-auto">
                <a href="home.php" class="btn btn-outline-primary btn-sm me-2">
                    <i class="fas fa-home"></i> Home
                </a>
                <!-- <a href="user_bookings.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list"></i> My Bookings
                </a> -->
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <?php
        if (isset($_SESSION['booked'])) {
            $b = $_SESSION['booked'];
            $status = isset($b['status']) ? htmlspecialchars($b['status']) : 'Pending';
            $statusClass = 'pending';
            
            if ($status == 'Confirmed') {
                $statusClass = 'confirmed';
            } else if ($status == 'Cancelled') {
                $statusClass = 'cancelled';
            }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="booking-card">
                    <div class="booking-header">
                        <h2 class="h-font text-center mb-0">Booking Confirmation</h2>
                    </div>
                    <div class="booking-body">
                        <div class="text-center mb-4">
                            <span class="booking-status <?php echo $statusClass; ?>">
                                <?php if ($status == 'Pending'): ?>
                                    <i class="fas fa-clock"></i>
                                <?php elseif ($status == 'Confirmed'): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i>
                                <?php endif; ?>
                                <?php echo $status; ?>
                            </span>
                            <div class="mt-2 small text-muted">Booking ID: #<?php echo $b['id']; ?></div>
                        </div>
                        
                        <div class="booking-dates">
                            <div class="booking-date">
                                <div class="booking-date-label">CHECK-IN</div>
                                <div class="booking-date-value"><?php echo isset($b['checkIn']) ? htmlspecialchars($b['checkIn']) : ''; ?></div>
                            </div>
                            <div class="booking-date">
                                <div class="booking-date-label">CHECK-OUT</div>
                                <div class="booking-date-value"><?php echo isset($b['checkOut']) ? htmlspecialchars($b['checkOut']) : ''; ?></div>
                            </div>
                        </div>
                        
                        <div class="booking-info">
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">GUEST NAME</div>
                                    <div class="booking-info-value"><?php echo isset($b['name']) ? htmlspecialchars($b['name']) : ''; ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">EMAIL</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['email']); ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">PHONE</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['phone']); ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-bed"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">ROOM TYPE</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['roomType']); ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-couch"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">BED TYPE</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['bed']); ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">NUMBER OF NIGHTS</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['nights']); ?></div>
                                </div>
                            </div>
                            
                            <?php if (isset($b['meal']) && !empty($b['meal'])): ?>
                            <div class="booking-info-item">
                                <div class="booking-info-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="booking-info-content">
                                    <div class="booking-info-label">MEAL PLAN</div>
                                    <div class="booking-info-value"><?php echo htmlspecialchars($b['meal']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <a href="home.php" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="booking-card">
                    <div class="no-booking">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Recent Booking</h3>
                        <p>You don't have any recent booking to display. Make a reservation to see your booking details here.</p>
                        <a href="home.php" class="btn-back">
                            <i class="fas fa-home"></i> Go to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Juju Homestay. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

