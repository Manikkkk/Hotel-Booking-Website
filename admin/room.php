<?php
session_start();
include '../config.php';

// Check if required columns exist
$check_room_no = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'room_no'");
$check_price = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'price'");
$check_image = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'image'");
$check_bed = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'bed'");

// If columns don't exist, create them
if(mysqli_num_rows($check_room_no) == 0 || mysqli_num_rows($check_price) == 0 || 
   mysqli_num_rows($check_image) == 0 || mysqli_num_rows($check_bed) == 0) {
    
    // Add missing columns
    if(mysqli_num_rows($check_room_no) == 0) {
        mysqli_query($conn, "ALTER TABLE room ADD COLUMN room_no VARCHAR(10) NOT NULL DEFAULT ''");
    }
    if(mysqli_num_rows($check_price) == 0) {
        mysqli_query($conn, "ALTER TABLE room ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0");
    }
    if(mysqli_num_rows($check_image) == 0) {
        mysqli_query($conn, "ALTER TABLE room ADD COLUMN image VARCHAR(255) DEFAULT NULL");
    }
    if(mysqli_num_rows($check_bed) == 0) {
        mysqli_query($conn, "ALTER TABLE room ADD COLUMN bed VARCHAR(10) NOT NULL DEFAULT 'Single'");
    }
}

// Create image upload directory if it doesn't exist
$upload_dir = "../images/rooms/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/room.css">
</head>

