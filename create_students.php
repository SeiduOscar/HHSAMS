<?php
// echo "<pre>";
// print_r($_GET);
// echo "</pre>";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include './Includes/dbcon.php';

// ✅ Validate required GET parameters
if (

  empty($_GET['valid']) ||
  empty($_GET['token'])
) {
  echo "<div class='result text-red-600 font-semibold'> Page Unavailable! Contact Administrator.</div>";
  echo '<meta http-equiv="refresh" content="3;url=./index.php">';
  exit;
}

// ✅ Sanitize inputs

$validPeriod = htmlspecialchars(trim($_GET['valid']));
$token = htmlspecialchars(trim($_GET['token']));

// ✅ Validate token (within last hour and still valid)
$stmt = $conn->prepare("SELECT COUNT(*) FROM qr_tokens WHERE token = ? AND is_valid = 1 AND created_at > NOW() - INTERVAL $validPeriod HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count < 1) {
  echo "<div class='result text-red-600 font-semibold'>Page Expired. Please Contact Your Administrator</div>";
  echo '<meta http-equiv="refresh" content="3;url=./index.php">';
  exit;
}

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $otherName = $_POST['otherName'];
  $admissionNumber = $_POST['admissionNumber'];
  $password = md5($_POST['password']);
  $programId = $_POST['program']; // Program ID (name or ID)
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $classArmId = $_POST['classArm'];
  $dateCreated = date("Y-m-d");

  $emailValid = preg_match('/^[a-zA-Z0-9._%+-]+@(hcuc\.edu\.gh|hcu\.edu\.gh)$/i', $email);
  if (!$emailValid) {
    $statusMsg = "<div class='alert alert-danger'>Invalid email domain. Email must end with @hcuc.edu.gh or @hcu.edu.gh.</div>";
  }

  // Get program code name
  $query = mysqli_query($conn, "SELECT ProgramDepartmentCodeName FROM tblprograms WHERE ProgramName ='$programId'");
  $row = mysqli_fetch_assoc($query);
  $programCode = $row['ProgramDepartmentCodeName'] ?? '';

  // Get department name
  $queryDepartment = mysqli_query($conn, "SELECT departmentName FROM tbldepartments WHERE CodeName ='$programCode'");
  $departmentRow = mysqli_fetch_assoc($queryDepartment);
  $departmentName = $departmentRow['departmentName'] ?? '';

  $imageName = null;
  $faceEncodingJson = null;

  if (!empty($_POST['capturedImage'])) {
    $data = $_POST['capturedImage'];

    // Validate base64 format
    if (strpos($data, ';') !== false && strpos($data, ',') !== false) {
      list($type, $data) = explode(';', $data);
      list(, $data) = explode(',', $data);
      $data = base64_decode($data);

      if ($data === false) {
        $statusMsg = "<div class='alert alert-danger'>Failed to decode captured image.</div>";
      } else {
        if (!is_dir('uploads')) {
          mkdir('uploads', 0755, true);
        }

        $imageName = uniqid('student_') . $admissionNumber . '.png';
        $uploadPath = 'uploads/' . $imageName;

        if (file_put_contents($uploadPath, $data) === false) {
          $statusMsg = "<div class='alert alert-danger'>Failed to save uploaded image.</div>";
        } else {
          // Call Python face encoding script
          $pythonExe = escapeshellarg("C:\\Python313\\python.exe");
          $pythonScript = escapeshellarg(__DIR__ . './recognition/facial_encode.py');
          $command = "$pythonExe $pythonScript " . escapeshellarg(realpath($uploadPath));

          $output = shell_exec($command);
          $result = json_decode($output, true);

          if ($result && isset($result['status'])) {
            if ($result['status'] === 'success' && !empty($result['face_encoding'])) {
              $faceEncodingJson = json_encode($result['face_encoding']);
            } else {
              $statusMsg = "<div class='alert alert-danger'>No face detected in the captured image. Please try again.</div>";
              $faceEncodingJson = null;
            }
          } else {
            $statusMsg = "<div class='alert alert-danger'>Invalid response from face recognition script.</div>";
            $faceEncodingJson = null;
          }
        }
      }
    } else {
      $statusMsg = "<div class='alert alert-danger'>Invalid image data format.</div>";
    }
  } else {
    $statusMsg = "<div class='alert alert-danger'>Please capture an image.</div>";
  }

  // Proceed to insert only if face encoding was obtained and email is valid
  if ($faceEncodingJson !== null && $emailValid) {
    // Check duplicate admission number
    $queryCheck = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber ='$admissionNumber'");
    if (mysqli_num_rows($queryCheck) > 0) {
      $statusMsg = "<div class='alert alert-danger'>Admission number already exists! Contact Admin for Support</div>";
    } else {
      // Validate class arm
      $queryClassArm = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE classArmName ='$classArmId'");
      if (mysqli_num_rows($queryClassArm) > 0) {
        // Insert student with facial encoding JSON as TEXT
        $stmt = $conn->prepare("INSERT INTO tblstudents (firstName,lastName,otherName,admissionNumber,password,Department,program,email,classArm,dateCreated, facialEncoding) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
          "sssssssssss",
          $firstName,
          $lastName,
          $otherName,
          $admissionNumber,
          $password,
          $departmentName,
          $programId,
          $email,
          $classArmId,
          $dateCreated,
          $faceEncodingJson
        );

        if ($stmt->execute()) {
          echo "<script>
                        alert('Student created successfully!');
                        window.location.href = 'create_Students.php';
                        </script>";
          exit();
        } else {
          $statusMsg = "<div class='alert alert-danger'>Insert failed: " . htmlspecialchars($stmt->error) . "</div>";
        }
      } else {
        $statusMsg = "<div class='alert alert-danger'>Invalid class arm!</div>";
      }
    }
  }
}

