<?php
session_start();
include '../Includes/dbcon.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admissionNumber'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$studentId = $_SESSION['admissionNumber'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['qrCode'])) {
    echo json_encode(['success' => false, 'message' => 'QR code missing']);
    exit();
}

$qrCode = $data['qrCode'];

// Fetch student admission number
$query = $conn->prepare("SELECT admissionNumber FROM tblstudents WHERE Id = ?");
$query->bind_param("i", $studentId);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit();
}

$admissionNumber = $student['admissionNumber'];

// Validate QR code matches student's admission number or other logic
if ($qrCode !== $admissionNumber) {
    echo json_encode(['success' => false, 'message' => 'Invalid QR code']);
    exit();
}

// Mark attendance for today if not already marked
$dateToday = date('Y-m-d');

$checkQuery = $conn->prepare("SELECT * FROM tblattendance WHERE admissionNo = ? AND dateTimeTaken = ?");
$checkQuery->bind_param("ss", $admissionNumber, $dateToday);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Attendance already marked for today']);
    exit();
}

// Insert attendance record
$insertQuery = $conn->prepare("INSERT INTO tblattendance (admissionNo, status, dateTimeTaken) VALUES (?, '1', ?)");
$insertQuery->bind_param("ss", $admissionNumber, $dateToday);

if ($insertQuery->execute()) {
    echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark attendance']);
}
?>