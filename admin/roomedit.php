<?php
include '../config.php';

// Check if required columns exist
$check_room_no = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'room_no'");
$check_price = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'price'");
$check_image = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'image'");

// If columns don't exist, create them
if(mysqli_num_rows($check_room_no) == 0 || mysqli_num_rows($check_price) == 0 || mysqli_num_rows($check_image) == 0) {
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
    
    // Refresh column check
    $check_room_no = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'room_no'");
    $check_price = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'price'");
    $check_image = mysqli_query($conn, "SHOW COLUMNS FROM room LIKE 'image'");
}

// Create image upload directory if it doesn't exist
$upload_dir = "../images/rooms/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fetch room data
$id = $_GET['id'];

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
$bedding = $row['bedding'];
$price = isset($row['price']) ? $row['price'] : 0;
$room_no = isset($row['room_no']) ? $row['room_no'] : '';
$image = isset($row['image']) ? $row['image'] : '';

// Handle form submission
if(isset($_POST['editroom'])) {
    $new_type = $_POST['troom'];
    $new_bedding = $_POST['bed'];
    $new_price = $_POST['price'];
    $new_room_no = $_POST['room_no'];
    $new_image = $image; // Default to existing image
    
    // Handle image upload if a new image is provided
    if(isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES['room_image']['name'];
        $filetype = $_FILES['room_image']['type'];
        $filesize = $_FILES['room_image']['size'];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            echo "<script>alert('Error: Please select a valid file format (JPG, JPEG, PNG, GIF)');</script>";
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
                echo "<script>alert('Error: Failed to upload image');</script>";
                $new_image = $image; // Keep old image if upload fails
            }
        }
    }
    
    // Validate inputs
    if(empty($new_type) || empty($new_bedding) || empty($new_price) || empty($new_room_no)) {
        echo "<script>alert('Please fill all the fields');</script>";
    } else {
        // Check if room number already exists (excluding current room)
        $check_sql = "SELECT * FROM room WHERE room_no = '$new_room_no' AND id != '$id'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Room number already exists');</script>";
        } else {
            $update_sql = "UPDATE room SET type = '$new_type', bedding = '$new_bedding', 
                          price = '$new_price', room_no = '$new_room_no', image = '$new_image' WHERE id = '$id'";
            $update_result = mysqli_query($conn, $update_sql);
            
            if($update_result) {
                echo "<script>
                    alert('Room updated successfully');
                    window.location.href = 'room.php';
                </script>";
            } else {
                echo "<script>alert('Failed to update room: " . mysqli_error($conn) . "');</script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="./css/room.css">
    <title>Edit Room</title>
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
    </style>
</head>
<body>
    <div class="edit-form-container">
        <h2 class="text-center mb-4">Edit Room</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="troom">Type of Room:</label>
                <select name="troom" class="form-control" required>
                    <option value="">Select Room Type</option>
                    <option value="Superior Room" <?php echo ($type == 'Superior Room') ? 'selected' : ''; ?>>SUPERIOR ROOM</option>
                    <option value="Deluxe Room" <?php echo ($type == 'Deluxe Room') ? 'selected' : ''; ?>>DELUXE ROOM</option>
                    <option value="Guest House" <?php echo ($type == 'Guest House') ? 'selected' : ''; ?>>GUEST HOUSE</option>
                    <option value="Single Room" <?php echo ($type == 'Single Room') ? 'selected' : ''; ?>>SINGLE ROOM</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="bed">Type of Bed:</label>
                <select name="bed" class="form-control" required>
                    <option value="">Select Bed Type</option>
                    <option value="Single" <?php echo ($bedding == 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Double" <?php echo ($bedding == 'Double') ? 'selected' : ''; ?>>Double</option>
                    <option value="Triple" <?php echo ($bedding == 'Triple') ? 'selected' : ''; ?>>Triple</option>
                    <option value="Quad" <?php echo ($bedding == 'Quad') ? 'selected' : ''; ?>>Quad</option>
                    <option value="None" <?php echo ($bedding == 'None') ? 'selected' : ''; ?>>None</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Price per Night:</label>
                <input type="number" name="price" class="form-control" value="<?php echo $price; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="room_no">Room Number:</label>
                <input type="text" name="room_no" class="form-control" value="<?php echo $room_no; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="room_image">Room Image:</label>
                <input type="file" name="room_image" class="form-control-file">
                <?php if(!empty($image)): ?>
                    <img src="../<?php echo $image; ?>" alt="Current Room Image" class="img-fluid mt-2" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            
            <div class="btn-container">
                <a href="room.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" name="editroom">Update Room</button>
            </div>
        </form>
    </div>
</body>
</html>

