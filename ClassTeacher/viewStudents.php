<?php
error_reporting(1);
include '../Includes/dbcon.php';
include '../Includes/session.php';



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


    <script>
    function classArmDropdown(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
            xmlhttp.send();
        }
    }
    </script>

    <script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize popovers
        $('[data-toggle="popover"]').popover();
    });
    </script>
</head>

<body id="page-top">
    <div id="wrapper">
        <div class="collapse sidebar-collapse d-lg-block" id="sidebarWrapper">
            <div id="sidebar">
                <!-- Sidebar -->
                <?php include "Includes/sidebar.php"; ?>

                <!-- Sidebar -->
            </div>
        </div>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Students in Class</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Students in Class</li>
                        </ol>
                    </div>
                    <div class="row d-flex flex-row">
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
                        <!-- <?php
                        // $query1 = mysqli_query($conn, "SELECT * from tblclass");
                        // $class = mysqli_num_rows($query1);
                        ?> -->

                        <!-- Pending Requests Card Example -->
                        <?php
                        $semesterCheckQuery = mysqli_query($conn, "SELECT * FROM tblsemester WHERE IsActive = '1'");
                        $semesterRow = mysqli_fetch_assoc($semesterCheckQuery);

                        $query1 = mysqli_query($conn, "SELECT * from tblattendance where lecturer_id = '$_SESSION[userId]' and semester = '$semesterRow[Id]'");
                        $totAttendance = mysqli_num_rows($query1);
                        ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Semeter
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

                    </div>
                    <!-- <div class="attendance-btn">
            <button type="submit" name="view" class="btn btn-primary">Genrate QR code</button>
            <button type="submit" name="view" class="btn btn-primary">Export Attendance</button>
            <button type="submit" name="view" class="btn btn-primary">View Classes</button>
            <button type="submit" name="view" class="btn btn-primary">Manually Mark Attendance</button>
          </div> -->
                    <!-- Form Basic -->


                    <!-- Attendance Trends Chart -->


                    <!-- Input Group -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">All Students In Class</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="mb-3 form-inline">
                                        <div class="form-group mr-2">
                                            <label for="course" class="mr-2">Select Course</label>
                                            <select name="cour" id="course" class="form-control">
                                                <option value="">Select Course</option>
                                                <?php
                                                $courseQuery = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '$_SESSION[userId]'");
                                                while ($courseRow = mysqli_fetch_assoc($courseQuery)) {
                                                    $selected = (isset($_POST['cour']) && $_POST['cour'] == $courseRow['Id']) ? "selected" : "";
                                                    echo "<option value='" . $courseRow['Id'] . "' $selected>" . $courseRow['courseName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group mr-2">
                                            <label for="classArm" class="mr-2">Select Class Arm</label>
                                            <select name="classArm" id="classArm" class="form-control">
                                                <option value="">All Class Arms</option>
                                                <?php
                                                $classArmQuery = mysqli_query($conn, "SELECT * FROM tblclassarms");
                                                while ($classArmRow = mysqli_fetch_assoc($classArmQuery)) {
                                                    $selectedArm = (isset($_POST['classArm']) && $_POST['classArm'] == $classArmRow['Id']) ? "selected" : "";
                                                    echo "<option value='" . $classArmRow['classArmName'] . "' $selectedArm>" . $classArmRow['classArmName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </form>
                                </div>

                                <div class="table-responsive p-3">
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Other Name</th>
                                                <th>Admission No</th>
                                                <th>Level</th>
                                                <th>Email</th>
                                                <th>Program</th>
                                                <th>Class Arm</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $rs = false;

                                            if (isset($_POST['cour']) && !empty($_POST['cour'])) {
                                                $selectedCourse = $_POST['cour'];
                                                $selectedClassArm = isset($_POST['classArm']) ? $_POST['classArm'] : "";

                                                echo "<input type='hidden' name='cour' value='$selectedCourse'>";
                                                if ($selectedClassArm) {
                                                    echo "<input type='hidden' name='classArm' value='$selectedClassArm'>";
                                                }

                                                // Get course details
                                                $courseLevelResult = mysqli_query($conn, "SELECT * FROM tblcourses WHERE Id = '$selectedCourse'");
                                                $courseLevelRow = mysqli_fetch_assoc($courseLevelResult);
                                                $level = "level_" . $courseLevelRow['Level'];

                                                // Build base query
                                                if ($courseLevelRow['general'] == '0') {
                                                    if (strpos($courseLevelRow['program'], '/') !== false) {
                                                        $programParts = explode('/', $courseLevelRow['program']);
                                                        $programConditions = [];
                                                        foreach ($programParts as $prog) {
                                                            $programConditions[] = "program = '" . mysqli_real_escape_string($conn, trim($prog)) . "'";
                                                        }
                                                        $whereClause = "(" . implode(" OR ", $programConditions) . ")";
                                                    } else {
                                                        $whereClause = "program = '" . mysqli_real_escape_string($conn, $courseLevelRow['program']) . "'";
                                                    }
                                                } else {
                                                    $whereClause = "1=1"; // all students in the level
                                                }

                                                // Add classArm filter if selected
                                                if (!empty($selectedClassArm)) {
                                                    $whereClause .= " AND classArm = '" . mysqli_real_escape_string($conn, $selectedClassArm) . "'";
                                                }

                                                // Final query
                                                $studentQuery = "SELECT * FROM `$level` WHERE $whereClause";
                                                $rs = mysqli_query($conn, $studentQuery);

                                                if (!$rs) {
                                                    die("Query failed: " . mysqli_error($conn));
                                                }
                                            }

                                            if ($rs) {
                                                $num = mysqli_num_rows($rs);
                                                $sn = 0;
                                                if ($num > 0) {
                                                    while ($rows = mysqli_fetch_assoc($rs)) {
                                                        $sn++;
                                                        echo "
                    <tr>
                      <td>{$sn}</td>
                      <td>{$rows['first_name']}</td>
                      <td>{$rows['last_name']}</td>
                      <td>" . ($rows['other_name'] ?? 'None') . "</td>
                      <td>{$rows['admissionNumber']}</td>
                      <td>{$level}</td>
                      <td>" . ($rows['email'] ?? 'None') . "</td>
                      <td>{$rows['program']}</td>
                      <td>" . ($rows['classArm'] ?? 'None') . "</td>
                    </tr>";
                                                    }
                                                } else {
                                                    echo "
                  <div class='alert alert-danger' role='alert'>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>


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
        $('#dataTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "pagingType": "full_numbers",
            "language": {
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                },
                "lengthMenu": "Show _MENU_ students per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "No students found",
                "infoFiltered": "(filtered from _MAX_ total students)"
            }
        });

        $('#dataTableHover').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "pagingType": "full_numbers",
            "language": {
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                },
                "lengthMenu": "Show _MENU_ students per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "No students found",
                "infoFiltered": "(filtered from _MAX_ total students)"
            }
        });
    });
    </script>
</body>

</html>