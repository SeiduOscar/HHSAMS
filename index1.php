<?php
include 'Includes/dbcon.php';
session_start();

// Get the current active semester
$activeSemesterQuery = "SELECT id FROM tblsemester WHERE isActive = 1";
$activeSemesterResult = mysqli_query($conn, $activeSemesterQuery);
$activeSemester = mysqli_fetch_assoc($activeSemesterResult);

// If there's an active semester
// if ($activeSemester) {
//     // Increment num_sem for all students
//     $updateQuery = "UPDATE tblstudents SET num_sem = num_sem + 1";
//     mysqli_query($conn, $updateQuery);
// }
// Check if there is a change in semester activity
$semesterCheckQuery = "SELECT semesterName, isActive FROM tblsemester ORDER BY id ASC";
$semesterCheckResult = mysqli_query($conn, $semesterCheckQuery);

$semesters = [];
while ($row = mysqli_fetch_assoc($semesterCheckResult)) {
    $semesters[$row['semesterName']] = $row['isActive'];
}

// If First was active and Second was inactive, but now First is inactive and Second is active
// then increment num_sem for all students
if (
    isset($_SESSION['semester_state']) &&
    isset($semesters['First'], $semesters['Second'])
) {
    $prev = $_SESSION['semester_state'];
    // Previous: First active, Second inactive
    // Now: First inactive, Second active
    if (
        $prev['First'] == 1 && $prev['Second'] == 0 &&
        $semesters['First'] == 0 && $semesters['Second'] == 1
    ) {
        $updateQuery = "UPDATE tblstudents SET num_sem = num_sem + 1";
        mysqli_query($conn, $updateQuery);
    }
}

// Store current semester state in session for next request
$_SESSION['semester_state'] = [
    'First' => $semesters['First'] ?? 0,
    'Second' => $semesters['Second'] ?? 0
];

// Initialize database refreshing for level 100
// Delete previous records from all level tables before initializing new ones
$conn->query("DELETE FROM level_100");
$conn->query("DELETE FROM level_200");
$conn->query("DELETE FROM level_300");
$conn->query("DELETE FROM level_400");

// Initialize level 100
$query = "SELECT * FROM tblstudents WHERE num_sem < 2 OR num_sem = 2";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $in = $conn->prepare("INSERT INTO level_100 (admissionNumber, first_name, last_name, other_name, program, department, email, classArm) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($in) {
            $in->bind_param(
                'ssssssss',
                $row['admissionNumber'],
                $row['firstName'],
                $row['lastName'],
                $row['otherName'],
                $row['program'],
                $row['Department'],
                $row['email'],
                $row['classArm']
            );
            $in->execute();
            $in->close();
        }
    }
}

// Initialize level 200
$query1 = "SELECT * FROM tblstudents WHERE num_sem = 3 OR num_sem = 4";
$result1 = $conn->query($query1);

if ($result1 && $result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $in = $conn->prepare("INSERT INTO level_200 (admissionNumber, first_name, last_name, other_name, program, department, email, classArm) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($in) {
            $in->bind_param(
                'ssssssss',
                $row['admissionNumber'],
                $row['firstName'],
                $row['lastName'],
                $row['otherName'],
                $row['program'],
                $row['Department'],
                $row['email'],
                $row['classArm']
            );
            $in->execute();
            $in->close();
        }
    }
}

// Initialize level 300
$query2 = "SELECT * FROM tblstudents WHERE num_sem > 4 OR num_sem = 6";
$result2 = $conn->query($query2);

if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $in = $conn->prepare("INSERT INTO level_300 (admissionNumber, first_name, last_name, other_name, program, department, email, classArm) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($in) {
            $in->bind_param(
                'ssssssss',
                $row['admissionNumber'],
                $row['firstName'],
                $row['lastName'],
                $row['otherName'],
                $row['program'],
                $row['Department'],
                $row['email'],
                $row['classArm']
            );
            $in->execute();
            $in->close();
        }
    }
}

// Initialize level 400
$query3 = "SELECT * FROM tblstudents WHERE num_sem > 6 OR num_sem = 8";
$result3 = $conn->query($query3);

if ($result3 && $result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $in = $conn->prepare("INSERT INTO level_400 (admissionNumber, first_name, last_name, other_name, program, department, email, classArm) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($in) {
            $in->bind_param(
                'ssssssss',
                $row['admissionNumber'],
                $row['firstName'],
                $row['lastName'],
                $row['otherName'],
                $row['program'],
                $row['Department'],
                $row['email'],
                $row['classArm']
            );
            $in->execute();
            $in->close();
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
    <title>AMS - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <!-- Firebase App (core SDK) -->
<script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js"></script>

<!-- Firebase Authentication -->
<script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-auth.js"></script>

<script>
  // Your Firebase config
  const firebaseConfig = {
    apiKey: "AIzaSyCpJccZn8oTVgEOzDRxaMUFzpWOjAz_JFY",
    authDomain: "StudentAttendanceSystem.firebaseapp.com",
    projectId: "studentattendancesystem-715ec",
    appId: "1:756897893020:web:06797132cfa90141e1fb59"
  };

  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
</script>


</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpe00g');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h5 align="center">STUDENT ATTENDANCE SYSTEM</h5>
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form id="loginForm">
  <input type="email" id="email" placeholder="Email" required>
  <input type="password" id="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>

<script>
  const auth = firebase.auth();

  document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    auth.signInWithEmailAndPassword(email, password)
      .then(userCredential => {
        // Login success
        const user = userCredential.user;
        alert("Welcome, " + user.email);
        // redirect to dashboard or fetch attendance data
      })
      .catch(error => {
        alert("Login failed: " + error.message);
      });
  });
</script>


                                    <?php

                                    if (isset($_POST['login'])) {

                                        $userType = $_POST['userType'];
                                        $username = $_POST['username'];
                                        $password = $_POST['password'];
                                        $password = md5($password);

                                        if ($userType == "Administrator") {

                                            $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if ($num > 0) {

                                                $_SESSION['userId'] = $rows['Id'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];

                                                echo "<script type = \"text/javascript\">
        window.location = (\"Admin/index.php\")
        </script>";
                                            } else {

                                                echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";
                                            }
                                        } else if ($userType == "Lecturer") {

                                            $query = "SELECT * FROM tblmoderator WHERE emailAddress = '$username' AND password = '$password'";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if ($num > 0) {

                                                $_SESSION['userId'] = $rows['Id'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];


                                                echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/index.php\")
        </script>";
                                            } else {

                                                echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";
                                            }
                                        } else {

                                            echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";
                                        }
                                    }
                                    ?>

                                    <!-- <hr>
                    <a href="index.html" class="btn btn-google btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> -->


                                    <div class="text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>