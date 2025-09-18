<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Handle Department Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createDepartment'])) {
    $departmentName = mysqli_real_escape_string($conn, $_POST['departmentName']);
    $departmentFaculty = mysqli_real_escape_string($conn, $_POST['departmentFaculty']);
    $departmentHead = mysqli_real_escape_string($conn, $_POST['departmentHead']);
    $codeName = mysqli_real_escape_string($conn, $_POST['codeName']);

    // Check if department already exists
    $checkQuery = "SELECT * FROM tbldepartments WHERE departmentName='$departmentName'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Department already exists.')</script>";
    } else {
        $insertQuery = "INSERT INTO tbldepartments (departmentName, departmentFaculty, departmentHead, codeName) VALUES ('$departmentName', '$departmentFaculty', '$departmentHead', '$codeName')";
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Department added successfully.')</script>";
        } else {
            echo "<script>alert('Failed to add department.')</script>";
        }
    }
}

// Handle Department Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editDepartment'])) {
    $departmentId = intval($_POST['departmentId']);
    $departmentName = mysqli_real_escape_string($conn, $_POST['departmentName']);
    $departmentFaculty = mysqli_real_escape_string($conn, $_POST['departmentFaculty']);
    $departmentHead = mysqli_real_escape_string($conn, $_POST['departmentHead']);
    $codeName = mysqli_real_escape_string($conn, $_POST['codeName']);

    $updateQuery = "UPDATE tbldepartments SET departmentName='$departmentName', departmentFaculty='$departmentFaculty', departmentHead='$departmentHead', codeName='$codeName' WHERE Id=$departmentId";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Department updated successfully.')</script>";
        echo "<meta http-equiv='refresh' content='2;url=./InstitutionManagement.php'>";
    } else {
        echo "<script>alert('Failed to update department.')</script>";
    }
}

// Handle Department Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteDepartmentId'])) {
    $deleteDepartmentId = intval($_POST['deleteDepartmentId']);
    $deleteQuery = "DELETE FROM tbldepartments WHERE Id=$deleteDepartmentId";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Department deleted successfully.')</script>";
    } else {
        echo "<script>alert('Failed to delete department.')</script>";
    }
}

// Handle Faculty Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createFaculty'])) {
    $facultyName = mysqli_real_escape_string($conn, $_POST['facultyName']);
    $facultyHead = mysqli_real_escape_string($conn, $_POST['facultyHead']);
    $insertFac = mysqli_query($conn, "INSERT INTO tblfaculty (facultyName, facultyHead) VALUES ('$facultyName', '$facultyHead')");
    if ($insertFac) {
        echo "<script>alert('Faculty created successfully.')</script>";
    } else {
        echo "<script>alert('Failed to create faculty.')</script>";
    }
}

// Handle Faculty Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editFaculty'])) {
    $editFacultyId = intval($_POST['editFacultyId']);
    $editFacultyName = mysqli_real_escape_string($conn, $_POST['editFacultyName']);
    $editFacultyHead = mysqli_real_escape_string($conn, $_POST['editFacultyHead']);
    $updateFac = mysqli_query($conn, "UPDATE tblfaculty SET facultyName='$editFacultyName', facultyHead='$editFacultyHead' WHERE Id=$editFacultyId");
    if ($updateFac) {
        echo "<script>alert('Faculty updated successfully.')</script>";
    } else {
        echo "<script>alert('Failed to update faculty.')</script>";
    }
}

// Handle Faculty Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteFacultyId'])) {
    $deleteFacultyId = intval($_POST['deleteFacultyId']);
    $deleteQuery = "DELETE FROM tblfaculty WHERE Id=$deleteFacultyId";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Faculty deleted successfully.')</script>";
    } else {
        echo "<script>alert('Failed to delete faculty.')</script>";
    }
}

