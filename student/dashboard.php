<?php

session_start();


if (!isset( $_SESSION['admissionNumber'])) {
    header("Location: ../index.php");
    exit();
}

include '../Includes/dbcon.php';

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
    .sidebar {
        min-height: 100vh;
        position: fixed;
        width: 250px;
        transition: all 0.3s;
        z-index: 1000;
    }

    .content {
        margin-left: 250px;
        transition: all 0.3s;
    }

    .sidebar.collapsed {
        margin-left: -250px;
    }

    .content.expanded {
        margin-left: 0;
    }

    .stat-card {
        border-left: 4px solid;
    }

    .stat-card.present {
        border-left-color: #28a745;
    }

    .stat-card.absent {
        border-left-color: #dc3545;
    }

    .stat-card.courses {
        border-left-color: #007bff;
    }

    .course-card {
        transition: all 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
    }

    #qr-reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }

    #qr-reader__dashboard_section_csr {
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
        }

        .sidebar.show {
            margin-left: 0;
        }

        .content {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar bg-dark text-white">
            <div class="sidebar-header p-4 border-bottom border-secondary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 mr-3">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 font-weight-bold" id="student-name">
                            
                            <!-- <?php 
                            $studNo = $_SESSION['admissionNumber'];
                            $query = "SELECT * FROM tblstudents WHERE admissionNumber = '$studNo' ";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if ($num > 0) {
                                                
                                                
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                            }
                            echo htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']); 
                            // ?> -->
                        </h5>
                        <small class="text-muted">Student</small>
                    </div>
                </div>
            </div>
            <ul class="list-unstyled components p-4">
                <li>
                    <a href="#dashboard" class="nav-link active d-flex align-items-center text-white rounded">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#qr-scanner" class="nav-link d-flex align-items-center text-muted rounded">
                        <i class="fas fa-qrcode mr-2"></i> QR Scanner
                    </a>
                </li>
                <li>
                    <a href="#courses" class="nav-link d-flex align-items-center text-muted rounded">
                        <i class="fas fa-book mr-2"></i> My Courses
                    </a>
                </li>
                <li>
                    <a href="#attendance" class="nav-link d-flex align-items-center text-muted rounded">
                        <i class="fas fa-calendar-alt mr-2"></i> Attendance History
                    </a>
                </li>
                <li>
                    <a href="#change-password" class="nav-link d-flex align-items-center text-muted rounded">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                </li>
                <li>
                    <a href="./logout.php" id="logout-btn" class="nav-link d-flex align-items-center text-muted rounded">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </li>
            </ul>
            <div class="p-4 border-top border-secondary position-absolute bottom-0 w-100">
                <button id="toggle-sidebar" class="btn btn-secondary btn-block">
                    <i class="fas fa-chevron-left mr-1"></i> Collapse
                </button>
            </div>
        </nav>

        <!-- Mobile Sidebar Toggle -->
        <button id="mobile-toggle-sidebar" class="btn btn-dark d-md-none fixed-top ml-3 mt-3 z-index-100">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand navbar-light bg-white shadow-sm sticky-top">
                <div class="container-fluid">
                    <h1 class="h5 mb-0 text-gray-800">Student Dashboard</h1>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <!-- <span class="position-relative">
                                    <i class="fas fa-bell text-gray-500"></i>
                                    <span
                                        class="position-absolute top-0 right-0 bg-danger text-white rounded-circle small px-1">3</span>
                                </span> -->
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="d-none d-md-inline text-gray-700"><?php echo htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']) ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="container-fluid p-4">
                <!-- Dashboard Section -->
                <section id="dashboard-section">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Welcome, <span id="welcome-name"
                                    class="text-primary"><?php echo htmlspecialchars($_SESSION['firstName']) ?></span>!</h2>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card stat-card courses">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div><?php
                                                    $levels = [
    ['level' => '100', 'condition' => 'num_sem BETWEEN 1 AND 2'],
    ['level' => '200', 'condition' => 'num_sem BETWEEN 3 AND 4'],
    ['level' => '300', 'condition' => 'num_sem BETWEEN 5 AND 6'],
    ['level' => '400', 'condition' => 'num_sem BETWEEN 7 AND 8'],
    ['level' => 'Alumni',    'condition' => 'num_sem >= 9'],
];

$level = null; // default

foreach ($levels as $item) {
    $query = "SELECT 1 FROM tblstudents WHERE {$item['condition']} AND admissionNumber = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $studNo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $level = $item['level']; // ✅ assign the matching level
            $stmt->close();
            break; // stop once we find the level
        }
        $stmt->close();
    }
}
// $level now contains "Level 100", "Level 200", ..., or "Alumni"


