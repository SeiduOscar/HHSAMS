0
<?php
session_start();
include '../Includes/dbcon.php';

// Check if student is logged in
if (!isset($_SESSION['studentId'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$studentId = $_SESSION['studentId'];
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmNewPassword = $_POST['confirmNewPassword'] ?? '';

// Validate inputs
if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if ($newPassword !== $confirmNewPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit();
}

// Check current password
$query = $conn->prepare("SELECT password FROM tblstudents WHERE Id = ?");
$query->bind_param("i", $studentId);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit();
}

$currentPasswordHash = md5($currentPassword);
if ($student['password'] !== $currentPasswordHash) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit();
}

// Update password
$newPasswordHash = md5($newPassword);
$updateQuery = $conn->prepare("UPDATE tblstudents SET password = ? WHERE Id = ?");
$updateQuery->bind_param("si", $newPasswordHash, $studentId);

if ($updateQuery->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to change password']);
}
?>