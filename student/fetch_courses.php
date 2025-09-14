<?php
session_start();
include '../Includes/dbcon.php';

// Check if student is logged in
if (!isset($_SESSION['admissionNumber'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$studentId = $_SESSION['admissionNumber'];

// ✅ Get student info
$studQuery = $conn->prepare("SELECT num_sem, program FROM tblstudents WHERE admissionNumber = ?");
$studQuery->bind_param('s', $studentId);
$studQuery->execute();
$studResult = $studQuery->get_result();
$rows = $studResult->fetch_assoc();
$studQuery->close();



$studSem = $rows['num_sem'];
$studProgram = $rows['program'];

// ✅ Determine level
$levels = [
    ['level' => '100', 'condition' => ($studSem >= 1 && $studSem <= 2)],
    ['level' => '200', 'condition' => ($studSem >= 3 && $studSem <= 4)],
    ['level' => '300', 'condition' => ($studSem >= 5 && $studSem <= 6)],
    ['level' => '400', 'condition' => ($studSem >= 7 && $studSem <= 8)],
    ['level' => 'Alumni', 'condition' => ($studSem >= 9)],
];

$level = null;
foreach ($levels as $item) {
    if ($item['condition']) {
        $level = $item['level'];
        break;
    }
}

// ✅ Get active semester
$activeSemester = mysqli_query($conn, "SELECT * FROM tblsemester WHERE IsActive = 1");
$semRow = mysqli_fetch_assoc($activeSemester);
$CurntSemId = $semRow['Id'];

// ✅ Query courses for this level + program (including multi-programs and general courses)
$studCourses = mysqli_query(
    $conn,
    "SELECT * 
     FROM tblcourses 
     WHERE Level = '$level' 
       AND (program = '$studProgram' OR program LIKE '%$studProgram%' OR general = 1)"
);

$courlecfetch = mysqli_fetch_assoc($studCourses);
$lecId = $courlecfetch['lecturer_id'];

$lecturerQuery = mysqli_query($conn, "SELECT * FROM tblmoderator WHERE Id ='$lecId'");
$lecturerfetch = mysqli_fetch_assoc($lecturerQuery);
$lecturer = $lecturerfetch['firstName'] . "" . $lecturerfetch['lastName'];

// ✅ Populate courses array
$courses = [];
if ($studCourses && mysqli_num_rows($studCourses) > 0) {
    while ($course = mysqli_fetch_assoc($studCourses)) {
        $courses[] = [
            'name'       => $course['course_name'],
            'code'       => $course['course_code'],
            'status'     => $course['status'] ?? 'Active', // fallback
            'instructor' => $lecturer ?? 'TBA', // fallback
            // 'schedule'   => $course['schedule'] ?? 'Not Assigned', // fallback
        ];
    }
}



// ✅ Return as JSON
echo json_encode($courses);
?>
