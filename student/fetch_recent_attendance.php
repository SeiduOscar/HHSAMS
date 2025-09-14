<?php
session_start();
include '../Includes/dbcon.php';

// Check if student is logged in
if (!isset($_SESSION['studentId'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$studentId = $_SESSION['studentId'];

// Fetch student admission number
$query = $conn->prepare("SELECT admissionNumber FROM tblstudents WHERE Id = ?");
$query->bind_param("i", $studentId);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode(['error' => 'Student not found']);
    exit();
}

$admissionNumber = $student['admissionNumber'];

// Fetch recent attendance records (last 10)
$sql = "SELECT dateTimeTaken, status FROM tblattendance WHERE admissionNo = ? ORDER BY dateTimeTaken DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admissionNumber);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = [
        'date' => date('Y-m-d', strtotime($row['dateTimeTaken'])),
        'course' => 'General', // Since no course in attendance table
        'status' => $row['status'] == '1' ? 'Present' : 'Absent'
    ];
}

echo json_encode($records);
?>