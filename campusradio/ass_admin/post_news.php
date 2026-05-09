<?php
session_start();
include '../config.php';

/* ADMIN LOGIN CHECK */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "ass_admin"){
    header("Location: ../login.php");
    exit();
}

/* KEEP FORM DATA AFTER PREVIEW */
$data = $_SESSION['preview'] ?? [];

$title = $data['title'] ?? "";
$description = $data['description'] ?? "";
$category = $data['category'] ?? "";
$media_type = $data['media_type'] ?? "";
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Post News</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ✅ Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* NAVBAR */
.navbar{
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* FOOTER */
.footer{
    margin-top:auto;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color:white;
    text-align:center;
    padding:12px;
}
</style>

</head>

<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- LEFT -->
        <div class="d-flex align-items-center">
            <a href="javascript:void(0)" onclick="goBack()" class="text-white me-3 fs-4">
                <i class="bi bi-arrow-left"></i>
            </a>

            <span class="navbar-brand mb-0 h1">
                Assistant Admin Post News
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
                 <li>
                    <a class="dropdown-item" href="../profile.php">👤 Profile</a>
                </li>
                <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
            </ul>
        </div>

    </div>
</nav>

<!-- ✅ CONTENT -->
<div class="container mt-5">

<div class="card shadow p-4 mx-auto" style="max-width:600px">

<h3 class="text-center mb-4"> Assistant Admin Post Campus News</h3>

<form action="preview_news.php" method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control"
value="<?php echo htmlspecialchars($title); ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Description</label>
<textarea name="description" class="form-control" rows="4" required>
<?php echo htmlspecialchars($description); ?>
</textarea>
</div>

<div class="mb-3">
<label class="form-label">Category</label>
<select name="category" class="form-select" required>

<option value="">Select Category</option>
<option <?= ($category=="Sports")?"selected":"" ?>>Sports</option>
<option <?= ($category=="Campus Life")?"selected":"" ?>>Campus Life</option>
<option <?= ($category=="Culturals")?"selected":"" ?>>Culturals</option>
<option <?= ($category=="Events")?"selected":"" ?>>Events</option>
<option <?= ($category=="Academics")?"selected":"" ?>>Academics</option>

</select>
</div>

<div class="mb-3">
<label class="form-label">Media Type</label>
<select name="media_type" class="form-select">

<option value="image" <?= ($media_type=="image")?"selected":"" ?>>Image</option>
<option value="audio" <?= ($media_type=="audio")?"selected":"" ?>>Audio</option>
<option value="video" <?= ($media_type=="video")?"selected":"" ?>>Video</option>

</select>
</div>

<div class="mb-3">
<label class="form-label">Upload Media</label>
<input type="file" name="media" class="form-control"
accept="image/*,audio/*,video/*">
</div>

<button class="btn btn-primary w-100">
Preview News
</button>

</form>
<a href="admin_dashboard.php" class="btn btn-secondary btn-sm">
⬅ Back to Dashboard
</a>
</div>

</div>

<!-- ✅ BACK SCRIPT -->
<script>
function goBack(){
    if(document.referrer !== ""){
        window.history.back();
    } else {
        window.location.href = "admin_dashboard.php";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ FOOTER -->
<footer class="footer">
Copyright © 2026 - Azad Lingampalli
</footer>

</body>
</html>