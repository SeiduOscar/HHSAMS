
<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Handle Department Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createDepartment'])) {
  $departmentName = mysqli_real_escape_string($conn, $_POST['departmentName']);
  $insertDept = mysqli_query($conn, "INSERT INTO tbldepartment (departmentName) VALUES ('$departmentName')");
  if ($insertDept) {
    $deptMsg = "<div class='alert alert-success'>Department created successfully.</div>";
  } else {
    $deptMsg = "<div class='alert alert-danger'>Failed to create department.</div>";
  }
}

// Handle Department Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editDepartment'])) {
  $editDepartmentId = intval($_POST['editDepartmentId']);
  $editDepartmentName = mysqli_real_escape_string($conn, $_POST['editDepartmentName']);
  $updateDept = mysqli_query($conn, "UPDATE tbldepartment SET departmentName='$editDepartmentName' WHERE Id=$editDepartmentId");
  if ($updateDept) {
    $deptMsg = "<div class='alert alert-success'>Department updated successfully.</div>";
  } else {
    $deptMsg = "<div class='alert alert-danger'>Failed to update department.</div>";
  }
}

// Handle Faculty Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createFaculty'])) {
  $facultyName = mysqli_real_escape_string($conn, $_POST['facultyName']);
  $insertFac = mysqli_query($conn, "INSERT INTO tblfaculty (facultyName) VALUES ('$facultyName')");
  if ($insertFac) {
    $facMsg = "<div class='alert alert-success'>Faculty created successfully.</div>";
  } else {
    $facMsg = "<div class='alert alert-danger'>Failed to create faculty.</div>";
  }
}

// Handle Faculty Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editFaculty'])) {
  $editFacultyId = intval($_POST['editFacultyId']);
  $editFacultyName = mysqli_real_escape_string($conn, $_POST['editFacultyName']);
  $updateFac = mysqli_query($conn, "UPDATE tblfaculty SET facultyName='$editFacultyName' WHERE Id=$editFacultyId");
  if ($updateFac) {
    $facMsg = "<div class='alert alert-success'>Faculty updated successfully.</div>";
  } else {
    $facMsg = "<div class='alert alert-danger'>Failed to update faculty.</div>";
  }
}

// Handle College Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCollege'])) {
  $collegeName = mysqli_real_escape_string($conn, $_POST['collegeName']);
  $insertCol = mysqli_query($conn, "INSERT INTO tblcollege (collegeName) VALUES ('$collegeName')");
  if ($insertCol) {
    $colMsg = "<div class='alert alert-success'>College created successfully.</div>";
  } else {
    $colMsg = "<div class='alert alert-danger'>Failed to create college.</div>";
  }
}

// Handle College Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCollege'])) {
  $editCollegeId = intval($_POST['editCollegeId']);
  $editCollegeName = mysqli_real_escape_string($conn, $_POST['editCollegeName']);
  $updateCol = mysqli_query($conn, "UPDATE tblcollege SET collegeName='$editCollegeName' WHERE Id=$editCollegeId");
  if ($updateCol) {
    $colMsg = "<div class='alert alert-success'>College updated successfully.</div>";
  } else {
    $colMsg = "<div class='alert alert-danger'>Failed to update college.</div>";
  }
}

// Handle Add Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addAdmin'])) {
  $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
  $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
  $emailAddress = mysqli_real_escape_string($conn, $_POST['emailAddress']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if email already exists
  $checkQuery = "SELECT * FROM tbladmin WHERE emailAddress='$emailAddress'";
  $checkResult = mysqli_query($conn, $checkQuery);
  if (mysqli_num_rows($checkResult) > 0) {
    $adminMsg = "<div class='alert alert-danger'>Email already exists.</div>";
  } else {
    $insertQuery = "INSERT INTO tbladmin (firstName, lastName, emailAddress, password) VALUES ('$firstName', '$lastName', '$emailAddress', '$password')";
    if (mysqli_query($conn, $insertQuery)) {
      $adminMsg = "<div class='alert alert-success'>Admin user added successfully.</div>";
    } else {
      $adminMsg = "<div class='alert alert-danger'>Failed to add admin user.</div>";
    }
  }
}

