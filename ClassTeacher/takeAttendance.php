<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$classArms = mysqli_query($conn, "SELECT * FROM tblclassarms");
$classArmsRow = mysqli_fetch_array($classArms);

$courses = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '$_SESSION[userId]'");

// Initialize an array to store all course names
$courseNames = [];

if (mysqli_num_rows($courses) > 0) {
  while ($course = mysqli_fetch_array($courses)) {
    $courseNames[] = $course['courseName']; // Add each course name to the array
  }
} else {
  $courseNames = []; // No courses found
}

$courseNamesString = "'" . implode("','", $courseNames) . "'"; // Convert array to quoted string

// Get course level
$courseLevelQuery = mysqli_query($conn, "SELECT * FROM tblcourses WHERE courseName IN ($courseNamesString)");
$courseLevelRow = mysqli_fetch_array($courseLevelQuery);
$courseLevel = "level_" . $courseLevelRow['Level'];

// Fetch all students in the selected courses
$query1 = mysqli_query($conn, "SELECT * FROM `$courseLevel` WHERE program = '$courseLevelRow[program]' AND classArm = '$classArmsRow[Id]'");


// Join all course names into a single string for display
$ros = mysqli_fetch_array($query1);
//session and Term
$querey = mysqli_query($conn, "SELECT * from tblsemester where isActive ='1'");
$rwws = mysqli_fetch_array($querey);
$sessionTermId = $rwws['Id'];

$dateTaken = date("Y-m-d");

$qurty = mysqli_query($conn, "SELECT * from tblattendance  where lecturer_id = '$_SESSION[userId]' and dateTimeTaken='$dateTaken'");
$count = mysqli_num_rows($qurty);

