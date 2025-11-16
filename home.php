<?php

include 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['usermail']) || empty($_SESSION['usermail'])) {
    header("location: index.php");
    exit();
}

$usermail = $_SESSION['usermail'];

// Fetch user's bookings
$bookings_sql = "SELECT * FROM roombook WHERE Email = '$usermail' ORDER BY id DESC";
$bookings_result = mysqli_query($conn, $bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juju Homestay</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand me-5 fw-bold fs-3" href="index.php">
                <span class="text-primary">Juju</span> Homestay
            </a>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active me-2" href="#hero-section">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-2" href="#rooms-section">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-2" href="#facilities-section">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-2" href="#contact-section">Contact</a>
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
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
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

    <!-- Add this right after the navbar or at the top of the page content -->
    <?php if(isset($_SESSION['booking_error'])): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <div id="hero-section" class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-card">
                        <div class="hero-image">
                            <img src="./images/carousel/1.jpg" class="w-100 d-block" />
                        </div>
                        <div class="hero-content text-center">
                            <h1 class="text-white display-4 fw-bold">Welcome to Juju Homestay</h1>
                            <h5 class="text-white">Experience comfort like never before</h5>
                            <!-- <button class="btn btn-light rounded-pill px-4 py-2 mt-3" onclick="scrollToRooms()">
                                Book Now
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Section -->
    <div id="rooms-section" class="container py-5">
        <h2 class="text-center fw-bold h-font mb-4">Our Rooms</h2>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card search-filter-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3 mb-md-0">
                                <input type="text" id="roomSearch" class="form-control" placeholder="Search rooms...">
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <select id="roomTypeFilter" class="form-select">
                                    <option value="">All Room Types</option>
                                    <?php
                                    $room_types_sql = "SELECT DISTINCT type FROM room ORDER BY type ASC";
                                    $room_types_result = mysqli_query($conn, $room_types_sql);
                                    while($room_type = mysqli_fetch_assoc($room_types_result)) {
                                        echo '<option value="'.$room_type['type'].'">'.$room_type['type'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="priceFilter" class="form-select">
                                    <option value="">Sort by Price</option>
                                    <option value="1">Low to High</option>
                                    <option value="2">High to Low</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="roomContainer">
            <?php
            // Fetch rooms from database
            $room_sql = "SELECT r.id, r.type, r.bed, r.price, r.room_no, r.image FROM room r ORDER BY r.price ASC";
            $room_result = mysqli_query($conn, $room_sql);

            if(mysqli_num_rows($room_result) > 0) {
                while($room = mysqli_fetch_assoc($room_result)) {
                    // Use bed column directly
                    $bedType = isset($room['bed']) ? $room['bed'] : 'Single'; // Default fallback
                    
                    // Get room features based on type
                    $features = [];
                    
                    if($room['type'] == 'Superior Room') {
                        $features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access', 'Pool Access'];
                    } else if($room['type'] == 'Deluxe Room') {
                        $features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access'];
                    } else if($room['type'] == 'Guest House') {
                        $features = ['Wi-Fi', 'Room Service', 'Spa Access'];
                    } else {
                        $features = ['Wi-Fi', 'Room Service'];
                    }
                    
                    // Output room card
                    echo '<div class="col-lg-4 col-md-6 mb-4 room-card" data-type="'.$room['type'].'" data-price="'.$room['price'].'" data-bed="'.$bedType.'">
                            <div class="card border-0 shadow">
                                <div class="room-image-container">';
                    
                    // Show room image if available, otherwise show placeholder
                    if(!empty($room['image'])) {
                        echo '<img src="'.$room['image'].'" class="card-img-top">';
                    } else {
                        echo '<img src="images/rooms/placeholder.jpg" class="card-img-top">';
                    }
                    
                    echo '</div>
                            <div class="card-body">
                                <h5 class="card-title">'.$room['type'].'</h5>
                                <h6 class="mb-3">Rs. '.number_format($room['price']).' per night</h6>
                                <div class="features mb-3">
                                    <h6 class="mb-1">Features</h6>
                                    <div class="features-list">';
                    
                    // Display features with icons
                    foreach($features as $feature) {
                        $icon = '';
                        if($feature == 'Wi-Fi') $icon = 'fa-wifi';
                        else if($feature == 'Room Service') $icon = 'fa-bell-concierge';
                        else if($feature == 'Spa Access') $icon = 'fa-spa';
                        else if($feature == 'Gym Access') $icon = 'fa-dumbbell';
                        else if($feature == 'Pool Access') $icon = 'fa-person-swimming';
                        
                        echo '<span class="badge rounded-pill bg-light text-dark mb-1 me-1">
                                <i class="fa-solid '.$icon.' me-1"></i> '.$feature.'
                              </span>';
                    }
                    
                    echo '</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <i class="fa-solid fa-bed me-1"></i> '.$bedType.'
                                    </span>
                                    <button class="btn btn-sm btn-outline-dark shadow-none book-now-btn" 
                                            onclick="bookRoom(\''.$room['id'].'\', \''.$room['type'].'\', \''.$bedType.'\')">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12 text-center py-5">
                        <h5 class="fw-bold">No rooms available at the moment</h5>
                      </div>';
            }
            ?>
        </div>
    </div>

    <!-- Facilities Section -->
    <div id="facilities-section" class="container-fluid py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Our Facilities</h2>
            <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="facility-card">
                        <div class="facility-image">
                            <img src="images/facilities/helipad.jpg" alt="Helipad">
                        </div>
                        <div class="facility-overlay">
                            <div class="facility-content">
                                <h3>Helipad</h3>
                                <p>Arrive in style with our private helipad service, offering convenient access for VIP guests.</p>
                                <!-- <a href="#" class="btn btn-outline-light btn-sm">Learn More</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="facility-card">
                        <div class="facility-image">
                            <img src="images/facilities/pool.jpeg" alt="Swimming Pool">
                        </div>
                        <div class="facility-overlay">
                            <div class="facility-content">
                                <h3>Swimming Pool</h3>
                                <p>Relax and unwind in our pristine swimming pool with panoramic mountain views.</p>
                                <!-- <a href="#" class="btn btn-outline-light btn-sm">Learn More</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="facility-card">
                        <div class="facility-image">
                            <img src="images/facilities/spa.jpg" alt="Spa">
                        </div>
                        <div class="facility-overlay">
                            <div class="facility-content">
                                <h3>Spa</h3>
                                <p>Indulge in rejuvenating treatments at our luxury spa with skilled therapists.</p>
                                <!-- <a href="#" class="btn btn-outline-light btn-sm">Learn More</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="facility-card">
                        <div class="facility-image">
                            <img src="images/facilities/food.jpg" alt="Restaurant">
                        </div>
                        <div class="facility-overlay">
                            <div class="facility-content">
                                <h3>Restaurant</h3>
                                <p>Savor delicious local and international cuisine prepared by our expert chefs.</p>
                                <!-- <a href="#" class="btn btn-outline-light btn-sm">Learn More</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="facility-card">
                        <div class="facility-image">
                            <img src="images/facilities/gym.jpg" alt="Fitness Center">
                        </div>
                        <div class="facility-overlay">
                            <div class="facility-content">
                                <h3>Fitness Center</h3>
                                <p>Stay fit during your stay with our modern gym equipment and personal trainers.</p>
                                <!-- <a href="#" class="btn btn-outline-light btn-sm">Learn More</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div id="contact-section" class="container-fluid bg-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6 mb-4">
                    <h3 class="h-font fw-bold fs-3 mb-3">Juju Homestay</h3>
                    <p>
                        Experience the perfect blend of comfort and luxury at Juju Homestay. 
                        Our accommodations offer a peaceful retreat with all modern amenities.
                    </p>
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="m-0 me-3 fs-5">Social Links:</h5>
                        <a href="#" class="d-inline-block text-dark fs-5 me-2">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" class="d-inline-block text-dark fs-5 me-2">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="d-inline-block text-dark fs-5">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a><br>
                    <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a><br>
                    <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a><br>
                    <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Contact</a><br>
                    <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">About</a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">Contact</h5>
                    <a href="tel: +9779876543210" class="d-inline-block mb-2 text-decoration-none text-dark">
                        <i class="fa-solid fa-phone me-1"></i> +977 9876543210
                    </a><br>
                    <a href="mailto: info@jujuhomestay.com" class="d-inline-block mb-2 text-decoration-none text-dark">
                        <i class="fa-solid fa-envelope me-1"></i> info@jujuhomestay.com
                    </a><br>
                    <a href="#" class="d-inline-block text-decoration-none text-dark">
                        <i class="fa-solid fa-location-dot me-1"></i> Kathmandu, Nepal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-white p-3">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">© 2025 Juju Homestay. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Designed by Juju Team</p>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="bookingModalLabel">
                        <i class="fas fa-calendar-check me-2"></i>Book a Room
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bookingForm" method="POST" action="process_booking.php">
                    <div class="modal-body">
                        <!-- Room Preview Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="room-preview-image" id="modalRoomImage">
                                    <!-- Room image will be set by JavaScript -->
                                    <img src="images/rooms/placeholder.jpg" class="img-fluid rounded" alt="Room Preview">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="room-preview-details">
                                    <h4 id="modalRoomTypeHeader" class="mb-2">Room Type</h4>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-dark me-2">
                                            <i class="fas fa-bed me-1"></i>
                                            <span id="modalBedTypeDisplay">Bed Type</span>
                                        </span>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-tag me-1"></i>
                                            <span id="modalRoomPriceDisplay">Price</span>/night
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-door-closed me-1"></i>Room #<span id="modalRoomNumberDisplay">-</span>
                                        </span>
                                    </div>
                                    <div id="modalRoomFeatures" class="mb-3">
                                        <!-- Features will be populated by JavaScript -->
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        You're about to book this room. Please fill in the details below to complete your reservation.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <!-- Booking Form -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                                <input type="text" name="Name" class="form-control" required
                                 pattern="[A-Za-z\s]+" 
       oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" name="Email" class="form-control" value="<?php echo isset($_SESSION['usermail']) ? $_SESSION['usermail'] : ''; ?>" readonly>
                                <small class="text-muted">Email is automatically filled from your account</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone me-2"></i>Phone Number
                                </label>
                                <input type="text" name="Phone" class="form-control" required
                                 pattern="\d+" maxlength="10"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-home me-2"></i>Room Type
                                </label>
                                <input type="text" name="RoomType" id="modalRoomType" class="form-control" readonly>
                                <input type="hidden" name="roomId" id="modalRoomId">
                                <input type="hidden" name="roomPrice" id="modalRoomPrice">
                                <input type="hidden" name="roomNumber" id="modalRoomNumber">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-bed me-2"></i>Bed Type
                                </label>
                                <input type="text" name="Bed" id="modalBedType" class="form-control" readonly>
                                <small class="text-muted">Bed type is set by the admin for this room</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="hidden" name="NoofRoom" id="modalNoOfRooms" value="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Check-in Date
                                </label>
                                <input type="date" name="cin" id="modalCheckIn" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Check-out Date
                                </label>
                                <input type="date" name="cout" id="modalCheckOut" class="form-control" required>
                            </div>
                        </div>
                        
                        <!-- Price Calculation -->
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-calculator me-2"></i>Price Summary
                                </h5>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-1">Room Rate: <span id="roomRateDisplay">Rs. 0</span> × <span id="nightsCountDisplay">0</span> night(s)</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p class="mb-1">Rs. <span id="totalPriceDisplay">0</span></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-0 fw-bold">Estimated Total:</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p class="mb-0 fw-bold">Rs. <span id="finalTotalDisplay">0</span></p>
                                    </div>
                                </div>
                                <small class="text-muted">Final price may vary based on additional services or taxes</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="submit" name="guestdetailsubmit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chat Icon and Box -->
    <div id="chat-icon" onclick="toggleChatbox()">
        <i class="fas fa-comment-dots"></i>
    </div>
    <div id="chatbox" style="display: none;">
        <div class="chat-header">
            <span><i class="fas fa-comment-dots me-2"></i>Chat with Us</span>
            <span onclick="toggleChatbox()">&times;</span>
        </div>
        <div id="chat-messages" class="chat-body">
            <div class="message bot">Hi! How can I help you today? 👋</div>
        </div>
        <div class="chat-input">
            <input type="text" id="chat-input" placeholder="Type a message..." onkeydown="handleChat(event)">
            <button onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Scroll to top button -->
    <div id="scroll-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // Show/hide scroll to top button
        window.addEventListener('scroll', function() {
            const scrollTopBtn = document.getElementById('scroll-top');
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Set minimum date for check-in and check-out
        window.onload = function() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="cin"]').min = today;
            document.querySelector('input[name="cout"]').min = today;
            
            // Add event listeners for room filtering
            document.getElementById('roomSearch').addEventListener('keyup', filterRooms);
            document.getElementById('roomTypeFilter').addEventListener('change', filterRooms);
            document.getElementById('priceFilter').addEventListener('change', filterRooms);
            
            // Reset filters on page load
            document.getElementById('roomSearch').value = '';
            document.getElementById('roomTypeFilter').value = '';
            document.getElementById('priceFilter').value = '';
            
            // Remove any existing "no rooms" message
            const noRoomsMsg = document.querySelector('.no-rooms');
            if(noRoomsMsg) {
                noRoomsMsg.remove();
            }
        };

        // Room filtering function
        function filterRooms() {
            const searchInput = document.getElementById('roomSearch').value.toLowerCase();
            const typeFilter = document.getElementById('roomTypeFilter').value;
            const priceFilter = document.getElementById('priceFilter').value;
            const roomContainer = document.getElementById('roomContainer');
            const rooms = document.querySelectorAll('#roomContainer .room-card');
            
            // Sort rooms if price filter is selected
            if(priceFilter) {
                const roomsArray = Array.from(rooms);
                roomsArray.sort((a, b) => {
                    const priceA = parseFloat(a.getAttribute('data-price'));
                    const priceB = parseFloat(b.getAttribute('data-price'));
                    
                    return priceFilter == '1' ? priceA - priceB : priceB - priceA;
                });
                
                // Remove all rooms
                rooms.forEach(room => room.remove());
                
                // Add sorted rooms back
                roomsArray.forEach(room => roomContainer.appendChild(room));
            }
            
            // Apply filters
            let visibleCount = 0;
            rooms.forEach(room => {
                const roomType = room.getAttribute('data-type');
                const roomText = room.textContent.toLowerCase();
                
                // Check if room matches all filters
                const matchesSearch = roomText.includes(searchInput);
                const matchesType = typeFilter === '' || roomType === typeFilter;
                
                // Show/hide based on filter results
                if(matchesSearch && matchesType) {
                    room.style.display = 'block';
                    visibleCount++;
                } else {
                    room.style.display = 'none';
                }
            });
            
            // Show message if no rooms match filters
            const noRoomsMsg = document.querySelector('.no-rooms');
            
            if(visibleCount === 0) {
                // Only create the message if it doesn't exist
                if(!noRoomsMsg) {
                    const newNoRoomsMsg = document.createElement('div');
                    newNoRoomsMsg.className = 'col-12 text-center py-4 no-rooms';
                    newNoRoomsMsg.innerHTML = '<div class="alert alert-info"><i class="fas fa-search me-2"></i>No rooms match your search criteria.</div>';
                    roomContainer.appendChild(newNoRoomsMsg);
                }
            } else {
                // Remove the message if it exists and rooms are visible
                if(noRoomsMsg) {
                    noRoomsMsg.remove();
                }
            }
            
            console.log(`Filter applied: ${visibleCount} rooms visible`);
        }

        // Toggle chatbox visibility
        function toggleChatbox() {
            const chatbox = document.getElementById("chatbox");
            chatbox.style.display = (chatbox.style.display === "none" || !chatbox.style.display) ? "flex" : "none";
            
            // If opening the chatbox, focus on the input
            if (chatbox.style.display === "flex") {
                document.getElementById("chat-input").focus();
            }
        }

        // Get user info from PHP session
        const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : $_SESSION['usermail']; ?>";
        const email = "<?php echo $_SESSION['usermail']; ?>";

        // Add context tracking to the chat system
        let chatContext = {
            lastTopic: null,
            roomTypeDiscussed: null,
            mentionedDates: false,
            mentionedFacilities: false
        };

        // Enhanced sendMessage function with context tracking
        function sendMessage() {
            const input = document.getElementById("chat-input");
            const msgContainer = document.getElementById("chat-messages");
            const message = input.value.trim();

            if (!message) return;

            // Show user message
            const userMsg = document.createElement("div");
            userMsg.className = "message user";
            userMsg.innerText = message;
            msgContainer.appendChild(userMsg);
            
            // Clear input and focus
            input.value = "";
            input.focus();
            
            // Scroll to bottom
            msgContainer.scrollTop = msgContainer.scrollHeight;
            
            // Show typing indicator
            const typingIndicator = document.createElement("div");
            typingIndicator.className = "message bot typing-indicator";
            typingIndicator.innerHTML = "<span>.</span><span>.</span><span>.</span>";
            msgContainer.appendChild(typingIndicator);
            msgContainer.scrollTop = msgContainer.scrollHeight;
            
            // Update context based on user message
            updateChatContext(message.toLowerCase());
            
            // Generate and show bot response after a short delay
            setTimeout(() => {
                // Remove typing indicator
                typingIndicator.remove();
                
                // Get bot response with context
                const botResponse = getBotResponseWithContext(message);
                const botReply = document.createElement("div");
                botReply.className = "message bot";
                botReply.innerText = botResponse;
                msgContainer.appendChild(botReply);
                
                // Scroll to bottom
                msgContainer.scrollTop = msgContainer.scrollHeight;
                
                // Save messages to backend
                const timestamp = new Date().toISOString();
                fetch("save_message.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        username,
                        email,
                        user_message: message,
                        bot_response: botResponse,
                        time: timestamp
                    })
                }).catch(err => console.error("Error saving message:", err));
            }, 1000);
        }

        // Update chat context based on user message
        function updateChatContext(message) {
            // Track room types
            if (message.includes("superior")) {
                chatContext.roomTypeDiscussed = "superior";
                chatContext.lastTopic = "rooms";
            } else if (message.includes("deluxe")) {
                chatContext.roomTypeDiscussed = "deluxe";
                chatContext.lastTopic = "rooms";
            } else if (message.includes("guest house") || message.includes("guest")) {
                chatContext.roomTypeDiscussed = "guest";
                chatContext.lastTopic = "rooms";
            } else if (message.includes("single")) {
                chatContext.roomTypeDiscussed = "single";
                chatContext.lastTopic = "rooms";
            }
            
            // Track other topics
            if (message.includes("check in") || message.includes("check out") || 
                message.includes("checkin") || message.includes("checkout")) {
                chatContext.lastTopic = "check-in/out";
                chatContext.mentionedDates = true;
            } else if (message.includes("price") || message.includes("cost") || message.includes("rate")) {
                chatContext.lastTopic = "pricing";
            } else if (message.includes("book") || message.includes("reservation")) {
                chatContext.lastTopic = "booking";
            } else if (message.includes("facilities") || message.includes("amenities")) {
                chatContext.lastTopic = "facilities";
                chatContext.mentionedFacilities = true;
            }
        }

        // Get bot response with context awareness
        function getBotResponseWithContext(msg) {
            const response = getBotResponse(msg.toLowerCase());
            
            // Add follow-up questions based on context
            if (chatContext.lastTopic === "rooms" && chatContext.roomTypeDiscussed) {
                return response + "\n\nWould you like to know about pricing or availability for this room type?";
            } else if (chatContext.lastTopic === "pricing" && !chatContext.mentionedDates) {
                return response + "\n\nDo you have specific room in your mind for your stay?";
            } else if (chatContext.lastTopic === "booking" && !chatContext.roomTypeDiscussed) {
                return response + "\n\nWhich room type are you interested in booking?";
            }
            
            return response;
        }

        // Handle Enter key
        function handleChat(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // Enhanced bot response logic with retrieval-based approach
        function getBotResponse(msg) {
            msg = msg.toLowerCase();
            
            // Define common patterns and responses
            const patterns = [
                // Room related queries
                { keywords: ["room", "available"], response: "We have Superior, Deluxe, Guest, and Single rooms available. Would you like to know more about any specific type? 😊" },
                { keywords: ["superior", "room"], response: "Our Superior Rooms feature premium amenities including Wi-Fi, Room Service, Spa Access, Gym Access, and Pool Access. Perfect for a luxury stay!" },
                { keywords: ["deluxe", "room"], response: "Deluxe Rooms come with Wi-Fi, Room Service, Spa Access, and Gym Access. They offer a perfect balance of comfort and luxury." },
                { keywords: ["guest", "house"], response: "Our Guest Houses provide a homely atmosphere with Wi-Fi, Room Service, and Spa Access. Great for longer stays!" },
                { keywords: ["single", "room"], response: "Single Rooms are cozy and comfortable with Wi-Fi and Room Service. Perfect for solo travelers!" },
                
                // Check-in/out related queries
                { keywords: ["check in", "checkin"], response: "Check-in time is flexible, but typically starts at 2:00 PM. Early check-in can be arranged based on availability." },
                { keywords: ["check out", "checkout"], response: "Check-out time is 11:00 AM. Late check-out may be available upon request, subject to availability." },
                
                // Price related queries
                { keywords: ["price", "cost", "rate", "pp", "pricing"], response: "Our room rates vary by type and season. Superior Rooms start at Rs. 5,000, Deluxe Rooms at Rs. 4,000, Guest Houses at Rs. 3,500, and Single Rooms at Rs. 2,500 per night. Please check our Rooms section for current rates." },
                
                // Booking related queries
                { keywords: ["book", "reservation"], response: "You can book a room directly through our website. Just browse the Rooms section, select your preferred room, and click 'Book Now'. You can also call us at +977 9876543210 for assistance." },
                
                // Facilities related queries
                { keywords: ["facilities", "amenities"], response: "We offer a range of facilities including free Wi-Fi, swimming pool, spa, restaurant, and fitness center. All rooms come with air conditioning, TV, and private bathrooms." },
                
                // Location related queries
                { keywords: ["location", "address", "where"], response: "We are located in Kathmandu, Nepal. Our exact address and directions can be found in the Contact section." },
                
                // Contact related queries
                { keywords: ["contact", "phone", "call"], response: "You can reach us at +977 9876543210 or email us at info@jujuhomestay.com. We're available 24/7 to assist you." },
                
                // Greetings
                { keywords: ["hi", "hello", "hey"], response: "Hello! Welcome to Juju Homestay. How can I assist you today? 😊" },
                
                // Thank you responses
                { keywords: ["thank", "thanks", "thank you"], response: "You're welcome! Is there anything else I can help you with?" }
            ];
            
            // Check for matches in our patterns
            for (const pattern of patterns) {
                // Check if message contains any of the keywords
                if (pattern.keywords.some(keyword => msg.includes(keyword))) {
                    return pattern.response;
                }
            }
            
            // If no match is found, check for partial matches
            let bestMatch = null;
            let highestScore = 0;
            
            for (const pattern of patterns) {
                let score = 0;
                for (const keyword of pattern.keywords) {
                    if (msg.includes(keyword)) {
                        score += 1;
                    }
                }
                
                if (score > highestScore) {
                    highestScore = score;
                    bestMatch = pattern.response;
                }
            }
            
            // If we found a partial match with at least one keyword
            if (highestScore > 0) {
                return bestMatch;
            }
            
            // Default response if no match is found
            return "I'm here to help with information about our rooms, facilities, booking process, and more. Feel free to ask specific questions about your stay at Juju Homestay! 🏨";
        }

        // Function to reset the booking form
        function resetBookingForm() {
            document.getElementById('bookingForm').reset();
            
            // Reset price calculation displays
            document.getElementById('nightsCountDisplay').textContent = '0';
            document.getElementById('totalPriceDisplay').textContent = '0';
            document.getElementById('finalTotalDisplay').textContent = '0';
            
            // Reset room features
            document.getElementById('modalRoomFeatures').innerHTML = '';
            
            // Reset room image to placeholder
            document.querySelector('#modalRoomImage img').src = 'images/rooms/placeholder.jpg';
            
            // Reset headers and displays
            document.getElementById('modalRoomTypeHeader').textContent = 'Room Type';
            document.getElementById('modalBedTypeDisplay').textContent = 'Bed Type';
            document.getElementById('modalRoomPriceDisplay').textContent = 'Price';
            document.getElementById('modalRoomNumberDisplay').textContent = '-';
            document.getElementById('roomRateDisplay').textContent = 'Rs. 0';
        }
        
        // Add event listener to reset form when modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            const bookingModal = document.getElementById('bookingModal');
            
            // Reset form when modal is hidden (after closing animation completes)
            bookingModal.addEventListener('hidden.bs.modal', function() {
                resetBookingForm();
            });
            
            // Also reset when close button is clicked (for better user experience)
            const closeButtons = bookingModal.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', resetBookingForm);
            });
        });
        
        // Function to open the booking modal
        function bookRoom(roomId, roomType, bedType) {
            console.log("bookRoom called with:", { roomId, roomType, bedType });
            
            // Get room price and image from data attribute as fallback
            const roomCard = document.querySelector(`.room-card[data-type="${roomType}"]`);
            const fallbackPrice = roomCard ? roomCard.getAttribute('data-price') : 0;
            const fallbackImage = roomCard ? roomCard.querySelector('img').src : 'images/rooms/placeholder.jpg';
            
            // Show loading indicator
            document.getElementById('modalRoomTypeHeader').textContent = "Loading...";
            
            // Fetch room details from the database using AJAX
            fetch(`get_room_details.php?id=${roomId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(roomData => {
                    if (roomData.error) {
                        throw new Error(roomData.error);
                    }
                    
                    console.log("Room data received:", roomData); // Debug log
                    
                    // Use data from API or fallback to data attributes
                    const roomPrice = roomData.price || fallbackPrice;
                    const roomImage = roomData.image || fallbackImage;
                    const roomNumber = roomData.room_no || 'N/A';
                    
                    // Get bed type directly from the bed column
                    const actualBedType = roomData.bed || bedType;
                    console.log("Bed type:", actualBedType); // Debug log
                    
                    // Set room details in the modal
                    document.getElementById('modalRoomId').value = roomId;
                    document.getElementById('modalRoomType').value = roomType;
                    document.getElementById('modalRoomTypeHeader').textContent = roomType;
                    document.getElementById('modalBedType').value = actualBedType;
                    document.getElementById('modalBedTypeDisplay').textContent = actualBedType;
                    document.getElementById('modalRoomPrice').value = roomPrice;
                    document.getElementById('modalRoomPriceDisplay').textContent = `Rs. ${Number(roomPrice).toLocaleString()}`;
                    document.getElementById('roomRateDisplay').textContent = `Rs. ${Number(roomPrice).toLocaleString()}`;
                    document.getElementById('modalRoomNumber').value = roomNumber;
                    document.getElementById('modalRoomNumberDisplay').textContent = roomNumber;
                    
                    // Set room image
                    document.querySelector('#modalRoomImage img').src = roomImage;
                    
                    // Set room features
                    const featuresContainer = document.getElementById('modalRoomFeatures');
                    featuresContainer.innerHTML = '';
                    
                    // Get features based on room type
                    let features = [];
                    if (roomType === 'Superior Room') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access', 'Pool Access'];
                    } else if (roomType === 'Deluxe Room') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access'];
                    } else if (roomType === 'Guest House') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access'];
                    } else {
                        features = ['Wi-Fi', 'Room Service'];
                    }
                    
                    // Add features to modal
                    features.forEach(feature => {
                        let icon = '';
                        if (feature === 'Wi-Fi') icon = 'fa-wifi';
                        else if (feature === 'Room Service') icon = 'fa-bell-concierge';
                        else if (feature === 'Spa Access') icon = 'fa-spa';
                        else if (feature === 'Gym Access') icon = 'fa-dumbbell';
                        else if (feature === 'Pool Access') icon = 'fa-person-swimming';
                        
                        const featureBadge = document.createElement('span');
                        featureBadge.className = 'badge rounded-pill bg-light text-dark mb-1 me-1';
                        featureBadge.innerHTML = `<i class="fa-solid ${icon} me-1"></i> ${feature}`;
                        featuresContainer.appendChild(featureBadge);
                    });
                    
                    // Set minimum date for check-in to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('modalCheckIn').min = today;
                    document.getElementById('modalCheckOut').min = today;
                    
                    // Add event listeners for price calculation
                    document.getElementById('modalCheckIn').addEventListener('change', calculatePrice);
                    document.getElementById('modalCheckOut').addEventListener('change', calculatePrice);
                    
                    // Initialize price calculation
                    calculatePrice();
                })
                .catch(error => {
                    console.error('Error fetching room details:', error);
                    
                    // Use fallback data from the page
                    document.getElementById('modalRoomId').value = roomId;
                    document.getElementById('modalRoomType').value = roomType;
                    document.getElementById('modalRoomTypeHeader').textContent = roomType;
                    document.getElementById('modalBedType').value = bedType;
                    document.getElementById('modalBedTypeDisplay').textContent = bedType;
                    document.getElementById('modalRoomPrice').value = fallbackPrice;
                    document.getElementById('modalRoomPriceDisplay').textContent = `Rs. ${Number(fallbackPrice).toLocaleString()}`;
                    document.getElementById('roomRateDisplay').textContent = `Rs. ${Number(fallbackPrice).toLocaleString()}`;
                    document.getElementById('modalRoomNumber').value = 'N/A';
                    document.getElementById('modalRoomNumberDisplay').textContent = 'N/A';
                    
                    // Set room image
                    document.querySelector('#modalRoomImage img').src = fallbackImage;
                    
                    // Set room features
                    const featuresContainer = document.getElementById('modalRoomFeatures');
                    featuresContainer.innerHTML = '';
                    
                    // Get features based on room type
                    let features = [];
                    if (roomType === 'Superior Room') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access', 'Pool Access'];
                    } else if (roomType === 'Deluxe Room') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access', 'Gym Access'];
                    } else if (roomType === 'Guest House') {
                        features = ['Wi-Fi', 'Room Service', 'Spa Access'];
                    } else {
                        features = ['Wi-Fi', 'Room Service'];
                    }
                    
                    // Add features to modal
                    features.forEach(feature => {
                        let icon = '';
                        if (feature === 'Wi-Fi') icon = 'fa-wifi';
                        else if (feature === 'Room Service') icon = 'fa-bell-concierge';
                        else if (feature === 'Spa Access') icon = 'fa-spa';
                        else if (feature === 'Gym Access') icon = 'fa-dumbbell';
                        else if (feature === 'Pool Access') icon = 'fa-person-swimming';
                        
                        const featureBadge = document.createElement('span');
                        featureBadge.className = 'badge rounded-pill bg-light text-dark mb-1 me-1';
                        featureBadge.innerHTML = `<i class="fa-solid ${icon} me-1"></i> ${feature}`;
                        featuresContainer.appendChild(featureBadge);
                    });
                    
                    // Set minimum date for check-in to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('modalCheckIn').min = today;
                    document.getElementById('modalCheckOut').min = today;
                    
                    // Add event listeners for price calculation
                    document.getElementById('modalCheckIn').addEventListener('change', calculatePrice);
                    document.getElementById('modalCheckOut').addEventListener('change', calculatePrice);
                    
                    // Initialize price calculation
                    calculatePrice();
                })
                .finally(() => {
                    // Show the modal regardless of whether the fetch succeeded or failed
                    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                    bookingModal.show();
                });
        }
        
        // Function to calculate price
        function calculatePrice() {
            const checkInDate = new Date(document.getElementById('modalCheckIn').value);
            const checkOutDate = new Date(document.getElementById('modalCheckOut').value);
            const roomPrice = parseFloat(document.getElementById('modalRoomPrice').value) || 0;
            
            // Calculate number of nights
            let nights = 0;
            if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
                const timeDiff = checkOutDate - checkInDate;
                nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                nights = nights > 0 ? nights : 0;
            }
            
            // Calculate total price
            const totalPrice = roomPrice * nights;
            
            // Update display
            document.getElementById('nightsCountDisplay').textContent = nights;
            document.getElementById('totalPriceDisplay').textContent = totalPrice.toLocaleString();
            document.getElementById('finalTotalDisplay').textContent = totalPrice.toLocaleString();
        }
        
        // Set minimum date for check-in and check-out when page loads
        window.onload = function() {
            const today = new Date().toISOString().split('T')[0];
            const checkInInputs = document.querySelectorAll('input[name="cin"]');
            const checkOutInputs = document.querySelectorAll('input[name="cout"]');
            
            checkInInputs.forEach(input => {
                input.min = today;
                input.addEventListener('change', function() {
                    // Update minimum date for checkout to be at least check-in date
                    const checkoutInput = this.closest('form').querySelector('input[name="cout"]');
                    if (checkoutInput) {
                        checkoutInput.min = this.value;
                        
                        // If checkout date is before new check-in date, update it
                        if (checkoutInput.value && checkoutInput.value < this.value) {
                            checkoutInput.value = this.value;
                        }
                    }
                });
            });
            
            checkOutInputs.forEach(input => {
                input.min = today;
            });
            
            // Add event listeners for room filtering
            document.getElementById('roomSearch').addEventListener('keyup', filterRooms);
            document.getElementById('roomTypeFilter').addEventListener('change', filterRooms);
            document.getElementById('priceFilter').addEventListener('change', filterRooms);
            
            // Reset filters on page load
            document.getElementById('roomSearch').value = '';
            document.getElementById('roomTypeFilter').value = '';
            document.getElementById('priceFilter').value = '';
            
            // Remove any existing "no rooms" message
            const noRoomsMsg = document.querySelector('.no-rooms');
            if(noRoomsMsg) {
                noRoomsMsg.remove();
            }
        };

        // Function to scroll to the rooms section
        function scrollToRooms() {
            document.getElementById('rooms-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Intersection Observer for animations
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe facility cards
            document.querySelectorAll('.facility-card').forEach(card => {
                observer.observe(card);
            });
        });
    </script>

    <!-- Include custom JavaScript files -->
    <script src="js/facilities.js"></script>
</body>
</html>
