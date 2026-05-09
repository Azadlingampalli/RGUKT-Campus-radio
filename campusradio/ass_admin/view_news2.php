<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!="ass_admin"){
header("Location: ../login.php");
exit();
}

if(!isset($_GET['id'])){
echo "News not found";
exit();
}

$news_id=$_GET['id'];

$news=mysqli_query($conn,
"SELECT * FROM news WHERE news_id='$news_id'");

$data=mysqli_fetch_assoc($news);

$media=mysqli_query($conn,
"SELECT * FROM news_media WHERE news_id='$news_id'");
?>

<!DOCTYPE html>
<html>
<head>

<title><?php echo $data['title']; ?></title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>

body{
background:#eef1f5;
font-family:Arial, Helvetica, sans-serif;
}

/* Main Card */

.news-card{
border:none;
border-radius:12px;
}

/* Title */

.news-title{
font-weight:700;
margin-bottom:5px;
}

/* Date */

.news-date{
color:#777;
font-size:14px;
}

/* Description */

.news-desc{
font-size:16px;
line-height:1.7;
color:#444;
}

/* Media section */

.media-box{
background:#fafafa;
padding:15px;
border-radius:10px;
text-align:center;
}

/* Image */

.news-img{
max-width:100%;
max-height:450px;
width:auto;
height:auto;
object-fit:contain;
border-radius:8px;
}

/* Video */

video{
width:100%;
max-height:400px;
border-radius:8px;
}

/* Audio */

audio{
width:100%;
margin-top:10px;
}

/* Section title */

.section-title{
font-weight:600;
margin-bottom:15px;
}

</style>

</head>

<body>

<div class="container mt-5">

<div class="card news-card shadow p-4">

<!-- Title -->

<h2 class="news-title">
<?php echo htmlspecialchars($data['title']); ?>
</h2>

<p class="news-date">

<?php echo date("d M Y",strtotime($data['created_at'])); ?>

</p>

<hr>

<!-- Description -->

<p class="news-desc">

<?php echo nl2br(htmlspecialchars($data['description'])); ?>

</p>

<hr>

<!-- Media Section -->

<h4 class="section-title">Media</h4>

<div class="media-box">

<?php
if(mysqli_num_rows($media)>0){

while($m=mysqli_fetch_assoc($media)){

$path="../".$m['file_path'];

if($m['media_type']=="image"){
?>

<img src="<?php echo $path; ?>" class="news-img mt-3">

<?php
}

elseif($m['media_type']=="audio"){
?>

<audio controls class="mt-3">
<source src="<?php echo $path; ?>">
</audio>

<?php
}

elseif($m['media_type']=="video"){
?>

<video controls class="mt-3">
<source src="<?php echo $path; ?>">
</video>

<?php
}

}

}else{

echo "<p class='text-muted'>No media uploaded</p>";

}
?>

</div>

<!-- Back Button -->

<div class="mt-4">

<a href="news_history.php" class="btn btn-secondary">

⬅ Back

</a>

</div>

</div>

</div>

</body>
</html>