if ($count == 0) { //if Record does not exsit, insert the new record

  //insert the students record into the attendance table on page load
  // $qus = mysqli_query($conn, "SELECT * from tblstudents  where lecturer_id = '$_SESSION[userId]'");
  while ($students = $query1->fetch_assoc()) {
    $qquery = mysqli_query($conn, "INSERT into tblattendance(admissionNo,classId,classArmId,sessionId,status,dateTimeTaken) 
              values('$ros[admissionNumber]','$_SESSION[userid]','1','$sessionTermId','0','$dateTaken')");
  }
}






if (isset($_POST['save'])) {

  // Safely get admissionNo and check arrays from POST, or set as empty arrays if not set
  $admissionNo = isset($_POST['admissionNo']) && is_array($_POST['admissionNo']) ? $_POST['admissionNo'] : [];
  $check = isset($_POST['check']) && is_array($_POST['check']) ? $_POST['check'] : [];
  $N = count($admissionNo);
  $status = "";


  //check if the attendance has not been taken i.e if no record has a status of 1
  $qurty = mysqli_query($conn, "SELECT * from tblattendance  where lecturer_id= '$_SESSION[userId]' and dateTimeTaken='$dateTaken' and status = '1'");
  $count = mysqli_num_rows($qurty);

  if ($count > 0) {

    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has been taken for today!</div>";
  } else //update the status to 1 for the checkboxes checked
  {

    for ($i = 0; $i < $N; $i++) {
      $admissionNo[$i]; //admission Number

      if (isset($check[$i])) //the checked checkboxes
      {
        $qquery = mysqli_query($conn, "UPDATE tblattendance set status='1' where admissionNo = '$check[$i]'");

        if ($qquery) {

          $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Attendance Taken Successfully!</div>";
        } else {
          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
      }
    }
  }
}


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
            <!-- Sidebar -->
        </div>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Take Attendance (Today's Date :
                            <?php echo $todaysDate = date("m-d-Y"); ?>)
                        </h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Students in Class</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div id="qrcodeCourseChoice">
                                <?php
                // Fetch the courses for the lecturer
                $courses = mysqli_query($conn, "SELECT * FROM tblcourses WHERE lecturer_id = '$_SESSION[userId]'");
                $classArms = mysqli_query($conn, "SELECT * FROM tblclassarms")
                  ?>
                                <form method="post" class="d-flex align-items-center flex-wrap">
                                    <div class="form-group me-3 mb-2">
                                        <label for="qrcodecourse" class="sr-only">Course</label>
                                        <select class="form-control" name="qrcodecourse" id="qrcodecourse" required>
                                            <option value="">---Select Course To Mark Attendance---</option>
                                            <?php
                      while ($row = mysqli_fetch_assoc($courses)) {
                        echo "<option value='" . $row['courseName'] . "'>" . $row['courseName'] . "</option>";
                      }
                      ?>
                                        </select>
                                    </div>

                                    <div class="form-group me-3 mb-2">
                                        <label for="qrcodeclassarm" class="sr-only">Class Arm</label>
                                        <select class="form-control" name="qrcodeclassarm" id="qrcodeclassarm">
                                            <option value="">---Select ClassArm To Mark Attendance---</option>
                                            <?php
                      while ($row1 = mysqli_fetch_assoc($classArms)) {
                        echo "<option value='" . $row1['classArmName'] . "'>" . $row1['classArmName'] . "</option>";
                      }
                      ?>
                                        </select>
                                    </div>

                                    <button type="submit" name="filter" class="btn btn-primary mb-2"
                                        onclick="return confirm('Are you sure you want to take attendance for the selected course and classArm not students will be auto marked absent?');">Filter</button>
                                </form>


                                <!-- <form method="post">
                                    <label for="level"></label>
                                    <select name="level" id="level" required>
                                        <option value="">---Select Course To Mark Attendance---</option>
                                        <option value="level_100">---Level_100 ---</option>
                                        <option value="level_200">--- Level_200---</option>
                                        <option value="level_300">--- Level_300---</option>
                                        <option value="level_400">--- level_400---</option>
                                    </select>
                                    <button type="submit" name="filterLevel" class="btn btn-primary">Submit</button>
                                </form> -->


                            </div>

                            <div class="attendance-btn">
                                <?php
                // require '../phpqrcode/qrlib.php'; // Include the phpqrcode library
                
                // if (isset($_POST['filter'])) {
                //   // Fetch the selected course name
                //   $coursename = $_POST['qrcodecourse'];
                //   $classArm = $_POST['qrcodeclassarm'];
                
                //   $coursfilter = mysqli_query($conn, "SELECT * FROM tblcourses WHERE courseName = '$coursename' AND classArm = '$classArm'");
                //   $ro = $coursfilter->fetch_assoc();
                //   $level = 'level' . $ro['Level'];
                //   $ATT = mysqli_query($conn, "SELECT * FROM '$level'");
                //   echo $ro['Level'];
                
                // if (!empty($classArm && $coursename)) {
                //     // Generate a unique token for the QR code
                //     $token = bin2hex(random_bytes(16));
                //     $_SESSION['qrcode_token'] = $token; // Store the token in the session
                
                //     // Generate the QR code link
                //     $link = "http://localhost/execute.php?userId=" . $_SESSION['userId'] . "&courseName=" . urlencode($coursename) . "&classarm=" . urldecode($classArm) . "&token=" . $token;
                //     $encodedLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
                
                //     // Display the QR code
                //     echo "<div id='qrCodeContainer'>";
                //     ob_start(); // Start output buffering
                //     QRcode::png($encodedLink, null, QR_ECLEVEL_L, 10);
                //     $imageData = base64_encode(ob_get_clean()); // Get the QR code image as base64
                //     echo "<img src='data:image/png;base64," . $imageData . "' alt='QR Code'>";
                //     echo "</div>";
                
                //     // Show the close button
                //     echo "<form method='post' action=''>
                //             <button type='submit' name='invalidate_qrcode' class='btn btn-danger'>Close QR Code</button>
                //           </form>";
                // } else {
                //     echo "<script>alert('Please select a course and classarm to generate the QR code.');</script>";
                // }
                // } 
                ?>

                                <!-- Input Group -->
                                <form method="post">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card mb-4">
                                                <div
                                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                    <h6 class="m-0 font-weight-bold text-primary">All Students in
                                                        (<?php //echo $rrw['className'] . ' - ' . $rrw['classArmName']; 
                            ?>)
                                                        Class</h6>
                                                    <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the
                                                            checkboxes beside each student to take attendance!</i></h6>
                                                </div>

                                                <div class="table-responsive p-3">
                                                    <?php echo isset($statusMsg) ? $statusMsg : ''; ?>
                                                    <table class="table align-items-center table-flush table-hover">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>First Name</th>
                                                                <th>Last Name</th>
                                                                <th>Other Name</th>
                                                                <th>Admission No</th>
                                                                <th>Check</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>

                                                            <?php
                              if (isset($_POST['filter'])) {
                                $selectedCourse = $_POST['qrcodecourse'];
                                $selectedClassArm = $_POST['qrcodeclassarm'];

                                // Fetch the course row
                                $courseQuery = mysqli_query($conn, "SELECT * FROM tblcourses WHERE courseName = '$selectedCourse'");
                                $row = mysqli_fetch_assoc($courseQuery);

                                if ($row && isset($row['Level'])) {
                                  if ($row['Level'] == 100) {
                                    $level = "level_100";
                                  } elseif ($row['Level'] == 200) {
                                    $level = "level_200";
                                  } elseif ($row['Level'] == 300) {
                                    $level = "level_300";
                                  } elseif ($row['Level'] == 400) {
                                    $level = "level_400";
                                  } else {
                                    $level = "alumni";
                                  }
                                } else {
                                  $level = "";
                                }

                                echo $level;

                                $classStudents = [];

                                // ✅ Fetch actual classArmId value
                                $classArmResult = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE classArmName = '$selectedClassArm'");
                                $classArmRow = mysqli_fetch_assoc($classArmResult);
                                $classArmId = $classArmRow ? $classArmRow['Id'] : null;
                                $classArmName = $classArmRow ? $classArmRow['classArmName'] : null;

                                // ✅ Fetch actual courseCode value (if needed)
                                $courseCodeResult = mysqli_query($conn, "SELECT courseCode FROM tblcourses WHERE courseName = '$selectedCourse'");
                                $courseCodeRow = mysqli_fetch_assoc($courseCodeResult);
                                $courseCode = $courseCodeRow ? $courseCodeRow['courseCode'] : null;

                                if (!empty($level) && $classArmId !== null) {
                                  $ATT = mysqli_query($conn, "SELECT * FROM $level WHERE classArm = '$classArmName'");
                                  if ($ATT && mysqli_num_rows($ATT) > 0) {
                                    while ($r = $ATT->fetch_assoc()) {
                                      $classStudents[] = $r['admissionNumber'];  // Collect all admission numbers
                                      $markAbsent = mysqli_query($conn, "INSERT INTO tblattendance (admissionNo, classArmId, status, dateTimeTaken, lecturer_id, courseCode, semester) VALUES ('{$r['admissionNumber']}', '$classArmId', '0', NOW(), '{$_SESSION['userId']}', '$courseCode', '$sessionTermId')");
                                    }
                                  }
                                }

                                if (!empty($classStudents)) {
                                  $classStudentsString = "'" . implode("','", $classStudents) . "'"; // Properly format the string
                                  $query = "SELECT * FROM tblstudents WHERE admissionNumber IN ($classStudentsString)";
                                  $rs = $conn->query($query);
                                  $num = $rs->num_rows;
                                } else {
                                  $rs = null;
                                  $num = 0;
                                }
                                //   $rs = null;
                                //   $num = 0;
                                // }
                              }
                              $sn = 0;
                              // Check if there are any results
                              if ($num > 0) {
                                while ($rows = $rs->fetch_assoc()) {
                                  $sn++;

                                  echo "
                    <tr>
                      <td>" . $sn . "</td>
                      <td>" . htmlspecialchars($rows['firstName'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($rows['lastName'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($rows['otherName'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($rows['admissionNumber'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td><input name='check[]' type='checkbox' value='" . htmlspecialchars($rows['admissionNumber'], ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>
                    </tr>
                    <input name='admissionNo[]' value='" . htmlspecialchars($rows['admissionNumber'], ENT_QUOTES, 'UTF-8') . "' type='hidden' class='form-control'>";
                                }
                              } else {
                                echo "
                    <tr>
                    <td colspan='6'>
                      <div class='alert alert-danger' role='alert'>
                      No Record Found!
                      </div>
                    </td>
                    </tr>";
                              }
                              ?>
                                                        </tbody>
                                                    </table>
                                                    <br>
                                                    <button type="submit" name="save" class="btn btn-primary">Take
                                                        Attendance</button>
                                </form>
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
    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 2. Bootstrap JS (bundle includes Popper.js) -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- 3. DataTables (if using) -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- 4. Template JS -->
    <script src="js/ruang-admin.min.js"></script>


    <script src="../vendor/jquery/jquery.min.js"></script>

    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>


    <!-- Page level custom scripts -->
    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable(); // ID From dataTable 
        $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
    </script>
</body>

</html>