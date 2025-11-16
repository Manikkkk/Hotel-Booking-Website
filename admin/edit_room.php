<?php
include '../config.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('Invalid room ID');
        window.location.href = 'room.php';
    </script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch room data
$sql = "SELECT * FROM room WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    echo "<script>
        alert('Room not found');
        window.location.href = 'room.php';
    </script>";
    exit;
}

$row = mysqli_fetch_assoc($result);
$type = $row['type'];
$bed = isset($row['bed']) ? $row['bed'] : 'Single';
$price = isset($row['price']) ? $row['price'] : 0;
$room_no = isset($row['room_no']) ? $row['room_no'] : '';
$image = isset($row['image']) ? $row['image'] : '';

// Handle form submission
if(isset($_POST['editroom'])) {
    $new_type = mysqli_real_escape_string($conn, $_POST['troom']);
    $new_bed = mysqli_real_escape_string($conn, $_POST['bed']);
    $new_price = mysqli_real_escape_string($conn, $_POST['price']);
    $new_room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $new_image = $image; // Default to existing image
    
    // Handle image upload if a new image is provided
    if(isset($_FILES['room_image']) && $_FILES['room_image']['size'] > 0) {
        $filename = $_FILES['room_image']['name'];
        $allowed = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            echo "<script>
                alert('Error: Please select a valid file format (JPG, JPEG, PNG, GIF)');
            </script>";
        } else {
            // Generate unique filename
            $new_filename = uniqid() . '.' . $ext;
            $new_image = "images/rooms/" . $new_filename;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['room_image']['tmp_name'], "../" . $new_image)) {
                // Delete old image if it exists
                if(!empty($image) && file_exists("../" . $image)) {
                    unlink("../" . $image);
                }
            } else {
                echo "<script>
                    alert('Error: Failed to upload image');
                </script>";
                $new_image = $image; // Keep old image if upload fails
            }
        }
    }
    
    // Validate inputs
    if(empty($new_type) || empty($new_bed) || empty($new_price) || empty($new_room_no)) {
        echo "<script>
            alert('Please fill all the fields');
        </script>";
    } else {
        // Check if room number already exists (excluding current room)
        $check_sql = "SELECT * FROM room WHERE room_no = '$new_room_no' AND id != '$id'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>
                alert('Room number already exists');
            </script>";
        } else {
            $update_sql = "UPDATE room SET type = '$new_type', bed = '$new_bed', 
                          price = '$new_price', room_no = '$new_room_no', image = '$new_image' WHERE id = '$id'";
            $update_result = mysqli_query($conn, $update_sql);
            
            if($update_result) {
                echo "<script>
                    alert('Room updated successfully');
                    window.location.href = 'room.php';
                </script>";
            } else {
                echo "<script>
                    alert('Failed to update room: " . mysqli_error($conn) . "');
                </script>";
            }
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
    <title>Edit Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/room.css">
    <style>
        .edit-form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--gray-600);
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            font-weight: 500;
            font-size: 1rem;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .back-btn:hover {
            color: var(--primary);
        }
        .back-btn i {
            margin-right: 0.5rem;
        }
        .current-image {
            margin-bottom: 15px;
            text-align: center;
        }
        .current-image img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <a href="room.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Rooms
    </a>
    
    <div class="edit-form-container">
        <h2 class="text-center mb-4">Edit Room</h2>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label for="troom" class="form-label">Room Type</label>
                <select name="troom" id="troom" class="form-select" required>
                    <option value="">Select Room Type</option>
                    <option value="Superior Room" <?php echo ($type == 'Superior Room') ? 'selected' : ''; ?>>Superior Room</option>
                    <option value="Deluxe Room" <?php echo ($type == 'Deluxe Room') ? 'selected' : ''; ?>>Deluxe Room</option>
                    <option value="Guest House" <?php echo ($type == 'Guest House') ? 'selected' : ''; ?>>Guest House</option>
                    <option value="Single Room" <?php echo ($type == 'Single Room') ? 'selected' : ''; ?>>Single Room</option>
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="bed" class="form-label">Bed Type</label>
                <select name="bed" id="bed" class="form-select" required>
                    <option value="">Select Bed Type</option>
                    <option value="Single" <?php echo ($bed == 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Double" <?php echo ($bed == 'Double') ? 'selected' : ''; ?>>Double</option>
                    <option value="Triple" <?php echo ($bed == 'Triple') ? 'selected' : ''; ?>>Triple</option>
                    <option value="Quad" <?php echo ($bed == 'Quad') ? 'selected' : ''; ?>>Quad</option>
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="room_no" class="form-label">Room Number</label>
                <input type="text" name="room_no" id="room_no" class="form-control" value="<?php echo $room_no; ?>" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="price" class="form-label">Price Per Night</label>
                <div class="input-group">
                    <span class="input-group-text">Rs</span>
                    <input type="number" name="price" id="price" class="form-control" value="<?php echo $price; ?>" step="0.01" min="0" required>
                </div>
            </div>
            
            <div class="form-group mb-4">
                <label for="room_image" class="form-label">Room Image</label>
                
                <?php if(!empty($image) && file_exists('../' . $image)): ?>
                <div class="current-image">
                    <p class="text-muted">Current Image:</p>
                    <img src="../<?php echo $image; ?>" alt="Room Image">
                </div>
                <?php endif; ?>
                
                <input type="file" name="room_image" id="room_image" class="form-control" accept="image/*">
                <div class="form-text">Leave empty to keep current image. Recommended size: 800x600px. Max size: 2MB</div>
            </div>
            
            <div class="btn-container">
                <a href="room.php" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary" name="editroom">
                    <i class="fas fa-save me-1"></i> Update Room
                </button>
            </div>
        </form>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>