<?php
session_start();
include '../config.php';

// 🔒 Security Check
if(!isset($_SESSION['role']) || $_SESSION['role'] !== "ass_admin"){
    header("Location: ../login.php");
    exit();
}

/* 🔥 AUTO REMOVE EXPIRED PINS */
mysqli_query($conn, "
UPDATE news 
SET pinned=0, pinned_by=NULL, pinned_until=NULL 
WHERE pinned=1 AND pinned_until < NOW()
");

// 📄 Fetch Recent Approved News
$sql = "SELECT n.news_id, n.title, n.created_at 
        FROM news n 
        WHERE n.status='approved' 
        ORDER BY n.created_at DESC 
        LIMIT 10";

$result = mysqli_query($conn, $sql);

/* 🔥 FLASH NEWS (FIXED FULL) */
$flash_stmt = mysqli_prepare($conn,"
SELECT 
    n.*, 
    m.media_type, 
    m.file_path,
    s1.college_id AS posted_college,
    COALESCE(s2.college_id, n.pinned_by) AS pinned_college

FROM news n

LEFT JOIN news_media m ON n.news_id = m.news_id
LEFT JOIN students s1 ON n.posted_by = s1.id
LEFT JOIN students s2 ON n.pinned_by = s2.id

WHERE n.status='approved'
AND n.pinned=1
AND n.pinned_until > NOW()

ORDER BY n.pinned_until DESC
");
mysqli_stmt_execute($flash_stmt);
$flash_result = mysqli_stmt_get_result($flash_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asst. Admin Dashboard - Campus Radio</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{background:#f5f7fb;}
.news-link { text-decoration:none; color:inherit; }
.news-date { color:#a40000; font-weight:bold; margin-right:4px; }

.card { transition:0.3s; border:none; }
.card:hover { transform: translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.1); }

.navbar,.footer{
    background: linear-gradient(135deg, #667eea, #764ba2);
    color:white;
}

.footer{
    text-align:center;
    padding:15px;
}

.news-media{
    max-height:200px;
    object-fit:cover;
    border-radius:6px;
}
</style>
</head>

<body>

<!-- 🔝 NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid d-flex justify-content-between">

<div class="d-flex align-items-center">
<span onclick="goBack()" class="text-white me-3 fs-4">
<i class="bi bi-arrow-left"></i>
</span>

<span class="navbar-brand">
RGUKTB Campus Radio - Assistant Admin Panel
</span>
</div>

<div class="dropdown">
<a class="text-white dropdown-toggle text-decoration-none"
href="#" data-bs-toggle="dropdown">
Welcome <?php echo htmlspecialchars($_SESSION['college_id']); ?>
</a>

<ul class="dropdown-menu dropdown-menu-end">
<li><a class="dropdown-item" href="../profile.php">👤 Profile</a></li>
<li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
</ul>
</div>

</div>
</nav>
<!-- ✅ DASHBOARD -->
<div class="container mt-5">

<div class="row g-4">

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📰 Manage News</h4>
<p>Approve, Reject, Delete News</p>
<a href="manage_news.php" class="btn btn-primary">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📡 Live Stream</h4>
<p>Control Radio ON / OFF</p>
<a href="manage_stream.php" class="btn btn-success">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>👨‍🎓 Manage Students</h4>
<p>View & Delete Student Accounts</p>
<a href="manage_students.php" class="btn btn-warning">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>➕ Post News</h4>
<p>Publish campus news directly</p>
<a href="post_news.php" class="btn btn-info">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📰 View News</h4>
<p>See approved campus updates</p>
<a href="view_news.php" class="btn btn-primary">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📜 My Posting History</h4>
<p>View news posted by you</p>
<a href="news_history.php" class="btn btn-secondary">View</a>
</div>
</div>

</div>

<!-- 🔥 FLASH NEWS -->
<?php if(mysqli_num_rows($flash_result)>0){ ?>
<hr>
<h4 class="text-danger"> Flash News</h4>

<div class="row">

<?php while($flash=mysqli_fetch_assoc($flash_result)){ ?>
<?php $path = "../".$flash['file_path']; ?>

<div class="col-md-6 mb-4">
<div class="card shadow position-relative h-100">

<span class="badge bg-danger position-absolute top-0 end-0 m-2">📌</span>

<div class="card-body d-flex flex-column">

<h5><?php echo htmlspecialchars($flash['title']); ?></h5>

<!-- MEDIA -->
<?php if($flash['media_type']=="image"){ ?>
<img src="<?php echo $path; ?>" class="img-fluid news-media mb-2">

<?php } elseif($flash['media_type']=="audio"){ ?>
<audio controls class="w-100 mb-2">
<source src="<?php echo $path; ?>">
</audio>

<?php } elseif($flash['media_type']=="video"){ ?>
<video controls class="w-100 news-media mb-2">
<source src="<?php echo $path; ?>">
</video>
<?php } ?>

<p><?php echo substr(htmlspecialchars($flash['description']),0,100); ?>...</p>

<small class="text-muted">
🎓 <?php echo $flash['posted_college'] ?? 'N/A'; ?> |
📌 <?php echo $flash['pinned_college'] ?? 'Admin'; ?> |
⏳ <?php echo date("d M Y h:i A", strtotime($flash['pinned_until'])); ?>
</small>

<button class="btn btn-danger mt-auto"
data-bs-toggle="modal"
data-bs-target="#flashModal<?php echo $flash['news_id']; ?>">
View Full News
</button>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="flashModal<?php echo $flash['news_id']; ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5><?php echo htmlspecialchars($flash['title']); ?></h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<?php if($flash['media_type']=="image"){ ?>
<img src="<?php echo $path; ?>" class="img-fluid mb-3">

<?php } elseif($flash['media_type']=="audio"){ ?>
<audio controls class="w-100 mb-3">
<source src="<?php echo $path; ?>">
</audio>

<?php } elseif($flash['media_type']=="video"){ ?>
<video controls class="w-100 mb-3">
<source src="<?php echo $path; ?>">
</video>
<?php } ?>

<p><?php echo nl2br(htmlspecialchars($flash['description'])); ?></p>

</div>

</div>
</div>
</div>

<?php } ?>
</div>
<?php } ?>

<!-- 📰 RECENT NEWS -->
<div class="card shadow p-4 mb-5">
<h3 class="mb-3">Recent Campus News</h3>

<ul class="list-group">
<?php 
if(mysqli_num_rows($result) > 0){
while($row=mysqli_fetch_assoc($result)){ ?>
<a href="view_news1.php?id=<?php echo $row['news_id']; ?>" class="news-link">
<li class="list-group-item">
<span class="news-date">
<?php echo date("d M Y", strtotime($row['created_at'])); ?>
</span>
: <?php echo htmlspecialchars($row['title']); ?>
</li>
</a>
<?php }} else {
echo "<li class='list-group-item text-muted'>No news available</li>";
} ?>
</ul>
</div>

</div>

<!-- FOOTER -->
<footer class="footer">
Copyright © 2026 - Azad Lingampalli. All rights reserved.
</footer>

<script>
function goBack(){
if(document.referrer!==""){
window.history.back();
}else{
window.location.href="../login.php";
}
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>