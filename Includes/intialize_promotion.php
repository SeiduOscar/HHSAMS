<?php 
          // Get the current active semester
$activeSemesterQuery = "SELECT id FROM tblsemester WHERE isActive = 1";
$activeSemesterResult = mysqli_query($conn, $activeSemesterQuery);
$activeSemester = mysqli_fetch_assoc($activeSemesterResult);

// If there's an active semester
// if ($activeSemester) {
//     // Increment num_sem for all students
//     $updateQuery = "UPDATE tblstudents SET num_sem = num_sem + 1";
//     mysqli_query($conn, $updateQuery);
// }
// Check if there is a change in semester activity
$semesterCheckQuery = "SELECT semesterName, isActive FROM tblsemester ORDER BY id ASC";
$semesterCheckResult = mysqli_query($conn, $semesterCheckQuery);

$semesters = [];
while ($row = mysqli_fetch_assoc($semesterCheckResult)) {
    $semesters[$row['semesterName']] = $row['isActive'];
}

// Only increment num_sem if there is a transition from First active to Second active
if (
    isset($_SESSION['semester_state']) &&
    isset($semesters['First'], $semesters['Second'])
) {
    $prev = $_SESSION['semester_state'];
    // Check for transition: previously First was active and Second was inactive,
    // now First is inactive and Second is active
    if (
        $prev['First'] == 1 && $prev['Second'] == 0 &&
        $semesters['First'] == 0 && $semesters['Second'] == 1
    ) {
        $updateQuery = "UPDATE tblstudents SET num_sem = num_sem + 1";
        mysqli_query($conn, $updateQuery);
    }
}

// Store current semester state in session for next request
$_SESSION['semester_state'] = [
    'First' => $semesters['First'] ?? 0,
    'Second' => $semesters['Second'] ?? 0
];

// Initialize database refreshing for level 100
// Delete previous records from all level tables before initializing new ones
$conn->query("DELETE FROM level_100");
$conn->query("DELETE FROM level_200");
$conn->query("DELETE FROM level_300");
$conn->query("DELETE FROM level_400");
$conn->query("DELETE FROM alumni");

$levels = [
    ['table' => 'level_100', 'condition' => 'num_sem BETWEEN 1 AND 2'],
    ['table' => 'level_200', 'condition' => 'num_sem BETWEEN 3 AND 4'],
    ['table' => 'level_300', 'condition' => 'num_sem BETWEEN 5 AND 6'],
    ['table' => 'level_400', 'condition' => 'num_sem BETWEEN 7 AND 8'],
    ['table' => 'alumni',    'condition' => 'num_sem >= 9'],
];

foreach ($levels as $level) {
    $query = "SELECT * FROM tblstudents WHERE {$level['condition']}";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT INTO {$level['table']}
                (admissionNumber, first_name, last_name, other_name, program, department, email, classArm)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param(
                    'ssssssss',
                    $row['admissionNumber'],
                    $row['firstName'],
                    $row['lastName'],
                    $row['otherName'],
                    $row['program'],
                    $row['Department'],
                    $row['email'],
                    $row['classArm']
                );
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

          ?>
