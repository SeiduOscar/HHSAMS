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
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

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

// Build query with date filters
$sql = "SELECT DATE(dateTimeTaken) as date, 
               SUM(status = '1') as present,
               SUM(status = '0') as absent
        FROM tblattendance 
        WHERE admissionNo = ?";

$params = [$admissionNumber];
$types = "s";

if (!empty($fromDate) && !empty($toDate)) {
    $sql .= " AND DATE(dateTimeTaken) BETWEEN ? AND ?";
    $params[] = $fromDate;
    $params[] = $toDate;
    $types .= "ss";
}

$sql .= " GROUP BY DATE(dateTimeTaken) ORDER BY date";

$stmt = $conn->prepare($sql);

if ($types === "s") {
    $stmt->bind_param("s", ...$params);
} else {
    $stmt->bind_param("sss", ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$presentData = [];
$absentData = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['date'];
    $presentData[] = (int) $row['present'];
    $absentData[] = (int) $row['absent'];
}

// If no data found, return empty arrays
if (empty($labels)) {
    $labels = ['No Data'];
    $presentData = [0];
    $absentData = [0];
}

echo json_encode([
    'labels' => $labels,
    'present' => $presentData,
    'absent' => $absentData
]);
?>