<?php
include '../config.php';

$id = (int)$_GET['id'];

$sql = "SELECT * FROM roombook WHERE id = '$id'";
$re = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_array($re)) {
    $stat = $row['stat'];

    if ($stat == "NotConfirm") {
        // Update booking status to Confirm
        $updateSql = "UPDATE roombook SET stat = 'Confirm' WHERE id = '$id'";
        if (mysqli_query($conn, $updateSql)) {
            // Check if payment table exists
            $check_payment = mysqli_query($conn, "SHOW TABLES LIKE 'payment'");
            if (mysqli_num_rows($check_payment) == 0) {
                // Create payment table if it doesn't exist
                $create_payment = "CREATE TABLE `payment` (
                    `id` int(30) NOT NULL,
                    `Name` varchar(30) NOT NULL,
                    `Email` varchar(30) NOT NULL,
                    `RoomType` varchar(30) NOT NULL,
                    `Bed` varchar(30) NOT NULL,
                    `NoofRoom` int(30) NOT NULL DEFAULT 1,
                    `cin` date NOT NULL,
                    `cout` date NOT NULL,
                    `noofdays` int(30) NOT NULL,
                    `roomtotal` double(8,2) NOT NULL DEFAULT 0.00,
                    `bedtotal` double(8,2) NOT NULL DEFAULT 0.00,
                    `meal` varchar(30) NOT NULL DEFAULT 'Room only',
                    `mealtotal` double(8,2) NOT NULL DEFAULT 0.00,
                    `finaltotal` double(8,2) NOT NULL DEFAULT 0.00,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                mysqli_query($conn, $create_payment);
            }
            
            // Add to payment table
            $name = $row['Name'];
            $email = $row['Email'];
            $roomtype = $row['RoomType'];
            $bed = $row['Bed'];
            $noofroom = isset($row['NoofRoom']) ? $row['NoofRoom'] : 1;
            $cin = $row['cin'];
            $cout = $row['cout'];
            $noofdays = $row['nodays'];
            $meal = isset($row['Meal']) ? $row['Meal'] : 'Room only';
            
            // Calculate room rates
            $type_of_room = 0;
            if ($roomtype == "Superior Room") {
                $type_of_room = 3000;
            } else if ($roomtype == "Deluxe Room") {
                $type_of_room = 2000;
            } else if ($roomtype == "Guest House") {
                $type_of_room = 1500;
            } else if ($roomtype == "Single Room") {
                $type_of_room = 1000;
            }
            
            // Calculate bed rates
            $type_of_bed = 0;
            if ($bed == "Single") {
                $type_of_bed = 0;
            } else if ($bed == "Double") {
                $type_of_bed = 1000;
            } else if ($bed == "Triple") {
                $type_of_bed = 2000;
            } else if ($bed == "Quad") {
                $type_of_bed = 3000;
            }
            
            // Calculate meal rates
            $type_of_meal = 0;
            if ($meal == "Room only") {
                $type_of_meal = 0;
            } else if ($meal == "Breakfast") {
                $type_of_meal = 500;
            } else if ($meal == "Half Board") {
                $type_of_meal = 1000;
            } else if ($meal == "Full Board") {
                $type_of_meal = 1500;
            }
            
            // Calculate totals
            $ttot = $type_of_room * $noofdays * $noofroom;
            $mepr = $type_of_meal * $noofdays;
            $btot = $type_of_bed * $noofdays;
            $fintot = $ttot + $mepr + $btot;
            
            // Insert into payment table
            $payment_sql = "INSERT INTO payment(id, Name, Email, RoomType, Bed, NoofRoom, cin, cout, noofdays, roomtotal, bedtotal, meal, mealtotal, finaltotal) 
                           VALUES ('$id', '$name', '$email', '$roomtype', '$bed', '$noofroom', '$cin', '$cout', '$noofdays', '$ttot', '$btot', '$meal', '$mepr', '$fintot')";
            
            // Check if payment record already exists
            $check_payment_record = mysqli_query($conn, "SELECT * FROM payment WHERE id = '$id'");
            if (mysqli_num_rows($check_payment_record) > 0) {
                // Update existing payment record
                $payment_sql = "UPDATE payment SET 
                               Name='$name', Email='$email', RoomType='$roomtype', Bed='$bed', NoofRoom='$noofroom', 
                               cin='$cin', cout='$cout', noofdays='$noofdays', roomtotal='$ttot', bedtotal='$btot', 
                               meal='$meal', mealtotal='$mepr', finaltotal='$fintot' 
                               WHERE id = '$id'";
            }
            
            mysqli_query($conn, $payment_sql);
            
            // Redirect after success
            header("Location: roombook.php");
            exit();
        } else {
            echo "<script>alert('Error updating booking status.'); window.location.href='roombook.php';</script>";
        }
    } else {
        echo "<script>alert('Guest already confirmed.'); window.location.href='roombook.php';</script>";
    }
} else {
    echo "<script>alert('Invalid booking ID.'); window.location.href='roombook.php';</script>";
}
?>
