<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
include './downloadRecord.php';


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
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

    <style>
    #sidebar {
        position: fixed;
        /* keeps it in place */
        top: 0;
        left: 0;
        height: 100vh;
        /* full height */
        width: 250px;
        /* adjust as needed */

        color: white;
        overflow-y: auto;
        z-index: 1000;
    }

    #content-wrapper {
        margin-left: 220px;
        /* same as #sidebar width */

        /* optional for spacing */
    }
    </style>

</head>

<body id="page-top">
    <div id="wrapper">
        <div id="sidebar">
            <!-- Sidebar -->
            <?php include "Includes/sidebar.php"; ?>
        </div>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">View Class Attendance</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Class Attendance</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">View Class Attendance</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <!-- Form Basic -->
                                        <div class="card mb-4">

                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-row mb-3 d-flex align-items-end">
                                                        <!-- Course -->
                                                        <div class="form-group col-md-4">
                                                            <label class="form-control-label">Select Course <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="course" class="form-control" required>
                                                                <option value="">Select Course</option>
                                                                <?php
                                                                $courses = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '$_SESSION[userId]' ORDER BY courseName ASC");
                                                                while ($row = mysqli_fetch_array($courses)) {
                                                                    $selected = (isset($_POST['course']) && $_POST['course'] == $row['courseCode']) ? "selected" : "";
                                                                    echo "<option value='$row[courseCode]' $selected>$row[courseName]</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <!-- ClassArm -->
                                                        <div class="form-group col-md-4">
                                                            <label class="form-control-label">Select ClassArm <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="classArm" class="form-control" required>
                                                                <option value="">Select ClassArm</option>
                                                                <?php
                                                                $classArms = mysqli_query($conn, "SELECT * FROM tblclassarms");
                                                                while ($classArm = mysqli_fetch_array($classArms)) {
                                                                    $selected = (isset($_POST['classArm']) && $_POST['classArm'] == $classArm['Id']) ? "selected" : "";
                                                                    echo "<option value='$classArm[Id]' $selected>$classArm[classArmName]</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <!-- Date -->
                                                        <div class="form-group col-md-4 row">
                                                            <label class="form-control-label">From <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="from_date"
                                                                class="form-control col-md-4"
                                                                value="<?php echo $_POST['from_date'] ?? ''; ?>"
                                                                required>

                                                            <label class="form-control-label">To <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="to_date"
                                                                class="form-control col-md-4"
                                                                value="<?php echo $_POST['to_date'] ?? ''; ?>" required>
                                                        </div>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <button type="submit" name="view" class="btn btn-success">View
                                                        Attendance</button>
                                                    <!-- Export Button -->
                                                    <!-- <form method="post" action="downloadRecord.php" -->
                                                    <!-- style="display:inline;">
                                                        <input type="hidden" name="course"
                                                            value="<?php //echo $_POST['course'] ?? ''; ?>">
                                                        <input type="hidden" name="classArm"
                                                            value="<?php //echo $_POST['classArm'] ?? ''; ?>">
                                                        <input type="hidden" name="from_date"
                                                            value="<?php //echo $_POST['from_date'] ?? ''; ?>">
                                                        <input type="hidden" name="to_date"
                                                            value="<?php //echo $_POST['to_date'] ?? ''; ?>"> -->
                                                    <button type="submit" name="export"
                                                        class="btn btn-primary ml-2">Export Attendance</button>

                                                </form>
                                            </div>
                                        </div>
                                        <br>

                                        <!-- Attendance Trends -->
                                        <div class="card mb-4">
                                            <div class="bg-white rounded-lg shadow p-4 mb-6">
                                                <div
                                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-2">
                                                    <h3 class="text-lg font-semibold text-gray-800">Attendance Trends
                                                    </h3>
                                                </div>

                                                <!-- Trend Buttons moved here -->
                                                <div class="mb-4">
                                                    <form method="post" class="d-flex gap-2">
                                                        <?php
                                                        $trendType = $_POST['trend'] ?? 'weekly';
                                                        $trendTypes = ['weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];
                                                        foreach ($trendTypes as $type => $label): ?>
                                                        <button type="submit" name="trend" value="<?php echo $type; ?>"
                                                            class="btn btn-sm <?php echo $trendType === $type ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                                            <?php echo $label; ?>
                                                        </button>
                                                        <?php endforeach; ?>
                                                        <!-- Preserve filters -->
                                                        <input type="hidden" name="course"
                                                            value="<?php echo $_POST['course'] ?? ''; ?>">
                                                        <input type="hidden" name="classArm"
                                                            value="<?php echo $_POST['classArm'] ?? ''; ?>">
                                                        <input type="hidden" name="from_date"
                                                            value="<?php echo $_POST['from_date'] ?? ''; ?>">
                                                        <input type="hidden" name="to_date"
                                                            value="<?php echo $_POST['to_date'] ?? ''; ?>">
                                                    </form>
                                                </div>

                                                <div class="chart-container relative">
                                                    <?php
                                                    if (isset($_POST['view']) || isset($_POST['trend'])) {
                                                        $course = $_POST['course'] ?? '';
                                                        $classArm = $_POST['classArm'] ?? '';
                                                        $fromDateTaken = $_POST['from_date'] ?? '';
                                                        $toDateTaken = $_POST['to_date'] ?? '';
                                                        $lecturerId = $_SESSION['userId'];

                                                        $labels = [];
                                                        $present = [];
                                                        $absent = [];

                                                        if ($trendType === 'yearly') {
                                                            for ($i = 5; $i >= 0; $i--) {
                                                                $year = date('Y', strtotime("-$i year"));
                                                                $labels[] = $year;

                                                                $query = "SELECT 
                                    SUM(status='1') as present_count,
                                    SUM(status='0') as absent_count
                                FROM tblattendance WHERE lecturer_id = '$lecturerId' 
                                AND courseCode = '$course' 
                                AND YEAR(dateTimeTaken) = '$year'";
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
                                    SUM(status='1') as present_count,
                                    SUM(status='0') as absent_count
                                FROM tblattendance 
                                WHERE lecturer_id = '$lecturerId' 
                                AND courseCode = '$course'
                                AND DATE_FORMAT(dateTimeTaken, '%Y-%m') = '$month'";
                                                                $result = mysqli_query($conn, $query);
                                                                $row = mysqli_fetch_assoc($result);
                                                                $present[] = $row ? (int) $row['present_count'] : 0;
                                                                $absent[] = $row ? (int) $row['absent_count'] : 0;
                                                            }
                                                        } else { // weekly
                                                            for ($i = 5; $i >= 0; $i--) {
                                                                $start = date('Y-m-d', strtotime("-$i week", strtotime('monday this week')));
                                                                $end = date('Y-m-d', strtotime("$start +6 days"));
                                                                $labels[] = date('M j', strtotime($start)) . '-' . date('M j', strtotime($end));

                                                                $query = "SELECT 
                                    SUM(status='1') as present_count,
                                    SUM(status='0') as absent_count
                                FROM tblattendance 
                                WHERE lecturer_id = '$lecturerId' 
                                AND courseCode = '$course'
                                AND dateTimeTaken BETWEEN '$start' AND '$end'";
                                                                $result = mysqli_query($conn, $query);
                                                                $row = mysqli_fetch_assoc($result);
                                                                $present[] = $row ? (int) $row['present_count'] : 0;
                                                                $absent[] = $row ? (int) $row['absent_count'] : 0;
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                    <canvas id="attendanceChart"></canvas>
                                                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input Group -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card mb-4">
                                                    <div
                                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                        <h6 class="m-0 font-weight-bold text-primary">Class Attendance
                                                        </h6>
                                                    </div>
                                                    <div class="table-responsive p-3">
                                                        <table class="table align-items-center table-flush table-hover"
                                                            id="dataTableHover">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>First Name</th>
                                                                    <th>Last Name</th>
                                                                    <th>Other Name</th>
                                                                    <th>Admission No</th>
                                                                    <th>Class Arm</th>
                                                                    <th>Status</th>
                                                                    <th>Date</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
                                                                if (isset($_POST['view'])) {
                                                                    $course = $_POST['course'];
                                                                    $classArm = $_POST['classArm'];
                                                                    $fromDateTaken = $_POST['from_date'];
                                                                    $toDateTaken = $_POST['to_date'];

                                                                    $query = "SELECT tblattendance.admissionNo, tblattendance.classArmId, tblattendance.status, tblattendance.dateTimeTaken 
                                        FROM tblattendance 
                                        WHERE DATE(dateTimeTaken) BETWEEN '$fromDateTaken' AND '$toDateTaken' 
                                        AND courseCode = '$course' 
                                        AND classArmId = '$classArm' 
                                        AND lecturer_id = '$_SESSION[userId]'";
                                                                    $rs = $conn->query($query);
                                                                    $num = $rs->num_rows;
                                                                    $sn = 0;
                                                                    $status = "";
                                                                    if ($num > 0) {
                                                                        while ($rows = $rs->fetch_assoc()) {
                                                                            $studentNameQuery = mysqli_query($conn, "SELECT tblstudents.firstName, tblstudents.lastName, tblstudents.otherName 
                                                FROM tblstudents WHERE tblstudents.admissionNumber = '$rows[admissionNo]'");
                                                                            $studentNameRow = mysqli_fetch_assoc($studentNameQuery);

                                                                            $classArmQuery = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE tblclassarms.Id = '$rows[classArmId]'");
                                                                            $classArmRow = mysqli_fetch_assoc($classArmQuery);
                                                                            if ($rows['status'] == '1') {
                                                                                $status = "Present";
                                                                                $colour = "#00FF00";
                                                                            } else {
                                                                                $status = "Absent";
                                                                                $colour = "#FF0000";
                                                                            }

                                                                            $sn = $sn + 1;
                                                                            echo "
                                            <tr>
                                                <td>" . $sn . "</td>
                                                <td>" . $studentNameRow['firstName'] . "</td>
                                                <td>" . $studentNameRow['lastName'] . "</td>
                                                <td>" . $studentNameRow['otherName'] . "</td>
                                                <td>" . $rows['admissionNo'] . "</td>
                                                <td>" . $classArmRow['classArmName'] . "</td>                               
                                                <td style='background-color:" . $colour . "'>" . $status . "</td>
                                                <td>" . $rows['dateTimeTaken'] . "</td>
                                                <td>
                                                    <a href='editAttendance.php?admissionNo=" . $rows['admissionNo'] . "&dateTimeTaken=" . $rows['dateTimeTaken'] . "&course=" . $course . "&status=" . $status . "' class='btn btn-sm btn-primary'>Change Status</a>
                                            </tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<div class='alert alert-danger' role='alert'>
                                            No Record Found!
                                        </div>";
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Row-->

                                    <!-- Documentation Link -->
                                    <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>For more documentations you can visit<a href="https://getbootstrap.com/docs/4.3/components/forms/"
                  target="_blank">
                  bootstrap forms documentations.</a> and <a
                  href="https://getbootstrap.com/docs/4.3/components/input-group/" target="_blank">bootstrap input
                  groups documentations</a></p>
            </div>
          </div> -->

                                </div>
                                <!---Container Fluid-->
                            </div>
                            <!-- Footer -->
                            <?php include "Includes/footer.php"; ?>
                            <!-- Footer -->
                        </div>
                    </div>

                    <!-- Scroll to top -->
                    <a class="scroll-to-top rounded" href="#page-top">
                        <i class="fas fa-angle-up"></i>
                    </a>

                    <script src="../vendor/jquery/jquery.min.js"></script>
                    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/ruang-admin.min.js"></script>
                    <!-- Page level plugins -->
                    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
                    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

                    <script src="../vendor/jquery/jquery.min.js"></script>
                    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/ruang-admin.min.js"></script>
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

                    <!-- Page level custom scripts -->
                    <script>
                    $(document).ready(function() {
                        $('#dataTable').DataTable(); // ID From dataTable 
                        $('#dataTableHover').DataTable(); // ID From dataTable with Hover
                    });
                    </script>
</body>

</html>