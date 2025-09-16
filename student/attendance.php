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
        // Fetch recent attendance records (for table)
        $admissionNumber = $_SESSION['admissionNumber'];
        $fromDate = $_GET['from'] ?? '';
        $toDate = $_GET['to'] ?? '';
        $course = $_GET['course'] ?? '';

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
        $sql .= " ORDER BY a.dateTimeTaken DESC LIMIT 20";
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
    } elseif ($action === 'export_csv') {
        // Export attendance as CSV with filters
        $admissionNumber = $_SESSION['admissionNumber'];
        $fromDate = $_GET['from'] ?? '';
        $toDate = $_GET['to'] ?? '';
        $course = $_GET['course'] ?? '';

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
        $sql .= " ORDER BY a.dateTimeTaken DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $filename = "Attendance_" . date("Y-m-d") . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Course', 'Status']);
        while ($row = $result->fetch_assoc()) {
            // Output date as text for Excel (prepend tab) to avoid ######
            $excelDate = "\t" . date('Y-m-d', strtotime($row['dateTimeTaken']));
            fputcsv($output, [
                $excelDate,
                $row['courseName'] ?? 'General',
                $row['status'] == '1' ? 'Present' : 'Absent'
            ]);
        }
        fclose($output);
        exit();
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Student Attendance History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../Admin/css/sidebar-fix.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
        z-index: 1050;
        left: 0;
        top: 0;
        background: #343a40;
    }

    .content {
        margin-left: 250px;
        transition: all 0.3s;
    }

    .sidebar.toggled {
        margin-left: -250px;
    }

    .sidebar-overlay {
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1049;
        display: none;
    }

    .sidebar-open .sidebar-overlay {
        display: block;
    }

    .sidebar-open .sidebar {
        margin-left: 0 !important;
    }

    .sidebar-open .content {
        margin-left: 0 !important;
    }

    #sidebarToggleTop {
        display: none;
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1100;
        background-color: #343a40;
        border: none;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
            width: 250px;
        }

        .sidebar.toggled {
            margin-left: 0;
        }

        .content {
            margin-left: 0;
        }

        #sidebarToggleTop {
            display: block;
        }
    }
    </style>
</head>

<body>
    <button id="sidebarToggleTop" type="button" class="btn btn-link d-md-none rounded-circle mr-3"
        style="position:fixed;top:0px;right:10px;left:auto;z-index:1100;background:#343a40;color:#fff;">
        <i class="fa fa-bars"></i>
    </button>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar bg-dark text-white">
            <?php include 'includes/sidebar.php'; ?>
        </nav>
        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
        <!-- Main Content -->
        <div id="content-wrapper" class="content">
            <!-- Top Navigation -->
            <?php include './includes/topbar.php' ?>
            <main class="container-fluid p-3 p-md-4">
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
                                            <button type="button" id="exportBtn" class="btn btn-success mr-2">
                                                Export CSV
                                            </button>
                                            <button type="reset" id="resetBtn" class="btn btn-outline-secondary">
                                                Reset
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Attendance Statistics
                                            </h3>
                                            <canvas id="attendanceChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Attendance
                                                Records
                                            </h3>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover table-sm">
                                                    <thead class="thead-dark">
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
        // Attendance chart and table logic only (sidebar toggle handled by sidebar-toggle.js)
        $(document).ready(function() {
            // Initialize attendance chart and fetch data
            initAttendanceChart();
            fetchRecentAttendance();

            // Fetch and update attendance chart with real data (histogram/bar chart)
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
                    success: function(data) {
                        if (window.attendanceChart) {
                            window.attendanceChart.data.labels = data.labels;
                            window.attendanceChart.data.datasets[0].data = data.present;
                            window.attendanceChart.data.datasets[1].data = data.absent.map(function(
                                val) {
                                return (val === 0 ? 0.01 : val);
                            }); // Ensure absent is visible
                            window.attendanceChart.update();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error fetching attendance: ' + textStatus + '\n' + jqXHR
                            .responseText);
                    }
                });
            }
            // Export CSV button handler
            $('#exportBtn').click(function() {
                const course = $('#courseSelect').val();
                const fromDate = $('#fromDate').val();
                const toDate = $('#toDate').val();
                let url = 'attendance.php?action=export_csv';
                let params = [];
                if (course) params.push('course=' + encodeURIComponent(course));
                if (fromDate) params.push('from=' + encodeURIComponent(fromDate));
                if (toDate) params.push('to=' + encodeURIComponent(toDate));
                if (params.length > 0) url += '&' + params.join('&');
                window.location = url;
            });

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
                                borderWidth: 2,
                                minBarLength: 2 // Always show a bar for absent
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
                    success: function(records) {
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
                    error: function() {
                        console.error('Failed to fetch recent attendance records');
                    }
                });
            }

            // Filter form submission handler
            $('#filterForm').submit(function(e) {
                e.preventDefault();
                const course = $('#courseSelect').val();
                const fromDate = $('#fromDate').val();
                const toDate = $('#toDate').val();

                fetchAttendanceData(fromDate, toDate, course);
                fetchRecentAttendance(fromDate, toDate, course);
            });

            // Also update chart and table when date fields change directly
            $('#fromDate, #toDate, #courseSelect').on('change', function() {
                const course = $('#courseSelect').val();
                const fromDate = $('#fromDate').val();
                const toDate = $('#toDate').val();
                fetchAttendanceData(fromDate, toDate, course);
                fetchRecentAttendance(fromDate, toDate, course);
            });

            // Reset button handler
            $('#resetBtn').click(function() {
                $('#courseSelect').val('');
                $('#fromDate').val('');
                $('#toDate').val('');
                fetchAttendanceData();
                fetchRecentAttendance();
            });

            // Logout button
            $('#logout-btn').click(function() {
                alert('You have been logged out.  This would redirect to the login page.');
                window.location = ("../index.php");
            });
        });
        </script>
</body>

</html>