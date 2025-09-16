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
    <title>Student Courses</title>
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
    <!-- <button id="sidebarToggleTop" type="button" class="btn btn-link d-md-none rounded-circle mr-3"
        style="position:fixed;top:10px;left:10px;z-index:1100;background:#343a40;color:#fff;">
        <i class="fa fa-bars"></i>
    </button> -->
    <button id="sidebarToggleTop" type="button" class="btn btn-link d-md-none rounded-circle mr-3"
        style="position:fixed;top:15px;right:10px;left:auto;z-index:1100;background:#343a40;color:#fff;">
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
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
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



</html>