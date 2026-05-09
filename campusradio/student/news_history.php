<?php
session_start();
include '../config.php';

// SECURITY CHECK
if(!isset($_SESSION['role'])){
    header("Location: ../login.php");
    exit();
}

$college_id = $_SESSION['college_id'];

// FETCH NEWS
$sql = "SELECT n.news_id, n.title, n.created_at, n.status
        FROM news n
        JOIN students u ON n.posted_by = u.id
        WHERE u.college_id = ?
        ORDER BY n.created_at DESC";

$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"s",$college_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
<title>My News History</title>

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
                My News History
            </span>
        </div>

        <!-- RIGHT -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none"
               href="#"
               data-bs-toggle="dropdown">
                Welcome <?php echo htmlspecialchars($college_id); ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../profile.php">👤 Profile</a></li>
                <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
            </ul>
        </div>

    </div>
</nav>

<!-- ✅ CONTENT -->
<div class="container mt-5">

<div class="card shadow">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center">
<h3>Your News Posts</h3>
<div class="d-flex gap-2">
    <a href="post_news.php" class="btn btn-dark">➕ Post News</a>
    <a href="../student_dashboard.php" class="btn btn-dark">🏠 Home</a>
</div>
</div>

<table class="table table-hover mt-3">

<thead class="table-dark">
<tr>
<th>Date</th>
<th>Title</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($result)>0): ?>

<?php while($row=mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo date("d M Y",strtotime($row['created_at'])); ?></td>

<td><?php echo htmlspecialchars($row['title']); ?></td>

<td>
<?php
$badge="bg-secondary";
if($row['status']=="approved") $badge="bg-success";
elseif($row['status']=="pending") $badge="bg-warning";
elseif($row['status']=="rejected") $badge="bg-danger";
?>
<span class="badge <?php echo $badge; ?>">
<?php echo ucfirst($row['status']); ?>
</span>
</td>

<td>
<a href="view_news1.php?id=<?php echo $row['news_id']; ?>"
class="btn btn-sm btn-primary">
View
</a>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="4" class="text-center">
No news posted yet
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>

</div>

<!-- ✅ BACK BUTTON SCRIPT -->
<script>
function goBack(){
    if(document.referrer !== ""){
        window.history.back();
    } else {
        window.location.href = "../student_dashboard.php";
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

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>