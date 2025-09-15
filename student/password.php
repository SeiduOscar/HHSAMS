<?php

session_start();


if (!isset($_SESSION['admissionNumber'])) {
    header("Location: ../index.php");
    exit();
}

include '../Includes/dbcon.php';

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../Admin/css/sidebar-fix.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="../Admin/js/sidebar-toggle.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        .content.expanded {
            margin-left: 0;
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.present {
            border-left-color: #28a745;
        }

        .stat-card.absent {
            border-left-color: #dc3545;
        }

        .stat-card.courses {
            border-left-color: #007bff;
        }

        .course-card {
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        #qr-reader__dashboard_section_csr {
            border-radius: 8px;
        }

        /* SB Admin 2 Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar.toggled {
            width: 0;
            overflow: hidden;
        }

        .content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        .sidebar-hidden {
            margin-left: 0 !important;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.toggled {
                margin-left: 0;
            }

            .content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-toggled .sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar bg-dark text-white">
            <?php include 'includes/sidebar.php'; ?>
        </nav>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay"></div>

        <!-- Main Content -->
        <div id="content-wrapper" class="content">
            <!-- Top Navigation -->
            <?php include './includes/topbar.php' ?>

            <main class="container-fluid p-4">
                <!-- Change Password Section -->
                <section id="change-password-section">
                    <div class="card mb-4 mx-auto" style="max-width: 800px;">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">Change Password</h2>
                            <form id="change-password-form">
                                <div class="form-group">
                                    <label for="current-password" class="small font-weight-bold">Current
                                        Password</label>
                                    <div class="input-group">
                                        <input type="password" id="current-password" class="form-control" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="current-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new-password" class="small font-weight-bold">New Password</label>
                                    <div class="input-group">
                                        <input type="password" id="new-password" class="form-control" required>
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
                                    <label for="confirm-password" class="small font-weight-bold">Confirm New
                                        Password</label>
                                    <div class="input-group">
                                        <input type="password" id="confirm-password" class="form-control" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="confirm-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="password-message" class="alert d-none mb-4"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </section>

            </main>

        </div>

        <script>
            // DOM Elements
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content-wrapper');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Initialize
            $(document).ready(function () {
                // SB Admin 2 style sidebar toggle
                if (sidebarToggleTop) {
                    sidebarToggleTop.addEventListener('click', function (e) {
                        e.preventDefault();
                        $('body').toggleClass('sidebar-toggled');
                        $('.sidebar').toggleClass('toggled');

                        if ($('.sidebar').hasClass('toggled')) {
                            $('#content-wrapper').addClass('sidebar-hidden');
                        } else {
                            $('#content-wrapper').removeClass('sidebar-hidden');
                        }
                    });
                }

                // Close sidebar when clicking overlay
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', function () {
                        $('body').removeClass('sidebar-toggled');
                        $('.sidebar').removeClass('toggled');
                        $('#content-wrapper').removeClass('sidebar-hidden');
                    });
                }

                // Close sidebar on window resize if on mobile
                $(window).resize(function () {
                    if ($(window).width() >= 768) {
                        $('body').removeClass('sidebar-toggled');
                        $('.sidebar').removeClass('toggled');
                        $('#content-wrapper').removeClass('sidebar-hidden');
                    }
                });

                // Show message function
                function showMessage(message, type) {
                    const messageDiv = $('#password-message');
                    messageDiv.removeClass('d-none alert-success alert-danger');
                    messageDiv.addClass('alert-' + (type === 'success' ? 'success' : 'danger'));
                    messageDiv.html(message);
                }

                // Change password form submission via AJAX
                $('#change-password-form').submit(function (e) {
                    e.preventDefault();

                    const currentPassword = $('#current-password').val();
                    const newPassword = $('#new-password').val();
                    const confirmPassword = $('#confirm-password').val();

                    // Reset message
                    $('#password-message').addClass('d-none').removeClass('alert-success alert-danger');

                    // Validate passwords
                    if (newPassword !== confirmPassword) {
                        showMessage('Passwords do not match', 'error');
                        return;
                    }

                    if (newPassword.length < 8) {
                        showMessage('Password must be at least 8 characters long', 'error');
                        return;
                    }

                    $.ajax({
                        url: 'change_password.php',
                        method: 'POST',
                        data: {
                            currentPassword: currentPassword,
                            newPassword: newPassword,
                            confirmNewPassword: confirmPassword
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                showMessage(response.message, 'success');
                                $('#change-password-form')[0].reset();
                            } else {
                                showMessage(response.message, 'error');
                            }
                        },
                        error: function () {
                            showMessage('Failed to change password. Please try again.',
                                'error');
                        }
                    });
                });

                // Logout button
                $('#logout-btn').click(function () {
                    alert('You have been logged out. This would redirect to the login page.');
                    window.location = '../index.php';
                });

                // Toggle password visibility
                $('.toggle-password').click(function () {
                    const target = $(this).data('target');
                    const input = $('#' + target);
                    const icon = $(this).find('i');
                    if (input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        input.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            });
        </script>
</body>

</html>