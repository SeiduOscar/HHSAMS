<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Set headers for CSV download before any output
$filename = "Attendance_list_" . date("Y-m-d") . ".csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$dateTaken = date("Y-m-d");
$query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
        tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
        tblstudents.firstName, tblstudents.lastName, tblstudents.otherName, tblstudents.admissionNumber
        FROM tblattendance
        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
        INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
        INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
        INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        WHERE tblattendance.dateTimeTaken = '$dateTaken' AND tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'";

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