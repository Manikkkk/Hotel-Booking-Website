<?php

include '../config.php';
session_start();

// page redirect
$usermail="";
$usermail=$_SESSION['usermail'];
if($usermail == true){

}else{
  header("location: http://localhost/hotelmanage_system/index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/admin.css">
    <!-- loading bar -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="../css/flash.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Admin Dashboard</title>
</head>

<body>
    <!-- mobile view warning -->
    <div id="mobileview" class="d-md-none">
        <div class="container text-center py-5">
            <i class="fas fa-desktop fa-4x mb-3 text-primary"></i>
            <h4>Admin panel is optimized for desktop view</h4>
            <p>Please use a larger screen for the best experience</p>
        </div>
    </div>
  
    <!-- main layout for desktop -->
    <div class="d-none d-md-flex">
        <!-- sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <i class="fas fa-hotel"></i>
                    <span class="brand-name">Juju Homestay</span>
                </div>
                <button class="btn toggle-btn d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-user">
                <img src="admin.avif" alt="Admin" class="user-avatar">
                <div class="user-info">
                    <h6 class="user-name">Admin User</h6>
                    <span class="user-role">Administrator</span>
                </div>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link active pagebtn" data-index="0">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="1">
                        <i class="fas fa-calendar-check"></i>
                        <span>Room Booking</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="2">
                        <i class="fas fa-bed"></i>
                        <span>Rooms</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="3">
                        <i class="fas fa-users"></i>
                        <span>Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="4">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="5">
                        <i class="fas fa-chart-line"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link pagebtn" data-index="6">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li> -->
            </ul>
            
            <div class="sidebar-footer">
                <a href="../logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- main content -->
        <div class="main-content">
            <!-- top navbar -->
            <nav class="top-navbar">
                <div class="container-fluid">
                    <!-- <button class="btn toggle-sidebar-btn" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button> -->
                    
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                    
                    <div class="navbar-right">
                    
                        <!-- <div class="nav-item">
                            <a href="messages.php" class="nav-link">
                                <i class="fas fa-envelope"></i>
                                <span class="badge">7</span>
                            </a>
                        </div> -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                                <img src="admin.avif" alt="Admin" class="user-avatar-sm">
                                <span class="d-none d-md-inline-block">Admin</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li> -->
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- content frames -->
            <div class="content-container">
                <iframe class="content-frame frame1 active" src="./dashboard.php" frameborder="0"></iframe>
                <iframe class="content-frame frame2" src="./roombook.php" frameborder="0"></iframe>
                <iframe class="content-frame frame3" src="./room.php" frameborder="0"></iframe>
                <iframe class="content-frame frame4" src="./staff.php" frameborder="0"></iframe>
                <iframe class="content-frame frame5" src="./messages.php" frameborder="0"></iframe>
                <!-- <iframe class="content-frame frame6" src="./reports.php" frameborder="0"></iframe>
                <iframe class="content-frame frame7" src="./settings.php" frameborder="0"></iframe> -->
            </div>
        </div>
    </div>

    <script src="./javascript/script.js"></script>
</body>
</html>
