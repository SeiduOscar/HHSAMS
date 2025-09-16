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
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../Admin/css/sidebar-fix.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

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

    /* Desktop Styles */
    @media (min-width: 769px) {
        .sidebar.toggled {
            width: 0;
            overflow: hidden;
        }
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
            width: 250px;
        }

        .sidebar.toggled {
            margin-left: 0;
            width: 250px;
            /* Ensure width is restored on mobile */
            overflow: visible;

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

    /* Sidebar toggle button */
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

    /* Show toggle button on small screens */
    @media (max-width: 767.98px) {
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
            <?php include './includes/sidebar.php' ?>
        </nav>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <!-- Main Content -->
        <div id="content-wrapper" class="content">
            <!-- Top Navigation -->
            <?php include './includes/topbar.php' ?>

            <main class="container-fluid p-3 p-md-4">

                <!-- Dashboard Section -->
                <section id="dashboard-section">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Welcome, <span id="welcome-name"
                                    class="text-primary"><?php echo htmlspecialchars($_SESSION['firstName']) ?></span>!
                            </h2>
                            <div class="row mb-4">
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card stat-card courses h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <?php
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
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card stat-card present h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="text-muted small mb-1">Attendance Rate</p>
                                                    <h3 class="h5 font-weight-bold">
                                                        <?php
                                                        $studNo = $_SESSION['admissionNumber'];

                                                        // Get current active semester
                                                        $activeSemesterQuery = "SELECT Id FROM tblsemester WHERE IsActive = 1 LIMIT 1";
                                                        $activeSemesterResult = $conn->query($activeSemesterQuery);
                                                        $activeSemesterRow = $activeSemesterResult->fetch_assoc();
                                                        $activeSemesterId = $activeSemesterRow['Id'];

                                                        // Get total attendance records for the student in active semester
                                                        $totalQuery = "SELECT COUNT(*) as total FROM tblattendance WHERE admissionNo = ? AND semester IN (SELECT Id FROM tblsemester WHERE Id = ?)";
                                                        $stmtTotal = $conn->prepare($totalQuery);
                                                        $stmtTotal->bind_param("si", $studNo, $activeSemesterId);
                                                        $stmtTotal->execute();
                                                        $resultTotal = $stmtTotal->get_result();
                                                        $totalRow = $resultTotal->fetch_assoc();
                                                        $total = $totalRow['total'];

                                                        // Get present attendance records for the student in active semester
                                                        $presentQuery = "SELECT COUNT(*) as present FROM tblattendance WHERE admissionNo = ? AND status = 1 AND semester IN (SELECT Id FROM tblsemester WHERE Id = ?)";
                                                        $stmtPresent = $conn->prepare($presentQuery);
                                                        $stmtPresent->bind_param("si", $studNo, $activeSemesterId);
                                                        $stmtPresent->execute();
                                                        $resultPresent = $stmtPresent->get_result();
                                                        $presentRow = $resultPresent->fetch_assoc();
                                                        $present = $presentRow['present'];

                                                        $attendanceRate = 0;
                                                        if ($total > 0) {
                                                            $attendanceRate = round(($present / $total) * 100);
                                                        }

                                                        echo $attendanceRate . '%';
                                                        ?>
                                                    </h3>
                                                </div>
                                                <div class="bg-success rounded-circle p-3">
                                                    <i class="fas fa-check-circle text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card stat-card absent h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="text-muted small mb-1">Current Semester</p>
                                                    <h3 class="h5 font-weight-bold">
                                                        <?php echo $semRow['semesterName'] ?>
                                                    </h3>
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
                                <div class="col-12 col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Present Attendance
                                            </h3>
                                            <div class="list-group">
                                                <?php
                                                $studNo = $_SESSION['admissionNumber'];
                                                $activeSemesterQuery = "SELECT Id FROM tblsemester WHERE IsActive = 1 LIMIT 1";
                                                $activeSemesterResult = $conn->query($activeSemesterQuery);
                                                $activeSemesterRow = $activeSemesterResult->fetch_assoc();
                                                $activeSemesterId = $activeSemesterRow['Id'];

                                                $recentPresentQuery = "SELECT a.dateTimeTaken, c.courseCode FROM tblattendance a JOIN tblcourses c ON a.courseCode = c.courseCode WHERE a.admissionNo = ? AND a.status = 1 AND a.semester IN (SELECT Id FROM tblsemester WHERE Id = ?) ORDER BY a.dateTimeTaken DESC LIMIT 5";
                                                $stmtPresent = $conn->prepare($recentPresentQuery);
                                                $stmtPresent->bind_param("si", $studNo, $activeSemesterId);
                                                $stmtPresent->execute();
                                                $resultPresent = $stmtPresent->get_result();
                                                $recentPresent = $resultPresent->fetch_all(MYSQLI_ASSOC);

                                                foreach ($recentPresent as $present): ?>

                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success rounded-circle p-2 mr-3">
                                                            <i class="fas fa-check text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">
                                                                <?php

                                                                    $courNameQuery = mysqli_query($conn, "SELECT courseName FROM tblcourses WHERE courseCode = '" . $present['courseCode'] . "'");
                                                                    $PresentcourNameRow = mysqli_fetch_assoc($courNameQuery);

                                                                    echo htmlspecialchars($PresentcourNameRow['courseName']);
                                                                    ?>
                                                            </p>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars($present['dateTimeTaken']); ?></small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-success">Present</span>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($recentPresent)): ?>
                                                <div class="list-group-item text-muted">No recent present attendance
                                                    records found.</div>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6 mb-4">
                                    <div class="card h-100">

                                        <div class="card-body">
                                            <h3 class="h5 font-weight-bold text-gray-800 mb-3">Recent Absent Attendance
                                            </h3>
                                            <div class="list-group">
                                                <?php
                                                $studNo = $_SESSION['admissionNumber'];
                                                $activeSemesterQuery = "SELECT Id FROM tblsemester WHERE IsActive = 1 LIMIT 1";
                                                $activeSemesterResult = $conn->query($activeSemesterQuery);
                                                $activeSemesterRow = $activeSemesterResult->fetch_assoc();
                                                $activeSemesterId = $activeSemesterRow['Id'];

                                                $recentAbsentQuery = "SELECT a.dateTimeTaken, c.courseCode FROM tblattendance a JOIN tblcourses c ON a.courseCode = c.courseCode WHERE a.admissionNo = ? AND a.status = 0 AND a.semester IN (SELECT Id FROM tblsemester WHERE Id = ?) ORDER BY a.dateTimeTaken DESC LIMIT 5";
                                                $stmtAbsent = $conn->prepare($recentAbsentQuery);
                                                $stmtAbsent->bind_param("si", $studNo, $activeSemesterId);
                                                $stmtAbsent->execute();
                                                $resultAbsent = $stmtAbsent->get_result();
                                                $recentAbsent = $resultAbsent->fetch_all(MYSQLI_ASSOC);

                                                foreach ($recentAbsent as $absent): ?>

                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-danger rounded-circle p-2 mr-3">
                                                            <i class="fas fa-times text-white small"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-weight-bold mb-0">
                                                                <?php

                                                                    $courNameQuery = mysqli_query($conn, "SELECT courseName FROM tblcourses WHERE courseCode = '" . $absent['courseCode'] . "'");
                                                                    $courNameRow = mysqli_fetch_assoc($courNameQuery);

                                                                    echo htmlspecialchars($courNameRow['courseName']); ?>

                                                            </p>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars($absent['dateTimeTaken']); ?></small>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-danger">Absent</span>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($recentAbsent)): ?>
                                                <div class="list-group-item text-muted">No recent absent attendance
                                                    records found.</div>

                                                <?php endif; ?>
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
    </div>

</body>



</html>