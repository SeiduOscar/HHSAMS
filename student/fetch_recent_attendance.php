<?php
session_start();
include '../Includes/dbcon.php';

// Check if student is logged in
if (!isset($_SESSION['admissionNumber'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$admissionNumber = $_SESSION['admissionNumber'];
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';
$course = $_GET['course'] ?? '';

// Build query with filters
$sql = "SELECT a.dateTimeTaken, a.status, c.courseName
        FROM tblattendance a
        LEFT JOIN tblcourses c ON a.courseCode = c.courseCode
        WHERE a.admissionNo = ?";

$params = [$admissionNumber];
$types = "s";

if (!empty($course)) {
    $sql .= " AND a.courseCode = ?";
    $params[] = $course;
    $types .= "s";
}

if (!empty($fromDate) && !empty($toDate)) {
    $sql .= " AND DATE(a.dateTimeTaken) BETWEEN ? AND ?";
    $params[] = $fromDate;
    $params[] = $toDate;
    $types .= "ss";
}

$sql .= " ORDER BY a.dateTimeTaken DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = [
        'date' => date('Y-m-d', strtotime($row['dateTimeTaken'])),
        'course' => $row['courseName'] ?? 'General',
        'status' => $row['status'] == '1' ? 'Present' : 'Absent'
    ];
}

echo json_encode($records);
?>