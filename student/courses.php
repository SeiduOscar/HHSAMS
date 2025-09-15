<?php

session_start();


if (!isset($_SESSION['admissionNumber'])) {
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
    <link rel="stylesheet" href="../Admin/css/sidebar-fix.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="../Admin/js/sidebar-toggle.js"></script>
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

        /* SB Admin 2 Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar.toggled {
            width: 0;
            overflow: hidden;
        }

        .content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        .sidebar-hidden {
            margin-left: 0 !important;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.toggled {
                margin-left: 0;
            }

            .content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-toggled .sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar bg-dark text-white">
            <?php include 'includes/sidebar.php'; ?>

        </nav>
        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay"></div>

        <!-- Main Content -->
        <div id="content-wrapper" class="content">
            <!-- Top Navigation -->
            <?php include './includes/topbar.php' ?>

            <main class="container-fluid p-4">
                <!-- My Courses Section -->
                <section id="courses-section" class="">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">My Courses</h2>
                            <div class="row">
                                <?php
                                $studNo = $_SESSION['admissionNumber'];
                                $levels = [
                                    ['level' => '100', 'condition' => 'num_sem BETWEEN 1 AND 2'],
                                    ['level' => '200', 'condition' => 'num_sem BETWEEN 3 AND 4'],
                                    ['level' => '300', 'condition' => 'num_sem BETWEEN 5 AND 6'],
                                    ['level' => '400', 'condition' => 'num_sem BETWEEN 7 AND 8'],
                                    ['level' => 'Alumni', 'condition' => 'num_sem >= 9'],
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
                                                            <span
                                                                class="small"><?php echo htmlspecialchars($lecturer); ?></span>
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
            </main>

        </div>

        <script>
            // DOM Elements
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content-wrapper');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = {
                'dashboard': document.getElementById('dashboard-section'),
                'qr-scanner-section': document.getElementById('qr-scanner-section'),
                'courses': document.getElementById('courses-section'),
                'attendance-section': document.getElementById('attendance-section'),
                'change-password-section': document.getElementById('change-password-section')
            };

            // QR Scanner Variables
            let html5QrCode;
            let qrScannerActive = false;

            // Initialize the dashboard
            $(document).ready(function () {
                // SB Admin 2 style sidebar toggle
                if (sidebarToggleTop) {
                    sidebarToggleTop.addEventListener('click', function (e) {
                        e.preventDefault();
                        $('body').toggleClass('sidebar-toggled');
                        $('.sidebar').toggleClass('toggled');

                        if ($('.sidebar').hasClass('toggled')) {
                            $('#content-wrapper').addClass('sidebar-hidden');
                        } else {
                            $('#content-wrapper').removeClass('sidebar-hidden');
                        }
                    });
                }

                // Close sidebar when clicking overlay
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', function () {
                        $('body').removeClass('sidebar-toggled');
                        $('.sidebar').removeClass('toggled');
                        $('#content-wrapper').removeClass('sidebar-hidden');
                    });
                }

                // Close sidebar on window resize if on mobile
                $(window).resize(function () {
                    if ($(window).width() >= 768) {
                        $('body').removeClass('sidebar-toggled');
                        $('.sidebar').removeClass('toggled');
                        $('#content-wrapper').removeClass('sidebar-hidden');
                    }
                });

                // Navigation link click handlers
                navLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        const href = this.getAttribute('href');
                        if (!href.startsWith('#')) {
                            return; // let it navigate
                        }
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
                        const target = href.substring(1);
                        $(sections[target]).removeClass('d-none');

                        // Specific actions for each section
                        if (target === 'dashboard') {
                            fetchRecentAttendance();
                        } else if (target === 'qr-scanner-section') {
                            // Delay initialization to ensure section is visible and rendered
                            setTimeout(() => {
                                console.log('Initializing QR Scanner...');
                                initQRScanner
                                    (); // Initialize QR scanner when showing the section
                            }, 500); // Increased delay
                        } else if (target === 'courses') {
                            fetchCourses();
                        } else if (target === 'attendance-section') {
                            initAttendanceChart();
                            fetchRecentAttendance();
                        }
                    });
                });

                // Set dashboard as active by default
                if (document.querySelector('.nav-link.active')) {
                    document.querySelector('.nav-link.active').click();
                }
                // Call fetchCourses on page load to populate courses section
                fetchCourses();



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
                        success: function (data) {
                            if (window.attendanceChart) {
                                window.attendanceChart.data.labels = data.labels;
                                window.attendanceChart.data.datasets[0].data = data.present;
                                window.attendanceChart.data.datasets[1].data = data.absent;
                                window.attendanceChart.update();
                            }
                        },
                        error: function () {
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
                                        callback: function (value) {
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
                        success: function (courses) {
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
                        error: function () {
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
                        success: function (records) {
                            // Update attendance section table
                            const recentAttendanceTableBody = $('#attendance-section tbody');
                            recentAttendanceTableBody.empty();
                            if (records.length === 0) {
                                recentAttendanceTableBody.append(
                                    '<tr><td colspan="3">No attendance records found.</td></tr>'
                                );
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
                                    const statusText = record.status === '1' ? 'Present' :
                                        'Absent';
                                    const badgeClass = record.status === '1' ?
                                        'badge-success' :
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
                        error: function () {
                            console.error('Failed to fetch recent attendance records');
                        }
                    });
                }

                // Change password form submission via AJAX
                $('#change-password-form').submit(function (e) {
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
                        success: function (response) {
                            if (response.success) {
                                showMessage(response.message, 'success');
                                $('#change-password-form')[0].reset();
                            } else {
                                showMessage(response.message, 'error');
                            }
                        },
                        error: function () {
                            showMessage('Failed to change password. Please try again.',
                                'error');
                        }
                    });
                });

                // Logout button
                $('#logout-btn').click(function () {
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
                    startScanBtn.click(function () {
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
                            () => { } // No verbose logging
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
                    stopScanBtn.click(function () {
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
                    scanAgainBtn.click(function () {
                        qrReaderResults.addClass('d-none');
                        startScanBtn.click();
                    });

                    // Close scan button
                    closeScanBtn.click(function () {
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

                            // Check if decodedText is a URL
                            const isUrl = /^https?:\/\/[^\s/$.?#].[^\s]*$/i.test(decodedText);
                            const linkContainer = $('#link-container');
                            const scannedLink = $('#scanned-link');

                            if (isUrl) {
                                scannedLink.attr('href', decodedText);
                                scannedLink.text(decodedText);
                                scannedLink.attr('target', '_blank');
                                linkContainer.removeClass('d-none');
                            } else {
                                linkContainer.addClass('d-none');
                            }

                            // Call mark_attendance.php to mark attendance
                            $.ajax({
                                url: 'mark_attendance.php',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    qrCode: decodedText
                                }),
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        alert(response.message);
                                        // Refresh attendance data and recent attendance list
                                        fetchAttendanceData();
                                        fetchRecentAttendance();
                                    } else {
                                        alert('Failed to mark attendance: ' +
                                            response
                                                .message);
                                    }
                                },
                                error: function () {
                                    alert(
                                        'Error marking attendance. Please try again.'
                                    );
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