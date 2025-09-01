<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $admissionNumber = $_POST['admissionNumber'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $dateCreated = date("Y-m-d");

    // Check for duplicate student
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber ='$admissionNumber'");
    if (mysqli_num_rows($query) > 0) {
        $statusMsg = "<div class='alert alert-danger'>Admission number already exists!</div>";
    } else {
        // Insert new student
        $query = mysqli_query($conn, "INSERT INTO tblstudents(firstName,lastName,otherName,admissionNumber,password,email,classId,classArmId,dateCreated) 
        VALUES('$firstName','$lastName','$otherName','$admissionNumber','$password','$email','$classId','$classArmId','$dateCreated')");

        if ($query) {
            echo "<script>
                alert('Student created successfully!');
                window.location.href = 'createStudents_fixed.php';
                </script>";
            exit();
        } else {
            $statusMsg = "<div class='alert alert-danger'>Error creating student: " . mysqli_error($conn) . "</div>";
        }
    }
}

//------------------------UPDATE--------------------------------------------------
if (isset($_POST['update'])) {
    $Id = $_POST['Id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $admissionNumber = $_POST['admissionNumber'];
    $email = $_POST['email'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];

    $query = mysqli_query($conn, "UPDATE tblstudents SET 
        firstName='$firstName', 
        lastName='$lastName',
        otherName='$otherName', 
        admissionNumber='$admissionNumber',
        email='$email',
        classId='$classId',
        classArmId='$classArmId'
        WHERE Id='$Id'");

    if ($query) {
        echo "<script>
            alert('Student updated successfully!');
            window.location.href = 'createStudents_fixed.php';
            </script>";
        exit();
    } else {
        $statusMsg = "<div class='alert alert-danger'>Error updating student: " . mysqli_error($conn) . "</div>";
    }
}

//------------------------DELETE--------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    
    $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$Id'");
    
    if ($query) {
        echo "<script>
            alert('Student deleted successfully!');
            window.location.href = 'createStudents_fixed.php';
            </script>";
        exit();
    } else {
        $statusMsg = "<div class='alert alert-danger'>Error deleting student: " . mysqli_error($conn) . "</div>";
    }
}

//------------------------FETCH STUDENT FOR EDIT--------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id='$Id'");
    $row = mysqli_fetch_array($query);
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
    <?php include 'includes/title.php';?>
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
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET","ajaxClassArms2.php?cid="+str,true);
                xmlhttp.send();
            }
        }
    </script>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php";?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php";?>
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active">Create Students</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <?php echo isset($_GET['action']) && $_GET['action'] == "edit" ? "Edit Student" : "Create Student"; ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <?php if (isset($_GET['action']) && $_GET['action'] == "edit") { ?>
                                            <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>">
                                        <?php } ?>
                                        
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">First Name<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="firstName" value="<?php echo isset($row['firstName']) ? $row['firstName'] : ''; ?>" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Last Name<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="lastName" value="<?php echo isset($row['lastName']) ? $row['lastName'] : ''; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Other Name</label>
                                                <input type="text" class="form-control" name="otherName" value="<?php echo isset($row['otherName']) ? $row['otherName'] : ''; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Admission Number<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="admissionNumber" value="<?php echo isset($row['admissionNumber']) ? $row['admissionNumber'] : ''; ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Class<span class="text-danger ml-2">*</span></label>
                                                <select class="form-control" name="classId" required>
                                                    <option value="">Select Class</option>
                                                    <?php
                                                    $classQuery = mysqli_query($conn, "SELECT * FROM tblclasses");
                                                    while ($classRow = mysqli_fetch_array($classQuery)) {
                                                        $selected = (isset($row['classId']) && $row['classId'] == $classRow['Id']) ? "selected" : "";
                                                        echo "<option value='" . $classRow['Id'] . "' $selected>" . $classRow['className'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Class Arm<span class="text-danger ml-2">*</span></label>
                                                <select class="form-control" name="classArmId" id="classArmId" required>
                                                    <option value="">Select Class Arm</option>
                                                    <?php
                                                    $classArmQuery = mysqli_query($conn, "SELECT * FROM tblclassarms");
                                                    while ($classArmRow = mysqli_fetch_array($classArmQuery)) {
                                                        $selected = (isset($row['classArmId']) && $row['classArmId'] == $classArmRow['Id']) ? "selected" : "";
                                                        echo "<option value='" . $classArmRow['Id'] . "' $selected>" . $classArmRow['armName'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Date Created</label>
                                                <input type="text" class="form-control" name="dateCreated" value="<?php echo isset($row['dateCreated']) ? $row['dateCreated'] : ''; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-xl-12">
                                                <button type="submit" name="<?php echo isset($row['Id']) ? 'update' : 'save'; ?>" class="btn btn-primary"><?php echo isset($row['Id']) ? 'Update' : 'Create'; ?> Student</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
// - Image capture and face encoding integration
// - Proper prepared statements for insertion
// - Form with all necessary fields

// The createStudents.php file seems to be an older or less complete version with some issues.

// To "make the edit work", it seems the user should be using create_students.php instead of createStudents.php for student creation and editing.

// I need to confirm if the edit functionality exists in create_students.php or if it needs to be added.

// I will search for "edit" keyword in create_students.php to check if edit functionality is implemented there.
// </thinking>

// <search_files>
// <path>Admin</path>
// <regex>edit</regex>
// <file_pattern>create_students.php</file_pattern>
// </search_files>
