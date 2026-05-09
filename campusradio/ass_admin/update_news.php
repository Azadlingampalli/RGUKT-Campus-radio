<?php
include '../config.php';

if($_SESSION['role']!="ass_admin"){
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$action = $_GET['action'];

$stmt = mysqli_prepare($conn,
    "UPDATE news SET status=? WHERE news_id=?"
);

mysqli_stmt_bind_param($stmt,"si",$action,$id);
mysqli_stmt_execute($stmt);

header("Location: manage_news.php");
exit();
?>      
