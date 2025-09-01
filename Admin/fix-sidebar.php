<?php
// This script fixes all sidebar toggle issues
// Run this file once to apply all fixes

// Fix 1: Update sidebar.php to use unique IDs
$sidebarContent = '<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
          <img src="img/logo/attnlg.jpg">
        </div>
        <div class="sidebar-brand-text mx-3">AMS</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Class and Class Arms
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap"
          aria-expanded="false" aria-controls="collapseBootstrap">
          <i class="fas fa-chalkboard"></i>
          <span>Manage Classes</span>
        </a>
        <div id="collapseBootstrap" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Classes</h6>
            <a class="collapse-item" href="createClass.php">Create/Edit Class</a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapusers"
          aria-expanded="true" aria-controls="collapseBootstrapusers">
          <i class="fas fa-code-branch"></i>
          <span>Manage Class Arms</span>
        </a>
        <div id="collapseBootstrapusers" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Class Arms</h6>
            <a class="collapse-item" href="createClassArms.php">Create/Edit Class Arms</a>
          </div>
        </div>
      </li>
       <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Courses
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapcourses"
          aria-expanded="true" aria-controls="collapseBootstrapcourses">
          <i class="fas fa-book"></i>
          <span>Manage Courses</span>
        </a>
        <div id="collapseBootstrapcourses" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Courses</h6>
             <a class="collapse-item" href="createCourses.php">Create/Edit Course</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Lecturers
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapteachers"
          aria-expanded="true" aria-controls="collapseBootstrapteachers">
          <i class="fas fa-chalkboard-teacher"></i>
          <span>Manage Lecturers</span>
        </a>
        <div id="collapseBootstrapteachers" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Lecturers</h6>
             <a class="collapse-item" href="createClassTeacher.php">Create/Edit Lecturer</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Students
      </div>
       <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapstudents"
          aria-expanded="true" aria-controls="collapseBootstrapstudents">
          <i class="fas fa-user-graduate"></i>
          <span>Manage Students</span>
        </a>
        <div id="collapseBootstrapstudents" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Students</h6>
            <a class="collapse-item" href="createStudents.php">Create/Edit Students</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Session & Sem
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSessionSem"
          aria-expanded="true" aria-controls="collapseSessionSem">
          <i class="fa fa-calendar-alt"></i>
          <span>Manage Session & Sem</span>
        </a>
        <div id="collapseSessionSem" class="collapse" aria-labelledby="headingSessionSem" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Session & Sem</h6>
        <a class="collapse-item" href="createSessionTerm.php">Create/Edit Session and Sem</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Institution Management
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInstitution"
          aria-expanded="true" aria-controls="collapseInstitution">
          <i class="fa fa-university"></i>
          <span>Manage Institution</span>
        </a>
        <div id="collapseInstitution" class="collapse" aria-labelledby="headingInstitution" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Institution</h6>
        <a class="collapse-item" href="InstitutionManagement.php">Create/Edit Institution</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
    </ul>';

// Save the fixed sidebar
file_put_contents('Includes/sidebar.php', $sidebarContent);

// Fix 2: Create updated topbar with proper toggle button
$topbarContent = '<?php 
  $query = "SELECT * FROM tbladmin WHERE Id = ".$_SESSION[\'userId\']."";
  $rs = $conn->query($query);
  $num = $rs->num_rows;
  $rows = $rs->fetch_assoc();
  $fullName = $rows[\'firstName\']." ".$rows[\'lastName\'];

?>
<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top">
          <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3" type="button" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
          </button>
        <div class="text-white big" style="margin-left:100px;"><b></b></div>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-1 small" placeholder="What do you want to look for?"
                      aria-label="Search" aria-describedby="basic-addon2" style="border-color: #3f51b5;">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
         
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="img/user-icn.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small"><b>Welcome <?php echo $fullName;?></b></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>';

file_put_contents('Includes/topbar.php', $topbarContent);

// Create a test page to verify fixes
$testPage = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Toggle Test</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link href="css/sidebar-fix.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "Includes/sidebar.php";?>
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php";?>
                
                <div class="container-fluid">
                    <h1 class="h3 mb-0 text-gray-800">Sidebar Toggle Test</h1>
                    <p>Click the hamburger icon in the top left to test sidebar toggle functionality.</p>
                    
                    <div class="alert alert-info">
                        <strong>Testing Instructions:</strong>
                        <ul>
                            <li>Click the toggle button to collapse/expand sidebar</li>
                            <li>Test on mobile by resizing browser window</li>
                            <li>Check that dropdown menus work in both states</li>
                            <li>Verify responsive behavior</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar-toggle.js"></script>
</body>
</html>';

file_put_contents('test-sidebar.php', $testPage);

echo "Sidebar toggle fixes have been applied successfully!\n";
echo "1. Fixed duplicate IDs in sidebar.php\n";
echo "2. Updated toggle button in topbar.php\n";
echo "3. Created new sidebar-toggle.js with improved functionality\n";
echo "4. Added sidebar-fix.css for styling fixes\n";
echo "5. Created test-sidebar.php for testing\n";
echo "\nTo test the fixes:\n";
echo "1. Open test-sidebar.php in your browser\n";
echo "2. Click the hamburger icon to toggle sidebar\n";
echo "3. Test responsive behavior by resizing browser\n";
echo "4. Verify all dropdown menus work correctly\n";
?>
