<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';



//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {

  // $sessionName=$_POST['sessionName'];
  $semesterId = $_POST['semesterName'];
  $dateCreated = date("Y-m-d");

  // $query=mysqli_query($conn,"select * from tblsession where sessionName ='$sessionName' and semesterId = '$semesterId'");
  // $ret=mysqli_fetch_array($query);

  // if($ret > 0){ 

  //     $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Session and Term Already Exists!</div>";
  // }


  $query = mysqli_query($conn, "insert into tblsemester(semesterName,isActive,dateCreated) value('$semesterName','0','$dateCreated')");

  if ($query) {

    $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
  }
}


//---------------------------------------EDIT-------------------------------------------------------------






//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "select * from tblsemester where Id ='$Id'");
  $row = mysqli_fetch_array($query);

  //------------UPDATE-----------------------------

  if (isset($_POST['update'])) {

    //         $sessionName=$_POST['sessionName'];
    $semesterId = $_POST['semesterId'];
    $dateCreated = date("Y-m-d");

    $query = mysqli_query($conn, "update tblsemester set semesterName='$semesterName',Id='$semesterId',isActive='0' where Id='$Id'");

    if ($query) {

      echo "<script type = \"text/javascript\">
                window.location = (\"createSessionTerm.php\")
                </script>";
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  }
}


//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "DELETE FROM tblsmester WHERE Id='$Id'");

  if ($query == TRUE) {

    echo "<script type = \"text/javascript\">
                window.location = (\"createSessionTerm.php\")
                </script>";
  } else {

    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
  }

}


//--------------------------------ACTIVATE------------------------------------------------------------------

// if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "activate")
// {
//       $Id= $_GET['Id'];
//       $date = date("Y-m-d");

//       // Reset ActiveFrom and ActiveTo for all semesters
//       $conn->query("UPDATE tblsemester SET ActiveFrom = NULL, ActiveTo = NULL");

//       // Set all semesters to inactive
//       $query = mysqli_query($conn, "UPDATE tblsemester SET isActive='0' WHERE isActive='1'");

//       if ($query) {
//           // Activate the selected semester
//           $que = mysqli_query($conn, "UPDATE tblsemester SET isActive='1' WHERE Id='$Id'");

//           if ($que) {
//               // Use switch to handle ActiveFrom and ActiveTo based on the semester being activated
//               $semesterQuery = mysqli_query($conn, "SELECT semesterName FROM tblsemester WHERE Id='$Id'");
//               $semesterRow = mysqli_fetch_assoc($semesterQuery);
//               $semesterName = $semesterRow['semesterName'];

//               switch ($semesterName) {
//                   case 'First':
//                       $conn->query("UPDATE tblsemester SET ActiveFrom = '$date' WHERE Id = '$Id'");
//                       break;
//                   case 'Second':
//                       $conn->query("UPDATE tblsemester SET ActiveTo = '$date' WHERE Id = '$Id'");
//                       break;
//                   default:
//                       // For other semesters, you can add more cases if needed
//                       break;
//               }

//               echo "<script type = \"text/javascript\">
//               window.location = (\"createSessionTerm.php\")
//               </script>";
//           } else {
//               $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
//           }
//       } else {
//           $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
//       }
// }
/*
 * Ensure that when one semester is activated and its ActiveFrom is set,
 * the ActiveTo in the other semester is set to the same date.
 */
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "activate") {
  $Id = $_GET['Id'];
  $date = date("Y-m-d");

  // Reset ActiveFrom and ActiveTo for all semesters
  $conn->query("UPDATE tblsemester SET ActiveFrom = NULL, ActiveTo = NULL");

  // Set all semesters to inactive
  $query = mysqli_query($conn, "UPDATE tblsemester SET isActive='0' WHERE isActive='1'");

  if ($query) {
    // Activate the selected semester
    $que = mysqli_query($conn, "UPDATE tblsemester SET isActive='1' WHERE Id='$Id'");

    if ($que) {
      // Get the semester name and the other semester's Id
      $semesterQuery = mysqli_query($conn, "SELECT Id, semesterName FROM tblsemester WHERE Id='$Id'");
      $semesterRow = mysqli_fetch_assoc($semesterQuery);
      $semesterName = $semesterRow['semesterName'];

      // Find the other semester (assuming only two: First and Second)
      $otherSemesterName = ($semesterName == 'First') ? 'Second' : 'First';
      $otherSemesterQuery = mysqli_query($conn, "SELECT Id FROM tblsemester WHERE semesterName='$otherSemesterName' LIMIT 1");
      $otherSemesterRow = mysqli_fetch_assoc($otherSemesterQuery);
      $otherSemesterId = $otherSemesterRow ? $otherSemesterRow['Id'] : null;

      if ($semesterName == 'First') {
        // Set ActiveFrom for First, ActiveTo for Second
        $conn->query("UPDATE tblsemester SET ActiveFrom = '$date' WHERE Id = '$Id'");
        if ($otherSemesterId) {
          $conn->query("UPDATE tblsemester SET ActiveTo = '$date' WHERE Id = '$otherSemesterId'");
        }
      } elseif ($semesterName == 'Second') {
        // Set ActiveFrom for Second, ActiveTo for First
        $conn->query("UPDATE tblsemester SET ActiveFrom = '$date' WHERE Id = '$Id'");
        if ($otherSemesterId) {
          $conn->query("UPDATE tblsemester SET ActiveTo = '$date' WHERE Id = '$otherSemesterId'");
        }
      }
      // For other semesters, add more cases if needed

      echo "<script type = \"text/javascript\">
      window.location = (\"createSessionTerm.php\")
      </script>";
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
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
    <?php include 'includes/title.php'; ?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<style>
#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1030;
    width: 150px;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#content-wrapper {
    padding-left: 0px;
}

@media (max-width: 768px) {
    #sidebar {
        position: static;
        width: 100%;
        height: auto;
    }

    #content-wrapper {
        margin-left: 0;
    }
}
</style>

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
                        <h1 class="h3 mb-0 text-gray-800">Create Semester </h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Semester</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Create Semester</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row mb-3">

                                            <div class="col-xl-6">
                                                <label class="form-control-label">Semester<span
                                                        class="text-danger ml-2">*</span></label>
                                                <input type="text" name="semesterName" class="form-control"
                                                    placeholder="Enter Semester Name" value="<?php if (isset($row['semesterName'])) {
                            echo $row['semesterName'];
                          } ?>" required>
                                                <!-- <?php
                        // $qry= "SELECT  `Id`, `semesterName` FROM tblsemester ORDER BY semesterName ASC";
                        // $result = $conn->query($qry);
                        // $num = $result->num_rows;		
                        // if ($num > 0){
                        //   echo ' <select required name="semesterId" class="form-control mb-3">';
                        //   echo'<option value="">--Select Semester--</option>';
                        //   while ($rows = $result->fetch_assoc()){
                        //   echo'<option value="'.$rows['Id'].'" >'.$rows['semesterName'].'</option>';
                        //       }
                        //           echo '</select>';
                        //       }
                        ?>   -->
                                            </div>
                                        </div>
                                        <?php
                    if (isset($Id)) {
                      ?>
                                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                    } else {
                      ?>
                                        <button type="submit" name="save" class="btn btn-primary">Save</button>
                                        <?php
                    }
                    ?>
                                    </form>
                                </div>
                            </div>

                            <!-- Input Group -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card mb-4">
                                        <div
                                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                            <h6 class="m-0 font-weight-bold text-primary">All Semesters</h6>
                                            <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the check
                                                    symbol besides a semester
                                                    to activate!</i></h6>
                                        </div>
                                        <div class="table-responsive p-3">
                                            <table class="table align-items-center table-flush table-hover"
                                                id="dataTableHover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>#</th>

                                                        <th>Semester</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Activate</th>
                                                        <th>Edit</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                          $query = "SELECT * FROM tblsemester ORDER BY dateCreated DESC";
                          $rs = $conn->query($query);
                          $num = $rs->num_rows;
                          $sn = 0;
                          if ($num > 0) {
                            while ($rows = $rs->fetch_assoc()) {
                              if ($rows['isActive'] == '1') {
                                $status = "Active";
                              } else {
                                $status = "InActive";
                              }
                              $sn = $sn + 1;
                              $date = DATE('Y-M-D');
                              echo "
                              <tr>
                                <td>" . $sn . "</td>
                                <td>" . $rows['semesterName'] . "</td>
                                <td>" . $status . "</td>
                                <td>" . $rows['dateCreated'] . "</td>

                                 <td><a href='?action=activate&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-check'></i></a></td>
                                <td><a href='?action=edit&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                            }
                          } else {
                            echo
                              "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
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
                        <?php
            // Include the promotion initialization script
            include_once('../Includes/intialize_promotion.php');
            ?>
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


        <!-- Page level custom scripts -->
        <script>
        $(document).ready(function() {
            $('#dataTable').DataTable(); // ID From dataTable 
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
        </script>
</body>

</html>