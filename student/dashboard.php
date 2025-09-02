<?php
// Example PHP section for file upload or authentication
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/ruang-admin.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <!-- Html5 QR Code -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-light border-right d-none d-md-block" id="sidebar" style="width: 250px; min-height: 100vh;">
            <div class="sidebar-heading">Dashboard</div>
            <div class="list-group list-group-flush">
                <a href="#dashboard" class="list-group-item list-group-item-action bg-light">Dashboard</a>
                <a href="#qr-scanner" class="list-group-item list-group-item-action bg-light">QR Scanner</a>
                <a href="#courses" class="list-group-item list-group-item-action bg-light">Courses</a>
                <a href="#attendance" class="list-group-item list-group-item-action bg-light">Attendance</a>
                <a href="#change-password" class="list-group-item list-group-item-action bg-light">Change Password</a>
                <button id="logout-btn" class="btn btn-danger mt-3">Logout</button>
            </div>
        </div>

        <!-- Page Content -->
        <div id="content" class="flex-grow-1 p-4">
            <div class="container-fluid">
                <button class="btn btn-secondary mb-3" id="toggle-sidebar"><i class="fas fa-chevron-left"></i>
                    Collapse</button>

                <!-- Dashboard Section -->
                <section id="dashboard-section" class="mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Welcome, <?php echo $_SESSION['user']; ?></h2>
                            <p class="card-text">This is your dashboard.</p>
                            <!-- File Upload Form -->
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="uploadFile">Upload File</label>
                                    <input type="file" class="form-control-file" id="uploadFile" name="uploadFile">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </section>

                <!-- QR Scanner Section -->
                <section id="qr-scanner-section" class="mb-4 d-none">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">QR Scanner</h2>
                            <div id="qr-reader" style="width:100%;"></div>
                            <div class="mt-3 d-none" id="qr-reader-results">
                                <h5>Scan Result:</h5>
                                <p id="scan-result"></p>
                                <button class="btn btn-primary" id="scan-again-btn">Scan Again</button>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-success" id="start-scan-btn">Start Scan</button>
                                <button class="btn btn-warning d-none" id="stop-scan-btn">Stop Scan</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Courses Section -->
                <section id="courses-section" class="mb-4 d-none">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Courses</h2>
                            <!-- Course content here -->
                        </div>
                    </div>
                </section>

                <!-- Attendance Section -->
                <section id="attendance-section" class="mb-4 d-none">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Attendance History</h2>

                            <!-- Filter Form -->
                            <form class="mb-4">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Course</label>
                                        <select class="form-control">
                                            <option>All Courses</option>
                                            <option>Mathematics</option>
                                            <option>Physics</option>
                                            <option>Chemistry</option>
                                            <option>Biology</option>
                                            <option>Computer Science</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>From Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>To Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary">Apply Filter</button>
                                <button type="reset" class="btn btn-secondary ml-2">Reset</button>
                            </form>

                            <div class="row">
                                <!-- Attendance Chart -->
                                <div class="col-lg-6 mb-3">
                                    <h5>Attendance Statistics</h5>
                                    <canvas id="attendanceChart" height="300"></canvas>
                                </div>

                                <!-- Attendance Table -->
                                <div class="col-lg-6 mb-3">
                                    <h5>Recent Attendance Records</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Course</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2023-06-15</td>
                                                    <td>Mathematics</td>
                                                    <td><span class="badge badge-success">Present</span></td>
                                                </tr>
                                                <tr>
                                                    <td>2023-06-14</td>
                                                    <td>Physics</td>
                                                    <td><span class="badge badge-danger">Absent</span></td>
                                                </tr>
                                                <tr>
                                                    <td>2023-06-13</td>
                                                    <td>Chemistry</td>
                                                    <td><span class="badge badge-success">Present</span></td>
                                                </tr>
                                                <tr>
                                                    <td>2023-06-12</td>
                                                    <td>Biology</td>
                                                    <td><span class="badge badge-success">Present</span></td>
                                                </tr>
                                                <tr>
                                                    <td>2023-06-09</td>
                                                    <td>Computer Science</td>
                                                    <td><span class="badge badge-success">Present</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Change Password Section -->
                <section id="change-password-section" class="mb-4 d-none">
                    <div class="card mx-auto" style="max-width:600px;">
                        <div class="card-body">
                            <h2 class="card-title">Change Password</h2>
                            <form id="change-password-form">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current-password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="current-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new-password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="new-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Password must be at least 8 characters
                                        long</small>
                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm-password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="confirm-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="password-message" class="alert d-none"></div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/ruang-admin.min.js"></script>

    <script>
    // Sidebar toggle
    $('#toggle-sidebar').click(function() {
        $('#sidebar').toggleClass('d-none');
        if ($('#sidebar').hasClass('d-none')) {
            $(this).html('<i class="fas fa-chevron-right"></i> Expand');
        } else {
            $(this).html('<i class="fas fa-chevron-left"></i> Collapse');
        }
    });

    // Navigation
    $('.list-group-item').click(function(e) {
        e.preventDefault();
        $('.list-group-item').removeClass('active bg-secondary text-white');
        $(this).addClass('active bg-secondary text-white');
        var target = $(this).attr('href').substring(1);
        $('section').addClass('d-none');
        $('#' + target + '-section').removeClass('d-none');

        if (target === 'attendance') {
            initAttendanceChart();
        }
    });

    // Password toggle
    $('.toggle-password').click(function() {
        var input = $('#' + $(this).data('target'));
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Change password form
    $('#change-password-form').submit(function(e) {
        e.preventDefault();
        var currentPassword = $('#current-password').val();
        var newPassword = $('#new-password').val();
        var confirmPassword = $('#confirm-password').val();
        var msgDiv = $('#password-message');
        msgDiv.addClass('d-none').removeClass('alert-success alert-danger');

        if (newPassword !== confirmPassword) {
            showMessage('Passwords do not match', 'error');
            return;
        }
        if (newPassword.length < 8) {
            showMessage('Password must be at least 8 characters long', 'error');
            return;
        }

        // Simulate AJAX
        setTimeout(function() {
            showMessage('Password changed successfully!', 'success');
            $('#change-password-form')[0].reset();
        }, 1000);
    });

    function showMessage(message, type) {
        var msgDiv = $('#password-message');
        msgDiv.text(message).removeClass('d-none');
        if (type === 'error') {
            msgDiv.removeClass('alert-success').addClass('alert-danger');
        } else {
            msgDiv.removeClass('alert-danger').addClass('alert-success');
        }
    }

    // QR Scanner
    var html5QrCode;
    var qrScannerActive = false;
    $('#start-scan-btn').click(function() {
        if (qrScannerActive) return;
        html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start({
            facingMode: "environment"
        }, {
            fps: 10,
            qrbox: 250
        }, (decodedText) => {
            html5QrCode.stop().then(() => {
                qrScannerActive = false;
                $('#stop-scan-btn').addClass('d-none');
                $('#start-scan-btn').removeClass('d-none');
                $('#scan-result').text(decodedText);
                $('#qr-reader-results').removeClass('d-none');
                setTimeout(() => alert(
                        `Attendance marked successfully for: ${decodedText}`),
                    500);
            });
        }).then(() => {
            qrScannerActive = true;
            $('#start-scan-btn').addClass('d-none');
            $('#stop-scan-btn').removeClass('d-none');
            $('#qr-reader-results').addClass('d-none');
        }).catch(err => {
            console.error(err);
            alert('Could not start QR scanner.');
        });
    });

    $('#stop-scan-btn').click(function() {
        if (!qrScannerActive) return;
        html5QrCode.stop().then(() => {
            qrScannerActive = false;
            $('#stop-scan-btn').addClass('d-none');
            $('#start-scan-btn').removeClass('d-none');
        }).catch(err => console.error(err));
    });

    $('#scan-again-btn').click(function() {
        $('#qr-reader-results').addClass('d-none');
        $('#start-scan-btn').click();
    });

    // Attendance Chart
    function initAttendanceChart() {
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        var presentData = [85, 79, 92, 88, 91, 87];
        var absentData = [15, 21, 8, 12, 9, 13];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Present (%)',
                        data: presentData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.1
                    },
                    {
                        label: 'Absent (%)',
                        data: absentData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    </script>
</body>

</html>