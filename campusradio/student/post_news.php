<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student"){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* CHECK IF STUDENT IS BLOCKED */
$stmt = mysqli_prepare($conn, "SELECT status FROM students WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if($row && $row['status'] == 'blocked'){ 
    echo "<script>
            alert('Your account is blocked.');
            window.location='../student_dashboard.php';
          </script>";
    exit();
}

/* PREVIEW DATA */
$data = $_SESSION['preview'] ?? [];

$title = htmlspecialchars($data['title'] ?? "");
$description = htmlspecialchars($data['description'] ?? "");
$category = $data['category'] ?? "";
$media_type = $data['media_type'] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
<title>Post News</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ✅ Bootstrap Icons (IMPORTANT) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* NAVBAR */
.navbar {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* FOOTER */
.footer {
    margin-top: auto;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    text-align: center;
    padding: 12px;
}
</style>
</head>

<body class="bg-light">

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- LEFT -->
        <div class="d-flex align-items-center">

            <!-- ✅ BACK BUTTON -->
            <a href="javascript:void(0)" onclick="goBack()" class="text-white me-3 fs-4">
                <i class="bi bi-arrow-left"></i>
            </a>

            <span class="navbar-brand mb-0 h1">
                RGUKT-B Campus Radio/Post News
            </span>
        </div>

        <!-- RIGHT -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none"
               href="#"
               data-bs-toggle="dropdown">

                Welcome <?php echo htmlspecialchars($_SESSION['college_id']); ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../profile.php">👤 Profile</a></li>
                <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
            </ul>
        </div>

    </div>
</nav>

<!-- ✅ MAIN CONTENT -->
<div class="container mt-5">

<div class="card shadow p-4 mx-auto" style="max-width:600px">

<h3 class="text-center mb-4">Post Campus News</h3>

<form action="preview_news.php" method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Title</label>
<input type="text" name="title" class="form-control" value="<?php echo $title ?>" required>
</div>

<div class="mb-3">
<label>Description</label>
<textarea name="description" class="form-control" rows="4" required><?php echo $description ?></textarea>
</div>

<div class="mb-3">
<label>Category</label>
<select name="category" class="form-select" required>
<option value="">Select</option>
<option <?= ($category=="Sports")?"selected":"" ?>>Sports</option>
<option <?= ($category=="Campus Life")?"selected":"" ?>>Campus Life</option>
<option <?= ($category=="Culturals")?"selected":"" ?>>Culturals</option>
<option <?= ($category=="Events")?"selected":"" ?>>Events</option>
<option <?= ($category=="Announcements")?"selected":"" ?>>Announcements</option>
</select>
</div>

<div class="mb-3">
<label>Media Type</label>
<select name="media_type" class="form-select">
<option value="">Select</option>
<option value="image">Image</option>
<option value="audio">Audio</option>
<option value="video">Video</option>
<option value="pdf">PDF</option>
</select>
</div>

<div class="mb-3">
<label>Upload Media</label>
<input type="file" name="media" class="form-control" accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.pdf">
</div>

<button class="btn btn-primary w-100">
Preview News
</button>
<a href="../student_dashboard.php" class="btn btn-secondary btn-sm">
⬅ Back to Dashboard
</a>
</form>

</div>
</div>

<!-- ✅ BACK BUTTON SCRIPT -->
<script>
function goBack() {
    if (document.referrer !== "") {
        window.history.back();
    } else {
        window.location.href = "../student_dashboard.php";
    }
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ FOOTER -->
<footer class="footer">
    Copyright © 2026 - Azad Lingampalli. All rights reserved.
</footer>

</body>
</html>