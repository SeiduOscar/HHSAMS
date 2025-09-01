<?php
$rs = false; // initialize

if (isset($_POST['cour'])) {
    $selectedCourse = $_POST['cour'];
    echo "<input type='hidden' name='cour' value='$selectedCourse'>";

    // Get course details
    $courseLevelResult = mysqli_query($conn, "SELECT * FROM tblcourses WHERE Id = '$selectedCourse'");
    $courseLevelRow = mysqli_fetch_assoc($courseLevelResult);

    // Build level table name
    $level = "level_" . $courseLevelRow['Level'];

    // Build student query
    if ($courseLevelRow['general'] == '0') {
        // Check if multiple programs
        if (strpos($courseLevelRow['program'], '/') !== false) {
            $programParts = explode('/', $courseLevelRow['program']);
            $programConditions = [];

            foreach ($programParts as $prog) {
                $programConditions[] = "program = '" . mysqli_real_escape_string($conn, trim($prog)) . "'";
            }

            $whereClause = implode(" OR ", $programConditions);
            $studentQuery = "SELECT * FROM `$level` WHERE $whereClause";
        } else {
            $studentQuery = "SELECT * FROM `$level` WHERE program = '" . mysqli_real_escape_string($conn, $courseLevelRow['program']) . "'";
        }
    } else {
        // General course → select all students in that level
        $studentQuery = "SELECT * FROM `$level`";
    }

    // Run query once
    $rs = mysqli_query($conn, $studentQuery);

    if (!$rs) {
        die("Query failed: " . mysqli_error($conn));
    }
}
?>

<!-- Table -->
<div class="table-responsive p-3">
  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Other Name</th>
        <th>Admission No</th>
        <th>Level</th>
        <th>Email</th>
        <th>Program</th>
        <th>Class Arm</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($rs) {
        $num = mysqli_num_rows($rs);
        $sn = 0;
        if ($num > 0) {
          while ($rows = mysqli_fetch_assoc($rs)) {
            $sn++;
            echo "
            <tr>
              <td>{$sn}</td>
              <td>{$rows['firstName']}</td>
              <td>{$rows['lastName']}</td>
              <td>" . ($rows['otherName'] ?? 'None') . "</td>
              <td>{$rows['admissionNumber']}</td>
              <td>{$level}</td>
              <td>" . ($rows['email'] ?? 'None') . "</td>
              <td>{$rows['program']}</td>
              <td>" . ($rows['classArmName'] ?? 'None') . "</td>
            </tr>";
          }
        } else {
          echo "
          <div class='alert alert-danger' role='alert'>
            No Record Found!
          </div>";
        }
      }
      ?>