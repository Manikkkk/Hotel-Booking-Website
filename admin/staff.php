<?php
session_start();
include '../config.php';

// Process staff addition form
if(isset($_POST['addstaff'])) {
    // Get form data
    $staffname = mysqli_real_escape_string($conn, $_POST['staffname']);
    $staffwork = mysqli_real_escape_string($conn, $_POST['staffwork']);
    
    // Validate inputs
    if(empty($staffname) || empty($staffwork)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill all the required fields',
                confirmButtonColor: '#4361ee'
            });
        </script>";
    } else {
        // Insert staff into database
        $sql = "INSERT INTO staff (name, work) VALUES ('$staffname', '$staffwork')";
        $result = mysqli_query($conn, $sql);
        
        if($result) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Staff Added',
                    text: 'The staff member has been added successfully',
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
                    text: 'Failed to add staff member. Please try again.',
                    confirmButtonColor: '#4361ee'
                });
            </script>";
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
    <title>Staff Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* Modern color palette */
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #eef2ff;
            --secondary: #2ec4b6;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
            --info: #118ab2;
            
            /* Neutral colors */
            --dark: #1b263b;
            --light: #f8f9fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* UI elements */
            --body-bg: #f5f7fb;
            --card-bg: var(--white);
            --card-border: var(--gray-200);
            --card-radius: 16px;
            --input-radius: 10px;
            --btn-radius: 10px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Transitions */
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: var(--gray-800);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem 1.5rem;
        }
        
        .card-title {
            color: var(--gray-800);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            color: var(--primary);
            margin-right: 0.75rem;
        }
        
        /* Form styling */
        .form-label {
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: var(--input-radius);
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            background-color: var(--gray-50);
            color: var(--gray-800);
            font-size: 0.95rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            background-color: var(--white);
        }
        
        .form-control::placeholder {
            color: var(--gray-400);
        }
        
        /* Button styling */
        .btn {
            border-radius: var(--btn-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-primary, .btn-success {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover, .btn-primary:focus,
        .btn-success:hover, .btn-success:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background-color: #d63d62;
            border-color: #d63d62;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        /* Staff grid styling */
        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        /* Staff card styling */
        .staff-card {
            background-color: var(--white);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .staff-avatar {
            width: 100px;
            height: 100px;
            margin: 2rem auto 1rem;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 2.5rem;
            transition: var(--transition);
        }
        
        .staff-card:hover .staff-avatar {
            transform: scale(1.05);
            background-color: var(--primary);
            color: white;
        }
        
        .staff-content {
            padding: 1.5rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .staff-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }
        
        .staff-position {
            display: inline-block;
            padding: 0.35rem 1rem;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 30px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .staff-actions {
            margin-top: auto;
        }
        
        /* Position badge colors */
        .position-manager {
            background-color: var(--info);
            color: white;
        }
        
        .position-cook {
            background-color: var(--warning);
            color: var(--gray-800);
        }
        
        .position-helper {
            background-color: var(--success);
            color: white;
        }
        
        .position-cleaner {
            background-color: var(--secondary);
            color: white;
        }
        
        .position-waiter {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        /* Empty state styling */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background-color: var(--white);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }
        
        .empty-title {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            font-size: 1.5rem;
        }
        
        .empty-description {
            color: var(--gray-500);
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Search input styling */
        .search-container {
            position: relative;
            width: 280px;
        }
        
        .search-input {
            padding-left: 2.75rem;
            height: 45px;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            z-index: 10;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .staff-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
                padding: 1rem;
            }
            
            .search-container {
                width: 220px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .search-container {
                width: 100%;
                margin-top: 1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .card-title {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    <div class="container-fluid px-4 py-4">
        
        
        
        <div class="row g-4">
            <!-- Add Staff Form Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-user-plus"></i>Add New Staff
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST" class="staff-form">
                            <div class="mb-3">
                                <label for="staffname" class="form-label">Full Name</label>
                                <input type="text" name="staffname" id="staffname" class="form-control" placeholder="Enter staff name" required>
                            </div>

                            <div class="mb-4">
                                <label for="staffwork" class="form-label">Position</label>
                                <select name="staffwork" id="staffwork" class="form-select" required>
                                    <option value="" selected disabled>Select position</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Cook">Cook</option>
                                    <option value="Helper">Helper</option>
                                    <option value="cleaner">Cleaner</option>
                                    <option value="waiter">Waiter</option>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success" name="addstaff">
                                    <i class="fas fa-save me-2"></i>Add Staff
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Staff List -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="fas fa-users"></i>Staff Directory
                        </h5>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="staffSearch" class="form-control search-input" placeholder="Search staff...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="staff-grid">
                            <?php
                            $sql = "SELECT * FROM staff ORDER BY id DESC";
                            $re = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($re) > 0) {
                                while($row = mysqli_fetch_array($re)) {
                                    // Determine position class for styling
                                    $positionClass = '';
                                    switch(strtolower($row['work'])) {
                                        case 'manager':
                                            $positionClass = 'position-manager';
                                            $icon = 'user-tie';
                                            break;
                                        case 'cook':
                                            $positionClass = 'position-cook';
                                            $icon = 'utensils';
                                            break;
                                        case 'helper':
                                            $positionClass = 'position-helper';
                                            $icon = 'hands-helping';
                                            break;
                                        case 'cleaner':
                                            $positionClass = 'position-cleaner';
                                            $icon = 'broom';
                                            break;
                                        case 'waiter':
                                        case 'waiter':
                                            $positionClass = 'position-waiter';
                                            $icon = 'concierge-bell';
                                            break;
                                        default:
                                            $icon = 'user';
                                    }
                            ?>
                                    <div class="staff-card">
                                        <div class="staff-avatar">
                                            <i class="fas fa-<?php echo $icon; ?>"></i>
                                        </div>
                                        <div class="staff-content">
                                            <h3 class="staff-name"><?php echo $row['name']; ?></h3>
                                            <span class="staff-position <?php echo $positionClass; ?>"><?php echo $row['work']; ?></span>
                                            <div class="staff-actions">
                                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo '<div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users-slash"></i>
                                    </div>
                                    <h3 class="empty-title">No Staff Members Yet</h3>
                                    <p class="empty-description">Start by adding your first staff member using the form.</p>
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Staff search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('staffSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const staffCards = document.querySelectorAll('.staff-card');
                    
                    staffCards.forEach(card => {
                        const name = card.querySelector('.staff-name').textContent.toLowerCase();
                        const position = card.querySelector('.staff-position').textContent.toLowerCase();
                        
                        if (name.includes(searchTerm) || position.includes(searchTerm)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        });
        
        // Delete confirmation
        function confirmDelete(staffId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This staff member will be permanently removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef476f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'staffdelete.php?id=' + staffId;
                }
            });
        }
    </script>
</body>

</html>
