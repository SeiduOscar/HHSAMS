<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//------------------------EDIT/DELETE/SAVE/UPDATE-------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = intval($_GET['Id']);
    
    // Get course details
    $stmt = $conn->prepare("SELECT * FROM tblcourses WHERE Id = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    
    // Get assigned lecturers
    $assignedLecturers = [];
    $stmt = $conn->prepare("SELECT lecturerId FROM tblcourselecturers WHERE courseId = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($lecturer = $result->fetch_assoc()) {
        $assignedLecturers[] = $lecturer['lecturerId'];
    }
}

//------------------------DELETE------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = intval($_GET['Id']);
    
    try {
        $conn->begin_transaction();
        
        // Delete from junction table first
        $stmt = $conn->prepare("DELETE FROM tblcourselecturers WHERE courseId = ?");
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        
        // Then delete from courses table
        $stmt = $conn->prepare("DELETE FROM tblcourses WHERE Id = ?");
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        
        $conn->commit();
        echo "<script>alert('Course deleted successfully'); window.location='createCourses.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting course: " . $e->getMessage());
        echo "<script>alert('Error deleting course');</script>";
    }
}

//------------------------SAVE/UPDATE-------------------------------------------
if(isset($_POST['save']) || isset($_POST['update'])){
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $Id = isset($_POST['Id']) ? intval($_POST['Id']) : null;
    $courseCode = htmlspecialchars($_POST['courseCode']);
    $courseName = htmlspecialchars($_POST['courseName']);
    $semester = intval($_POST['semester']);
    $level = isset($_POST['level']) ? intval($_POST['level']) : null;
    $generality = intval($_POST['generality']);

    // Handle program as string (join if multiple selected)
    if (isset($_POST['program']) && is_array($_POST['program'])) {
        $program = implode('/', array_map('htmlspecialchars', $_POST['program']));
    } else {
        $program = isset($_POST['program']) ? htmlspecialchars($_POST['program']) : "General";
    }

    $dateCreated = date("Y-m-d");

    try {
        $conn->begin_transaction();

        if(isset($_POST['update'])) {
            // Update course
            $stmt = $conn->prepare("UPDATE tblcourses SET 
                courseCode = ?,
                courseName = ?,
                Level = ?,
                general = ?,
                program = ?,
                semester = ?
                WHERE Id = ?");
            $stmt->bind_param("ssiissi", $courseCode, $courseName, $level, $generality, $program, $semester, $Id);
            $stmt->execute();
            
            // Delete existing lecturer assignments
            $stmt = $conn->prepare("DELETE FROM tblcourselecturers WHERE courseId = ?");
            $stmt->bind_param("i", $Id);
            $stmt->execute();
        } else {
            // ====== HIGHLIGHT START: Creation of a new course ======
            // Insert new course
            $stmt = $conn->prepare("INSERT INTO tblcourses (courseCode, courseName, Level, general, program, semester, dateCreated) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiisss", $courseCode, $courseName, $level, $generality, $program, $semester, $dateCreated);
            $stmt->execute();
            $Id = $conn->insert_id;
            // ====== HIGHLIGHT END ======
        }
        // Assign lecturers to the course
        if(isset($_POST['lecturers'])) {
            $stmt = $conn->prepare("INSERT INTO tblcourselecturers(courseId, lecturerId, dateAssigned) 
                VALUES(?, ?, ?)");
            $lecturersArray = $_POST['lecturers'];
            if (!is_array($lecturersArray)) {
                $lecturersArray = [$lecturersArray];
            }
            foreach ($lecturersArray as $lecturer) {
                $lecturerId = intval($lecturer);
                $stmt->bind_param("iis", $Id, $lecturerId, $dateCreated);
                $stmt->execute();
            }
        }
        $conn->commit();
        
        if(isset($_POST['update'])) {
            $statusMsg = "<div class='alert alert-success'>Course updated successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-success'>Course created successfully!</div>";
        }
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Database error: " . $e->getMessage());
        $statusMsg = "<div class='alert alert-danger'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Fetch classes and lecturers for dropdowns
$classQuery = $conn->query("SELECT * FROM tblclass");
$coursesQuery = $conn->query("SELECT * FROM tblcourses");
$classArmQuery = $conn->query("SELECT * FROM tblclassarms");
$programQuery = $conn->query("SELECT * FROM tblprograms");

$resultprog = $programQuery->fetch_assoc();
$progcodeName = $resultprog['ProgramDepartmentCodeName'];
$progDepartmentQuery = mysqli_query($conn, "SELECT departmentFaculty FROM tbldepartments WHERE codeName = '$progcodeName'");

// Get all lecturers for the select2 dropdown
$lecturerQuery = $conn->query("SELECT Id, CONCAT(firstName, ' ', lastName) AS name FROM tblmoderator");
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
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Select2 Bootstrap 4 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
    <!-- <style>
    Fix for Select2 width inside Bootstrap modal or container
    .select2-container {
        width: 100% !important;
    } -->
    <!-- </style> -->




</head>
<style>
#accordionSidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1030;
    width: 220px;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#content-wrapper {
    padding-left: 220px;
    overflow-x: hidden;
    position: relative;
    z-index: 1;
    background-color: #f8f9fc;
    min-height: 100vh;
}

@media (max-width: 768px) {
    #accordionSidebar {
        position: static;
        width: fit-content;
        height: auto;
        display: block !important;
    }

    #content-wrapper {
        margin-left: 0%;
        padding-left: 0;
    }
}
</style>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="accordionSidebar">
            <?php include "Includes/sidebar.php";?>
            <!-- Sidebar -->
        </div>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php";?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Create Courses</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Courses</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Create Course</h6>
                                    <?php if (isset($statusMsg)) echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <div class="form-group">
                                            <label>Course Code</label>
                                            <input type="text" class="form-control" name="courseCode"
                                                value="<?php echo isset($course['courseCode']) ? $course['courseCode'] : ''; ?>"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label>Course Name</label>
                                            <input type="text" class="form-control" name="courseName"
                                                value="<?php echo isset($course['courseName']) ? $course['courseName'] : ''; ?>"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Level</label>
                                            <select name="level" class="form-control" required>
                                                <option value="">--Select Class--</option>
                                                <option value="100"
                                                    <?php if(isset($course['Level']) && $course['Level'] == 100) echo 'selected'; ?>>
                                                    LEVEL 100</option>
                                                <option value="200"
                                                    <?php if(isset($course['Level']) && $course['Level'] == 200) echo 'selected'; ?>>
                                                    LEVEL 200</option>
                                                <option value="300"
                                                    <?php if(isset($course['Level']) && $course['Level'] == 300) echo 'selected'; ?>>
                                                    LEVEL 300</option>
                                                <option value="400"
                                                    <?php if(isset($course['Level']) && $course['Level'] == 400) echo 'selected'; ?>>
                                                    LEVEL 400</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Program</label>
                                            <select name="program[]" class="form-control select2" multiple="multiple">
                                                <?php
        if ($programQuery && $programQuery->num_rows > 0) {
            $selectedPrograms = [];

            // Handle case when editing: programs are stored as "IT/IS"
            if (isset($course['program'])) {
                $selectedPrograms = array_map('trim', explode('/', $course['program']));
            }

            $programQuery->data_seek(0);

            while ($prog = $programQuery->fetch_assoc()) {
                $progName = htmlspecialchars($prog['ProgramCodeName']);
                $selected = in_array($prog['ProgramCodeName'], $selectedPrograms) ? 'selected' : '';
                echo "<option value=\"$progName\" $selected>$progName</option>";
            }
        }
        ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Select Generality</label>
                                            <select name="generality" class="form-control" required>
                                                <option value="">--Select Generality--</option>
                                                <option value="1"
                                                    <?php if(isset($course['general']) && $course['general'] == 1) echo 'selected'; ?>>
                                                    1</option>
                                                <option value="0"
                                                    <?php if(isset($course['general']) && $course['general'] == 0) echo 'selected'; ?>>
                                                    0</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Semester</label>
                                            <select name="semester" class="form-control" required>
                                                <option value="">--Select Semester--</option>
                                                <option value="1"
                                                    <?php if(isset($course['semester']) && $course['semester'] == 1) echo 'selected'; ?>>
                                                    First</option>
                                                <option value="2"
                                                    <?php if(isset($course['semester']) && $course['semester'] == 2) echo 'selected'; ?>>
                                                    Second</option>
                                            </select>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-12">
                                                <label class="form-control-label">Select Lecturers<span
                                                        class="text-danger ml-2">*</span></label>
                                                <select name="lecturers" class="form-control select2" required>
                                                    <option value="">-- Select Lecturers --</option>
                                                    <?php
                      if ($lecturerQuery && $lecturerQuery->num_rows > 0) {
                      // Reset pointer for edit mode
                      $lecturerQuery->data_seek(0);
                      while ($row = $lecturerQuery->fetch_assoc()) {
                          $selected = (isset($assignedLecturers) && in_array($row['Id'], $assignedLecturers)) ? 'selected' : '';
                          echo '<option value="' . htmlspecialchars($row['Id']) . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                      }
                      }
                      ?>
                                                </select>
                                            </div>
                                            </br>

                                            <?php if(isset($Id)): ?>
                                            <input type="hidden" name="Id" value="<?php echo $Id; ?>">
                                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                                            <?php else: ?>
                                            <button type="submit" name="save" class="btn btn-primary">Save</button>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>


                                <div class="container mt-5">
                                    <h2>Course List</h2>
                                    <div class="table-responsive" style="overflow-x:auto;">
                                        <table class="table table-bordered" style="min-width: 700px;">
                                            <thead>
                                                <tr>
                                                    <th>Course Code</th>
                                                    <th>Course Name</th>
                                                    <th>Class</th>
                                                    <th>Lecturers</th>
                                                    <th>Semester</th>
                                                    <th>Program</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
            // Fetch courses with class, class arm, and lecturers
            $courseQuery = $conn->query("
                SELECT c.Id, c.courseCode, c.courseName, 
                    c.Level, c.semester, c.program 
                FROM tblcourses c
                LEFT JOIN tblclass cl ON c.classId = cl.Id  
            ");

            while ($course = $courseQuery->fetch_assoc()) {
                // Get lecturers for this course
                $lecturers = [];
                $lecturerStmt = $conn->prepare("
                    SELECT CONCAT(m.firstName, ' ', m.lastName) AS name
                    FROM tblcourselecturers l
                    JOIN tblmoderator m ON l.lecturerId = m.Id
                    WHERE l.courseId = ?
                ");
                $lecturerStmt->bind_param("i", $course['Id']);
                $lecturerStmt->execute();
                $lecturerResult = $lecturerStmt->get_result();
                while ($lect = $lecturerResult->fetch_assoc()) {
                    $lecturers[] = htmlspecialchars($lect['name']);
                }
                $lecturerList = implode(', ', $lecturers);

                echo "<tr>
                    <td>" . htmlspecialchars($course['courseCode']) . "</td>
                    <td>" . htmlspecialchars($course['courseName']) . "</td>
                    <td> Level " . htmlspecialchars($course['Level']  ?? '') . "</td>
                    

                    <td>" . $lecturerList . "</td>

                    <td>" . htmlspecialchars($course['semester']) . "</td>
                    <td>" . htmlspecialchars($course['program']) . "</td>

                    <td>
                        <a href='?action=edit&Id={$course['Id']}' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='?action=delete&Id={$course['Id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this course?')\">Delete</a>
                    </td>
                    
                </tr>";
            }
            ?>
                                                <!-- No courses found message -->
                                                <?php if ($courseQuery->num_rows == 0): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No courses found.</td>
                                                </tr>
                                                <?php endif; ?>

                                                </br>
                                            </tbody>
                                        </table>
                                    </div>
                                    </br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
            <link
                href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css"
                rel="stylesheet" />

            <!-- Select2 JS -->
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <script>
            document.addEventListener("DOMContentLoaded", () => {
                const sidebar = document.getElementById("accordionSidebar"); // ✅ must match your sidebar ID
                const toggleBtn = document.getElementById("sidebarToggleTop"); // ✅ must match your button ID

                if (toggleBtn && sidebar) {
                    // Toggle sidebar open/close
                    toggleBtn.addEventListener("click", (e) => {
                        e.stopPropagation(); // stop click from bubbling
                        sidebar.classList.toggle("toggled");
                    });

                    // Close sidebar when clicking outside
                    document.addEventListener("click", (event) => {
                        const isClickInside = sidebar.contains(event.target) || toggleBtn.contains(event
                            .target);
                        if (!isClickInside && sidebar.classList.contains("toggled")) {
                            sidebar.classList.remove("toggled");
                        }
                    });
                } else {
                    console.warn("Sidebar or toggle button not found in DOM.");
                }
            });
            </script>>


            <script>
            $(document).ready(function() {
                $('select[name="program[]"]').select2({
                    placeholder: "Select Programs",
                    theme: "bootstrap4",
                    width: '100%'
                });
            });
            </script>

            <script>
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
                $('select[name="lecturers"]').attr('multiple', 'multiple');
                $('select[name="lecturers"]').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
                $('select[name="classId"]').change(function() {
                    var classId = $(this).val();
                    if (classId) {
                        $.ajax({
                            url: 'ajaxClassArms.php',
                            type: 'GET',
                            data: {
                                cid: classId
                            },
                            success: function(data) {
                                $('#classArmDropdown').html(data);
                            }
                        });
                    } else {
                        $('#classArmDropdown').html('<option value="">--Select Class Arm--</option>');
                    }
                });
            });
            </script>
</body>

</html>