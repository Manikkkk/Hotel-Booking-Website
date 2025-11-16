<?php
include '../config.php';

// Check if contact table exists, if not create it
$check_contact_table = mysqli_query($conn, "SHOW TABLES LIKE 'contact'");
if(mysqli_num_rows($check_contact_table) == 0) {
    // Create contact table
    $create_contact_table = "CREATE TABLE `contact` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `subject` varchar(200) NOT NULL,
        `message` text NOT NULL,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    mysqli_query($conn, $create_contact_table);
}

// Check if messages table exists for the chatbot messages
$check_messages_table = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
if(mysqli_num_rows($check_messages_table) == 0) {
    // Create messages table
    $create_messages_table = "CREATE TABLE `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `user_message` text NOT NULL,
        `bot_response` text NOT NULL,
        `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    mysqli_query($conn, $create_messages_table);
}

// Count total booked rooms
$roombookingsql = "SELECT * FROM roombook";
$roombookresult = mysqli_query($conn, $roombookingsql);
$roombookrow = mysqli_num_rows($roombookresult);

// Count total rooms
$roomsql = "SELECT * FROM room";
$roomresult = mysqli_query($conn, $roomsql);
$roomrow = mysqli_num_rows($roomresult);

// Count total staff
$staffsql = "SELECT * FROM staff";
$staffresult = mysqli_query($conn, $staffsql);
$staffrow = mysqli_num_rows($staffresult);

// Count total messages (combining both contact and chatbot messages)
$messagesql = "SELECT (SELECT COUNT(*) FROM contact) + (SELECT COUNT(*) FROM messages) as total_messages";
$messageresult = mysqli_query($conn, $messagesql);
$messagerow = mysqli_fetch_assoc($messageresult);
$messagecount = $messagerow['total_messages'] ? $messagerow['total_messages'] : 0;

// Calculate revenue
$revenuesql = "SELECT SUM(price) as total FROM roombook WHERE stat='Confirm'";
$revenueresult = mysqli_query($conn, $revenuesql);
$revenuerow = mysqli_fetch_assoc($revenueresult);
$totalRevenue = $revenuerow['total'] ? $revenuerow['total'] : 0;

// Get recent bookings
$recentBookingsSql = "SELECT * FROM roombook ORDER BY id DESC LIMIT 5";
$recentBookingsResult = mysqli_query($conn, $recentBookingsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="container-fluid px-4 py-3">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="fw-bold">Dashboard</h4>
                <p class="text-muted">Welcome to Juju Homestay Admin Panel</p>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="stat-icon bg-primary-subtle text-primary rounded-circle p-3">
                                    <i class="fas fa-bed fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-9 text-end">
                                <h5 class="card-title mb-1">Room Bookings</h5>
                                <h2 class="mb-0 fw-bold"><?php echo $roombookrow; ?> / <?php echo $roomrow; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-primary text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="roombook.php"><span>View Details</span></a>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="stat-icon bg-success-subtle text-success rounded-circle p-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-9 text-end">
                                <h5 class="card-title mb-1">Staff Members</h5>
                                <h2 class="mb-0 fw-bold"><?php echo $staffrow; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-success text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="staff.php"><span>View Details</span></a>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="stat-icon bg-info-subtle text-info rounded-circle p-3">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-9 text-end">
                                <h5 class="card-title mb-1">Messages</h5>
                                <h2 class="mb-0 fw-bold"><?php echo $messagecount; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-info text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="messages.php"><span>View Details</span></a>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="stat-icon bg-warning-subtle text-warning rounded-circle p-3">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                            <!-- <div class="col-9 text-end">
                                <h5 class="card-title mb-1">Revenue</h5>
                                <h2 class="mb-0 fw-bold">Rs. <?php echo number_format($totalRevenue); ?></h2>
                            </div> 
                        </div>
                    </div>
                    <div class="card-footer bg-warning text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>View Details</span>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        
        <!-- Charts Row -->
        <!-- <div class="row mb-4">
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Booking Statistics</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Room Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="roomDistributionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div> -->
        
        <!-- Recent Bookings Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Bookings</h5>
                        <a href="roombook.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Guest Name</th>
                                        <th scope="col">Room Type</th>
                                        <th scope="col">Check In</th>
                                        <th scope="col">Check Out</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(mysqli_num_rows($recentBookingsResult) > 0) {
                                        while($booking = mysqli_fetch_assoc($recentBookingsResult)) {
                                            $statusClass = '';
                                            if($booking['stat'] == 'Confirm') {
                                                $statusClass = 'bg-success';
                                            } else {
                                                $statusClass = 'bg-warning';
                                            }
                                            
                                            echo '<tr>
                                                <td>'.$booking['id'].'</td>
                                                <td>'.$booking['Name'].'</td>
                                                <td>'.$booking['RoomType'].'</td>
                                                <td>'.date('d M Y', strtotime($booking['cin'])).'</td>
                                                <td>'.date('d M Y', strtotime($booking['cout'])).'</td>
                                                <td><span class="badge '.$statusClass.'">'.$booking['stat'].'</span></td>
                                            </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center py-3">No recent bookings found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // // Sample data for charts
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Booking Statistics Chart
        //    // const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        //     //const bookingChart = new Chart(bookingCtx, {
        //        // type: 'line',
        //        // data: {
        //          //   labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        //          //   datasets: [{
        //          //       label: 'Bookings',
        //          //       data: [12, 19, 15, 25, 22, 30, 28, 32, 24, 18, 22, <?php echo $roombookrow; ?>],
        //          //       backgroundColor: 'rgba(52, 152, 219, 0.2)',
        //          //       borderColor: 'rgba(52, 152, 219, 1)',
        //          //       borderWidth: 2,
        //          //       tension: 0.3
        //          //   }]
        //        // },
        //        // options: {
        //        //     responsive: true,
        //        //     maintainAspectRatio: false,
        //        //     scales: {
        //        //         y: {
        //        //             beginAtZero: true
        //        //         }
        //       //      }
        //       //  }
        //   //  });
            
        //     // Room Distribution Chart
        //     const roomCtx = document.getElementById('roomDistributionChart').getContext('2d');
        //     const roomChart = new Chart(roomCtx, {
        //         type: 'doughnut',
        //         data: {
        //             labels: ['Superior Room', 'Deluxe Room', 'Guest House', 'Single Room'],
        //             datasets: [{
        //                 data: [30, 25, 20, 25],
        //                 backgroundColor: [
        //                     'rgba(52, 152, 219, 0.7)',
        //                     'rgba(46, 204, 113, 0.7)',
        //                     'rgba(155, 89, 182, 0.7)',
        //                     'rgba(241, 196, 15, 0.7)'
        //                 ],
        //                 borderColor: [
        //                     'rgba(52, 152, 219, 1)',
        //                     'rgba(46, 204, 113, 1)',
        //                     'rgba(155, 89, 182, 1)',
        //                     'rgba(241, 196, 15, 1)'
        //                 ],
        //                 borderWidth: 1
        //             }]
        //         },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             plugins: {
        //                 legend: {
        //                     position: 'bottom'
        //                 }
        //             }
        //         }
        //     });
        // });
    </script>
</body>
                    </div
