<?php

echo "<pre>";
print_r($_GET);
echo "</pre>";


include "../Includes/dbcon.php";

// ✅ Validate required GET parameters
if (
    empty($_GET['userId']) ||
    empty($_GET['courseName']) ||
    empty($_GET['classarm']) ||
    empty($_GET['token'])
) {
    echo "<div class='result text-red-600 font-semibold'>Missing required parameters.</div>";
    echo '<meta http-equiv="refresh" content="3;url=../index.php">';
    exit;
}

// ✅ Sanitize inputs
$lecturer_id = htmlspecialchars(trim($_GET['userId']));
$courseName = htmlspecialchars(trim($_GET['courseName']));
$classArm = htmlspecialchars(trim($_GET['classarm']));
$token = htmlspecialchars(trim($_GET['token']));

// ✅ Validate token (within last hour and still valid)
$stmt = $conn->prepare("SELECT COUNT(*) FROM qr_tokens WHERE token = ? AND is_valid = 1 AND created_at > NOW() - INTERVAL 1 HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count < 1) {
    echo "<div class='result text-red-600 font-semibold'>Invalid or expired QR code token.</div>";
    echo '<meta http-equiv="refresh" content="3;url=../index.php">';
    exit;
}

// ✅ Get course details
$stmt = $conn->prepare("SELECT * FROM tblcourses WHERE courseName = ?");
$stmt->bind_param("s", $courseName);
$stmt->execute();
$result = $stmt->get_result();
$rowCourse = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

if (!$rowCourse) {
    echo "<div class='result text-red-600 font-semibold'>Course not found.</div>";
    exit;
}

$courseCode = $rowCourse["courseCode"];
$program = $rowCourse["program"];
$general = $rowCourse["general"];
$Level = "level_" . $rowCourse["Level"]; // assuming Level is numeric like 100, 200 etc.

// ✅ Get semester ID
$stmt = $conn->prepare("SELECT Id FROM tblsemester WHERE isActive = 1");
$stmt->execute();
$result = $stmt->get_result();
$semester = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();
$semesterId = $semester['Id'] ?? null;

if (!$semesterId) {
    echo "No active semester found.";
    exit;
}

// ✅ Get classArmId
$stmt = $conn->prepare("SELECT Id FROM tblclassarms WHERE classArmName = ?");
$stmt->bind_param("s", $classArm);
$stmt->execute();
$result = $stmt->get_result();
$classArmRow = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

$classArmId = $classArmRow['Id'] ?? null;
if (!$classArmId) {
    echo "Class arm not found.";
    exit;
}

// ✅ Get student list
if ($general == 1) {
    // General course - select all students from level table
    $levelQuery = mysqli_query($conn, "SELECT * FROM {$Level}");
} else {
    // Program-specific course
    $levelQuery = mysqli_query($conn, "SELECT * FROM {$Level} WHERE program = '" . mysqli_real_escape_string($conn, $program) . "'");
}

if (!$levelQuery) {
    echo "Failed to fetch student list.";
    exit;
}

// // ✅ Attendance check: Has this attendance already been taken?
// $dateTaken = date("Y-m-d");

// $attendanceCheck = $conn->prepare("
//     SELECT COUNT(*) 
//     FROM tblattendance 
//     WHERE lecturer_id = ? AND classArmId = ? AND courseCode = ? AND dateTimeTaken = ?
// ");
// $attendanceCheck->bind_param("siss", $lecturer_id, $classArmId, $courseCode, $dateTaken);
// $attendanceCheck->execute();
// $attendanceCheck->bind_result($attendanceCount);
// $attendanceCheck->fetch();
// $attendanceCheck->close();

