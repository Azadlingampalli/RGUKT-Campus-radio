<?php

session_start();
include '../config.php';

$admin_id=$_SESSION['admin_id'];

mysqli_query($conn,"
DELETE FROM admin_search_history
WHERE admin_id='$admin_id'
");

header("Location: manage_students.php");

?>
