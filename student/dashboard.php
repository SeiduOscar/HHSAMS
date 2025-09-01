<?php
session_start();
include '../Includes/dbcon.php';

// Check if student is logged in
if (!isset($_SESSION['studentId'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch student info
$studentId = $_SESSION['studentId'];
$query = $conn->prepare("SELECT * FROM tblstudents WHERE Id = ?");
$query->bind_param("i", $studentId);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

// Fetch enrolled courses and moderators
$courses = [];
if ($student) {
    $admissionNumber = $student['admissionNumber'];
    $sql = "SELECT c.courseCode, c.courseName, m.firstName AS modFirstName, m.lastName AS modLastName
            FROM tblcourses c
            JOIN tblmoderator m ON c.lecturer_id = m.Id
            JOIN tblattendance a ON a.courseCode = c.courseCode
            WHERE a.admissionNo = ?
            GROUP BY c.courseCode";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admissionNumber);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $courses[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
    /* Custom styles that can't be easily done with Tailwind */
    #sidebar {
        transition: all 0.3s;
    }

    #sidebar.collapsed {
        margin-left: -220px;
    }

    #content {
        transition: all 0.3s;
    }

    #content.expanded {
        margin-left: 0;
    }

    #qr-reader {
        width: 100%;
        max-width: 500px;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-gray-800 text-white w-64 md:w-72 flex-shrink-0 flex-col h-full fixed">
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 rounded-full w-10 h-10 flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg" id="student-name">John Doe</h3>
                        <p class="text-gray-400 text-sm">Student</p>
                    </div>
                </div>
            </div>
            <nav class="flex-grow p-4 space-y-2">
                <a href="#dashboard" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 active-nav">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#qr-scanner" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-qrcode"></i>
                    <span>QR Scanner</span>
                </a>
                <a href="#courses" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>
                <a href="#attendance" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance History</span>
                </a>
                <a href="#change-password" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-key"></i>
                    <span>Change Password</span>
                </a>
                <a href="#" id="logout-btn" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <button id="toggle-sidebar" class="w-full p-2 bg-gray-700 rounded hover:bg-gray-600">
                    <i class="fas fa-chevron-left"></i> Collapse
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div id="content" class="flex-1 overflow-auto ml-64 md:ml-72">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">Student Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-gray-600 cursor-pointer"></i>
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="https://via.placeholder.com/40" alt="Profile" class="rounded-full w-8 h-8">
                        <span class="hidden md:inline">John Doe</span>
                    </div>
                </div>
            </header>

            <main class="p-4 md:p-6">
                <!-- Dashboard Section -->
                <section id="dashboard-section" class="mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Welcome, <span id="welcome-name">John</span>!
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-blue-600">Total Courses</p>
                                        <h3 class="text-2xl font-bold text-blue-800">5</h3>
                                    </div>
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <i class="fas fa-book text-blue-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-green-600">Attendance Rate</p>
                                        <h3 class="text-2xl font-bold text-green-800">85%</h3>
                                    </div>
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <i class="fas fa-calendar-check text-green-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-purple-600">Recent Activity</p>
                                        <h3 class="text-2xl font-bold text-purple-800">3</h3>
                                    </div>
                                    <div class="bg-purple-100 p-3 rounded-full">
                                        <i class="fas fa-clock text-purple-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-lg mb-4">Recent Attendance</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-green-100 p-2 rounded-full">
                                                <i class="fas fa-check text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Mathematics</p>
                                                <p class="text-sm text-gray-500">Today, 10:30 AM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-green-600">Present</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-red-100 p-2 rounded-full">
                                                <i class="fas fa-times text-red-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Physics</p>
                                                <p class="text-sm text-gray-500">Yesterday, 2:00 PM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-red-600">Absent</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-green-100 p-2 rounded-full">
                                                <i class="fas fa-check text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Chemistry</p>
                                                <p class="text-sm text-gray-500">Monday, 9:00 AM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-green-600">Present</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-lg mb-4">Upcoming Classes</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-blue-100 p-2 rounded-full">
                                                <i class="fas fa-book text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Biology</p>
                                                <p class="text-sm text-gray-500">Tomorrow, 11:00 AM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-blue-600">Room 302</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-purple-100 p-2 rounded-full">
                                                <i class="fas fa-book text-purple-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Computer Science</p>
                                                <p class="text-sm text-gray-500">Tomorrow, 1:30 PM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-purple-600">Lab 4</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-yellow-100 p-2 rounded-full">
                                                <i class="fas fa-book text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Mathematics</p>
                                                <p class="text-sm text-gray-500">Tomorrow, 3:00 PM</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-yellow-600">Room 205</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- QR Scanner Section -->
                <section id="qr-scanner-section" class="mb-8 hidden">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">QR Code Scanner</h2>
                        <div class="flex flex-col items-center">
                            <div id="qr-reader" class="mb-4"></div>
                            <div id="qr-reader-results"
                                class="w-full max-w-md bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                                <h3 class="font-semibold mb-2">Scan Result:</h3>
                                <p id="scan-result" class="text-gray-700"></p>
                                <div class="mt-4">
                                    <button id="scan-again-btn"
                                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        Scan Again
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button id="start-scan-btn"
                                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                    Start Scanning
                                </button>
                                <button id="stop-scan-btn"
                                    class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 ml-2 hidden">
                                    Stop Scanning
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- My Courses Section -->
                <section id="courses-section" class="mb-8 hidden">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Courses</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg">Mathematics</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Active</span>
                                </div>
                                <p class="text-gray-600 mb-4">Advanced calculus and linear algebra</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm">Prof. Smith</span>
                                    </div>
                                    <span class="text-sm text-gray-500">Mon, Wed, Fri</span>
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg">Physics</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Active</span>
                                </div>
                                <p class="text-gray-600 mb-4">Classical mechanics and thermodynamics</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm">Dr. Johnson</span>
                                    </div>
                                    <span class="text-sm text-gray-500">Tue, Thu</span>
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg">Chemistry</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Active</span>
                                </div>
                                <p class="text-gray-600 mb-4">Organic and inorganic chemistry</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm">Prof. Williams</span>
                                    </div>
                                    <span class="text-sm text-gray-500">Mon, Wed</span>
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg">Biology</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Active</span>
                                </div>
                                <p class="text-gray-600 mb-4">Cell biology and genetics</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm">Dr. Brown</span>
                                    </div>
                                    <span class="text-sm text-gray-500">Tue, Thu</span>
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg">Computer Science</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Active</span>
                                </div>
                                <p class="text-gray-600 mb-4">Data structures and algorithms</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm">Prof. Davis</span>
                                    </div>
                                    <span class="text-sm text-gray-500">Mon, Fri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Attendance History Section -->
                <section id="attendance-section" class="mb-8 hidden">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Attendance History</h2>

                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold mb-3">Filter Attendance</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Courses</option>
                                        <option>Mathematics</option>
                                        <option>Physics</option>
                                        <option>Chemistry</option>
                                        <option>Biology</option>
                                        <option>Computer Science</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <input type="date"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <input type="date"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Apply Filter
                                </button>
                                <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 ml-2">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-semibold mb-4">Attendance Statistics</h3>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <canvas id="attendanceChart" height="300"></canvas>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-semibold mb-4">Recent Attendance Records</h3>
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Date</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Course</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        2023-06-15</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Mathematics</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        2023-06-14</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Physics</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        2023-06-13</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Chemistry</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        2023-06-12</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Biology</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        2023-06-09</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Computer Science</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                    </td>
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
                <section id="change-password-section" class="mb-8 hidden">
                    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h2>

                        <form id="change-password-form">
                            <div class="mb-4">
                                <label for="current-password"
                                    class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <div class="relative">
                                    <input type="password" id="current-password" name="current-password"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    <button type="button"
                                        class="absolute right-3 top-2 text-gray-500 hover:text-gray-700 toggle-password"
                                        data-target="current-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">New
                                    Password</label>
                                <div class="relative">
                                    <input type="password" id="new-password" name="new-password"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    <button type="button"
                                        class="absolute right-3 top-2 text-gray-500 hover:text-gray-700 toggle-password"
                                        data-target="new-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters long</p>
                            </div>

                            <div class="mb-6">
                                <label for="confirm-password"
                                    class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" id="confirm-password" name="confirm-password"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    <button type="button"
                                        class="absolute right-3 top-2 text-gray-500 hover:text-gray-700 toggle-password"
                                        data-target="confirm-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="password-message" class="hidden mb-4 p-3 rounded"></div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                    Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    const navLinks = document.querySelectorAll('nav a');
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        qrScanner: document.getElementById('qr-scanner-section'),
        courses: document.getElementById('courses-section'),
        attendance: document.getElementById('attendance-section'),
        changePassword: document.getElementById('change-password-section')
    };

    // QR Scanner Variables
    let html5QrCode;
    let qrScannerActive = false;

    // Initialize the dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sidebar toggle
        toggleSidebarBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');

            if (sidebar.classList.contains('collapsed')) {
                toggleSidebarBtn.innerHTML = '<i class="fas fa-chevron-right"></i> Expand';
            } else {
                toggleSidebarBtn.innerHTML = '<i class="fas fa-chevron-left"></i> Collapse';
            }
        });

        // Navigation link click handlers
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all links
                navLinks.forEach(navLink => {
                    navLink.classList.remove('bg-gray-700');
                });

                // Add active class to clicked link
                this.classList.add('bg-gray-700');

                // Hide all sections
                Object.values(sections).forEach(section => {
                    section.classList.add('hidden');
                });

                // Show the selected section
                const target = this.getAttribute('href').substring(1);
                if (target === 'dashboard') {
                    sections.dashboard.classList.remove('hidden');
                } else if (target === 'qr-scanner') {
                    sections.qrScanner.classList.remove('hidden');
                } else if (target === 'courses') {
                    sections.courses.classList.remove('hidden');
                } else if (target === 'attendance') {
                    sections.attendance.classList.remove('hidden');
                    initAttendanceChart();
                } else if (target === 'change-password') {
                    sections.changePassword.classList.remove('hidden');
                }
            });
        });

        // Set dashboard as active by default
        document.querySelector('nav a').click();

        // Initialize QR Scanner
        initQRScanner();

        // Initialize attendance chart
        initAttendanceChart();

        // Password toggle functionality
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Change password form submission
        document.getElementById('change-password-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const messageDiv = document.getElementById('password-message');

            // Reset message
            messageDiv.classList.add('hidden');
            messageDiv.textContent = '';

            // Validate passwords
            if (newPassword !== confirmPassword) {
                showMessage('Passwords do not match', 'error');
                return;
            }

            if (newPassword.length < 8) {
                showMessage('Password must be at least 8 characters long', 'error');
                return;
            }

            // Simulate AJAX call (in a real app, this would be a fetch or XMLHttpRequest)
            setTimeout(() => {
                showMessage('Password changed successfully!', 'success');
                document.getElementById('change-password-form').reset();
            }, 1000);
        });

        // Logout button
        document.getElementById('logout-btn').addEventListener('click', function() {
            // In a real app, this would redirect to logout.php
            alert('You have been logged out. In a real app, this would redirect to the login page.');
        });
    });

    // Initialize QR Scanner
    function initQRScanner() {
        const qrReader = document.getElementById('qr-reader');
        const qrReaderResults = document.getElementById('qr-reader-results');
        const scanResult = document.getElementById('scan-result');
        const startScanBtn = document.getElementById('start-scan-btn');
        const stopScanBtn = document.getElementById('stop-scan-btn');
        const scanAgainBtn = document.getElementById('scan-again-btn');

        // Start scan button
        startScanBtn.addEventListener('click', function() {
            if (qrScannerActive) return;

            html5QrCode = new Html5Qrcode("qr-reader");
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                handleScanSuccess(decodedText, decodedResult);
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
                () => {} // No verbose logging
            ).then(() => {
                qrScannerActive = true;
                startScanBtn.classList.add('hidden');
                stopScanBtn.classList.remove('hidden');
                qrReaderResults.classList.add('hidden');
            }).catch(err => {
                console.error("Error starting QR scanner:", err);
                alert("Could not start QR scanner. Please ensure camera access is allowed.");
            });
        });

        // Stop scan button
        stopScanBtn.addEventListener('click', function() {
            if (!qrScannerActive) return;

            html5QrCode.stop().then(() => {
                qrScannerActive = false;
                stopScanBtn.classList.add('hidden');
                startScanBtn.classList.remove('hidden');
            }).catch(err => {
                console.error("Error stopping QR scanner:", err);
            });
        });

        // Scan again button
        scanAgainBtn.addEventListener('click', function() {
            qrReaderResults.classList.add('hidden');
            startScanBtn.click();
        });

        // Handle scan success
        function handleScanSuccess(decodedText, decodedResult) {
            html5QrCode.stop().then(() => {
                qrScannerActive = false;
                stopScanBtn.classList.add('hidden');
                startScanBtn.classList.remove('hidden');

                scanResult.textContent = decodedText;
                qrReaderResults.classList.remove('hidden');

                // In a real app, you would send this to your server for processing
                console.log("QR Code scanned:", decodedText);

                // Simulate attendance marking
                setTimeout(() => {
                    alert(`Attendance marked successfully for: ${decodedText}`);
                }, 500);
            }).catch(err => {
                console.error("Error stopping scanner after success:", err);
            });
        }
    }

    // Initialize Attendance Chart
    function initAttendanceChart() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        // Sample data - in a real app, this would come from an AJAX request
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const presentData = [85, 79, 92, 88, 91, 87];
        const absentData = [15, 21, 8, 12, 9, 13];

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Present (%)',
                        data: presentData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Absent (%)',
                        data: absentData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
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

    // Show message in change password form
    function showMessage(message, type) {
        const messageDiv = document.getElementById('password-message');
        messageDiv.textContent = message;
        messageDiv.classList.remove('hidden');

        if (type === 'error') {
            messageDiv.classList.remove('bg-green-100', 'text-green-700');
            messageDiv.classList.add('bg-red-100', 'text-red-700');
        } else {
            messageDiv.classList.remove('bg-red-100', 'text-red-700');
            messageDiv.classList.add('bg-green-100', 'text-green-700');
        }
    }
    </script>
</body>

</html>