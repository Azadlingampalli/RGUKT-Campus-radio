<?php
session_start();
include '../config.php';

if(!isset($_SESSION['preview'])){
header("Location: post_news.php");
exit();
}

$data = $_SESSION['preview'];

$title = $data['title'];
$description = $data['description'];
$category = $data['category'];
$media_type = $data['media_type'];
$user_id = $_SESSION['user_id'];

/* INSERT NEWS */

$stmt = mysqli_prepare($conn,
"INSERT INTO news
(title,description,category,posted_by,status,created_at)
VALUES (?,?,?,?, 'pending',NOW())"
);

mysqli_stmt_bind_param($stmt,"sssi",
$title,$description,$category,$user_id
);

mysqli_stmt_execute($stmt);

$news_id = mysqli_insert_id($conn);


/* MEDIA UPLOAD */

if(isset($data['file'])){

$fileName = $data['file'];

if($media_type=="image"){
$folder = "images";
}
elseif($media_type=="audio"){
$folder = "audio";
}
else{
$folder = "videos";
}

/* SOURCE TEMP PATH */

$tmp = "../uploads/temp/".$fileName;

/* FINAL PATH */

$uploadPath = "../uploads/".$folder."/".$fileName;

/* MOVE FILE */

rename($tmp,$uploadPath);

/* DATABASE PATH */

$dbPath = "uploads/".$folder."/".$fileName;

/* INSERT MEDIA */

$stmt2 = mysqli_prepare($conn,
"INSERT INTO news_media(news_id,media_type,file_path)
VALUES(?,?,?)"
);

mysqli_stmt_bind_param($stmt2,"iss",
$news_id,$media_type,$dbPath
);

mysqli_stmt_execute($stmt2);

}

/* CLEAR SESSION */

unset($_SESSION['preview']);

echo "<script>
alert('News Submitted for Approval!');
window.location='../student_dashboard.php';
</script>";

?>