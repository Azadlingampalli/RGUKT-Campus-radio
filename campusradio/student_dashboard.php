<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student"){
    header("Location: login.php");
    exit();
}

/* AUTO REMOVE EXPIRED PINS */
mysqli_query($conn, "
UPDATE news 
SET pinned=0, pinned_by=NULL, pinned_until=NULL 
WHERE pinned=1 AND pinned_until < NOW()
");

/* LATEST NEWS */
$stmt = mysqli_prepare($conn,"
SELECT n.*, m.media_type, m.file_path,
s.college_id AS posted_college
FROM news n
LEFT JOIN news_media m ON n.news_id = m.news_id
LEFT JOIN students s ON n.posted_by = s.id
WHERE n.status='approved'
ORDER BY n.created_at DESC
LIMIT 10
");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/* FLASH NEWS */
$flash_stmt = mysqli_prepare($conn,"
SELECT n.*, m.media_type, m.file_path,
s1.college_id AS posted_college,
n.pinned_by AS pinned_college
FROM news n
LEFT JOIN news_media m ON n.news_id = m.news_id
LEFT JOIN students s1 ON n.posted_by = s1.id
WHERE n.status='approved' 
AND n.pinned=1
AND n.pinned_until > NOW()
ORDER BY n.pinned_until DESC
");
mysqli_stmt_execute($flash_stmt);
$flash_result = mysqli_stmt_get_result($flash_stmt);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{background:#f5f7fb;}
.card:hover{transform:scale(1.02);transition:0.3s;}
.news-media{height:200px;object-fit:cover;border-radius:8px;}
.navbar,.footer{
    background: linear-gradient(135deg, #667eea, #764ba2);
    color:white;
}
.footer{text-align:center;padding:10px;}
.section-title{margin:20px 0 10px;}
</style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- 🔝 NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid d-flex justify-content-between">

<div class="d-flex align-items-center">
<a href="#" id="backBtn" class="text-white me-3" style="display:none;">
<i class="bi bi-arrow-left"></i>
</a>
<span class="navbar-brand">   RGUKTB Campus Radio - Student Panel</span>
</div>

<div class="dropdown">
<a class="text-white dropdown-toggle text-decoration-none" href="#" data-bs-toggle="dropdown">
Welcome <?php echo htmlspecialchars($_SESSION['college_id']); ?>
</a>

<ul class="dropdown-menu dropdown-menu-end">
<li><a class="dropdown-item" href="profile.php">👤 Profile</a></li>
<li><a class="dropdown-item text-danger" href="logout.php">🚪 Logout</a></li>
</ul>
</div>

</div>
</nav>

<div class="container mt-5 flex-grow-1">

<!-- DASHBOARD CARDS -->
<div class="row g-4">

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📝 Post News</h4>
<p>Upload campus news</p>
<a href="student/post_news.php" class="btn btn-success">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📰 View News</h4>
<p>See approved updates</p>
<a href="news/view_news.php" class="btn btn-primary">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📡 Listen Live</h4>
<p>Listen to radio</p>
<a href="student/manage_stream.php" class="btn btn-warning">Go</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3 text-center">
<h4>📜 My History</h4>
<p>Your posted news</p>
<a href="student/news_history.php" class="btn btn-secondary">View</a>
</div>
</div>

</div>

<!-- 🔥 FLASH NEWS -->
<?php if(mysqli_num_rows($flash_result)>0){ ?>
<hr class="mt-5">
<h4 class="text-danger section-title">Flash News</h4>

<div class="row">
<?php while($flash=mysqli_fetch_assoc($flash_result)){ ?>

<div class="col-md-6 mb-4">
<div class="card shadow position-relative h-100">

<span class="badge bg-danger position-absolute top-0 end-0 m-2">📌</span>

<div class="card-body d-flex flex-column">

<h5><?php echo htmlspecialchars($flash['title']); ?></h5>

<?php if($flash['media_type']=="image"){ ?>
<img src="<?php echo $flash['file_path']; ?>" class="img-fluid news-media mb-2">
<?php } elseif($flash['media_type']=="audio"){ ?>
<audio controls class="w-100 mb-2"><source src="<?php echo $flash['file_path']; ?>"></audio>
<?php } elseif($flash['media_type']=="video"){ ?>
<video controls class="w-100 news-media mb-2"><source src="<?php echo $flash['file_path']; ?>"></video>
<?php } ?>

<p><?php echo substr(htmlspecialchars($flash['description']),0,100); ?>...</p>

<small class="text-muted">
🎓 <?php echo $flash['posted_college']; ?> |
📌 <?php echo $flash['pinned_college']; ?> |
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
<img src="<?php echo $flash['file_path']; ?>" class="img-fluid mb-3">
<?php } elseif($flash['media_type']=="audio"){ ?>
<audio controls class="w-100 mb-3"><source src="<?php echo $flash['file_path']; ?>"></audio>
<?php } elseif($flash['media_type']=="video"){ ?>
<video controls class="w-100 mb-3"><source src="<?php echo $flash['file_path']; ?>"></video>
<?php } ?>

<p><?php echo nl2br(htmlspecialchars($flash['description'])); ?></p>

</div>

</div>
</div>
</div>

<?php } ?>
</div>
<?php } ?>

<hr class="mt-5">

<h3 class="mb-4">Latest Campus News</h3>

<div class="row">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="col-md-6 mb-4">
<div class="card shadow-sm h-100">

<div class="card-body d-flex flex-column">

<h5><?php echo htmlspecialchars($row['title']); ?></h5>

<?php if($row['media_type']=="image"){ ?>
<img src="<?php echo $row['file_path']; ?>" class="img-fluid news-media mb-2">
<?php } elseif($row['media_type']=="audio"){ ?>
<audio controls class="w-100"><source src="<?php echo $row['file_path']; ?>"></audio>
<?php } elseif($row['media_type']=="video"){ ?>
<video controls class="w-100 news-media"><source src="<?php echo $row['file_path']; ?>"></video>
<?php } ?>

<p><?php echo substr(htmlspecialchars($row['description']),0,120); ?>...</p>

<small class="text-muted">
<?php echo date("d M Y", strtotime($row['created_at'])); ?>
</small>

<button class="btn btn-outline-primary mt-auto"
data-bs-toggle="modal"
data-bs-target="#newsModal<?php echo $row['news_id']; ?>">
Read Full News
</button>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="newsModal<?php echo $row['news_id']; ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5><?php echo htmlspecialchars($row['title']); ?></h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<?php if($row['media_type']=="image"){ ?>
<img src="<?php echo $row['file_path']; ?>" class="img-fluid mb-3">
<?php } elseif($row['media_type']=="audio"){ ?>
<audio controls class="w-100 mb-3"><source src="<?php echo $row['file_path']; ?>"></audio>
<?php } elseif($row['media_type']=="video"){ ?>
<video controls class="w-100 mb-3"><source src="<?php echo $row['file_path']; ?>"></video>
<?php } ?>

<p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>

</div>

</div>
</div>
</div>

<?php } ?>

</div>
</div>


<!-- 🔙 BACK BUTTON -->
<script>
const backBtn=document.getElementById("backBtn");
if(document.referrer!=="") backBtn.style.display="inline";
backBtn.onclick=()=>history.back();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- 🔻 FOOTER -->
<footer class="footer">
Copyright © 2026 - Azad Lingampalli. All rights reserved.
</footer>

</body>
</html>