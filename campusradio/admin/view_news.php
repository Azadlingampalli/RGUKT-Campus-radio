<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

$search="";
$category="";
$date="";

/* BASE QUERY */
$sql="SELECT news.*, students.college_id
      FROM news
      LEFT JOIN students ON news.posted_by = students.id
      WHERE 1";

/* CATEGORY FILTER */
if(isset($_GET['category']) && !empty($_GET['category'])){
    $category=mysqli_real_escape_string($conn,$_GET['category']);
    $sql.=" AND news.category='$category'";
}

/* SEARCH FILTER */
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search=mysqli_real_escape_string($conn,$_GET['search']);
    $sql.=" AND (
            news.title LIKE '%$search%' 
            OR students.college_id LIKE '%$search%'
           )";
}

/* DATE FILTER */
if(isset($_GET['date']) && !empty($_GET['date'])){
    $date=mysqli_real_escape_string($conn,$_GET['date']);
    $sql.=" AND DATE(news.created_at)='$date'";
}

$sql.=" ORDER BY news.created_at DESC";
$result=mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>View News</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#eef1f5;
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

/* CARD */
.news-card{
    border:none;
    border-radius:12px;
}

/* MEDIA */
.media-box{
    background:#fafafa;
    padding:10px;
    border-radius:10px;
    text-align:center;
}

.media-img{
    max-width:100%;
    max-height:300px;
    object-fit:contain;
    border-radius:8px;
}

video{ width:100%; max-height:280px; }
audio{ width:100%; }

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
                Campus News
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

<!-- ✅ FILTERS -->
<div class="container mt-4">



<div class="d-flex justify-content-between align-items-center">

<div class="mb-3">
<a href="view_news.php" class="btn btn-outline-primary btn-sm">All</a>
<a href="?category=Academics" class="btn btn-outline-primary btn-sm">Academics</a>
<a href="?category=Sports" class="btn btn-outline-primary btn-sm">Sports</a>
<a href="?category=Campus Life" class="btn btn-outline-primary btn-sm">Campus Life</a>
<a href="?category=Culturals" class="btn btn-outline-primary btn-sm">Culturals</a>
<a href="?category=Events" class="btn btn-outline-primary btn-sm">Events</a>
<a href="?category=Announcements" class="btn btn-outline-primary btn-sm">Announcements</a>

</div>


<div class="d-flex gap-2">
    <a href="post_news.php" class="btn btn-dark">➕ Post News</a>
    <a href="admin_dashboard.php" class="btn btn-dark">🏠 Home</a>
</div>

</div>

<form method="GET" class="row mb-4">

<div class="col-md-4">
<input type="text" name="search" class="form-control"
placeholder="Search by Title or Student ID"
value="<?php echo htmlspecialchars($search); ?>">
</div>

<div class="col-md-3">
<input type="date" name="date" class="form-control"
value="<?php echo htmlspecialchars($date); ?>">
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">Search</button>
</div>

<div class="col-md-2">
<a href="view_news.php" class="btn btn-secondary w-100">Reset</a>
</div>

</form>

</div>

<!-- ✅ NEWS LIST -->
<div class="container">

<?php if(mysqli_num_rows($result)>0){ ?>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card news-card shadow mb-4">
<div class="card-body">
<div class="row">

<!-- TEXT -->
<div class="col-md-7">

<h4><?php echo htmlspecialchars($row['title']); ?></h4>
<p><?php echo htmlspecialchars($row['description']); ?></p>

<p class="text-muted">
<b>Category:</b> <?php echo $row['category']; ?> |
<b>Posted By:</b> <?php echo $row['college_id'] ?? "Admin"; ?> |
<b>Status:</b>

<span class="badge bg-<?php
if($row['status']=="approved") echo "success";
elseif($row['status']=="rejected") echo "danger";
else echo "warning text-dark";
?>">
<?php echo ucfirst($row['status']); ?>
</span>
</p>

</div>

<!-- MEDIA -->
<div class="col-md-5">
<div class="media-box">

<?php
$media=mysqli_query($conn,"SELECT * FROM news_media WHERE news_id='".$row['news_id']."'");
while($m=mysqli_fetch_assoc($media)){
$path="../".$m['file_path'];

if($m['media_type']=="image"){
echo "<img src='$path' class='media-img mb-2'>";
}
elseif($m['media_type']=="video"){
echo "<video controls class='mb-2'><source src='$path'></video>";
}
elseif($m['media_type']=="audio"){
echo "<audio controls><source src='$path'></audio>";
}
}
?>

</div>
</div>

</div>
</div>
</div>

<?php } ?>

<?php } else { ?>

<div class="alert alert-warning text-center">
No such results found
</div>

<?php } ?>

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