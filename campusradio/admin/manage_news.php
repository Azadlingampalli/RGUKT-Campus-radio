<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

/* AUTO REMOVE EXPIRED PINS */
mysqli_query($conn, "
UPDATE news 
SET pinned=0, pinned_by=NULL, pinned_until=NULL 
WHERE pinned=1 AND pinned_until < NOW()
");

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

/* PIN FIRST */
$sql.=" ORDER BY news.pinned DESC, news.created_at DESC";

$result=mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage News</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#eef1f5;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
.navbar{
    background: linear-gradient(135deg, #667eea, #764ba2);
}
.footer{
    margin-top:auto;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color:white;
    text-align:center;
    padding:12px;
}
.news-card{
    border:none;
    border-radius:12px;
}
.media-box{
    background:#fafafa;
    padding:10px;
    border-radius:10px;
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

<!-- NAVBAR -->
<nav class="navbar navbar-dark shadow-sm">
<div class="container-fluid d-flex justify-content-between align-items-center">

<div class="d-flex align-items-center">
<a href="javascript:void(0)" onclick="goBack()" class="text-white me-3 fs-4">
<i class="bi bi-arrow-left"></i>
</a>
<span class="navbar-brand mb-0 h1">Manage News</span>
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

<!-- ✅ FILTERS -->
<div class="container mt-4">
<div class="d-flex justify-content-between align-items-center">
<h3 class="mb-3">📰 Campus News</h3>
<div class="d-flex gap-2">
    <a href="post_news.php" class="btn btn-dark">➕ Post News</a>
    <a href="admin_dashboard.php" class="btn btn-dark">🏠 Home</a>
</div>
</div>
<div class="mb-3">
<a href="manage_news.php" class="btn btn-outline-primary btn-sm">All</a>
<a href="?category=Academics" class="btn btn-outline-primary btn-sm">Academics</a>
<a href="?category=Sports" class="btn btn-outline-primary btn-sm">Sports</a>
<a href="?category=Campus Life" class="btn btn-outline-primary btn-sm">Campus Life</a>
<a href="?category=Culturals" class="btn btn-outline-primary btn-sm">Culturals</a>
<a href="?category=Events" class="btn btn-outline-primary btn-sm">Events</a>
<a href="?category=Announcements" class="btn btn-outline-primary btn-sm">Announcements</a>
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
<a href="manage_news.php" class="btn btn-secondary w-100">Reset</a>
</div>

</form>

</div>
<!-- CONTENT -->
<div class="container mt-4">

<?php if(mysqli_num_rows($result)>0){ ?>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card news-card shadow mb-4">
<div class="card-body">
<div class="row">

<!-- TEXT -->
<div class="col-md-7">

<h4>
<?php echo htmlspecialchars($row['title']); ?>

<?php if($row['pinned']==1){ ?>
<span class="badge bg-danger">📌 Pinned</span>
<?php } ?>
</h4>

<p><?php echo htmlspecialchars($row['description']); ?></p>

<p class="text-muted">
<b>Category:</b> <?php echo $row['category']; ?> |
<b>Posted By:</b> <?php echo $row['college_id'] ?? "Admin"; ?>
</p>

<!-- ✅ STATUS -->
<p>
<b>Status:</b>
<span class="badge bg-<?php
if($row['status']=="approved") echo "success";
elseif($row['status']=="rejected") echo "danger";
else echo "warning text-dark";
?>">
<?php echo ucfirst($row['status']); ?>
</span>
</p>

<!-- ACTION BUTTONS -->
<div class="mt-2">

<!-- APPROVE / REJECT -->
<?php if($row['status']=="pending"){ ?>

<a href="update_news.php?id=<?php echo $row['news_id']; ?>&action=approved"
class="btn btn-success btn-sm">✔ Approve</a>

<a href="update_news.php?id=<?php echo $row['news_id']; ?>&action=rejected"
class="btn btn-warning btn-sm">✖ Reject</a>

<?php } ?>

<!-- PIN / UNPIN (ONLY IF APPROVED) -->
<?php if($row['status']=="approved"){ ?>

    <?php if($row['pinned']==1){ ?>

    <a href="pin_news.php?id=<?php echo $row['news_id']; ?>&action=unpin"
    class="btn btn-warning btn-sm">📌 Unpin</a>

    <?php } else { ?>

    <button class="btn btn-outline-primary btn-sm"
    onclick="openPinModal(<?php echo $row['news_id']; ?>)">
    📍 Pin
    </button>

    <?php } ?>

<?php } ?>

<!-- DELETE -->
<a href="delete_news.php?id=<?php echo $row['news_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this news?');">
Delete
</a>

</div>

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
<div class="alert alert-warning text-center">No news found</div>
<?php } ?>

</div>

<!-- PIN MODAL -->
<div class="modal fade" id="pinModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">📌 Pin News</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<label>Select Duration</label>
<select id="pinDuration" class="form-control">
<option value="">-- Select --</option>
<option value="6h">6 Hours</option>
<option value="12h">12 Hours</option>
<option value="24h">24 Hours</option>
<option value="2d">2 Days</option>
<option value="3d">3 Days</option>
<option value="7d">7 Days</option>
</select>
</div>

<div class="modal-footer">
<button class="btn btn-primary" onclick="submitPin()">Confirm</button>
</div>

</div>
</div>
</div>

<!-- JS -->
<script>
let selectedId = 0;

function openPinModal(id){
    selectedId = id;
    let modal = new bootstrap.Modal(document.getElementById('pinModal'));
    modal.show();
}

function submitPin(){
    let duration = document.getElementById("pinDuration").value;

    if(!duration){
        alert("Please select duration");
        return;
    }

    window.location.href =
        "pin_news.php?id=" + selectedId +
        "&action=pin&duration=" + duration;
}

function goBack(){
    if(document.referrer !== ""){
        window.history.back();
    } else {
        window.location.href = "admin_dashboard.php";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<footer class="footer">
Copyright © 2026 - Azad Lingampalli
</footer>

</body>
</html>