if (isset($_GET['status'])) {
  $status = $_GET['status'];
  if ($status == 'success') {
    $statusMsg = "<div class='alert alert-success'>Student created successfully!</div>";
  } elseif ($status == 'error') {
    $statusMsg = "<div class='alert alert-danger'>Error creating student. Please try again.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Student</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    #video,
    #canvas {
        width: 300px;
        /* fixed width */
        height: 300px;
        /* same as width to make it a perfect square */
        border: 1px solid #ddd;
        border-radius: 50%;
        /* makes it circular */
        object-fit: cover;
        /* keeps video from distorting */
        overflow: hidden;
        /* hides any overflow outside the circle */
    }

    #preview img {
        width: 300px;
        height: 300px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    #preview {
        width: 300px;
        /* fixed width */
        height: 300px;
        /* same as width to make it a perfect square */
        border: 1px solid #ddd;
        border-radius: 50%;
    }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Create Student</h5>
                <?php if (isset($statusMsg))
          echo $statusMsg;

        ?>

            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <div class="mb-3 row">
                        <label for="firstName" class="col-sm-3 col-form-label">First Name <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="firstName" id="firstName" required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="lastName" class="col-sm-3 col-form-label">Last Name <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="lastName" id="lastName" required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="otherName" class="col-sm-3 col-form-label">Other Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="otherName" id="otherName" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="password" class="col-sm-3 col-form-label">Password <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="password" id="password" required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="admissionNumber" class="col-sm-3 col-form-label">Admission Number <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="admissionNumber" id="admissionNumber"
                                required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="program" class="col-sm-3 col-form-label">Select Program of Study <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select required name="program" id="program" class="form-control">
                                <option value="">--Select Program--</option>
                                <?php
                $qry = "SELECT * FROM tblprograms ORDER BY ProgramName ASC";
                $result = $conn->query($qry);
                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($row['ProgramCodeName']) . '">' . htmlspecialchars($row['ProgramName']) . '</option>';
                }
                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="classArm" class="col-sm-3 col-form-label">Class Arm <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="classArm" id="classArm" class="form-control" required>
                                <option value="">--Select Class Arm--</option>
                                <?php
                $qry = "SELECT * FROM tblclassarms ORDER BY classArmName ASC";
                $result = $conn->query($qry);
                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($row['classArmName']) . '">' . htmlspecialchars($row['classArmName']) . '</option>';
                }
                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-sm-3 col-form-label">Email <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" id="email"
                                pattern="^[a-zA-Z0-9._%+-]+@(hcuc\.edu\.gh|hcu\.edu\.gh)$" required />
                        </div>
                    </div>

                    <!-- Webcam Capture Section -->
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Take Picture <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <video id="video" autoplay playsinline></video>
                            <button type="button" id="captureBtn" class="btn btn-primary mt-2">Capture</button>
                            <canvas id="canvas" style="display:none;"></canvas>
                            <input type="hidden" name="capturedImage" id="capturedImage" required />
                            <div id="preview" class="mt-3"></div>
                        </div>
                    </div>

                    <button type="submit" name="save" class="btn btn-success">Save Student</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const preview = document.getElementById('preview');
    const capturedImageInput = document.getElementById('capturedImage');

    // Access webcam
    navigator.mediaDevices.getUserMedia({
            video: true
        })
        .then(stream => {
            video.srcObject = stream;
            video.play();
        })
        .catch(err => {
            preview.innerHTML = '<p class="text-danger">Cannot access webcam: ' + err.message + '</p>';
        });

    captureBtn.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/png');
        capturedImageInput.value = dataUrl;

        preview.innerHTML = `<img src="${dataUrl}" alt="Captured Image" class="img-fluid rounded" />`;
    });
    </script>
</body>

</html>