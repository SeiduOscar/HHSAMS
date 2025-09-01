<?php
include './Includes/dbcon.php'; // your DB connection

// Fetch courses for dropdown
$courses = $conn->query("SELECT courseCode, courseName FROM tblcourses ORDER BY courseName ASC");

// Fetch class arms for dropdown
$classArms = $conn->query("SELECT Id, classArmName FROM tblclassarms ORDER BY classArmName ASC");

// Initialize attendance data
$attendanceData = [];

if (isset($_POST['filter'])) {
    $courseCode = $_POST['course'];
    $classArmId = $_POST['classArm'];
    $semester = $_POST['semester'];
    $period = $_POST['period']; // weekly, monthly, yearly

    // Determine grouping based on period
    if ($period == 'Weekly') {
        $groupBy = "YEARWEEK(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d'))";
        $labelFormat = "CONCAT('Week ', WEEK(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d')))"; 
    } elseif ($period == 'Monthly') {
        $groupBy = "DATE_FORMAT(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d'), '%Y-%m')";
        $labelFormat = "DATE_FORMAT(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d'), '%b %Y')";
    } else { // Yearly
        $groupBy = "YEAR(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d'))";
        $labelFormat = "YEAR(STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d'))";
    }

    $query = "
        SELECT 
            $labelFormat AS periodLabel,
            SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN a.status='Absent' THEN 1 ELSE 0 END) AS absent
        FROM tblattendance a
        WHERE a.courseCode = ?
          AND a.classArmId = ?
          AND a.semester = ?
        GROUP BY $groupBy
        ORDER BY $groupBy ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $courseCode, $classArmId, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Visualization</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; padding-top: 40px; }
        .card { margin-top: 20px; }
        .form-label { font-weight: bold; }
        canvas { background-color: #fff; border-radius: 8px; padding: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Attendance Visualization</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course" class="form-select" required>
                        <option value="">--Select Course--</option>
                        <?php while ($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['courseCode'] ?>" <?= (isset($_POST['course']) && $_POST['course'] == $c['courseCode']) ? 'selected' : '' ?>>
                                <?= $c['courseName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Class Arm</label>
                    <select name="classArm" class="form-select" required>
                        <option value="">--Select Class Arm--</option>
                        <?php while ($c = $classArms->fetch_assoc()): ?>
                            <option value="<?= $c['Id'] ?>" <?= (isset($_POST['classArm']) && $_POST['classArm'] == $c['Id']) ? 'selected' : '' ?>>
                                <?= $c['classArmName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <input type="text" name="semester" class="form-control" placeholder="e.g. 1" value="<?= $_POST['semester'] ?? '' ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Period</label>
                    <select name="period" class="form-select" required>
                        <option value="">--Select Period--</option>
                        <option value="Weekly" <?= (isset($_POST['period']) && $_POST['period'] == 'Weekly') ? 'selected' : '' ?>>Weekly</option>
                        <option value="Monthly" <?= (isset($_POST['period']) && $_POST['period'] == 'Monthly') ? 'selected' : '' ?>>Monthly</option>
                        <option value="Yearly" <?= (isset($_POST['period']) && $_POST['period'] == 'Yearly') ? 'selected' : '' ?>>Yearly</option>
                    </select>
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($attendanceData)): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <canvas id="attendanceChart" width="800" height="400"></canvas>
            </div>
        </div>

        <script>
            const labels = <?= json_encode(array_column($attendanceData, 'periodLabel')) ?>;
            const presentData = <?= json_encode(array_column($attendanceData, 'present')) ?>;
            const absentData = <?= json_encode(array_column($attendanceData, 'absent')) ?>;

            const data = {
                labels: labels,
                datasets: [
                    {
                        label: 'Present',
                        data: presentData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    },
                    {
                        label: 'Absent',
                        data: absentData,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    }
                ]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Attendance for Selected Course'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            new Chart(
                document.getElementById('attendanceChart'),
                config
            );
        </script>
    <?php elseif(isset($_POST['filter'])): ?>
        <div class="alert alert-warning mt-3">No attendance data found for the selected filters.</div>
    <?php endif; ?>
</div>
</body>
</html>
