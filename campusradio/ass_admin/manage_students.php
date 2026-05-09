<?php
session_start();
include '../config.php';

// 🔒 Security Check
if(!isset($_SESSION['role']) || $_SESSION['role'] != "ass_admin"){
    header("Location: ../login.php");
    exit();
}

$search = "";
$result = null;

if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    $sql = "SELECT * FROM students 
        WHERE role = 'student' 
        AND (
            name LIKE '%$search%' 
            OR college_id LIKE '%$search%' 
            OR email LIKE '%$search%'
        )
        ORDER BY id DESC";

    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .footer {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-align: center;
            padding: 12px;
        }

        .back-btn {
            cursor: pointer;
        }

        .back-btn:hover {
            transform: translateX(-3px);
            transition: 0.2s;
        }

        .main-content {
            flex: 1;
        }
    </style>
</head>

<body class="bg-light">

<!-- 🔝 NAVBAR -->
<nav class="navbar navbar-dark px-4 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- LEFT -->
        <div class="d-flex align-items-center">
            <span onclick="goBack()" class="text-white fs-4 me-3 back-btn">
                <i class="bi bi-arrow-left"></i>
            </span>

            <span class="navbar-brand mb-0 h5">Manage Students</span>
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

<!-- 📦 MAIN CONTENT -->
<div class="container mt-4 main-content">

<form method="GET" class="mb-4">
   <div class="d-flex gap-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="Search by Name, College ID or Email"
               value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-primary">Search</button>
    
        <a href="admin_dashboard.php" class="btn btn-dark">🏠 Home</a>
        </div>
</div>
</form>

<?php if($result !== null) { ?>

<div class="card shadow">
<div class="card-body">

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>College ID</th>
    <th>Email</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php 
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['college_id']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <?php if($row['status'] == 'active'){ ?>
                    <span class="badge bg-success">Active</span>
                <?php } else { ?>
                    <span class="badge bg-danger">Blocked</span>
                <?php } ?>
            </td>
            <td>
                <a href="student_profile.php?id=<?php echo $row['id']; ?>" 
                   class="btn btn-info btn-sm">
                   View Profile
                </a>
            </td>
        </tr>
<?php } 
} else { ?>
<tr>
    <td colspan="6" class="text-center text-danger">
        No students found
    </td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

<?php } ?>

</div>

<!-- 🔻 FOOTER (Always Bottom) -->
<footer class="footer">
    Copyright © 2026 - Azad Lingampalli. All rights reserved.
</footer>

<!-- 🔙 BACK SCRIPT -->
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

</body>
</html>