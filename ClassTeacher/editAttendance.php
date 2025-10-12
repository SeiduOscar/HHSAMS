<?php
include '../includes/dbcon.php';

echo $_GET['admissionNo'];
echo " " . $_GET['dateTimeTaken'];
echo " " . $_GET['course'];
echo " " . $_GET['status'] ?? "None";

$admissionNo = "";
$dateTimeTaken = "";
$course = "";
$status = "";
if (isset($_GET['admissionNo']) && isset($_GET['dateTimeTaken'])) {
    $admissionNo = $_GET['admissionNo'];
    $dateTimeTaken = $_GET['dateTimeTaken'];
    $course = $_GET['course'];
    $status = $_GET['status'];



    switch ($status) {
        case 'Present':
            $newStatus = '0';
            break;
        case 'Absent':
            $newStatus = '1';
            break;

    }
    $editSql = "SET status = '$newStatus' WHERE admissionNo = '$admissionNo' AND dateTimeTaken = '$dateTimeTaken' AND courseCode = '$course'";
    $editQuery = mysqli_query($conn, "UPDATE tblattendance $editSql") or die(mysqli_error($con));
    if ($editQuery) {
        echo "<script type='text/javascript'>alert('Successfully edited attendance!');</script>";
        echo "<script>document.location='viewAttendance.php?course=$course'</script>";


    } else {
        echo "<script type='text/javascript'>alert('Failed to edit attendance!');</script>";
        echo "<script>document.location='attendance.php?course=$course'</script>";

    }
}





?>