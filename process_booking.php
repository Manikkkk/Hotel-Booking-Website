<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['usermail']) || empty($_SESSION['usermail'])) {
    header("location: index.php");
    exit();
}

// Check if form was submitted
if (isset($_POST['guestdetailsubmit'])) {
    // Get form data
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $Email = mysqli_real_escape_string($conn, $_SESSION['usermail']); // Use email from session
    $Phone = mysqli_real_escape_string($conn, $_POST['Phone']);
    $RoomType = mysqli_real_escape_string($conn, $_POST['RoomType']);
    $Bed = mysqli_real_escape_string($conn, $_POST['Bed']);
    $NoofRoom = 1; // Fixed to 1 room
    $cin = mysqli_real_escape_string($conn, $_POST['cin']);
    $cout = mysqli_real_escape_string($conn, $_POST['cout']);
    $roomId = isset($_POST['roomId']) ? mysqli_real_escape_string($conn, $_POST['roomId']) : '';
    $roomNumber = isset($_POST['roomNumber']) ? mysqli_real_escape_string($conn, $_POST['roomNumber']) : '';
    
    // Validate dates
    $cinDate = new DateTime($cin);
    $coutDate = new DateTime($cout);
    $today = new DateTime();
    
    if ($cinDate < $today) {
        $_SESSION['booking_error'] = "Check-in date cannot be in the past.";
        header("location: home.php");
        exit();
    }
    
    if ($coutDate <= $cinDate) {
        $_SESSION['booking_error'] = "Check-out date must be after check-in date.";
        header("location: home.php");
        exit();
    }
    
    // Calculate number of days
    $interval = $cinDate->diff($coutDate);
    $nodays = $interval->days;
    
    // Check room availability
    $availability_sql = "SELECT COUNT(*) as booked_count FROM roombook 
                        WHERE RoomType = '$RoomType' 
                        AND ((cin <= '$cin' AND cout >= '$cin') 
                        OR (cin <= '$cout' AND cout >= '$cout') 
                        OR (cin >= '$cin' AND cout <= '$cout'))
                        AND stat = 'Confirm'";
    
    $availability_result = mysqli_query($conn, $availability_sql);
    $availability = mysqli_fetch_assoc($availability_result);
    
    // Get total rooms of this type
    $room_count_sql = "SELECT COUNT(*) as total_rooms FROM room WHERE type = '$RoomType'";
    $room_count_result = mysqli_query($conn, $room_count_sql);
    $room_count = mysqli_fetch_assoc($room_count_result);
    
    $available_rooms = $room_count['total_rooms'] - $availability['booked_count'];
    
    if ($available_rooms < 1) {
        $_SESSION['booking_error'] = "Sorry, this room type is not available for the selected dates.";
        header("location: home.php");
        exit();
    }
    
    // Get the structure of the roombook table to check required columns
    $table_structure = mysqli_query($conn, "DESCRIBE roombook");
    $columns = [];
    while ($col = mysqli_fetch_assoc($table_structure)) {
        $columns[$col['Field']] = $col;
    }
    
    // Check for required columns and add them if missing
    $required_columns = [
        'room_no' => "VARCHAR(10) DEFAULT NULL",
        'Phone' => "VARCHAR(30) DEFAULT NULL",
        'Country' => "VARCHAR(30) DEFAULT 'Not Specified'",
        'NoofRoom' => "INT(11) DEFAULT 1",
        'Meal' => "VARCHAR(30) DEFAULT 'Room only'"
    ];
    
    foreach ($required_columns as $column => $definition) {
        if (!isset($columns[$column])) {
            $add_column = mysqli_query($conn, "ALTER TABLE roombook ADD COLUMN $column $definition");
            if (!$add_column) {
                $_SESSION['booking_error'] = "Database error while adding column $column: " . mysqli_error($conn);
                header("location: home.php");
                exit();
            }
        }
    }
    
    // Prepare the SQL statement with only the columns we know exist
    $sql = "INSERT INTO roombook (Name, Email, RoomType, Bed, cin, cout, stat, nodays";
    
    // Add optional fields if they exist in the table
    if (isset($columns['Phone'])) {
        $sql .= ", Phone";
    }
    
    if (isset($columns['room_no'])) {
        $sql .= ", room_no";
    }
    
    if (isset($columns['Country'])) {
        $sql .= ", Country";
    }
    
    if (isset($columns['NoofRoom'])) {
        $sql .= ", NoofRoom";
    }
    
    if (isset($columns['Meal'])) {
        $sql .= ", Meal";
    }
    
    $sql .= ") VALUES ('$Name', '$Email', '$RoomType', '$Bed', '$cin', '$cout', 'NotConfirm', '$nodays'";
    
    // Add values for optional fields
    if (isset($columns['Phone'])) {
        $sql .= ", '$Phone'";
    }
    
    if (isset($columns['room_no'])) {
        $sql .= ", '$roomNumber'";
    }
    
    if (isset($columns['Country'])) {
        $sql .= ", 'Not Specified'";
    }
    
    if (isset($columns['NoofRoom'])) {
        $sql .= ", '$NoofRoom'";
    }
    
    if (isset($columns['Meal'])) {
        $sql .= ", 'Room only'";
    }
    
    $sql .= ")";
    
    if (mysqli_query($conn, $sql)) {
        // Get the booking ID
        $bookingId = mysqli_insert_id($conn);
        
        // Store booking details in session for confirmation page
        $_SESSION['booked'] = [
            'id' => $bookingId,
            'name' => $Name,
            'email' => $Email,
            'phone' => $Phone,
            'roomType' => $RoomType,
            'bed' => $Bed,
            'checkIn' => $cin,
            'checkOut' => $cout,
            'nights' => $nodays,
            'status' => 'Pending',
            'roomNumber' => $roomNumber
        ];
        
        // Redirect to booking confirmation page
        header("location: booked.php?id=$bookingId");
        exit();
    } else {
        $_SESSION['booking_error'] = "Error: " . mysqli_error($conn) . " SQL: " . $sql;
        header("location: home.php");
        exit();
    }
} else {
    // If form was not submitted, redirect to home page
    header("location: home.php");
    exit();
}
?>

