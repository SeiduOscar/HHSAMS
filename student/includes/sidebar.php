<div class="sidebar-header p-4 border-bottom border-secondary">

    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-circle p-2 mr-3">
                <i class="fas fa-user text-white"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold" id="student-name">

                    <!-- <?php
                    $studNo = $_SESSION['admissionNumber'];
                    $query = "SELECT * FROM tblstudents WHERE admissionNumber = '$studNo' ";
                    $rs = $conn->query($query);
                    $num = $rs->num_rows;
                    $rows = $rs->fetch_assoc();

                    if ($num > 0) {


                        $_SESSION['firstName'] = $rows['firstName'];
                        $_SESSION['lastName'] = $rows['lastName'];
                    }
                    echo htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']);
                    // ?> -->
                </h5>
                <small class="text-muted">Student</small>
            </div>
        </div>
        <!-- Close button for mobile -->
        <!-- <button id="sidebarCloseBtn" class="btn btn-link text-white d-md-none">
            <i class="fas fa-times"></i>
        </button> -->

    </div>
</div>

<ul class="list-unstyled components p-4">
    <li>
        <a href="./dashboard.php" class="nav-link active d-flex align-items-center text-white rounded">
            <i class="fas fa-home mr-2"></i> Dashboard
        </a>
    </li>
    <li>
        <a href="./qrscan.php" class="nav-link d-flex align-items-center text-muted rounded">
            <i class="fas fa-qrcode mr-2"></i> QR Scanner
        </a>
    </li>
    <li>
        <a href="./courses.php" class="nav-link d-flex align-items-center text-muted rounded">
            <i class="fas fa-book mr-2"></i> My Courses
        </a>
    </li>
    <li>
        <a href="./attendance.php" class="nav-link d-flex align-items-center text-muted rounded">
            <i class="fas fa-calendar-alt mr-2"></i> Attendance History
        </a>
    </li>
    <li>
        <a href="./password.php" class="nav-link d-flex align-items-center text-muted rounded">
            <i class="fas fa-key mr-2"></i> Change Password
        </a>
    </li>
    <li>
        <a href="./logout.php" id="logout-btn" class="nav-link d-flex align-items-center text-muted rounded">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </li>
</ul>