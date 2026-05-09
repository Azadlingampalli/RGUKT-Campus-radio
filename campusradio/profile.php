<?php
session_start();
include 'config.php';

// 🔒 SECURITY CHECK
if(!isset($_SESSION['role'])){
    header("Location: login.php");
    exit();
}

$college_id = $_SESSION['college_id'];

/* ✅ SECURE QUERY (Prepared Statement) */
$sql = "SELECT * FROM students WHERE college_id=?";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"s",$college_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
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

/* PROFILE CARD */
        
.profile-card{
    min-width: 400px;
    width: auto;
    max-width: 600px; /* optional limit so it doesn't stretch too much */

    margin: 80px auto;
    padding: 25px;
    border-radius: 15px;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    text-align: center;
}
.profile-card img{
    width: 90px;
    margin-bottom: 15px;
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
                My Profile
            </span>
        </div>

        <!-- RIGHT -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none"
               href="#"
               data-bs-toggle="dropdown">
                Welcome         <?php echo htmlspecialchars($college_id); ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item text-danger" href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

    </div>
</nav>

<!-- ✅ PROFILE -->
<div class="profile-card">

<img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">

<h4><?php echo htmlspecialchars($user['name']); ?></h4>

<p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

<hr>

<p><b>College ID:</b> <?php echo htmlspecialchars($user['college_id']); ?></p>

<p><b>Role:</b> <?php echo htmlspecialchars($user['role']); ?></p>

<?php
// Decide dashboard
$dashboard = "student_dashboard.php";

if($user['role'] == "admin"){
    $dashboard = "admin/admin_dashboard.php";
} elseif($user['role'] == "ass_admin"){
    $dashboard = "ass_admin/admin_dashboard.php";
}
?>

<a href="<?php echo $dashboard; ?>" class="btn btn-primary mt-3">
    ⬅ Back
</a>

<!--<a href="change_password.php" class="btn btn-danger mt-2 w-100">
    🔐 Change Password
</a>
-->
</div>

<!-- ✅ BACK SCRIPT -->
<script>
function goBack(){
    if(document.referrer !== ""){
        window.history.back();
    } else {
        window.location.href = "<?php echo $dashboard; ?>";
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