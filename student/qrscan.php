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
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="../Admin/js/sidebar-toggle.js"></script>
    <style>
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

        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
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
                <!-- QR Scanner Section -->
                <section id="qr-scanner-section" style="display:block;">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h4 font-weight-bold text-gray-800 mb-4">QR Code Scanner</h2>
                            <div class="text-center">
                                <div id="qr-reader" class="mb-4 mx-auto"></div>
                                <div id="qr-reader-results" class="card mb-4 p-4 d-none">
                                    <h3 class="h5 font-weight-bold text-gray-800 mb-2">Scan Result:</h3>
                                    <p id="scan-result" class="text-muted mb-3"></p>
                                    <div id="link-container" class="mb-3 d-none">
                                        <p class="mb-2">Scanned Link: <a id="scanned-link" href="#" target="_blank"
                                                class="text-primary font-weight-bold"></a></p>
                                        <button id="copy-link-btn" class="btn btn-sm btn-outline-secondary mr-2">
                                            <i class="fas fa-copy"></i> Copy Link
                                        </button>
                                        <button id="visit-link-btn" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Visit Link
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button id="scan-again-btn" class="btn btn-primary mr-2">
                                            Scan Again
                                        </button>
                                        <button id="close-scan-btn" class="btn btn-secondary">
                                            Close
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button id="start-scan-btn" class="btn btn-primary mr-2">
                                        Start Scanning
                                    </button>
                                    <button id="stop-scan-btn" class="btn btn-danger d-none">
                                        Stop Scanning
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

        </div>

        <script>
            $(document).ready(function () {
                // SB Admin 2 style sidebar toggle
                const sidebarToggleTop = document.getElementById('sidebarToggleTop');
                const sidebarOverlay = document.getElementById('sidebar-overlay');

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

                // QR Scanner integration
                function initQRScanner() {
                    const qrReaderResults = $('#qr-reader-results');
                    const scanResult = $('#scan-result');
                    const startScanBtn = $('#start-scan-btn');
                    const stopScanBtn = $('#stop-scan-btn');
                    const scanAgainBtn = $('#scan-again-btn');
                    const closeScanBtn = $('#close-scan-btn');

                    let html5QrCode;
                    let qrScannerActive = false;

                    // Start scan button
                    startScanBtn.on('click', function () {
                        if (qrScannerActive) return;

                        html5QrCode = new Html5Qrcode("qr-reader");
                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            scanResult.text(decodedText);
                            qrReaderResults.removeClass('d-none');

                            // Show link container if URL
                            const isUrl = /^https?:\/\/[^\s/$.?#].[^\s]*$/i.test(decodedText);
                            const linkContainer = $('#link-container');
                            const scannedLink = $('#scanned-link');

                            if (isUrl) {
                                scannedLink.attr('href', decodedText);
                                scannedLink.text(decodedText);
                                scannedLink.attr('target', '_blank');
                                linkContainer.removeClass('d-none');
                            } else {
                                linkContainer.addClass('d-none');
                            }
                        };

                        const config = {
                            fps: 10,
                            qrbox: 250
                        };

                        html5QrCode.start({
                            facingMode: "environment"
                        },
                            config,
                            qrCodeSuccessCallback,
                            () => { } // No verbose logging
                        ).then(() => {
                            qrScannerActive = true;
                            startScanBtn.addClass('d-none');
                            stopScanBtn.removeClass('d-none');
                            qrReaderResults.removeClass('d-none');
                        }).catch(err => {
                            console.error("Error starting QR scanner:", err);
                            alert(
                                "Could not start QR scanner. Please ensure camera access is allowed."
                            );
                        });
                    });

                    // Stop scan button
                    stopScanBtn.click(function () {
                        if (!qrScannerActive) return;

                        html5QrCode.stop().then(() => {
                            qrScannerActive = false;
                            stopScanBtn.addClass('d-none');
                            startScanBtn.removeClass('d-none');
                        }).catch(err => {
                            console.error("Error stopping QR scanner:", err);
                        });
                    });

                    // Scan again button
                    scanAgainBtn.click(function () {
                        qrReaderResults.addClass('d-none');
                        startScanBtn.click();
                    });

                    // Close scan button
                    closeScanBtn.click(function () {
                        if (qrScannerActive) {
                            html5QrCode.stop().then(() => {
                                qrScannerActive = false;
                            }).catch(err => {
                                console.error("Error stopping scanner:", err);
                            });
                        }
                        qrReaderResults.addClass('d-none');
                        stopScanBtn.addClass('d-none');
                        startScanBtn.removeClass('d-none');
                    });

                    // Copy link button
                    $('#copy-link-btn').click(function () {
                        const link = $('#scanned-link').attr('href');
                        navigator.clipboard.writeText(link).then(() => {
                            alert('Link copied to clipboard!');
                        }).catch(err => {
                            console.error('Failed to copy link:', err);
                            alert('Failed to copy link. Please try again.');
                        });
                    });

                    // Visit link button
                    $('#visit-link-btn').click(function () {
                        const link = $('#scanned-link').attr('href');
                        window.open(link, '_blank');
                    });
                }

                // Initialize QR Scanner
                initQRScanner();
            });
        </script>
</body>

</html>