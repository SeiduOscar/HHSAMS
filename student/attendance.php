<?php

session_start();

if (!isset($_SESSION['admissionNumber'])) {
    if (isset($_GET['action'])) {
        // For AJAX requests, return JSON error
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    } else {
        header("Location: ../index.php");
        exit();
    }
}

include '../Includes/dbcon.php';

// Handle AJAX requests for attendance data
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'fetch_attendance') {
        // Fetch attendance statistics for chart
        $admissionNumber = $_SESSION['admissionNumber'];
        $fromDate = $_GET['from'] ?? '';
        $toDate = $_GET['to'] ?? '';
        $course = $_GET['course'] ?? '';

        // Build query with date and course filters
        $sql = "SELECT DATE(dateTimeTaken) as date,
                       SUM(status = '1') as present,
                       SUM(status = '0') as absent
                FROM tblattendance
                WHERE admissionNo = ?";

        $params = [$admissionNumber];
        $types = "s";

        if (!empty($course)) {
            $sql .= " AND courseCode = ?";
            $params[] = $course;
            $types .= "s";
        }

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND DATE(dateTimeTaken) BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate;
            $types .= "ss";
        }

        $sql .= " GROUP BY DATE(dateTimeTaken) ORDER BY date";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $labels = [];
        $presentData = [];
        $absentData = [];

        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['date'];
            $total = (int) $row['present'] + (int) $row['absent'];
            if ($total > 0) {
                $presentData[] = round(((int) $row['present'] / $total) * 100, 2);
                $absentData[] = round(((int) $row['absent'] / $total) * 100, 2);
            } else {
                $presentData[] = 0;
                $absentData[] = 0;
            }
        }

        // If no data found, return empty arrays
        if (empty($labels)) {
            $labels = ['No Data'];
            $presentData = [0];
            $absentData = [0];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => $labels,
            'present' => $presentData,
            'absent' => $absentData
        ]);
        exit();

    } elseif ($action === 'fetch_recent') {
        // Fetch recent attendance records
        $admissionNumber = $_SESSION['admissionNumber'];
        $fromDate = $_GET['from'] ?? '';
        $toDate = $_GET['to'] ?? '';
        $course = $_GET['course'] ?? '';

        // Build query with filters
        $sql = "SELECT a.dateTimeTaken, a.status, c.courseName
                FROM tblattendance a
                LEFT JOIN tblcourses c ON a.courseCode = c.courseCode
                WHERE a.admissionNo = ?";

        $params = [$admissionNumber];
        $types = "s";

        if (!empty($course)) {
            $sql .= " AND a.courseCode = ?";
            $params[] = $course;
            $types .= "s";
        }

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND DATE(a.dateTimeTaken) BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate;
            $types .= "ss";
        }

        $sql .= " ORDER BY a.dateTimeTaken DESC LIMIT 10";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = [
                'date' => date('Y-m-d', strtotime($row['dateTimeTaken'])),
                'course' => $row['courseName'] ?? 'General',
                'status' => $row['status'] == '1' ? 'Present' : 'Absent'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($records);
        exit();
    }
}

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

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
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
                <!-- Attendance History Section -->
                <section id="attendance-section">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Attendance History</h2>

                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h3 class="h5 font-weight-bold text-gray-800 mb-3">Filter Attendance</h3>
                                    <form id="filterForm">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold" for="courseSelect">Course</label>
                                                <select id="courseSelect" name="course" class="form-control">
                                                    <option value=''>All Courses</option>

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
                                                    
                                                    // Get student program
                                                    $studQuery = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber = '$studNo'");
                                                    $rows = mysqli_fetch_assoc($studQuery);
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
                                                            ;

                                                            echo "
                                                    <option value='$courDetails[courseCode]'>$courDetails[courseName]</option>
                                                    ";
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold" for="fromDate">From Date</label>
                                                <input type="date" id="fromDate" name="from" class="form-control">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="small font-weight-bold" for="toDate">To Date</label>
                                                <input type="date" id="toDate" name="to" class="form-control">
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                Apply Filter
                                            </button>
                                            <button type="reset" id="resetBtn" class="btn btn-outline-secondary">
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
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Attendance
                                                Records
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
                                                    <tbody id="recentAttendanceBody">
                                                        <!-- Dynamic rows will be inserted here -->
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
            </main>

        </div>

        <script>
            // DOM Elements
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content-wrapper');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Initialize the page
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

                // Initialize attendance chart and fetch data
                initAttendanceChart();
                fetchRecentAttendance();

                // Fetch and update attendance chart with real data
                function fetchAttendanceData(fromDate = '', toDate = '', course = '') {
                    $.ajax({
                        url: 'attendance.php?action=fetch_attendance',
                        method: 'GET',
                        data: {
                            from: fromDate,
                            to: toDate,
                            course: course
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
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Present (%)',
                                data: [],
                                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Absent (%)',
                                data: [],
                                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
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



                // Fetch and display recent attendance records dynamically
                function fetchRecentAttendance(fromDate = '', toDate = '', course = '') {
                    $.ajax({
                        url: 'attendance.php?action=fetch_recent',
                        method: 'GET',
                        data: {
                            from: fromDate,
                            to: toDate,
                            course: course
                        },
                        dataType: 'json',
                        success: function (records) {
                            // Update attendance section table
                            const recentAttendanceTableBody = $('#recentAttendanceBody');
                            recentAttendanceTableBody.empty();
                            if (records.length === 0) {
                                recentAttendanceTableBody.append(
                                    '<tr><td colspan="3">No attendance records found.</td></tr>'
                                );
                            } else {
                                records.forEach(record => {
                                    const statusBadge = record.status === 'Present' ?
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
                        },
                        error: function () {
                            console.error('Failed to fetch recent attendance records');
                        }
                    });
                }

                // Filter form submission handler
                $('#filterForm').submit(function (e) {
                    e.preventDefault();
                    const course = $('#courseSelect').val();
                    const fromDate = $('#fromDate').val();
                    const toDate = $('#toDate').val();

                    fetchAttendanceData(fromDate, toDate, course);
                    fetchRecentAttendance(fromDate, toDate, course);
                });

                // Reset button handler
                $('#resetBtn').click(function () {
                    $('#courseSelect').val('');
                    $('#fromDate').val('');
                    $('#toDate').val('');
                    fetchAttendanceData();
                    fetchRecentAttendance();
                });

                // Logout button
                $('#logout-btn').click(function () {
                    alert('You have been logged out.  This would redirect to the login page.');
                    window.location = ("../index.php");
                });
            });
        </script>
</body>

</html>