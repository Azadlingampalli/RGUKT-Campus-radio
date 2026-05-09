<?php
$conn = new mysqli(
    "sql111.infinityfree.com",
    "if0_41801603",
    "HDwV5Dmitoj",
    "if0_41801603_campus_radio"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>