<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$conn = mysqli_connect(
    "sql111.infinityfree.com",     // Host
    "if0_41801603",               // Username
    "HDwV5Dmitoj",                // Password
    "if0_41801603_campus_radio"   // Database name
);

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}
?>