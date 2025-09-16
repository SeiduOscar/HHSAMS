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

// Build query with date and course filters
$sql = "SELECT DATE(dateTimeTaken) as date,
               SUM(status = '1') as present,
               SUM(status = '0') as absent
        FROM tblattendance
        WHERE admissionNo = ?";

$params = [$admissionNumber];
$types = "s";

if (!empty($course)) {
    $sql .= " AND courseCode = ?";
    $params[] = $course;
    $types .= "s";
}

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
    $total = (int) $row['present'] + (int) $row['absent'];
    if ($total > 0) {
        $presentData[] = round(((int) $row['present'] / $total) * 100, 2);
        $absentData[] = round(((int) $row['absent'] / $total) * 100, 2);
    } else {
        $presentData[] = 0;
        $absentData[] = 0;
    }
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