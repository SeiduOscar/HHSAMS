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
  $classId = $_POST['classId'];
  $classArmId = $_POST['classArmId'];
  $dateCreated = date("Y-m-d");

  // Check for duplicate student
  $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber ='$admissionNumber'");
  if (mysqli_num_rows($query) > 0) {
    $statusMsg = "<div class='alert alert-danger'>Admission number already exists!</div>";
  } else {
    // Check if class arm is already assigned to another student
    $query = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE Id ='$classArmId'");
    if (mysqli_num_rows($query) > 0) {
      // Proceed to insert new student
      $query = mysqli_query($conn, "INSERT INTO tblstudents(firstName,lastName,otherName,admissionNumber,password,classId,classArmId,dateCreated) 
            VALUES('$firstName','$lastName','$otherName','$admissionNumber','12345','$classId','$classArmId','$dateCreated')");

      if ($query) {
        echo "<script>
                    alert('Created Successfully!');
                    window.location.href = 'createStudents.php';
                    </script>";
        exit(); // Ensure nothing continues running
      } else {
        $statusMsg = "<div class='alert alert-danger'>Insert failed. Please try again.</div>";
      }
    } else {
      $statusMsg = "<div class='alert alert-danger'>Invalid class arm!</div>";
    }
  }
  $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
  echo "<script type = \"text/javascript\">
    window.location = (\"createStudents.php\")
    </script>";

}

//}


//---------------------------------------EDIT-------------------------------------------------------------






//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "select * from tblstudents where Id ='$Id'");
  $row = mysqli_fetch_array($query);

  //------------UPDATE-----------------------------

  if (isset($_POST['update'])) {

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];

    $admissionNumber = $_POST['admissionNumber'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $dateCreated = date("Y-m-d");

    $query = mysqli_query($conn, "update tblstudents set firstName='$firstName', lastName='$lastName',
    otherName='$otherName', admissionNumber='$admissionNumber',password='12345', classId='$classId',classArmId='$classArmId'
    where Id='$Id'");
    if ($query) {

      echo "<script type = \"text/javascript\">
                window.location = (\"createStudents.php\")
                </script>";
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  }
  $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
  echo "<script type = \"text/javascript\">
    window.location = (\"createStudents.php\")
    </script>";

}


//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];
  $classArmId = $_GET['classArmId'];

  $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$Id'");

  if ($query == TRUE) {

    echo "<script type = \"text/javascript\">
            window.location = (\"createStudents.php\")
            </script>";
  } else {

    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
  }
  $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
  echo "<script type = \"text/javascript\">
    window.location = (\"createStudents.php\")
    </script>";

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

    #main-content {
        margin-left: 10px;
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
</head>
<!-- <style>
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
</style> -->

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
                <div id="main-content">
                    <!-- Container Fluid-->
                    <form method="post" class="p-3 rounded shadow-sm bg-light">
                        <div class="mb-3">
                            <label for="valid_duration" class="form-label fw-bold">Link Validity Period (hours)</label>
                            <input type="number" name="valid_duration" id="valid_duration" placeholder="e.g. 24"
                                class="form-control" required>
                            <div class="form-text">Enter the number of hours the link should remain valid.</div>
                        </div>
                        <button type="submit" name="addMultipleStudents" class="btn btn-primary w-100">
                            ➕ Add Students
                        </button>
                    </form>

                    <?php
          $link = ""; // Initialize
          if (isset($_POST['addMultipleStudents'])) {
            $token = bin2hex(random_bytes(16));
            $_SESSION['qrcode_token'] = $token;
            $validPeriod = $_POST['valid_duration'];

            // Insert token into database
            $stmtInsert = $conn->prepare("INSERT INTO qr_tokens (token, is_valid, created_at) VALUES (?, 1, NOW())");
            $stmtInsert->bind_param("s", $token);
            $stmtInsert->execute();
            $stmtInsert->close();

            // Verify token validity
            $stmt = $conn->prepare("SELECT COUNT(*) FROM qr_tokens WHERE token = ? AND is_valid = 1 and created_at > NOW() - INTERVAL ? HOUR");
            $stmt->bind_param("si", $token, $validPeriod);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
              $link = "https://192.168.94.143/Student-Attendance-Management-System-main/create_students.php?token=$token&valid=$validPeriod";
            }
          }
          ?>

                    <!-- Link container (hidden by default) -->
                    <?php if (!empty($link)): ?>
                    <div id="linkContainer" class="d-flex align-items-center gap-2 p-3 border rounded bg-light mt-3">
                        <input type="text" id="copyLink" value="<?php echo $link; ?>" readonly class="form-control">
                        <button type="button" class="btn btn-primary" onclick="copyLink()">Copy</button>
                    </div>

                    <script>
                    function copyLink() {
                        const copyText = document.getElementById('copyLink');
                        copyText.select();
                        copyText.setSelectionRange(0, 99999); // mobile support
                        navigator.clipboard.writeText(copyText.value).then(() => {
                            alert('Link copied: ' + copyText.value);
                        });
                    }
                    </script>
                    <?php endif; ?>





                    <!-- Input Group -->
                    <?php
          // Assume $conn is your MySQLi connection
          
          $limit = 10;  // results per page
          $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
          if ($page < 1)
            $page = 1;

          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          $classArmFilter = isset($_GET['classArm']) ? trim($_GET['classArm']) : '';

          $search_escaped = $conn->real_escape_string($search);
          $classArm_escaped = $conn->real_escape_string($classArmFilter);

          // Build WHERE conditions for search and filter
          $whereClauses = [];

          if ($search !== '') {
            $whereClauses[] = "(firstName LIKE '%$search_escaped%' OR lastName LIKE '%$search_escaped%')";
          }
          if ($classArmFilter !== '') {
            // Assuming classArm is stored as string or id in tblstudents.classArm
            $whereClauses[] = "classArm = '$classArm_escaped'";
          }

          $where = '';
          if (count($whereClauses) > 0) {
            $where = "WHERE " . implode(" AND ", $whereClauses);
          }

          // Get total rows count for pagination
          $countSql = "SELECT COUNT(*) AS total FROM tblstudents $where";
          $countResult = $conn->query($countSql);
          $totalRows = 0;
          if ($countResult) {
            $row = $countResult->fetch_assoc();
            $totalRows = (int) $row['total'];
          }
          $totalPages = ceil($totalRows / $limit);
          if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;  // clamp page if out of range
          }
          $offset = ($page - 1) * $limit;

          // Fetch paginated results with WHERE filter and search
          $sql = "SELECT * FROM tblstudents $where ORDER BY Id LIMIT $limit OFFSET $offset";
          $result = $conn->query($sql);

          // Fetch distinct classArms for filter dropdown (optional: replace with your actual classArm source table)
          $classArmOptions = [];
          $classArmSql = "SELECT DISTINCT classArm FROM tblstudents ORDER BY classArm ASC";
          $classArmResult = $conn->query($classArmSql);
          if ($classArmResult) {
            while ($row = $classArmResult->fetch_assoc()) {
              $classArmOptions[] = $row['classArm'];
            }
          }
          ?>

                    <!-- Search & Filter Form -->
                    <form method="get" action="" class="mb-3 form-inline">
                        <div class="form-group mr-2">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by First or Last Name"
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <div class="form-group mr-2">
                            <select name="classArm" class="form-control">
                                <option value="">-- Filter by Class Arm --</option>
                                <?php foreach ($classArmOptions as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php if ($option === $classArmFilter)
                       echo "selected"; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Apply</button>
                    </form>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">All Students</h6>
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
                                                <th>Password</th>
                                                <th>Department</th>
                                                <th>Program</th>
                                                <th>Email</th>
                                                <th>Class Arm</th>
                                                <th>Date Created</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                      if ($result && $result->num_rows > 0) {
                        $sn = $offset;
                        while ($row = $result->fetch_assoc()) {
                          $sn++;
                          echo "<tr>
                      <td>{$sn}</td>
                      <td>" . htmlspecialchars($row['firstName']) . "</td>
                      <td>" . htmlspecialchars($row['lastName']) . "</td>
                      <td>" . htmlspecialchars($row['otherName']) . "</td>
                      <td>" . htmlspecialchars($row['admissionNumber']) . "</td>
                      <td>" . htmlspecialchars($row['password']) . "</td>
                      <td>" . htmlspecialchars($row['Department']) . "</td>
                      <td>" . htmlspecialchars($row['program']) . "</td>
                      <td>" . htmlspecialchars($row['email']) . "</td>
                      <td>" . htmlspecialchars($row['classArm']) . "</td>
                      <td>" . htmlspecialchars($row['dateCreated']) . "</td>
                      <td><a href='?action=edit&Id={$row['Id']}&page=$page&search=" . urlencode($search) . "&classArm=" . urlencode($classArmFilter) . "'><i class='fas fa-fw fa-edit'></i></a></td>
                      <td><a href='?action=delete&Id={$row['Id']}&page=$page&search=" . urlencode($search) . "&classArm=" . urlencode($classArmFilter) . "' onclick=\"return confirm('Are you sure?');\"><i class='fas fa-fw fa-trash'></i></a></td>
                  </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='13' class='text-center'>No Records Found</td></tr>";
                      }
                      ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php if ($page <= 1)
                      echo 'disabled'; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&classArm=<?php echo urlencode($classArmFilter); ?>">Previous</a>
                                        </li>

                                        <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    for ($i = $startPage; $i <= $endPage; $i++) {
                      $active = ($i == $page) ? 'active' : '';
                      echo "<li class='page-item $active'><a class='page-link' href='?page=$i&search=" . urlencode($search) . "&classArm=" . urlencode($classArmFilter) . "'>$i</a></li>";
                    }
                    ?>

                                        <li class="page-item <?php if ($page >= $totalPages)
                      echo 'disabled'; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&classArm=<?php echo urlencode($classArmFilter); ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="js/your-custom-script.js"></script>

    <!-- Page level custom scripts -->
    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable(); // ID From dataTable 
        $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
    </script>
</body>

</html>