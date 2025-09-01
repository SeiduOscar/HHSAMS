
<?php 
error_reporting(1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if(isset($_POST['save'])){
    
    $firstName=$_POST['firstName'];
  $lastName=$_POST['lastName'];
  $emailAddress=$_POST['emailAddress'];
  $facultyName = $_POST['faculty'];
  $phoneNo=$_POST['phoneNo'];
  $dateCreated = date("Y-m-d");
   
    $query=mysqli_query($conn,"select * from tblmoderator where emailAddress ='$emailAddress'");
    $ret=mysqli_fetch_array($query);

    $sampPass = $_POST['password'];
    $sampPass_2 = md5($sampPass);

    if($ret > 0){ 

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address Already Exists!</div>";
    }
    // else{

    //     $query=mysqli_query($conn,"select * from tblclassarms where Id ='$classArmId'");
    //     $ret=mysqli_fetch_array($query);
    //     if($ret > 0){ 

    //         $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Class Arm Already Assigned!</div>";
    //     }
    //     else{
    //         $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    //     }
    // }

    else{
        
    
        
    $query=mysqli_query($conn,"INSERT into tblmoderator(firstName,lastName,emailAddress,password,phoneNo,dateCreated, Faculty) 
    value('$firstName','$lastName','$emailAddress','$sampPass_2','$phoneNo','$dateCreated', '$facultyName')");

    if ($query) {
                $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
           
    }
    else
    {
         $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
         
  }
}
//---------------------------------------EDIT-------------------------------------------------------------






//--------------------EDIT------------------------------------------------------------

 if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit")
	{
        $Id= $_GET['Id'];

        $query=mysqli_query($conn,"select * from tblmoderator where Id ='$Id'");
        $row=mysqli_fetch_array($query);

        //------------UPDATE-----------------------------

        if(isset($_POST['update'])){

          $Ppassword = mysqli_query($conn, "SELECT * FROM tblmoderator WHERE Id = $Id");
          $rowp = mysqli_fetch_assoc($Ppassword);
    
             $firstName=$_POST['firstName'];
              $lastName=$_POST['lastName'];
              $emailAddress=$_POST['emailAddress'];
              $phoneNo=$_POST['phoneNo'];
              $dateCreated = date("Y-m-d");
              
              $facultyName = $_POST['faculty'];

              $pass = $_POST['password'];
              $password = md5($pass);              
              

              $query=mysqli_query($conn,"update tblmoderator set firstName='$firstName', lastName='$lastName',
    emailAddress='$emailAddress', password='$password',phoneNo='$phoneNo', Faculty='$facultyName'
    where Id='$Id'");
              

   
            if ($query) {
                
                echo "<script type = \"text/javascript\">
                window.location = (\"createClassTeacher.php\")
                </script>"; 
            }
            else
            {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
            }
        }
    }


//--------------------------------DELETE------------------------------------------------------------------

  if (isset($_GET['Id'])  && isset($_GET['action']) && $_GET['action'] == "delete")
	{
        $Id= $_GET['Id'];

        $query = mysqli_query($conn,"DELETE FROM tblmoderator WHERE Id='$Id'");

        if ($query == TRUE) {

          
                
                 echo "<script type = \"text/javascript\">
                window.location = (\"createClassTeacher.php\")
                </script>"; 
           
        }
        else{

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
        xmlhttp.open("GET","ajaxClassArms.php?cid="+str,true);
        xmlhttp.send();
    }
}
</script>
</head>
<style>
      #sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1030;
      width: 150px;
      
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }
      #content-wrapper {
      padding-left: 220px;
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
    <div id="sidebar">
      <?php include "Includes/sidebar.php";?>
    </div>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Lecturers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Lecturers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Lecturers</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo $row['firstName'];?>" id="exampleInputFirstName">
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="lastName" value="<?php echo $row['lastName'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Password<span class="text-danger ml-2">*</span></label>
                      <input type="password" class="form-control" required name="password" value="<?php echo $row['password']?>" id="exampleInputFirstName" >
                      
                        </div>
                        
                    </div>
                     <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo $row['emailAddress'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Phone No<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="phoneNo" value="<?php echo $row['phoneNo'];?>" id="exampleInputFirstName" >
                        </div>
                       
                  </select>
                      </div>
                        <div class="form-group row mb-3">
                          <div class="col-xl-6">
                         <label class="form-control-label">Faculty<span class="text-danger ml-2">*</span></label>
                        <select name="faculty" class="form-control select2"  required>
                          <option value="">-- Select Faculty --</option>
                      <?php
                      $facultyQuery = mysqli_query($conn, "SELECT * FROM tblfaculty");

                      if ($facultyQuery && $facultyQuery->num_rows > 0) {
                      // Reset pointer for edit mode
                       $facultyQuery->data_seek(0);
                       while ($row = $facultyQuery->fetch_assoc()) {
                           $selected = (isset($assignedLecturers) && in_array($row['Id'], $assignedLecturers)) ? 'selected' : '';
                         echo '<option value="' . htmlspecialchars($row['facultyName']) . '" ' .'>' . htmlspecialchars($row['facultyName']) . '</option>';
                          }
                      }
                      ?>
                      </select>
                        </div>
                    </div>
                    <br>
                      <?php
                    if (isset($Id))
                    {
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
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Lecturers</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Faculty</th>
                        <th>Password</th>
                        <th>Phone No</th>
                        <th>Date Created</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                   
                    <tbody>

                  <?php
                      $query = "SELECT tblmoderator.Id,
                      tblmoderator.firstName,
                      tblmoderator.lastName,
                      tblmoderator.emailAddress,
                      tblmoderator.phoneNo,
                      tblmoderator.dateCreated,
                      tblmoderator.password,
                      tblmoderator.Faculty
               FROM tblmoderator";    
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['emailAddress']."</td>
                                <td>".$rows['Faculty']."</td>
                                <td> ".$rows['password']."</td>
                                <td>".$rows['phoneNo']."</td>
                                 <td>".$rows['dateCreated']."</td>
                                 <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-fw fa-edit'></i>Edit</a></td>
                                <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                          }
                      }
                      else
                      {
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
       <?php include "Includes/footer.php";?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="js/your-custom-script.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>