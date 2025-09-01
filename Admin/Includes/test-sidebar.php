<!DOCTYPE html>
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
</html>