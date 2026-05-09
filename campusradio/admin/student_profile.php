<?php
session_start();
include '../config.php';

// 🔒 Security Check
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: manage_students.php");
    exit();
}

$id = intval($_GET['id']);

// BLOCK / UNBLOCK
if(isset($_GET['action'])){
    if($_GET['action'] == 'block'){
        mysqli_query($conn, "UPDATE students SET status='blocked' WHERE id=$id");
    }
    if($_GET['action'] == 'unblock'){
        mysqli_query($conn, "UPDATE students SET status='active' WHERE id=$id");
    }
    header("Location: student_profile.php?id=$id");
    exit();
}

// DELETE
if(isset($_GET['delete'])){
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
    header("Location: manage_students.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$student = mysqli_fetch_assoc($result);

if(!$student){
    echo "Student not found";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
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

        .main-content {
            flex: 1;
        }

        .back-btn {
            cursor: pointer;
        }

        .back-btn:hover {
            transform: translateX(-3px);
            transition: 0.2s;
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

            <span class="navbar-brand mb-0 h5">Student Profile</span>
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
<div class="container mt-5 main-content">

<div class="card shadow p-4">
    <h3>Student Profile</h3>
    <hr>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
    <p><strong>College ID:</strong> <?php echo htmlspecialchars($student['college_id']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    <p><strong>Role:</strong> <?php echo ucfirst($student['role']); ?></p>

    <p><strong>Status:</strong> 
    <?php if($student['status'] == 'active'){ ?>
        <span class="badge bg-success">Active</span>
    <?php } else { ?>
        <span class="badge bg-danger">Blocked</span>
    <?php } ?>
    </p>

    <hr>

    <div class="d-flex gap-2 flex-wrap">

    <?php if($student['status'] == 'active'){ ?>
        <a href="?id=<?php echo $id; ?>&action=block" 
           class="btn btn-warning">
           <i class="bi bi-person-x"></i> Block
        </a>
    <?php } else { ?>
        <a href="?id=<?php echo $id; ?>&action=unblock" 
           class="btn btn-success">
           <i class="bi bi-person-check"></i> Unblock
        </a>
    <?php } ?>

    <a href="?id=<?php echo $id; ?>&delete=1"
       onclick="return confirm('Are you sure you want to delete this student?');"
       class="btn btn-danger">
       <i class="bi bi-trash"></i> Delete
    </a>

    <a href="manage_students.php" class="btn btn-secondary">
        Back
    </a>

    </div>
</div>

</div>

<!-- 🔻 FOOTER -->
<footer class="footer">
    Copyright © 2026 - Azad Lingampalli. All rights reserved.
</footer>

<!-- 🔙 BACK SCRIPT -->
<script>
function goBack(){
    if(document.referrer !== ""){
        window.history.back();
    } else {
        window.location.href = "manage_students.php";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>