$studProgram = $rows['program'];

// Get active semester
$activeSemester = mysqli_query($conn, "SELECT * FROM tblsemester WHERE IsActive = 1");
$semRow = mysqli_fetch_assoc($activeSemester);
$CurntSemId = $semRow['Id'];

// Query courses: match exact program OR any course where program contains the student's program (e.g. IT inside IT/IS)
// Also include general courses
$studCourses = mysqli_query(
    $conn,
    "SELECT * 
     FROM tblcourses 
     WHERE Level = '$level' 
       AND (program = '$studProgram' OR program LIKE '%$studProgram%' OR general = 1)"
);

// Fetch all courses
$numOfCourses = mysqli_num_rows($studCourses);


                                                    
?>
                                                    <p class="text-muted small mb-1">Total Courses</p>
                                                    <h3 class="h5 font-weight-bold"><?php echo $numOfCourses ?></h3>
                                                </div>
                                                <div class="bg-primary rounded-circle p-3">
                                                    <i class="fas fa-book text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card stat-card present">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="text-muted small mb-1">Attendance Rate</p>
                                                    <h3 class="h5 font-weight-bold">85%</h3>
                                                </div>
                                                <div class="bg-success rounded-circle p-3">
                                                    <i class="fas fa-check-circle text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card stat-card absent">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="text-muted small mb-1">Current Semester</p>
                                                    <h3 class="h5 font-weight-bold"><?php echo $semRow['semesterName'] ?></h3>
                                                </div>
                                                <div class="bg-warning rounded-circle p-3">
                                                    <i class="fas fa-clock text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Present Attendance</h3>
                                            <div class="list-group">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success rounded-circle p-2 mr-3">
                                                            <i class="fas fa-check text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Mathematics</p>
                                                            <small class="text-muted">Today, 10:30 AM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-success">Present</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-danger rounded-circle p-2 mr-3">
                                                            <i class="fas fa-times text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Physics</p>
                                                            <small class="text-muted">Yesterday, 2:00 PM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-danger">Absent</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success rounded-circle p-2 mr-3">
                                                            <i class="fas fa-check text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Chemistry</p>
                                                            <small class="text-muted">Monday, 9:00 AM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-success">Present</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Absent Attendance</h3>
                                            <div class="list-group">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle p-2 mr-3">
                                                            <i class="fas fa-book text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Biology</p>
                                                            <small class="text-muted">Tomorrow, 11:00 AM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-primary">Room 302</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-purple rounded-circle p-2 mr-3">
                                                            <i class="fas fa-book text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Computer Science</p>
                                                            <small class="text-muted">Tomorrow, 1:30 PM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-purple">Lab 4</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-warning rounded-circle p-2 mr-3">
                                                            <i class="fas fa-book text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">Mathematics</p>
                                                            <small class="text-muted">Tomorrow, 3:00 PM</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-warning">Room 205</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- QR Scanner Section -->
                <section id="qr-scanner-section" class="d-none">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">QR Code Scanner</h2>
                            <div class="text-center">
                                <div id="qr-reader" class="mb-4 mx-auto"></div>
                                <div id="qr-reader-results" class="card mb-4 p-4 d-none">
                                    <h3 class="h5 font-weight-bold text-gray-800 mb-2">Scan Result:</h3>
                                    <p id="scan-result" class="text-muted mb-3"></p>
                                    <div class="d-flex justify-content-center">
                                        <button id="scan-again-btn" class="btn btn-primary mr-2">
                                            Scan Again
                                        </button>
                                        <button id="close-scan-btn" class="btn btn-secondary">
                                            Close
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button id="start-scan-btn" class="btn btn-primary mr-2">
                                        Start Scanning
                                    </button>
                                    <button id="stop-scan-btn" class="btn btn-danger d-none">
                                        Stop Scanning
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- My Courses Section -->
              <section id="courses-section" class="d-none">
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h4 font-weight-bold text-gray-800 mb-4">My Courses</h2>
            <div class="row">
                <?php 
                $studCourses = mysqli_query(
                    $conn,
                    "SELECT * 
                     FROM tblcourses 
                     WHERE Level = '$level' 
                       AND (program = '$studProgram' OR program LIKE '%$studProgram%' OR general = 1)"
                );

                if ($studCourses && mysqli_num_rows($studCourses) > 0) {
                    while ($courDetails = mysqli_fetch_assoc($studCourses)) {
                        $lecId = $courDetails['lecturer_id'];
                        $lecturer = "TBA";

                        // Fetch lecturer
                        $lecturerQuery = mysqli_query($conn, "SELECT * FROM tblmoderator WHERE Id ='$lecId'");
                        if ($lecturerQuery && mysqli_num_rows($lecturerQuery) > 0) {
                            $lecturerfetch = mysqli_fetch_assoc($lecturerQuery);
                            $lecturer = $lecturerfetch['firstName'] . " " . $lecturerfetch['lastName'];
                        }
                ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="h5 font-weight-bold">
                                            <?php echo htmlspecialchars($courDetails['courseName']); ?>
                                        </h3>
                                        <span class="badge badge-primary">Active</span>
                                    </div>
                                    <p class="text-muted small mb-4">
                                        <?php echo htmlspecialchars($courDetails['courseCode']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 mr-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <span class="small"><?php echo htmlspecialchars($lecturer); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    } // end while
                } else {
                    echo "<div class='col-12'><p class='text-muted'>No courses available.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>
</section>


                <!-- Attendance History Section -->
                <section id="attendance-section" class="d-none">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Attendance History</h2>

                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h3 class="h5 font-weight-bold text-gray-800 mb-3">Filter Attendance</h3>
                                    <form>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold">Course</label>
                                                <select class="form-control">
                                                    <option value="">All Courses</option>
                                                    <option>Mathematics</option>
                                                    <option>Physics</option>
                                                    <option>Chemistry</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold">From Date</label>
                                                <input type="date" class="form-control">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold">To Date</label>
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                Apply Filter
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">
                                                Reset
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Attendance Statistics
                                            </h3>
                                            <canvas id="attendanceChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Attendance Records
                                            </h3>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th class="small font-weight-bold">Date</th>
                                                            <th class="small font-weight-bold">Course</th>
                                                            <th class="small font-weight-bold">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="small">2023-06-15</td>
                                                            <td class="small">Mathematics</td>
                                                            <td><span class="badge badge-success">Present</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="small">2023-06-14</td>
                                                            <td class="small">Physics</td>
                                                            <td><span class="badge badge-danger">Absent</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="small">2023-06-13</td>
                                                            <td class="small">Chemistry</td>
                                                            <td><span class="badge badge-success">Present</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Change Password Section -->
                <section id="change-password-section" class="d-none">
                    <div class="card mb-4 mx-auto" style="max-width: 800px;">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Change Password</h2>
                            <form id="change-password-form">
                                <div class="form-group">
                                    <label for="current-password" class="small font-weight-bold">Current
                                        Password</label>
                                    <div class="input-group">
                                        <input type="password" id="current-password" class="form-control" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="current-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new-password" class="small font-weight-bold">New Password</label>
                                    <div class="input-group">
                                        <input type="password" id="new-password" class="form-control" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="new-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Password must be at least 8 characters
                                        long</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password" class="small font-weight-bold">Confirm New
                                        Password</label>
                                    <div class="input-group">
                                        <input type="password" id="confirm-password" class="form-control" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="confirm-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="password-message" class="alert d-none mb-4"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    const mobileToggleSidebarBtn = document.getElementById('mobile-toggle-sidebar');
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        qrScanner: document.getElementById('qr-scanner-section'),
        courses: document.getElementById('courses-section'),
        attendance: document.getElementById('attendance-section'),
        changePassword: document.getElementById('change-password-section')
    };

    // QR Scanner Variables
    let html5QrCode;
    let qrScannerActive = false;

    // Initialize the dashboard
    $(document).ready(function() {
        // Initialize sidebar toggle
        toggleSidebarBtn.addEventListener('click', function() {
            $(sidebar).toggleClass('collapsed');
            $(content).toggleClass('expanded');

            if ($(sidebar).hasClass('collapsed')) {
                $(this).html('<i class="fas fa-chevron-right mr-1"></i> Expand');
            } else {
                $(this).html('<i class="fas fa-chevron-left mr-1"></i> Collapse');
            }
        });

        // Mobile sidebar toggle
        mobileToggleSidebarBtn.addEventListener('click', function() {
            $(sidebar).toggleClass('show');
        });

        // Navigation link click handlers
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all links
                navLinks.forEach(navLink => {
                    navLink.classList.remove('active');
                });

                // Add active class to clicked link
                this.classList.add('active');

                // Hide all sections
                Object.values(sections).forEach(section => {
                    $(section).addClass('d-none');
                });

                // Show the selected section
                const target = this.getAttribute('href').substring(1);
                if (target === 'dashboard') {
                    $(sections.dashboard).removeClass('d-none');
                    fetchRecentAttendance();
                } else if (target === 'qr-scanner') {
                    $(sections.qrScanner).removeClass('d-none');
                } else if (target === 'courses') {
                    $(sections.courses).removeClass('d-none');
                    fetchCourses();
                } else if (target === 'attendance') {
                    $(sections.attendance).removeClass('d-none');
                    initAttendanceChart();
                    fetchRecentAttendance();
                } else if (target === 'change-password') {
                    $(sections.changePassword).removeClass('d-none');
                }
            });
        });

        // Set dashboard as active by default
        document.querySelector('.nav-link.active').click();



        // Fetch and update attendance chart with real data
        function fetchAttendanceData(fromDate = '', toDate = '') {
            $.ajax({
                url: 'fetch_attendance.php',
                method: 'GET',
                data: {
                    from: fromDate,
                    to: toDate
                },
                dataType: 'json',
                success: function(data) {
                    if (window.attendanceChart) {
                        window.attendanceChart.data.labels = data.labels;
                        window.attendanceChart.data.datasets[0].data = data.present;
                        window.attendanceChart.data.datasets[1].data = data.absent;
                        window.attendanceChart.update();
                    }
                },
                error: function() {
                    console.error('Failed to fetch attendance data');
                }
            });
        }

        // Initialize attendance chart with empty data and store chart instance globally
        function initAttendanceChart() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            window.attendanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Present (%)',
                            data: [],
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Absent (%)',
                            data: [],
                            borderColor: 'rgba(220, 53, 69, 1)',
                            backgroundColor: 'rgba(220, 53, 69, 0.2)',
                            tension: 0.1,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
            // Fetch initial attendance data
            fetchAttendanceData();
        }

        // Fetch and display courses dynamically
        function fetchCourses() {
            $.ajax({
                url: 'fetch_courses.php',
                method: 'GET',
                dataType: 'json',
                success: function(courses) {
                    const coursesSection = $('#courses-section .row');
                    coursesSection.empty();
                    if (courses.length === 0) {
                        coursesSection.append('<p>No courses found.</p>');
                        return;
                    }
                    courses.forEach(course => {
                        const courseCard = `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="h5 font-weight-bold">${course.name}</h3>
                                        <span class="badge badge-primary">${course.status}</span>
                                    </div>
                                    <p class="text-muted small mb-4">${course.code}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 mr-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <span class="small">${course.instructor}</span>
                                        </div>
                                        <span class="text-muted small">${course.schedule}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        coursesSection.append(courseCard);
                    });
                },
                error: function() {
                    console.error('Failed to fetch courses');
                }
            });
        }

        // Fetch and display recent attendance records dynamically
        function fetchRecentAttendance() {
            $.ajax({
                url: 'fetch_recent_attendance.php',
                method: 'GET',
                dataType: 'json',
                success: function(records) {
                    // Update attendance section table
                    const recentAttendanceTableBody = $('#attendance-section tbody');
                    recentAttendanceTableBody.empty();
                    if (records.length === 0) {
                        recentAttendanceTableBody.append(
                            '<tr><td colspan="3">No attendance records found.</td></tr>');
                    } else {
                        records.forEach(record => {
                            const statusBadge = record.status === '1' ?
                                '<span class="badge badge-success">Present</span>' :
                                '<span class="badge badge-danger">Absent</span>';
                            const row = `
                            <tr>
                                <td class="small">${record.date}</td>
                                <td class="small">${record.course}</td>
                                <td>${statusBadge}</td>
                            </tr>`;
                            recentAttendanceTableBody.append(row);
                        });
                    }

                    // Update dashboard recent attendance list
                    const dashboardAttendanceList = $('#dashboard-section .list-group');
                    dashboardAttendanceList.empty();
                    if (records.length === 0) {
                        dashboardAttendanceList.append(
                            '<div class="list-group-item text-muted">No recent attendance records found.</div>'
                        );
                    } else {
                        // Show only the first 3 records for dashboard
                        const displayRecords = records.slice(0, 3);
                        displayRecords.forEach(record => {
                            const statusClass = record.status === '1' ? 'success' :
                                'danger';
                            const statusIcon = record.status === '1' ? 'fa-check' :
                                'fa-times';
                            const statusText = record.status === '1' ? 'Present' : 'Absent';
                            const badgeClass = record.status === '1' ? 'badge-success' :
                                'badge-danger';

                            const listItem = `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-${statusClass} rounded-circle p-2 mr-3">
                                        <i class="fas ${statusIcon} text-white small"></i>
                                    </div>
                                    <div>
                                        <p class="font-weight-bold mb-0">${record.course}</p>
                                        <small class="text-muted">${record.date}</small>
                                    </div>
                                </div>
                                <span class="badge ${badgeClass}">${statusText}</span>
                            </div>`;
                            dashboardAttendanceList.append(listItem);
                        });
                    }
                },
                error: function() {
                    console.error('Failed to fetch recent attendance records');
                }
            });
        }

        // Change password form submission via AJAX
        $('#change-password-form').submit(function(e) {
            e.preventDefault();

            const currentPassword = $('#current-password').val();
            const newPassword = $('#new-password').val();
            const confirmPassword = $('#confirm-password').val();
            const messageDiv = $('#password-message');

            // Reset message
            messageDiv.addClass('d-none').removeClass('alert-success alert-danger');

            // Validate passwords
            if (newPassword !== confirmPassword) {
                showMessage('Passwords do not match', 'error');
                return;
            }

            if (newPassword.length < 8) {
                showMessage('Password must be at least 8 characters long', 'error');
                return;
            }

            $.ajax({
                url: 'change_password.php',
                method: 'POST',
                data: {
                    currentPassword: currentPassword,
                    newPassword: newPassword,
                    confirmNewPassword: confirmPassword
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage(response.message, 'success');
                        $('#change-password-form')[0].reset();
                    } else {
                        showMessage(response.message, 'error');
                    }
                },
                error: function() {
                    showMessage('Failed to change password. Please try again.', 'error');
                }
            });
        });

        // Logout button
        $('#logout-btn').click(function() {
            alert('You have been logged out.  This would redirect to the login page.');
             window.location = (\"../index.php\");
        });

        // QR Scanner integration with mark_attendance.php
        function initQRScanner() {
            const qrReader = document.getElementById('qr-reader');
            const qrReaderResults = $('#qr-reader-results');
            const scanResult = $('#scan-result');
            const startScanBtn = $('#start-scan-btn');
            const stopScanBtn = $('#stop-scan-btn');
            const scanAgainBtn = $('#scan-again-btn');
            const closeScanBtn = $('#close-scan-btn');

            let html5QrCode;
            let qrScannerActive = false;

            // Start scan button
            startScanBtn.click(function() {
                if (qrScannerActive) return;

                html5QrCode = new Html5Qrcode("qr-reader");
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    handleScanSuccess(decodedText, decodedResult);
                };

                const config = {
                    fps: 10,
                    qrbox: 250
                };

                html5QrCode.start({
                        facingMode: "environment"
                    },
                    config,
                    qrCodeSuccessCallback,
                    () => {} // No verbose logging
                ).then(() => {
                    qrScannerActive = true;
                    startScanBtn.addClass('d-none');
                    stopScanBtn.removeClass('d-none');
                    qrReaderResults.addClass('d-none');
                }).catch(err => {
                    console.error("Error starting QR scanner:", err);
                    alert(
                        "Could not start QR scanner. Please ensure camera access is allowed."
                    );
                });
            });

            // Stop scan button
            stopScanBtn.click(function() {
                if (!qrScannerActive) return;

                html5QrCode.stop().then(() => {
                    qrScannerActive = false;
                    stopScanBtn.addClass('d-none');
                    startScanBtn.removeClass('d-none');
                }).catch(err => {
                    console.error("Error stopping QR scanner:", err);
                });
            });

            // Scan again button
            scanAgainBtn.click(function() {
                qrReaderResults.addClass('d-none');
                startScanBtn.click();
            });

            // Close scan button
            closeScanBtn.click(function() {
                if (qrScannerActive) {
                    html5QrCode.stop().then(() => {
                        qrScannerActive = false;
                    }).catch(err => {
                        console.error("Error stopping scanner:", err);
                    });
                }
                qrReaderResults.addClass('d-none');
                stopScanBtn.addClass('d-none');
                startScanBtn.removeClass('d-none');
            });

            // Handle scan success
            function handleScanSuccess(decodedText, decodedResult) {
                html5QrCode.stop().then(() => {
                    qrScannerActive = false;
                    stopScanBtn.addClass('d-none');
                    startScanBtn.removeClass('d-none');

                    scanResult.text(decodedText);
                    qrReaderResults.removeClass('d-none');

                    console.log("QR Code scanned:", decodedText);

                    // Call mark_attendance.php to mark attendance
                    $.ajax({
                        url: 'mark_attendance.php',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            qrCode: decodedText
                        }),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // Refresh attendance data and recent attendance list
                                fetchAttendanceData();
                                fetchRecentAttendance();
                            } else {
                                alert('Failed to mark attendance: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error marking attendance. Please try again.');
                        }
                    });
                }).catch(err => {
                    console.error("Error stopping scanner after success:", err);
                });
            }
        }


    });
    </script>
</body>

</html>