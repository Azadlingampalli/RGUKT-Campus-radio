<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include '../config.php';

$id = $_GET['id'];

/* GET NEWS + USERNAME */

$sql = "SELECT news.*, students.college_id 
        FROM news
        LEFT JOIN students 
        ON news.posted_by = students.id
        WHERE news.news_id='$id'";

$result = mysqli_query($conn,$sql);

if(!$result){
die("Query Error: ".mysqli_error($conn));
}

$news = mysqli_fetch_assoc($result);


/* GET MEDIA */

$media_sql = "SELECT * FROM news_media WHERE news_id='$id'";
$media_result = mysqli_query($conn,$media_sql);
?>

<!DOCTYPE html>
<html>

<head>

<title><?php echo htmlspecialchars($news['title']); ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.news-img{
width:100%;
max-height:350px;
object-fit:contain;
background:#f8f9fa;
border-radius:8px;
}

.video-box{
width:100%;
max-height:350px;
margin-top:15px;
}

.audio-box{
width:100%;
margin-top:15px;
}

.meta{
font-size:14px;
color:#555;
}

</style>

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-body">

<h2><?php echo htmlspecialchars($news['title']); ?></h2>

<div class="meta">

<b>Category:</b> <?php echo $news['category']; ?> |

<b>Posted By:</b> <?php echo $news['college_id']; ?> |

<b>Status:</b> <?php echo $news['status']; ?> |

<b>Published:</b> <?php echo date("d M Y, h:i A", strtotime($news['created_at'])); ?>

</div>

<hr>

<div class="row">

<div class="col-md-7">

<p style="font-size:18px; line-height:1.7;">
<?php echo nl2br(htmlspecialchars($news['description'])); ?>
</p>

</div>

<div class="col-md-5">

<?php
mysqli_data_seek($media_result,0);

while($media=mysqli_fetch_assoc($media_result)){

if($media['media_type']=="image"){

echo "<img src='".$media['file_path']."' class='news-img mb-3'>";

}

}
?>

</div>

</div>

<div class="mt-4">

<?php
mysqli_data_seek($media_result,0);

while($media=mysqli_fetch_assoc($media_result)){

if($media['media_type']=="video"){

echo "<video controls class='video-box'>
<source src='".$media['file_path']."'>
</video>";

}

elseif($media['media_type']=="audio"){

echo "<audio controls class='audio-box'>
<source src='".$media['file_path']."'>
</audio>";

}

}
?>

</div>

</div>

</div>

</div>

</body>

</html>