// Handle College Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCollege'])) {
    $collegeName = mysqli_real_escape_string($conn, $_POST['collegeName']);
    $collegeHead = mysqli_real_escape_string($conn, $_POST['collegeHead']);
    $insertCol = mysqli_query($conn, "INSERT INTO tblcolleges (collegeName, collegeHead) VALUES ('$collegeName', '$collegeHead')");
    if ($insertCol) {
        echo "<script>alert('College created successfully.');</script>";
    } else {
        echo "<script>alert('Failed to create college.');</script>";
    }
}

// Handle College Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCollege'])) {
    $editCollegeId = intval($_POST['collegeId']);
    $editCollegeName = mysqli_real_escape_string($conn, $_POST['collegeName']);
    $editCollegeHead = mysqli_real_escape_string($conn, $_POST['collegeHead']);
    $updateCol = mysqli_query($conn, "UPDATE tblcolleges SET collegeName='$editCollegeName', collegeHead='$editCollegeHead' WHERE Id=$editCollegeId");
    if ($updateCol) {
        echo "<script>alert('College updated successfully.');</script>";
    } else {
        echo "<script>alert('Failed to update college.');</script>";
    }
}

// Handle College Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCollegeId'])) {
    $deleteCollegeId = intval($_POST['deleteCollegeId']);
    $deleteQuery = "DELETE FROM tblcolleges WHERE Id=$deleteCollegeId";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('College deleted successfully.')</script>";
    } else {
        echo "<script>alert('Failed to delete college.')</script>";
    }
}

