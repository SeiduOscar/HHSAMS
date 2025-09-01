<?php
include '../Includes/dbcon.php';








?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Information Form</title>
</head>

<body>
    <h2>Student Information Form</h2>
    <form method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <label for="admission_number">Admission Number:</label><br>
        <input type="text" id="admission_number" name="admission_number" required><br><br>
        <label for="course">Course:</label><br>
        <input type="text" id="course" name="course" required><br><br>
        <label for="class_arm">Class/Arm:</label><br>
        <input type="text" id="class_arm" name="class_arm" required><br><br>
        <input type="submit" value="Submit">
    </form>



</body>
<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $admission_number = $_POST['admission_number'];
    $course = $_POST['course'];
    $class_arm = $_POST['class_arm'];

    // Display the form data
    echo "<h2>Student Information</h2>";
    echo "Name: $name<br>";
    echo "Admission Number: $admission_number<br>";
    echo "Course: $course<br>";
    echo "Class/Arm: $class_arm<br>";

    $query = "INSERT INTO tblattendance(admissionNo) VALUES('$admission_number')";
    $me = $conn->query($query);
    // You can also store the data in a database or perform other operations here
}
?>

</html>