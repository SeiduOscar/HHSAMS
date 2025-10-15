<?php
// error_reporting(0);
// include '../Includes/dbcon.php';
// include '../Includes/session.php';

if (isset($_POST['export'])) {
    // Set headers for CSV download before any output
    $filename = "Attendance_list_" . date("Y-m-d") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $courseCode = isset($_POST['course']) ? $_POST['course'] : '';
    $classArm = isset($_POST['classArm']) ? $_POST['classArm'] : '';
    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    $where = [];
    $where[] = "tblattendance.lecturer_id = '{$_SESSION['userId']}'";
    if ($courseCode)
        $where[] = "tblattendance.courseCode = '" . mysqli_real_escape_string($conn, $courseCode) . "'";
    if ($classArm)
        $where[] = "tblattendance.classArmId = '" . mysqli_real_escape_string($conn, $classArm) . "'";
    if ($fromDate && $toDate)
        $where[] = "tblattendance.dateTimeTaken BETWEEN '" . mysqli_real_escape_string($conn, $fromDate) . "' AND '" . mysqli_real_escape_string($conn, $toDate) . "'";
    else if ($fromDate)
        $where[] = "tblattendance.dateTimeTaken >= '" . mysqli_real_escape_string($conn, $fromDate) . "'";
    else if ($toDate)
        $where[] = "tblattendance.dateTimeTaken <= '" . mysqli_real_escape_string($conn, $toDate) . "'";

    $whereClause = implode(' AND ', $where);

    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken,
    tblstudents.firstName, tblstudents.lastName, tblstudents.otherName, tblstudents.admissionNumber
    FROM tblattendance
    INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
    WHERE $whereClause";

    $ret = mysqli_query($conn, $query);

    $output = fopen('php://output', 'w');

    // Output the column headings always
    fputcsv($output, array('#', 'First Name', 'Last Name', 'Other Name', 'Admission No', 'Class', 'Class Arm', 'Session', 'Term', 'Status', 'Date'));

    $cnt = 1;
    if (mysqli_num_rows($ret) > 0) {
        while ($row = mysqli_fetch_assoc($ret)) {
            $status = ($row['status'] == '1') ? "Present" : "Absent";
            fputcsv($output, array(
                $cnt,
                $row['firstName'],
                $row['lastName'],
                $row['otherName'],
                $row['admissionNumber'],
                $row['className'],
                $row['classArmName'],
                $row['sessionName'],
                $row['termName'],
                $status,
                $row['dateTimeTaken']
            ));
            $cnt++;
        }
    }
    // If no data, just the headers are output, which will show the columns in Excel

    fclose($output);
    exit();
}