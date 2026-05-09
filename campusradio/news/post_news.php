<?php
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student"){
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['submit'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    $stmt = mysqli_prepare($conn,
        "INSERT INTO news(title, description, category, posted_by)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt,"sssi",
        $title, $description, $category, $user_id
    );

    mysqli_stmt_execute($stmt);

    echo "<script>alert('News Submitted for Approval!');</script>";
}
?>

<h2>Post Campus News</h2>

<form method="POST">
Title:<br>
<input type="text" name="title" required><br><br>

Description:<br>
<textarea name="description" required></textarea><br><br>

Category:<br>
<select name="category">
<option>Sports</option>
<option>Campus Life</option>
<option>Culturals</option>
<option>Events</option>
</select>

<br><br>
<button name="submit">Submit</button>
</form>