<body>
     <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    <div class="container-fluid p-4">
        <div class="row g-4">
            <!-- Add Room Form Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-bottom border-3 border-primary">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-plus-circle me-2 text-primary"></i>Add New Room
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST" enctype="multipart/form-data" class="add-room-form">
                            <div class="mb-3">
                                <label for="troom" class="form-label">Room Type</label>
                                <select name="troom" id="troom" class="form-select">
                                    <option value="" selected disabled>Select Room Type</option>
                                    <option value="Superior Room">Superior Room</option>
                                    <option value="Deluxe Room">Deluxe Room</option>
                                    <option value="Guest House">Guest House</option>
                                    <option value="Single Room">Single Room</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="bed" class="form-label">Bed Type</label>
                                <select name="bed" id="bed" class="form-select">
                                    <option value="" selected disabled>Select Bed Type</option>
                                    <option value="Single">Single</option>
                                    <option value="Double">Double</option>
                                    <option value="Triple">Triple</option>
                                    <option value="Quad">Quad</option>
                                    <!-- <option value="None">None</option> -->
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="room_no" class="form-label">Room Number</label>
                                <input type="text" name="room_no" id="room_no" class="form-control" placeholder="e.g. A101">
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price Per Night</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" name="price" id="price" class="form-control" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="room_image" class="form-label">Room Image</label>
                                <input type="file" name="room_image" id="room_image" class="form-control" accept="image/*">
                                <div class="form-text">Recommended size: 800x600px. Max size: 2MB</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2" name="addroom">
                                    <i class="fas fa-save me-2"></i>Add Room
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Room List -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-bed me-2 text-primary"></i>Room Inventory
                        </h5>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" id="roomSearch" class="form-control" placeholder="Search rooms...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="room">
                            <?php
                            $room_query = "SELECT * FROM room ORDER BY id DESC";
                            $room_result = mysqli_query($conn, $room_query);
                            
                            if(mysqli_num_rows($room_result) > 0) {
                                while($room = mysqli_fetch_assoc($room_result)) {
                                    // Determine room type for styling
                                    $roomTypeClass = '';
                                    switch($room['type']) {
                                        case 'Superior Room':
                                            $roomTypeClass = 'superior';
                                            $roomIcon = 'star';
                                            break;
                                        case 'Deluxe Room':
                                            $roomTypeClass = 'deluxe';
                                            $roomIcon = 'crown';
                                            break;
                                        case 'Guest House':
                                            $roomTypeClass = 'guest';
                                            $roomIcon = 'home';
                                            break;
                                        case 'Single Room':
                                            $roomTypeClass = 'single';
                                            $roomIcon = 'user';
                                            break;
                                        default:
                                            $roomTypeClass = '';
                                            $roomIcon = 'bed';
                                    }
                                    
                                    // Check if bed exists in the array, if not set a default value
                                    $bedType = isset($room['bed']) ? $room['bed'] : 'Single';
                            ?>
                                    <div class="roombox <?php echo $roomTypeClass; ?>">
                                        <div class="room-image">
                                            <?php if(!empty($room['image']) && file_exists('../' . $room['image'])): ?>
                                                <img src="../<?php echo $room['image']; ?>" alt="<?php echo $room['type']; ?>">
                                            <?php else: ?>
                                                <div class="no-image">
                                                    <i class="fas fa-<?php echo $roomIcon; ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="room-number"><?php echo $room['room_no']; ?></div>
                                        </div>
                                        <div class="room-details">
                                            <h3 class="room-type"><?php echo $room['type']; ?></h3>
                                            <div class="room-bed">
                                                <i class="fas fa-bed me-1"></i> <?php echo $bedType; ?> Bed
                                            </div>
                                            <div class="room-price">
                                                Rs.<?php echo number_format($room['price'], 2); ?> <span>/ night</span>
                                            </div>
                                            <div class="room-actions">
                                                <a href="edit_room.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $room['id']; ?>)" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo '<div class="no-rooms">
                                    <div class="no-rooms-icon">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                    <h3>No Rooms Added Yet</h3>
                                    <p>Start by adding your first room using the form.</p>
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Search functionality
        document.getElementById('roomSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const roomBoxes = document.querySelectorAll('.roombox');
            
            roomBoxes.forEach(box => {
                const roomType = box.querySelector('.room-type').textContent.toLowerCase();
                const roomNumber = box.querySelector('.room-number').textContent.toLowerCase();
                const roomBed = box.querySelector('.room-bed').textContent.toLowerCase();
                
                if(roomType.includes(searchValue) || roomNumber.includes(searchValue) || roomBed.includes(searchValue)) {
                    box.style.display = 'flex';
                } else {
                    box.style.display = 'none';
                }
            });
        });
        
        // Confirm delete function
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'roomdelete.php?id=' + id;
                }
            });
        }
    </script>

    <?php
    // Process form submission
    if(isset($_POST['addroom'])) {
        $typeofroom = $_POST['troom'];
        $typeofbed = $_POST['bed'];
        $price = $_POST['price'];
        $room_no = $_POST['room_no'];
        $image_path = NULL;

        // Validate inputs
        if(empty($typeofroom) || empty($typeofbed) || empty($price) || empty($room_no)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill all the required fields',
                    confirmButtonColor: '#4361ee'
                });
            </script>";
        } else {
            // Handle image upload
            if(isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
                $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
                $filename = $_FILES['room_image']['name'];
                $filetype = $_FILES['room_image']['type'];
                $filesize = $_FILES['room_image']['size'];
                
                // Verify file extension
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!array_key_exists($ext, $allowed)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File',
                            text: 'Please select a valid file format (JPG, JPEG, PNG, GIF)',
                            confirmButtonColor: '#4361ee'
                        });
                    </script>";
                    $image_path = NULL;
                } else {
                    // Generate unique filename
                    $new_filename = uniqid() . '.' . $ext;
                    $image_path = "images/rooms/" . $new_filename;
                    
                    // Move uploaded file
                    if(move_uploaded_file($_FILES['room_image']['tmp_name'], "../" . $image_path)) {
                        // File uploaded successfully
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: 'Failed to upload image. Please try again.',
                                confirmButtonColor: '#4361ee'
                            });
                        </script>";
                        $image_path = NULL;
                    }
                }
            }
            
            // Check if room number already exists
            $exists = false;
            if(mysqli_num_rows($check_room_no) > 0) {
                $check_sql = "SELECT * FROM room WHERE room_no = '$room_no'";
                $check_result = mysqli_query($conn, $check_sql);
                $exists = mysqli_num_rows($check_result) > 0;
            }
            
            if($exists) {
                // echo "<script>
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Room Already Exists',
                //         text: 'A room with this number already exists',
                //         confirmButtonColor: '#4361ee'
                //     });
                // </script>";
            } else {
                // Insert room into database
                $sql = "INSERT INTO room (type, bed, price, room_no, image) VALUES ('$typeofroom', '$typeofbed', '$price', '$room_no', '$image_path')";
                $result = mysqli_query($conn, $sql);
                
                if($result) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Room Added',
                            text: 'The room has been added successfully',
                            confirmButtonColor: '#4361ee'
                        }).then(() => {
                            location.reload();
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add room. Please try again.',
                            confirmButtonColor: '#4361ee'
                        });
                    </script>";
                }
            }
        }
    }
    ?>
</body>
</html>
