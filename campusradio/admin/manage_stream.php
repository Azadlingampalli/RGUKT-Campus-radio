<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>ass_admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f5f7fb;
}

.navbar,.footer{
    background: linear-gradient(135deg, #667eea, #764ba2);
    color:white;
}

.footer{
    text-align:center;
    padding:10px;
}

/* ✅ CENTER CONTENT */
.main-content{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
}

/* ✅ CARD STYLE */
.live-box{
    background:white;
    padding:40px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}
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
<span class="navbar-brand">RGUKTB Campus Radio - Live Stream</span>
</div>

<div class="dropdown">
<a class="text-white dropdown-toggle text-decoration-none" href="#" data-bs-toggle="dropdown">
Welcome <?php echo htmlspecialchars($_SESSION['college_id']); ?>
</a>

<ul class="dropdown-menu dropdown-menu-end">
<li><a class="dropdown-item" href="../profile.php">👤 Profile</a></li>
<li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
</ul>
</div>

</div>
</nav>

<!-- ✅ CENTER MESSAGE -->
<div class="container main-content">
    <div class="live-box">
        <h3 class="text-danger mb-3">
            <i class="bi bi-broadcast"></i> Live Stream Offline
        </h3>
        <p class="text-muted mb-0">
            The campus radio live stream is not available at the moment.<br>
            Please check back later.
        </p>
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