<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';



$query = "SELECT * from tblattendance where lecturer_id = '$_SESSION[userId]' order by dateTimeTaken desc limit 1";

$rs = $conn->query($query);
$num = $rs->num_rows;
$rrw = $rs->fetch_assoc();



$attendanceData = [];

// Query attendance for the most recent 3 dates, calculating attendance rates
$sql = "SELECT 
           SUM(status = 0) / COUNT(*) * 100 AS absentRate,
           SUM(status = 1) / COUNT(*) * 100 AS presentRate,
           DATE(dateTimeTaken) AS date
        FROM tblattendance
        WHERE lecturer_id = '{$_SESSION['userId']}'
        GROUP BY DATE(dateTimeTaken)
        ORDER BY date DESC
        LIMIT 3";

$result = mysqli_query($conn, $sql);

// Debugging: Check for SQL errors
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

// Debugging: Check if session variables are set
if (!isset($_SESSION['userId'])) {
    die("Session variables 'userId' is not set.");
}

$labels = [];
$present = [];
$absent = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['date'];
    $present[] = $row['presentRate'];
    $absent[] = $row['absentRate'];
}

// Reverse arrays to display in chronological order
$labels = array_reverse($labels);
$present = array_reverse($present);
$absent = array_reverse($absent);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>SAMS-Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .large-card {
        margin-top: 1%;
        width: 100%;
        height: auto;
        padding: 20px;
        background: white;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .attendance-btn {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .attendance-btn button {
        padding: 10px 20px;
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    .attendance-btn button:hover {
        background-color: #0056b3;
    }

    .graph {
        margin-top: 30px;
        text-align: center;
    }

    canvas {
        max-width: 100%;
    }

    #chartContainer,
    #listContainer {
        width: 100%;
        height: 350px;
        overflow-y: auto;
    }

    #listContainer {
        display: none;
    }

    .controls {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .controls label {
        font-size: 14px;
    }

    .controls button {
        padding: 5px 10px;
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
        border-radius: 5px;
    }

    .controls button.active {
        background: #007bff;
        color: white;
    }


    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        color: white;
        overflow-y: auto;
        z-index: 1000;
        transition: width .3s;
    }

    #content-wrapper {
        margin-left: 250px;
        transition: margin-left .3s;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 0 !important;
            overflow: hidden;
        }

        .sidebar.toggled {
            width: 80vw !important;
        }

        #content-wrapper {
            margin-left: 0 !important;
        }

        .container-fluid,
        .content-wrapper {
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 2vw !important;
            min-height: 100vh;
            background: #f8f9fc;
        }

        .col-xl-3,
        .col-md-6,
        .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .row.g-3 .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .attendance-btn {
            flex-direction: column;
            gap: 5px;
        }

        .attendance-btn button {
            width: 100%;
        }

        .chart-container {
            height: 300px !important;
        }
    }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">


        <!-- Sidebar -->
        <?php include "Includes/sidebar.php"; ?>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->
                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Class Teacher Dashboard
                        </h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>

                    <div class="row mb-3">
                        <!-- New User Card Example -->
                        <?php

                        $courseQuery = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '{$_SESSION['userId']}'");

                        $numOfStudents = 0;

                        while ($courseRow = mysqli_fetch_assoc($courseQuery)) {
                            $level = "level_" . $courseRow['Level'];

                            if ($courseRow['general'] == '0') {
                                // Students in same program
                                $studentQuery = mysqli_query(
                                    $conn,
                                    "SELECT * FROM `$level` WHERE program = '{$courseRow['program']}'"
                                );
                            } else {
                                // All students in the level
                                $studentQuery = mysqli_query(
                                    $conn,
                                    "SELECT * FROM `$level`"
                                );
                            }

                            if ($studentQuery) {
                                $numOfStudents += mysqli_num_rows($studentQuery);
                            }
                        }


                        // $query1 = mysqli_query($conn, "SELECT * from tblstudents where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]'");
                        // $students = mysqli_num_rows($query1);
                        ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Students
                                            </div>
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                <?php echo $numOfStudents; ?>
                                            </div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Earnings (Monthly) Card Example -->
                        <?php
                        $query1 = mysqli_query($conn, "SELECT * from tblcourses where lecturer_id = '$_SESSION[userId]'");
                        $class = mysqli_num_rows($query1);
                        ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Classes</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class; ?>
                                            </div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <!-- <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                        <span>Since last month</span> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chalkboard fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Earnings (Annual) Card Example -->
                        <?php
                        $query1 = mysqli_query($conn, "SELECT * from tblclassarms");
                        $classArms = mysqli_num_rows($query1);
                        ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Class Arms</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $classArms; ?>
                                            </div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                        <span>Since last years</span> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-code-branch fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <?php
                        $query1 = mysqli_query($conn, "SELECT * from tblattendance where lecturer_id = '$_SESSION[userId]'");
                        $totAttendance = mysqli_num_rows($query1);
                        ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student
                                                Attendance</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $totAttendance; ?>
                                            </div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <!-- <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                                  <span>Since yesterday</span> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-12 col-md-8 mb-4" id="qrcodeCourseChoice">
                        <?php
                        // Fetch the courses for the lecturer
                        $courses = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '$_SESSION[userId]'");
                        $classArms = mysqli_query($conn, "SELECT * FROM tblclassarms");
                        ?>

                        <div class="card shadow border-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-primary fw-bold">Generate Attendance QR Code</h5>

                                <form method="post" id="qrcodeForm">
                                    <div class="row g-3">
                                        <!-- Course Dropdown -->
                                        <div class="col-md-6">
                                            <label for="qrcodecourse" class="form-label fw-semibold">Select
                                                Course</label>
                                            <select class="form-select form-control" name="qrcodecourse"
                                                id="qrcodecourse" required>
                                                <option value="">--- Select Course ---</option>
                                                <?php
                                                while ($row = mysqli_fetch_assoc($courses)) {
                                                    echo "<option value='" . $row['courseName'] . "'>" . $row['courseName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Class Arm Dropdown -->
                                        <div class="col-md-6">
                                            <label for="qrcodeclassarm" class="form-label fw-semibold">Select Class
                                                Arm</label>
                                            <select class="form-select form-control" name="qrcodeclassarm"
                                                id="qrcodeclassarm">
                                                <option value="">--- Select Class Arm ---</option>
                                                <?php
                                                while ($row1 = mysqli_fetch_assoc($classArms)) {
                                                    echo "<option value='" . $row1['classArmName'] . "'>" . $row1['classArmName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-12 text-end pt-2">
                                            <button type="submit" name="generate_qrcode" class="btn btn-primary px-4"
                                                onclick="showQRCodePopup()">
                                                <i class="bi bi-qr-code-scan"></i> Generate QR Code
                                            </button>
                                        </div>
                                    </div>

                                </form>
                                <div class="attendance-btn">
                                    <a href="downloadRecord.php"><button type="submit" name="view"
                                            class="btn btn-primary">Export Attendance</button></a>
                                    <a href="viewStudents.php"><button type="submit" name="view"
                                            class="btn btn-primary">View
                                            Students</button></a>
                                    <a href="takeAttendance.php"><button type="submit" name="view"
                                            class="btn btn-primary">Manually Mark
                                            Attendance</button></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="attendance-btn">
                        <?php
                        require '../phpqrcode/qrlib.php'; // Include the phpqrcode library
                        
                        // Check if the QR code generation form was submitted
                        if (isset($_POST['generate_qrcode'])) {
                            // These are the POST variables sent with the QR code form:
                            // $_POST['qrcodecourse'] - selected course name
                            // $_POST['qrcodeclassarm'] - selected class arm
                        
                            $coursename = $_POST['qrcodecourse'];
                            $classArm = $_POST['qrcodeclassarm'];

                            if (!empty($classArm) && !empty($coursename)) {
                                // Generate a unique token for the QR code
                                $token = bin2hex(random_bytes(16));
                                $_SESSION['qrcode_token'] = $token; // Store the token in the session
                        
                                $time = date('Y-m-d H:i:s'); // Get the current time
                        
                                // Optionally, insert the token into the qr_tokens table as valid
                                $stmtInsert = $conn->prepare("INSERT INTO qr_tokens (token, is_valid, created_at) VALUES (?, 1, NOW())");
                                $stmtInsert->bind_param("s", $token);
                                $stmtInsert->execute();
                                $stmtInsert->close();

                                // Check if the token is valid in the database
                                $stmt = $conn->prepare("SELECT COUNT(*) FROM qr_tokens WHERE token = ? AND is_valid = 1 and created_at > NOW() - INTERVAL 1 HOUR");
                                $stmt->bind_param("s", $token);
                                $stmt->execute();
                                $stmt->bind_result($count);
                                $stmt->fetch();
                                $stmt->close();

                                if ($count > 0) {
                                    // Generate the QR code link
                                    $lecturer_id = $_SESSION['userId'];

                                    $link = "https://192.168.81.143/Student-Attendance-Management-System-main/recognition/index.php?userId=" . $lecturer_id . "&courseName=" . urlencode($coursename) . "&classarm=" . urlencode($classArm) . "&token=" . $token;
                                    $encodedLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
                                    //   echo $link;
                                    // ✅ Get course details and level quering
                                    $stmt = $conn->prepare("SELECT * FROM tblcourses WHERE courseName = ?");
                                    $stmt->bind_param("s", $coursename);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $rowCourse = $result->fetch_array(MYSQLI_ASSOC);
                                    $stmt->close();

                                    if (!$rowCourse) {
                                        echo "<div class='result text-red-600 font-semibold'>Course not found.</div>";
                                        exit;
                                    }

                                    $courseCode = $rowCourse["courseCode"];
                                    $program = $rowCourse["program"];
                                    $general = $rowCourse["general"];
                                    //echo $rowCourse["Level"]; // Debugging: Check if Level is set correctly
                                    $Level = "level_" . $rowCourse["Level"]; // assuming Level is numeric like 100, 200 etc.
                        
                                    // ✅ Get semester ID
                                    $stmt = $conn->prepare("SELECT Id FROM tblsemester WHERE isActive = 1");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $semester = $result->fetch_array(MYSQLI_ASSOC);
                                    $stmt->close();
                                    $semesterId = $semester['Id'] ?? null;

                                    if (!$semesterId) {
                                        echo "No active semester found.";
                                        exit;
                                    }

                                    // ✅ Get classArmId
                                    $stmt = $conn->prepare("SELECT Id FROM tblclassarms WHERE classArmName = ?");
                                    $stmt->bind_param("s", $classArm);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $classArmRow = $result->fetch_array(MYSQLI_ASSOC);
                                    $stmt->close();

                                    $classArmId = $classArmRow['Id'] ?? null;
                                    if (!$classArmId) {
                                        echo "Class arm not found.";
                                        exit;
                                    }
                                    // ✅ Get student list
                                    if ($general == 1) {
                                        // General course - select all students from level table
                                        $levelQuery = mysqli_query($conn, "SELECT * FROM {$Level} WHERE classArm = '$classArm'");
                                    } else {
                                        // Program-specific course
                                        $levelQuery = mysqli_query($conn, "SELECT * FROM {$Level} WHERE classArm = '$classArm' AND program = '" . mysqli_real_escape_string($conn, $program) . "'");
                                    }

                                    if (!$levelQuery) {
                                        echo "Failed to fetch student list.";
                                        exit;
                                    }

                                    // echo $Level; // Debugging: Check if Level is set correctly
                        
                                    // ✅ Attendance check: Has this attendance already been taken?
                                    $dateTaken = date("Y-m-d");

                                    $attendanceCheck = $conn->prepare(" SELECT COUNT(*) 
    FROM tblattendance 
    WHERE lecturer_id = ? AND classArmId = ? AND courseCode = ? AND dateTimeTaken = ?
");
                                    $attendanceCheck->bind_param("siss", $lecturer_id, $classArmId, $courseCode, $dateTaken);
                                    $attendanceCheck->execute();
                                    $attendanceCheck->bind_result($attendanceCount);
                                    $attendanceCheck->fetch();
                                    $attendanceCheck->close();

                                    // ✅ Mark absent if attendance not yet taken
                                    if ($attendanceCount < 1) {
                                        // Fetch all students from the appropriate level table
                                        if ($general == 1) {
                                            // General course - select all students from level table
                                            $studentQuery = $conn->query("SELECT * FROM {$Level} WHERE classArm = '$classArm'");
                                        } else {
                                            // Program-specific course
                                            $programEscaped = mysqli_real_escape_string($conn, $program);
                                            $studentQuery = $conn->query("SELECT * FROM {$Level} WHERE classArm = '$classArm' AND program = '{$programEscaped}'");
                                        }

                                        if ($studentQuery && $studentQuery->num_rows > 0) {
                                            while ($student = $studentQuery->fetch_assoc()) {
                                                $admissionNo = mysqli_real_escape_string($conn, $student['admissionNumber']);
                                                $status = 0;
                                                $insert = $conn->prepare("INSERT INTO tblattendance 
                (admissionNo, lecturer_id, classArmId, status, dateTimeTaken, courseCode, semester) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
                                                $insert->bind_param("sisssss", $admissionNo, $lecturer_id, $classArmId, $status, $dateTaken, $courseCode, $semesterId);
                                                $insert->execute();
                                                $insert->close();
                                            }
                                            echo "<script>alert('✅ All students marked absent successfully.')</script>";
                                        } else {
                                            echo "<script>alert('No students found in the selected class/level.')</script>";
                                        }
                                    } else {
                                        echo "<script>alert('⚠️ Attendance has already been recorded for today.')</script>";
                                    }
                                    // Display the QR code
                                    echo "<div id='qrCodeContainer'>";

                                    ob_start(); // Start output buffering
                        
                                    // Make sure $link is raw and unencoded, with actual ampersands (&)
                                    QRcode::png($link, null, QR_ECLEVEL_L, 10);

                                    $imageData = base64_encode(ob_get_clean()); // Get the QR code image as base64
                                    echo "<img src='data:image/png;base64," . $imageData . "' alt='QR Code'>";

                                    echo "</div>";


                                    // Show the close button
                                    echo "<form method='post' action=''>
                                            <button type='submit' name='invalidate_qrcode' class='btn btn-danger'>Close QR Code</button>
                                          </form>";

                                } else {
                                    echo "<script>alert('QR Code token is not valid.');</script>";
                                }
                            } else {
                                echo "<script>alert('Please select a course and classarm to generate the QR code.');</script>";
                            }
                        }
                        //echo $link;
                        // Invalidate the QR code when the close button is clicked
                        if (isset($_POST['invalidate_qrcode'])) {
                            if (isset($_SESSION['qrcode_token'])) {
                                $token = $_SESSION['qrcode_token'];
                                // Mark the token as invalid in the database
                                $stmt = $conn->prepare("UPDATE qr_tokens SET is_valid = 0 WHERE token = ? and created_at > NOW() - INTERVAL 1 HOUR");
                                $stmt->bind_param("s", $token);
                                $stmt->execute();
                                $stmt->close();
                                unset($_SESSION['qrcode_token']); // Remove the token from the session
                            }
                            echo "<script>
                                    alert('QR Code has been invalidated.');
                                    document.getElementById('qrCodeContainer').style.display = 'none';
                                  </script>";
                        }
                        ?>

                    </div>

                    <div id="qrCodePopup"
                        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 10px; z-index: 1000;">
                        <div id="qrCodeContainer"></div>
                        <button onclick="closeQRCodePopup()"
                            style="margin-top: 10px; padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                    </div>
                    <div id="overlay"
                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;">
                    </div>

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                    <script>
                    function showQRCodePopup() {
                        const container = document.getElementById('qrCodeContainer');
                        container.innerHTML = '';
                        const qrCode = new QRCode(container, {
                            text: "<?php echo $encodedLink; ?>",
                            width: 256,
                            height: 256
                        });
                        container.style.display = 'block'; // Ensure the container is visible
                        document.getElementById('qrCodePopup').style.display = 'block';
                        document.getElementById('overlay').style.display = 'block';
                    }

                    function closeQRCodePopup() {
                        document.getElementById('qrCodePopup').style.display = 'none';
                        document.getElementById('qrCodeContainer').style.display =
                            'none'; // Hide the QR code container
                        document.getElementById('qrCodePopup').style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }
                    </script>
                    </script>
                </div>
            </div>



            <!-- Attendance Trends Chart -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                    <h3 class="text-lg font-semibold text-gray-800">Attendance Trends</h3>
                    <form method="get" class="flex space-x-2">
                        <?php
                        $trendType = $_GET['trend'] ?? 'weekly';
                        $trendTypes = ['weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];
                        foreach ($trendTypes as $type => $label): ?>
                        <button class="btn btn-primary" type="submit" name="trend" value="<?php echo $type; ?>"
                            class="px-3 py-1 <?php echo $trendType === $type ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'; ?> rounded-full text-sm font-medium">
                            <?php echo $label; ?>
                        </button>
                        <?php endforeach; ?>
                    </form>

                </div>

                <div class="chart-container relative" style="width: 100%; height: 400px;">
                    <?php
                    $labels = [];
                    $present = [];
                    $absent = [];
                    $lecturerId = $_SESSION['userId'];

                    if ($trendType === 'yearly') {
                        for ($i = 5; $i >= 0; $i--) {
                            $year = date('Y', strtotime("-$i year"));
                            $labels[] = $year;

                            $query = "SELECT
                            SUM(status='1' ) as present_count,
                            SUM(status='0') as absent_count
                          FROM tblattendance
                          WHERE lecturer_id = '$lecturerId' AND YEAR(dateTimeTaken) = '$year'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            $present[] = $row ? (int) $row['present_count'] : 0;
                            $absent[] = $row ? (int) $row['absent_count'] : 0;
                        }
                    } elseif ($trendType === 'monthly') {
                        for ($i = 5; $i >= 0; $i--) {
                            $month = date('Y-m', strtotime("-$i month"));
                            $labels[] = date('M Y', strtotime($month));

                            $query = "SELECT
                            SUM(status='1' ) as present_count,
                            SUM(status='0') as absent_count
                          FROM tblattendance
                          WHERE lecturer_id = '$lecturerId'
                          AND DATE_FORMAT(dateTimeTaken, '%Y-%m') = '$month'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            $present[] = $row ? (int) $row['present_count'] : 0;
                            $absent[] = $row ? (int) $row['absent_count'] : 0;
                        }
                    } else {
                        for ($i = 5; $i >= 0; $i--) {
                            $start = date('Y-m-d', strtotime("-$i week", strtotime('monday this week')));
                            $end = date('Y-m-d', strtotime("$start +6 days"));
                            $labels[] = date('M j', strtotime($start)) . '-' . date('M j', strtotime($end));

                            $query = "SELECT
                            SUM(status='1' ) as present_count,
                            SUM(status='0') as absent_count
                          FROM tblattendance
                          WHERE lecturer_id = '$lecturerId'
                          AND dateTimeTaken BETWEEN '$start' AND '$end'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            $present[] = $row ? (int) $row['present_count'] : 0;
                            $absent[] = $row ? (int) $row['absent_count'] : 0;
                        }
                    }
                    ?>
                    <canvas id="attendanceChart" style="max-width: 100%; height: 100%;"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    if (window.attendanceChartInstance) window.attendanceChartInstance.destroy();
                    window.attendanceChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                    label: 'Present',
                                    data: <?php echo json_encode($present); ?>,
                                    borderColor: '#28a745',
                                    backgroundColor: 'rgba(40,167,69,0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#28a745'
                                },
                                {
                                    label: 'Absent',
                                    data: <?php echo json_encode($absent); ?>,
                                    borderColor: '#dc3545',
                                    backgroundColor: 'rgba(220,53,69,0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#dc3545'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                    </script>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <!-- <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
<div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h3 class="text-lg font-semibold text-gray-800">Attendance Records Table</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php
                // $query = "SELECT admissionNo, status, dateTimeTaken
                //           FROM tblattendance
                //           WHERE lecturer_id = '$lecturerId'
                //           ORDER BY dateTimeTaken DESC
                //           LIMIT 20";
                // $result = mysqli_query($conn, $query);
                // if ($result && mysqli_num_rows($result) > 0):
                //     while ($row = mysqli_fetch_assoc($result)):
                //         // Treat 'Late' as 'Present'
                //         $status = $row['status'] === 'Late' ? 'Present' : $row['status'];
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php
                        // echo htmlspecialchars($row['admissionNo']); 
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if ($status == 'Present'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Present</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Absent</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php
                        // echo date('M j, Y', strtotime($row['dateTimeTaken'])); 
                        ?>
                    </td>
                </tr>
                <?php
                // endwhile; else: 
                ?>
                <tr>
                    <td colspan="3" class="px-6 py-4 text-gray-500 text-center">No attendance records found.</td>
                </tr>
                <?php
                // endif; 
                ?>
            </tbody>
        </table>
    </div>
</div> -->



        </div>
    </div>
    <!--Row-->

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>

    <script>
    // Sidebar toggle for mobile
    $(document).ready(function() {
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').toggleClass('toggled');
        });
    });
    </script>

</body>

</html>