<?php
session_start();
include '../config.php';

// Check if payment table exists
$check_payment_table = mysqli_query($conn, "SHOW TABLES LIKE 'payment'");
if(mysqli_num_rows($check_payment_table) == 0) {
    // Create payment table
    $create_payment_table = "CREATE TABLE `payment` (
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
    
    mysqli_query($conn, $create_payment_table);
    
    // Add a message to notify admin
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Database Update',
                text: 'Payment table has been created successfully.',
                confirmButtonColor: '#0d6efd'
            });
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/roombook.css">
    <title>Room Bookings</title>
</head>

<body>
    <div class="container-fluid px-4 py-4">
        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <!-- Room Availability Stats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-primary"><i class="fas fa-bed me-2"></i>Room Availability</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBookingModal">
                        <i class="fas fa-plus me-2"></i>New Booking
                    </button>
                </div>
                
                <div class="roomavailable">
                    <?php
                    // Room availability calculations
                    $rsql = "select * from room";
                    $rre = mysqli_query($conn, $rsql);
                    $r = 0;
                    $sc = 0;
                    $gh = 0;
                    $sr = 0;
                    $dr = 0;

                    while ($rrow = mysqli_fetch_array($rre)) {
                        $r = $r + 1;
                        $s = $rrow['type'];
                        if ($s == "Superior Room") {
                            $sc = $sc + 1;
                        }
                        if ($s == "Guest House") {
                            $gh = $gh + 1;
                        }
                        if ($s == "Single Room") {
                            $sr = $sr + 1;
                        }
                        if ($s == "Deluxe Room") {
                            $dr = $dr + 1;
                        }
                    }

                    // Check if payment table exists before querying
                    $check_payment = mysqli_query($conn, "SHOW TABLES LIKE 'payment'");
                    $cr = 0;
                    $csc = 0;
                    $cgh = 0;
                    $csr = 0;
                    $cdr = 0;
                    
                    if(mysqli_num_rows($check_payment) > 0) {
                        $csql = "select * from payment";
                        $cre = mysqli_query($conn, $csql);
                        
                        while ($crow = mysqli_fetch_array($cre)) {
                            $cr = $cr + 1;
                            $cs = $crow['RoomType'];

                            if ($cs == "Superior Room") {
                                $csc = $csc + 1;
                            }

                            if ($cs == "Guest House") {
                                $cgh = $cgh + 1;
                            }
                            if ($cs == "Single Room") {
                                $csr = $csr + 1;
                            }
                            if ($cs == "Deluxe Room") {
                                $cdr = $cdr + 1;
                            }
                        }
                    }

                    // Available rooms
                    $f1 = $sc - $csc;
                    if ($f1 <= 0) {
                        $f1 = "NO";
                    }
                    $f2 = $gh - $cgh;
                    if ($f2 <= 0) {
                        $f2 = "NO";
                    }
                    $f3 = $sr - $csr;
                    if ($f3 <= 0) {
                        $f3 = "NO";
                    }
                    $f4 = $dr - $cdr;
                    if ($f4 <= 0) {
                        $f4 = "NO";
                    }
                    $f5 = $r - $cr;
                    if ($f5 <= 0) {
                        $f5 = "NO";
                    }
                    ?>

                    <!-- Superior Room -->
                    <div class="roombox superior">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-star fa-2x text-primary"></i>
                        </div>
                        <h2>Superior Rooms</h2>
                        <p><?php echo $f1; ?></p>
                        <span class="badge bg-light text-primary">Total: <?php echo $sc; ?></span>
                    </div>

                    <!-- Guest House -->
                    <div class="roombox guest">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-home fa-2x text-warning"></i>
                        </div>
                        <h2>Guest Houses</h2>
                        <p><?php echo $f2; ?></p>
                        <span class="badge bg-light text-warning">Total: <?php echo $gh; ?></span>
                    </div>

                    <!-- Single Room -->
                    <div class="roombox single">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-user fa-2x text-info"></i>
                        </div>
                        <h2>Single Rooms</h2>
                        <p><?php echo $f3; ?></p>
                        <span class="badge bg-light text-info">Total: <?php echo $sr; ?></span>
                    </div>

                    <!-- Deluxe Room -->
                    <div class="roombox deluxe">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-crown fa-2x text-success"></i>
                        </div>
                        <h2>Deluxe Rooms</h2>
                        <p><?php echo $f4; ?></p>
                        <span class="badge bg-light text-success">Total: <?php echo $dr; ?></span>
                    </div>

                    <!-- Total Rooms -->
                    <div class="roombox total">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-hotel fa-2x text-dark"></i>
                        </div>
                        <h2>Total Rooms</h2>
                        <p><?php echo $f5; ?></p>
                        <span class="badge bg-light text-dark">Total: <?php echo $r; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>Room Bookings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            $roombooktablesql = "SELECT * FROM roombook ORDER BY id DESC";
                            $roombookresult = mysqli_query($conn, $roombooktablesql);
                            $nums = mysqli_num_rows($roombookresult);
                            ?>
                            <table class="table table-hover align-middle" id="bookingsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Guest</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Room</th>
                                        <th scope="col">Check-In</th>
                                        <th scope="col">Check-Out</th>
                                        <th scope="col">Nights</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if ($nums > 0) {
                                        while ($res = mysqli_fetch_array($roombookresult)) {
                                    ?>
                                            <tr>
                                                <td><?php echo $res['id'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-2 bg-light rounded-circle text-center" style="width: 40px; height: 40px; line-height: 40px;">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo $res['Name'] ?></h6>
                                                            <small class="text-muted"><?php echo $res['Email'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $res['Phone'] ?></td>
                                                <td>
                                                    <span class="d-block"><?php echo $res['RoomType'] ?></span>
                                                    <small class="text-muted"><?php echo $res['Bed'] ?> bed • <?php echo $res['NoofRoom'] ?> room(s)</small>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($res['cin'])) ?></td>
                                                <td><?php echo date('M d, Y', strtotime($res['cout'])) ?></td>
                                                <td><?php echo $res['nodays'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($res['stat'] == "Confirm") {
                                                        echo '<span class="badge bg-success">Confirmed</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <?php
                                                        if ($res['stat'] != "Confirm") {
                                                        ?>
                                                            <a href="roomconfirm.php?id=<?php echo $res['id'] ?>" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                        <?php
                                                        }
                                                        ?>
                                                        <a href="roombookedit.php?id=<?php echo $res['id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $res['id'] ?>)" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center py-4">No bookings found</td></tr>';
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

    <!-- New Booking Modal -->
    <div class="modal fade" id="newBookingModal" tabindex="-1" aria-labelledby="newBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="newBookingModalLabel">
                        <i class="fas fa-calendar-plus me-2"></i>New Room Booking
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Guest Information -->
                            <div class="col-md-6">
                                <label for="Name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="Name" name="Name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="Email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="Email" name="Email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="Country" class="form-label">Country</label>
                                <select class="form-select" id="Country" name="Country" required>
                                    <option value="" selected disabled>Select Country</option>
                                    <option value="USA">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="India">India</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="Phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="Phone" name="Phone" required>
                            </div>

                            <!-- Room Information -->
                            <div class="col-md-6">
                                <label for="RoomType" class="form-label">Room Type</label>
                                <select class="form-select" id="RoomType" name="RoomType" required>
                                    <option value="" selected disabled>Select Room Type</option>
                                    <option value="Superior Room">Superior Room</option>
                                    <option value="Guest House">Guest House</option>
                                    <option value="Single Room">Single Room</option>
                                    <option value="Deluxe Room">Deluxe Room</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="Bed" class="form-label">Bed Type</label>
                                <select class="form-select" id="Bed" name="Bed" required>
                                    <option value="" selected disabled>Select Bed Type</option>
                                    <option value="Single">Single</option>
                                    <option value="Double">Double</option>
                                    <option value="Triple">Triple</option>
                                    <option value="Quad">Quad</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="NoofRoom" class="form-label">Number of Rooms</label>
                                <input type="number" class="form-control" id="NoofRoom" name="NoofRoom" min="1" value="1" required>
                            </div>

                            <!-- Dates -->
                            <div class="col-md-6">
                                <label for="cin" class="form-label">Check-In Date</label>
                                <input type="date" class="form-control" id="cin" name="cin" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cout" class="form-label">Check-Out Date</label>
                                <input type="date" class="form-control" id="cout" name="cout" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="guestdetailsubmit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Set minimum date for check-in and check-out
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('cin').min = today;
            document.getElementById('cout').min = today;
            
            // Update check-out min date when check-in changes
            document.getElementById('cin').addEventListener('change', function() {
                document.getElementById('cout').min = this.value;
                
                // If check-out date is before new check-in date, update it
                if(document.getElementById('cout').value < this.value) {
                    document.getElementById('cout').value = this.value;
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
                    window.location.href = 'roombookdelete.php?id=' + id;
                }
            });
        }
        
        // Form submission handling
        <?php
        if (isset($_POST['guestdetailsubmit'])) {
            $Name = $_POST['Name'];
            $Email = $_POST['Email'];
            $Country = $_POST['Country'];
            $Phone = $_POST['Phone'];
            $RoomType = $_POST['RoomType'];
            $Bed = $_POST['Bed'];
            $NoofRoom = $_POST['NoofRoom'];
            $cin = $_POST['cin'];
            $cout = $_POST['cout'];

            if ($Name == "" || $Email == "" || $Country == "") {
                echo "
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill all required fields!'
                });
                ";
            } else {
                $sta = "NotConfirm";
                $sql = "INSERT INTO roombook(Name,Email,Country,Phone,RoomType,Bed,NoofRoom,cin,cout,stat,nodays) VALUES ('$Name','$Email','$Country','$Phone','$RoomType','$Bed','$NoofRoom','$cin','$cout','$sta',datediff('$cout','$cin'))";
                
                // Check room availability
                if ($RoomType == "Superior Room" && $f1 == "NO") {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'No Availability',
                        text: 'Superior Room is not available!'
                    });
                    ";
                } else if ($RoomType == "Guest House" && $f2 == "NO") {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'No Availability',
                        text: 'Guest House is not available!'
                    });
                    ";
                } else if ($RoomType == "Single Room" && $f3 == "NO") {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'No Availability',
                        text: 'Single Room is not available!'
                    });
                    ";
                } else if ($RoomType == "Deluxe Room" && $f4 == "NO") {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'No Availability',
                        text: 'Deluxe Room is not available!'
                    });
                    ";
                } else {
                    $result = mysqli_query($conn, $sql);
                    if ($result) {
                        echo "
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Booking saved successfully!'
                        }).then(() => {
                            location.reload();
                        });
                        ";
                    } else {
                        echo "
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again.'
                        });
                        ";
                    }
                }
            }
        }
        ?>
    </script>
</body>
<script src="./javascript/roombook.js"></script>
</html>
