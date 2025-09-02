<?php

if (!isset($_SESSION['studentId'])) {
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
                            <?php echo htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']); ?>
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
                    <a href="#" id="logout-btn" class="nav-link d-flex align-items-center text-muted rounded">
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
                                <span class="position-relative">
                                    <i class="fas fa-bell text-gray-500"></i>
                                    <span
                                        class="position-absolute top-0 right-0 bg-danger text-white rounded-circle small px-1">3</span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <img src="https://via.placeholder.com/40" alt="Profile" class="rounded-circle mr-2"
                                    width="30">
                                <span class="d-none d-md-inline text-gray-700">John</span>
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
                                    class="text-primary">John</span>!</h2>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card stat-card courses">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="text-muted small mb-1">Total Courses</p>
                                                    <h3 class="h5 font-weight-bold">5</h3>
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
                                                    <p class="text-muted small mb-1">Recent Activity</p>
                                                    <h3 class="h5 font-weight-bold">3</h3>
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
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Attendance</h3>
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
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Upcoming Classes</h3>
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
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card course-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h5 font-weight-bold">Mathematics</h3>
                                                <span class="badge badge-primary">Active</span>
                                            </div>
                                            <p class="text-muted small mb-4">MATH101</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 mr-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <span class="small">Prof. Smith</span>
                                                </div>
                                                <span class="text-muted small">Mon, Wed, Fri</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card course-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h5 font-weight-bold">Physics</h3>
                                                <span class="badge badge-primary">Active</span>
                                            </div>
                                            <p class="text-muted small mb-4">PHYS201</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 mr-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <span class="small">Dr. Johnson</span>
                                                </div>
                                                <span class="text-muted small">Tue, Thu</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card course-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h5 font-weight-bold">Chemistry</h3>
                                                <span class="badge badge-primary">Active</span>
                                            </div>
                                            <p class="text-muted small mb-4">CHEM101</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 mr-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <span class="small">Prof. Williams</span>
                                                </div>
                                                <span class="text-muted small">Mon, Wed</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                } else if (target === 'qr-scanner') {
                    $(sections.qrScanner).removeClass('d-none');
                } else if (target === 'courses') {
                    $(sections.courses).removeClass('d-none');
                } else if (target === 'attendance') {
                    $(sections.attendance).removeClass('d-none');
                    initAttendanceChart();
                } else if (target === 'change-password') {
                    $(sections.changePassword).removeClass('d-none');
                }
            });
        });

        // Set dashboard as active by default
        document.querySelector('.nav-link.active').click();

        // Initialize QR Scanner
        initQRScanner();

        // Initialize attendance chart
        initAttendanceChart();

        // Password toggle functionality
        $('.toggle-password').click(function() {
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Change password form submission
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

            // Simulate AJAX call
            setTimeout(() => {
                showMessage('Password changed successfully!', 'success');
                $('#change-password-form')[0].reset();
            }, 1000);
        });

        // Logout button
        $('#logout-btn').click(function() {
            alert('You have been logged out. In a real app, this would redirect to the login page.');
        });
    });

    // Initialize QR Scanner
    function initQRScanner() {
        const qrReader = document.getElementById('qr-reader');
        const qrReaderResults = $('#qr-reader-results');
        const scanResult = $('#scan-result');
        const startScanBtn = $('#start-scan-btn');
        const stopScanBtn = $('#stop-scan-btn');
        const scanAgainBtn = $('#scan-again-btn');
        const closeScanBtn = $('#close-scan-btn');

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
                alert("Could not start QR scanner. Please ensure camera access is allowed.");
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

                // Simulate attendance marking
                setTimeout(() => {
                    alert(`Attendance marked successfully for: ${decodedText}`);
                }, 500);
            }).catch(err => {
                console.error("Error stopping scanner after success:", err);
            });
        }
    }

    // Initialize Attendance Chart
    function initAttendanceChart() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        // Sample data
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const presentData = [85, 79, 92, 88, 91, 87];
        const absentData = [15, 21, 8, 12, 9, 13];

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Present (%)',
                        data: presentData,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Absent (%)',
                        data: absentData,
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
    }

    // Show message in change password form
    function showMessage(message, type) {
        const messageDiv = $('#password-message');
        messageDiv.text(message).removeClass('d-none');

        if (type === 'error') {
            messageDiv.removeClass('alert-success').addClass('alert-danger');
        } else {
            messageDiv.removeClass('alert-danger').addClass('alert-success');
        }
    }
    </script>
</body>

</html>