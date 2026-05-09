<?php
session_start();
include '../config.php';

if($_SERVER['REQUEST_METHOD']=="POST"){

$title = $_POST['title'];
$description = $_POST['description'];
$category = $_POST['category'];
$media_type = $_POST['media_type'];

$_SESSION['preview']['title'] = $title;
$_SESSION['preview']['description'] = $description;
$_SESSION['preview']['category'] = $category;
$_SESSION['preview']['media_type'] = $media_type;

/* MEDIA UPLOAD */

if(isset($_FILES['media']) && $_FILES['media']['error']==0){

$fileName = time()."_".$_FILES['media']['name'];
$tmp = $_FILES['media']['tmp_name'];

if($media_type=="image"){
$folder="images";
}
elseif($media_type=="audio"){
$folder="audio";
}
else{
$folder="videos";
}

$uploadPath="../uploads/".$folder."/".$fileName;

move_uploaded_file($tmp,$uploadPath);

$_SESSION['preview']['file']=$fileName;
$_SESSION['preview']['folder']=$folder;

}

}

$data = $_SESSION['preview'];
?>

<!DOCTYPE html>
<html>
<head>

<title>Preview News</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow p-4 mx-auto" style="max-width:700px">

<h3 class="text-center mb-3">News Preview</h3>

<h4><?php echo htmlspecialchars($data['title']); ?></h4>

<p><?php echo nl2br(htmlspecialchars($data['description'])); ?></p>

<span class="badge bg-primary">
<?php echo $data['category']; ?>
</span>

<hr>

<?php

if(isset($data['file'])){

$path="../uploads/".$data['folder']."/".$data['file'];

if($data['media_type']=="image"){
echo "<img src='$path' class='img-fluid'>";
}

elseif($data['media_type']=="audio"){
echo "<audio controls src='$path'></audio>";
}

else{
echo "<video controls width='100%' src='$path'></video>";
}

}

?>

<hr>

<div class="d-flex justify-content-between">

<a href="post_news.php" class="btn btn-secondary">
Edit
</a>

<form action="publish_news.php" method="POST">

<button class="btn btn-success">
Publish
</button>

</form>

</div>

</div>

</div>

</body>
</html>

