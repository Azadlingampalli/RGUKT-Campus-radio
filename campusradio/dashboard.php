<?php
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
}
?>

<h2>Welcome <?php echo $_SESSION['college_id']; ?></h2>

<a href="logout.php">Logout</a>
