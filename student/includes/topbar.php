<nav class="bg-success text-white navbar navbar-expand navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">


        <h1 class="h5 mb-0 text-gray-800">Student Dashboard</h1>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                    <!-- <span class="position-relative">
                                    <i class="fas fa-bell text-gray-500"></i>
                                    <span
                                        class="position-absolute top-0 right-0 bg-danger text-white rounded-circle small px-1">3</span>
                                </span> -->
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span
                        class="d-none d-md-inline text-gray-700"><?php echo htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']) ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>