<?php
include '../../Includes/dbcon.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production

try {
    // Validate input
    if (!isset($_POST['course']) || !isset($_POST['period'])) {
        throw new Exception("Missing required parameters");
    }

    $courseId = $_POST['course'];
    $period = $_POST['period'];

    // Build query based on period
    switch ($period) {
        case 'weekly':
            $groupBy = "YEARWEEK(a.dateTimeTaken)";
            $labelFormat = "CONCAT('Week ', WEEK(a.dateTimeTaken))";
            break;
        case 'monthly':
            $groupBy = "DATE_FORMAT(a.dateTimeTaken, '%Y-%m')";
            $labelFormat = "DATE_FORMAT(a.dateTimeTaken, '%b %Y')";
            break;
        case 'yearly':
            $groupBy = "YEAR(a.dateTimeTaken)";
            $labelFormat = "YEAR(a.dateTimeTaken)";
            break;
        default:
            $groupBy = "DATE_FORMAT(a.dateTimeTaken, '%Y-%m')";
            $labelFormat = "DATE_FORMAT(a.dateTimeTaken, '%b %Y')";
    }

    // Get active semester
    $semesterQuery = "SELECT * FROM tblsemester WHERE isActive = 1 LIMIT 1";
    $semesterResult = $conn->query($semesterQuery);
    
    if (!$semesterResult || $semesterResult->num_rows == 0) {
        throw new Exception("No active semester found");
    }
    
    $semester = $semesterResult->fetch_assoc();
    $semesterId = $semester['Id'];

    // Build the attendance query
    $query = "
        SELECT 
            $labelFormat AS period_label,
            COUNT(*) AS attendance_count,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_count,
            SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count
        FROM tblattendance a
        WHERE a.semesterId = ?
    ";

    // Add course filter if specific course is selected
    $params = [$semesterId];
    $types = "i";
    
    if (!empty($courseId) && $courseId !== '') {
        $query .= " AND a.courseCode = ?";
        $params[] = $courseId;
        $types .= "s";
    }

    $query .= " GROUP BY $groupBy ORDER BY $groupBy ASC";

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Dynamically bind parameters
    if (count($params) > 1) {
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param($types, $params[0]);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $data = [];
    $presentData = [];
    $absentData = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['period_label'];
        $data[] = (int)$row['attendance_count'];
        $presentData[] = (int)$row['present_count'];
        $absentData[] = (int)$row['absent_count'];
    }

    $stmt->close();

    // Return JSON response
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'data' => $data,
        'present' => $presentData,
        'absent' => $absentData,
        'message' => count($labels) > 0 ? 'Data loaded successfully' : 'No attendance data found'
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'labels' => [],
        'data' => []
    ]);
}

$conn->close();
?>