// 
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Face Detection App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Remove Tailwind CSS CDN -->
    <style>
    body {
        background-color: #f3f4f6;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .container {
        width: 100%;
        max-width: 28rem;
        margin: 0 auto;
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        padding: 1rem;
    }

    @media (min-width: 640px) {
        .container {
            padding: 1.5rem;
        }
    }

    .title {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .center {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .avatar-wrapper {
        position: relative;
        width: 12rem;
        height: 12rem;
        border-radius: 9999px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        border: 4px solid #3b82f6;
        background: #e5e7eb;
    }

    .avatar-video {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 9999px;
    }

    .avatar-border {
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        border: 4px solid #3b82f6;
        pointer-events: none;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn {
        width: 100%;
        background: #2563eb;
        color: #fff;
        font-weight: 600;
        padding: 0.5rem 0;
        border-radius: 0.375rem;
        transition: background 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background: #1d4ed8;
    }

    #loading {
        text-align: center;
        margin-top: 0.75rem;
        color: #2563eb;
        font-weight: 500;
    }

    .result {
        margin-top: 1rem;
    }

    .text-red-600 {
        color: #dc2626;
    }

    .text-green-700 {
        color: #15803d;
    }

    .font-semibold {
        font-weight: 600;
    }

    pre {
        white-space: pre-wrap;
        word-break: break-word;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="title">Face Detection with PHP + Python</h2>

        <p class="text-center text-gray-600">Capture your face for recognition</p>
        <div class="center">
            <div class="avatar-wrapper">
                <video id="video" autoplay playsinline class="avatar-video"></video>
                <div class="avatar-border"></div>
            </div>
        </div>
        <form id="imageForm" method="POST" enctype="multipart/form-data" autocomplete="off">
            <label for="studentId">Student ID:</label>
            <input type="text" id="studentId" name="studentId" placeholder="Enter Student ID" required>

            <input type="hidden" id="imageData" name="imageData">

            <button type="button" id="captureBtn" class="btn">Capture & Detect</button>
        </form>

        <div id="loading" class="hidden">Processing...</div>
        <canvas id="canvas" style="display:none;"></canvas>
        <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const imageDataInput = document.getElementById('imageData');
        const captureBtn = document.getElementById('captureBtn');
        const form = document.getElementById('imageForm');
        const loading = document.getElementById('loading');

        // Camera access (use front camera)
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user"
                    }
                });
                video.srcObject = stream;
            } catch (err) {
                alert("Camera access denied: " + err.message);
                captureBtn.disabled = true;
            }
        }
        startCamera();

        captureBtn.addEventListener('click', function() {
            if (video.readyState < 2) {
                alert("Camera not ready.");
                return;
            }
            // Draw a circular crop of the video to the canvas
            const size = Math.min(video.videoWidth, video.videoHeight);
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            ctx.save();
            ctx.beginPath();
            ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2, true);
            ctx.closePath();
            ctx.clip();
            // Center crop the video
            const sx = (video.videoWidth - size) / 2;
            const sy = (video.videoHeight - size) / 2;
            ctx.drawImage(video, sx, sy, size, size, 0, 0, size, size);
            ctx.restore();

            const dataURL = canvas.toDataURL('image/jpeg', 0.95);
            imageDataInput.value = dataURL;
            loading.style.display = "block";
            form.submit();
        });

        // Responsive video sizing
        function resizeVideoWrapper() {
            const wrapper = document.querySelector('.video-wrapper');
            if (!wrapper) return;
            const width = wrapper.offsetWidth;
            wrapper.style.height = width + 'px';
        }
        window.addEventListener('resize', resizeVideoWrapper);
        window.addEventListener('DOMContentLoaded', resizeVideoWrapper);
        </script>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['imageData']) && isset($_POST['studentId'])) {
            $studentId = $_POST['studentId']; // entered Student ID
        
            // Fetch stored encoding from DB
            $stmt = $conn->prepare("SELECT facialEncoding FROM tblstudents WHERE admissionNumber = ?");
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $stmt->bind_result($faceEncodingJson);
            $stmt->fetch();
            $stmt->close();

            if (!$faceEncodingJson) {
                echo "<div class='result text-red-600 font-semibold'>❌ No face encoding found for Student ID: " .
                    htmlspecialchars($studentId) . "</div>";
                exit;
            }

            // Save captured face to uploads/
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $dataUrl = $_POST['imageData'];
            if (strpos($dataUrl, ',') !== false) {
                $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $data = base64_decode($data);
                $fileName = 'capture_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $filePath = "{$uploadDir}{$fileName}";
                file_put_contents($filePath, $data);

                // Run Python with image + DB encoding
                $python = escapeshellarg("C:\\Python313\\python.exe");
                $script = escapeshellarg(__DIR__ . "/recognize.py");
                $img = escapeshellarg($filePath);
                $encoding = escapeshellarg($faceEncodingJson); // send encoding JSON string
        
                $command = "$python $script $img $encoding 2>&1";
                $output = shell_exec($command);

                $result = json_decode($output, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($result['confidence'])) {
                    $confidence = floatval($result['confidence']);

                    if ($confidence >= 85) { // adjust threshold
                        // ✅ Mark attendance
                        $stmt = $conn->prepare("UPDATE tblattendance SET  status = 1
        WHERE admissionNo = ?");
                        $stmt->bind_param("s", $studentId);
                        if ($stmt->execute()) {
                            echo "<div class='result text-green-700 font-semibold'>✅ Attendance marked for $studentId (Confidence:
            $confidence%)</div>";
                        }
                        $stmt->close();
                    } else {
                        echo "<div class='result text-red-600 font-semibold'>❌ Low confidence ($confidence%). Try again.</div>";
                    }
                } else {
                    echo "<div class='result text-red-600 font-semibold'>❌ Recognition failed.<br>
            <pre>" . htmlspecialchars($output) . "</pre>
        </div>";
                }
            }
        }
        ?>
    </div>
</body>

</html>