$query = "SELECT Id, firstName, lastName, emailAddress, phoneNo, dateCreated FROM tblmoderator";
$rs = $conn->query($query);
$num = $rs->num_rows;
$rrw = $rs->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"
        type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
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
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Administrator Dashboard</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>
                    <div class="row mb-3" style="width: 100%">
                        <!-- Department Management Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-warning">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="mb-2 text-center">
                                        <i class="fas fa-building fa-2x text-warning"></i>
                                    </div>
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Department Management
                                    </div>
                                    <button class="btn btn-success btn-sm mt-2 mb-1" data-toggle="modal"
                                        data-target="#createDepartmentModal">Create Department</button>
                                    <button class="btn btn-info btn-sm mb-1" data-toggle="modal"
                                        data-target="#editDepartmentModal">Edit Department</button>
                                </div>
                            </div>
                        </div>
                        <!-- Faculty Management Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-danger">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="mb-2 text-center">
                                        <i class="fas fa-user-graduate fa-2x text-danger"></i>
                                    </div>
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Faculty Management</div>
                                    <button class="btn btn-success btn-sm mt-2 mb-1" data-toggle="modal"
                                        data-target="#createFacultyModal">Create Faculty</button>
                                    <button class="btn btn-info btn-sm mb-1" data-toggle="modal"
                                        data-target="#editFacultyModal">Edit Faculty</button>
                                </div>
                            </div>
                        </div>
                        <!-- College Management Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-secondary">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="mb-2 text-center">
                                        <i class="fas fa-university fa-2x text-secondary"></i>
                                    </div>
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">College Management</div>
                                    <button class="btn btn-success btn-sm mt-2 mb-1" data-toggle="modal"
                                        data-target="#createCollegeModal">Create College</button>
                                    <button class="btn btn-info btn-sm mb-1" data-toggle="modal"
                                        data-target="#editCollegeModal">Edit College</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
            <!-- Footer -->
        </div>
    </div>

    <!-- Modals for Institution Management -->
    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" role="dialog"
        aria-labelledby="createDepartmentModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="" method="post">
                <input type="hidden" name="createDepartment" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModalLabel">Create Department</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="departmentName">Name Of Department</label>
                            <input type="text" class="form-control" name="departmentName" required>
                        </div>
                        <div class="form-group">
                            <label for="departmentFaculty">Select Faculty</label>
                            <select class="form-control" name="departmentFaculty">
                                <option value="">--Select faculty --</option>
                                <?php
                                $facQuery = mysqli_query($conn, "SELECT * FROM tblfaculty");
                                while ($facRow = mysqli_fetch_assoc($facQuery)) {
                                    echo "<option value='" . $facRow['facultyName'] . "'>" . $facRow['facultyName'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="departmentHead">Head Of Department</label>
                            <input type="text" class="form-control" name="departmentHead" required>
                        </div>
                        <div class="form-group">
                            <label for="codeName">Code Name</label>
                            <p class="text-danger">Note this cannot be changed</p>
                            <input type="text" class="form-control" name="codeName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Department</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog"
        aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Faculty</th>
                                <th>Head Of Department</th>
                                <th>Code Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $departQuery = mysqli_query($conn, "SELECT * FROM tbldepartments");
                            while ($depart = mysqli_fetch_assoc($departQuery)) {
                                echo '<tr>
                                    <td>' . htmlspecialchars($depart['departmentName']) . '</td>
                                    <td>' . htmlspecialchars($depart['departmentFaculty']) . '</td>
                                    <td>' . htmlspecialchars($depart['departmentHead']) . '</td>
                                    <td>' . htmlspecialchars($depart['codeName']) . '</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="showEditDepartmentForm(' . $depart['Id'] . ', \'' . htmlspecialchars(addslashes($depart['departmentName'])) . '\', \'' . htmlspecialchars(addslashes($depart['departmentFaculty'])) . '\', \'' . htmlspecialchars(addslashes($depart['departmentHead'])) . '\', \'' . htmlspecialchars(addslashes($depart['codeName'])) . '\')">Edit</button>
                                        <form method="post" style="display:inline;" onsubmit="return confirm(&quot;Are you sure you want to delete this department?&quot;);">
                                            <input type="hidden" name="deleteDepartmentId" value="' . $depart['Id'] . '">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>                                
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div id="editDepartmentFormContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showEditDepartmentForm(id, departmentName, departmentFaculty, departmentHead, codeName) {
        var formHtml = `
            <form action="" method="post" class="mt-3">
                <input type="hidden" name="editDepartment" value="1">
                <input type="hidden" name="departmentId" value="` + id + `">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Department Name</label>
                        <input type="text" class="form-control" name="departmentName" value="` + departmentName + `" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Department Faculty</label>
                        <select class="form-control" name="departmentFaculty">
                            <option value="` + departmentFaculty + `">` + departmentFaculty + `</option>
                            <?php
                            $facQuery = mysqli_query($conn, "SELECT * FROM tblfaculty");
                            while ($facRow = mysqli_fetch_assoc($facQuery)) {
                                echo "<option value = '" . $facRow['facultyName'] . "'>" . $facRow['facultyName'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Department Head</label>
                        <input type="text" class="form-control" name="departmentHead" value="` + departmentHead + `" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Code Name</label>
                        <input type="text" class="form-control" name="codeName" value="` + codeName + `" readonly>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        `;
        document.getElementById('editDepartmentFormContainer').innerHTML = formHtml;
    }
    </script>

    <!-- Create Faculty Modal -->
    <div class="modal fade" id="createFacultyModal" tabindex="-1" role="dialog" aria-labelledby="createFacultyModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="" method="post">
                <input type="hidden" name="createFaculty" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFacultyModalLabel">Create Faculty/School</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="facultyName">Name Of Faculty</label>
                            <input type="text" class="form-control" name="facultyName" required>
                        </div>
                        <div class="form-group">
                            <label for="facultyHead">Faculty Head</label>
                            <input type="text" class="form-control" name="facultyHead" required>
                        </div>
                        <div class="form-group">
                            <label for="collegeId">Choose College</label>
                            <input type="text" class="form-control" name="collegeId" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Faculty/School</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Faculty/School Modal -->
    <div class="modal fade" id="editFacultyModal" tabindex="-1" role="dialog" aria-labelledby="editFacultyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFacultyModalLabel">Edit Faculty/School</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Faculty/School Name</th>
                                <th>Dean Of Faculty/School</th>
                                <th>College</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $facultyQuery = mysqli_query($conn, "SELECT * FROM tblfaculty");
                            while ($faculty = mysqli_fetch_assoc($facultyQuery)) {
                                echo '<tr>
                                    <td>' . htmlspecialchars($faculty['facultyName']) . '</td>
                                    <td>' . htmlspecialchars($faculty['facultyHead']) . '</td>
                                    <td>' . htmlspecialchars($faculty['collegeId']) . '</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="showEditFacultyForm(' . $faculty['Id'] . ', \'' . htmlspecialchars(addslashes($faculty['facultyName'])) . '\', \'' . htmlspecialchars(addslashes($faculty['facultyHead'])) . '\', \'' . htmlspecialchars(addslashes($faculty['collegeId'])) . '\')">Edit</button>
                                        <form method="post" style="display:inline;" onsubmit="return confirm(&quot;Are you sure you want to delete this Faculty/School?&quot;);">
                                            <input type="hidden" name="deleteFacultyId" value="' . $faculty['Id'] . '">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>                                
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div id="editFacultyFormContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showEditFacultyForm(id, facultyName, facultyHead, collegeId) {
        var formHtml = `
            <form action="" method="post" class="mt-3">
                <input type="hidden" name="editFaculty" value="1">
                <input type="hidden" name="facultyId" value="` + id + `">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Faculty Name</label>
                        <input type="text" class="form-control" name="facultyName" value="` + facultyName + `" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Faculty Dean</label>
                        <input type="text" class="form-control" name="facultyDean" value="` + facultyHead + `" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        `;
        document.getElementById('editFacultyFormContainer').innerHTML = formHtml;
    }
    </script>

    <!-- Create College Modal -->
    <div class="modal fade" id="createCollegeModal" tabindex="-1" role="dialog" aria-labelledby="createCollegeModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="" method="post">
                <input type="hidden" name="createCollege" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCollegeModalLabel">Create College</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="collegeName">Name Of College</label>
                            <input type="text" class="form-control" name="collegeName" required>
                        </div>
                        <div class="form-group">
                            <label for="collegeHead">College Head</label>
                            <input type="text" class="form-control" name="collegeHead" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save College</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit College Modal -->
    <div class="modal fade" id="editCollegeModal" tabindex="-1" role="dialog" aria-labelledby="editCollegeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCollegeModalLabel">Edit College</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th>Head Of College</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $collegeQuery = mysqli_query($conn, "SELECT * FROM tblcolleges");
                            while ($colle = mysqli_fetch_assoc($collegeQuery)) {
                                echo '<tr>
                                    <td>' . htmlspecialchars($colle['collegeName']) . '</td>
                                    <td>' . htmlspecialchars($colle['collegeHead']) . '</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="showEditCollegeForm(' . $colle['Id'] . ', \'' . htmlspecialchars(addslashes($colle['collegeName'])) . '\', \'' . htmlspecialchars(addslashes($colle['collegeHead'])) . '\')">Edit</button>
                                        <form method="post" style="display:inline;" onsubmit="return confirm(&quot;Are you sure you want to delete this college?&quot;);">
                                            <input type="hidden" name="deleteCollegeId" value="' . $colle['Id'] . '">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>                                
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div id="editCollegeFormContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showEditCollegeForm(id, collegeName, collegeHead) {
        var formHtml = `
            <form action="" method="post" class="mt-3">
                <input type="hidden" name="editCollege" value="1">
                <input type="hidden" name="collegeId" value="` + id + `">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>College Name</label>
                        <input type="text" class="form-control" name="collegeName" value="` + collegeName + `" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>College Head</label>
                        <input type="text" class="form-control" name="collegeHead" value="` + collegeHead + `" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        `;
        document.getElementById('editCollegeFormContainer').innerHTML = formHtml;
    }
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <!-- <script src="js/your-custom-script.js"></script> -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 Bootstrap 4 Theme (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
</body>

</html>