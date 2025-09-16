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

    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>QrCode Scan</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../Admin/css/sidebar-fix.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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


    <button id="sidebarToggleTop" type="button" class="btn btn-link d-md-none rounded-circle mr-3"
        style="position:fixed;top:0px;right:10px;left:auto;z-index:1100;background:#343a40;color:#fff;">
        <i class="fa fa-bars"></i>
    </button>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar bg-dark text-white">
            <?php include 'includes/sidebar.php'; ?>
        </nav>

        <!-- Sidebar Overlay -->

        <div id="sidebar-overlay" class="sidebar-overlay"></div>


        <!-- Main Content -->
        <div id="content-wrapper" class="content">
            <!-- Top Navigation -->
            <?php include './includes/topbar.php' ?>


            <main class="container-fluid p-3 p-md-4">

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

                                    <button id="stop-scan-btn" class="btn btn-danger mr-2 d-none">

                                        Stop Scanning
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <<<<<<< HEAD <script>
                    let html5QrcodeScanner = null;
                    let scanning = false;

                    function isValidUrl(string) {
                    try {
                    new URL(string);
                    return true;
                    } catch (_) {
                    return false;
                    }
                    }


                    function showScanResult(decodedText) {
                    $('#scan-result').text(decodedText);
                    $('#qr-reader-results').removeClass('d-none');
                    // Do not hide the scanner, allow multiple scans
                    // $('#qr-reader').hide();
                    // $('#stop-scan-btn').addClass('d-none');
                    // $('#start-scan-btn').removeClass('d-none');
                    // scanning = false;

                    if (isValidUrl(decodedText)) {
                    $('#link-container').removeClass('d-none');
                    $('#scanned-link').attr('href', decodedText).text(decodedText);
                    $('#scan-result').removeClass('text-danger').addClass('text-muted');
                    } else {
                    $('#link-container').addClass('d-none');
                    $('#scanned-link').attr('href', '#').text('');
                    $('#scan-result').removeClass('text-muted').addClass('text-danger');
                    $('#scan-result').text(decodedText + ' (Not a valid URL)');
                    }
                    }

                    function resetScannerUI() {
                    $('#qr-reader-results').addClass('d-none');
                    $('#scan-result').text('');
                    $('#link-container').addClass('d-none');
                    $('#scanned-link').attr('href', '#').text('');
                    $('#qr-reader').show();
                    }

                    function startScanner() {
                    if (scanning) return;
                    scanning = true;
                    $('#start-scan-btn').addClass('d-none');
                    $('#stop-scan-btn').removeClass('d-none');
                    resetScannerUI();

                    if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5Qrcode("qr-reader");
                    }
                    html5QrcodeScanner.start({
                    facingMode: "environment"
                    }, {
                    fps: 10,
                    qrbox: 250
                    },
                    (decodedText, decodedResult) => {
                    showScanResult(decodedText);
                    // Do not stop the scanner, allow multiple scans
                    },
                    (errorMessage) => {
                    // Optionally handle scan errors
                    }
                    ).catch(err => {
                    scanning = false;
                    alert("Unable to access camera or start scanner: " + err);
                    $('#start-scan-btn').removeClass('d-none');
                    $('#stop-scan-btn').addClass('d-none');
                    });
                    }

                    function stopScanner() {
                    if (html5QrcodeScanner && scanning) {
                    html5QrcodeScanner.stop().then(() => {
                    scanning = false;
                    $('#start-scan-btn').removeClass('d-none');
                    $('#stop-scan-btn').addClass('d-none');
                    $('#qr-reader').hide();
                    });
                    }
                    }

                    $(document).ready(function() {
                    $('#start-scan-btn').on('click', function() {
                    $('#qr-reader').show();
                    startScanner();
                    });

                    $('#stop-scan-btn').on('click', function() {
                    stopScanner();
                    });

                    $('#scan-again-btn').on('click', function() {
                    resetScannerUI();
                    $('#qr-reader').show();
                    startScanner();
                    });

                    $('#close-scan-btn').on('click', function() {
                    resetScannerUI();
                    $('#qr-reader').hide();
                    $('#start-scan-btn').removeClass('d-none');
                    $('#stop-scan-btn').addClass('d-none');
                    if (scanning) stopScanner();
                    });

                    $('#copy-link-btn').on('click', function() {
                    const link = $('#scanned-link').attr('href');
                    navigator.clipboard.writeText(link).then(function() {
                    alert('Link copied to clipboard!');
                    });
                    });

                    $('#visit-link-btn').on('click', function() {
                    const link = $('#scanned-link').attr('href');
                    window.open(link, '_blank');
                    });

                    // Hide QR reader on load
                    $('#qr-reader').hide();
                    });
                    </script>
            </main>
        </div>
    </div>
</body>

</html>