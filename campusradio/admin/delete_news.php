<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
header("Location: ../login.php");
exit();
}

$id = intval($_GET['id']);

/* DELETE MEDIA FIRST */

mysqli_query($conn,"DELETE FROM news_media WHERE news_id=$id");

/* DELETE NEWS */

$stmt = mysqli_prepare($conn,"DELETE FROM news WHERE news_id=?");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);

header("Location: manage_news.php");
exit();
?>