// Handle Edit Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editAdmin'])) {
  $adminId = intval($_POST['adminId']);
  $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
  $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
  $emailAddress = mysqli_real_escape_string($conn, $_POST['emailAddress']);
  $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

  if (!empty($newPassword)) {
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE tbladmin SET firstName='$firstName', lastName='$lastName', emailAddress='$emailAddress', password='$passwordHash' WHERE Id=$adminId";
  } else {
    $updateQuery = "UPDATE tbladmin SET firstName='$firstName', lastName='$lastName', emailAddress='$emailAddress' WHERE Id=$adminId";
  }

  if (mysqli_query($conn, $updateQuery)) {
    $adminMsg = "<div class='alert alert-success'>Admin user updated successfully.</div>";
    echo "<meta http-equiv='refresh' content='2;url=./index.php'>";
  } else {
    $adminMsg = "<div class='alert alert-danger'>Failed to update admin user.</div>";
  }
}

$query = "SELECT Id, firstName, lastName, emailAddress, phoneNo, dateCreated 
FROM tblmoderator";

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
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <!-- jQuery -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <!-- Chart.js -->
  <script src="../vendor/chart.js/Chart.min.js"></script>

  <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery, Popper.js, and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- TopBar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Container Fluid-->
      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Administrator Dashboard</h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="./">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
        </div>
        
        <div class="row mb-3">
          <!-- Students Card -->
          <?php $students = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblstudents")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-info">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Students</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $students;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-info"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $admins = mysqli_num_rows(mysqli_query($conn,"SELECT * from tbladmin")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-success">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">No. Admins</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $admins;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-code-branch fa-2x text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $class = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblclass")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-primary">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Classes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-chalkboard fa-2x text-primary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $classArms = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblclassarms")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-success">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Class Arms</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classArms;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-code-branch fa-2x text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <?php $totcourses = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblcourses")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-secondary">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Courses</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totcourses;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-book fa-2x text-secondary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $classTeacher = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblmoderator")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-danger">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Lecturers</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classTeacher;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-chalkboard-teacher fa-2x text-danger"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $sessTerm = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * from tblsemester where isActive=1")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-warning">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Semester</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sessTerm['semesterName'] ?? 'N/A';?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $termonly = mysqli_num_rows(mysqli_query($conn,"SELECT * from tblsemester")); ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-info">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">No. Semesters</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $termonly;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-th fa-2x text-info"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <?php 
            $query2 = mysqli_query($conn, "SELECT * FROM tblsemester WHERE isActive=1");                       
            $sessTerm2 = mysqli_fetch_assoc($query2);
            $totAttendance = 0;
            if ($sessTerm2) {
              $activeFrom = $sessTerm2['ActiveFrom'];
              $now = date('Y-m-d');
              $totAttendance = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblattendance WHERE dateTimeTaken BETWEEN '$activeFrom' AND '$now'"));
            }
          ?>
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-success">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Student Attendance (Current Semester)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totAttendance;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar-check fa-2x text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Admin User Management Card -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-left-primary">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="mb-2 text-center">
                  <i class="fas fa-user-shield fa-2x text-primary"></i>
                </div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Admin User Management</div>
                <button class="btn btn-success btn-sm mt-2 mb-1" data-toggle="modal" data-target="#addAdminModal">Add Admin User</button>
                <button class="btn btn-info btn-sm mb-1" data-toggle="modal" data-target="#editAdminModal">Edit Admin Users</button>
              </div>
            </div>
          </div>
         
         
              <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addAdmin'])) {
                $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
                $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
                $emailAddress = mysqli_real_escape_string($conn, $_POST['emailAddress']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Check if email already exists
                $checkQuery = "SELECT * FROM tbladmin WHERE emailAddress='$emailAddress'";
                $checkResult = mysqli_query($conn, $checkQuery);
                if (mysqli_num_rows($checkResult) > 0) {
                  echo "<div class='alert alert-danger'>Email already exists.</div>";
                } else {
                  $insertQuery = "INSERT INTO tbladmin (firstName, lastName, emailAddress, password) VALUES ('$firstName', '$lastName', '$emailAddress', '$password')";
                  if (mysqli_query($conn, $insertQuery)) {
                  echo "<div class='alert alert-success'>Admin user added successfully.</div>";
                  } else {
                  echo "<div class='alert alert-danger'>Failed to add admin user.</div>";
                  }
                }
                }
                ?>
                <form action="" method="post">
                <input type="hidden" name="addAdmin" value="1">
                <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Add Admin User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input type="text" class="form-control" name="firstName" required>
                </div>
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input type="text" class="form-control" name="lastName" required>
                </div>
                <div class="form-group">
                  <label for="emailAddress">Email Address</label>
                  <input type="email" class="form-control" name="emailAddress" required>
                </div>
                
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" name="password" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Add Admin</button>
                </div>
                </div>
                </form>
              </div>
              </div>

              <!-- Edit Admin Modal -->
              <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="editAdminModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Edit Admin Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php
                  $adminQuery = mysqli_query($conn, "SELECT * FROM tbladmin");
                  while ($admin = mysqli_fetch_assoc($adminQuery)) {
                  echo '<tr>
                  <td>' . htmlspecialchars($admin['firstName'] . ' ' . $admin['lastName']) . '</td>
                  <td>' . htmlspecialchars($admin['emailAddress']) . '</td>
                  <td>' . htmlspecialchars($admin['password']) . '</td>
                  <td>
                  <button class="btn btn-sm btn-primary" onclick="showEditAdminForm(' . $admin['Id'] . ', \'' . htmlspecialchars(addslashes($admin['firstName'])) . '\', \'' . htmlspecialchars(addslashes($admin['lastName'])) . '\', \'' . htmlspecialchars(addslashes($admin['emailAddress'])) . '\', \'' . htmlspecialchars(addslashes($admin['password'])) . '\')">Edit</button>
                  <form method="post" action="./deleteAdmin.php" style="display:inline;" onsubmit="return confirm(&quot;Are you sure you want to delete this admin user?&quot;);">
                    <input type="hidden" name="deleteAdminId" value="' . $admin['Id'] . '">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </form>                                
                  </td>
                  </tr>';
                  }
                  ?>
                </tbody>
                </table>
                <!-- Edit form will be injected here -->
                <div id="editAdminFormContainer"></div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
              </div>
              </div>


              
              <script>
              function showEditAdminForm(id, firstName, lastName, emailAddress, password) {
                var formHtml = `
                      <form action="" method="post" class="mt-3">
                      <input type="hidden" name="editAdmin" value="1">
                      <input type="hidden" name="adminId" value="` + id + `">
                      <div class="form-row">
                        <div class="form-group col-md-6">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="firstName" value="` + firstName + `" required>
                        </div>
                        <div class="form-group col-md-6">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="lastName" value="` + lastName + `" required>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="emailAddress" value="` + emailAddress + `" required>
                        </div>
                        <div class="form-group col-md-6">
                        <label>Password (hashed)</label>
                        <input type="text" class="form-control" name="password" value="` + password + `" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="newPassword">
                      </div>
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                      </form>
                `;
              
                // PHP for admin edit
               
                document.getElementById('editAdminFormContainer').innerHTML = formHtml;
              }
              </script>
              </div>
      <!---Container Fluid-->
      </div>
       <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editAdmin'])) {
                $adminId = intval($_POST['adminId']);
                $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
                $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
                $emailAddress = mysqli_real_escape_string($conn, $_POST['emailAddress']);
                $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

                if (!empty($newPassword)) {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE tbladmin SET firstName='$firstName', lastName='$lastName', emailAddress='$emailAddress', password='$passwordHash' WHERE Id=$adminId";
                } else {
                $updateQuery = "UPDATE tbladmin SET firstName='$firstName', lastName='$lastName', emailAddress='$emailAddress' WHERE Id=$adminId";
                }

                if (mysqli_query($conn, $updateQuery)) {
                 echo "<div class='alert alert-success'>Admin user updated successfully.</div>";
                 echo "<meta http-equiv='refresh' content='2;url=./index.php'>";
                } else {
                echo "<div class='alert alert-danger'>Failed to update admin user.</div>";
                }
              }

                ?>

      <!-- Footer -->
      <?php include 'includes/footer.php';?>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>  
</body>

</html>