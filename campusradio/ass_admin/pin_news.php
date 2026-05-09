<?php
session_start();
include '../config.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!="ass_admin"){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['action'])){

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == "pin" && isset($_GET['duration'])){

        $duration = $_GET['duration'];

        switch($duration){
            case "6h": $interval="INTERVAL 6 HOUR"; break;
            case "12h": $interval="INTERVAL 12 HOUR"; break;
            case "24h": $interval="INTERVAL 24 HOUR"; break;
            case "2d": $interval="INTERVAL 2 DAY"; break;
            case "3d": $interval="INTERVAL 3 DAY"; break;
            case "7d": $interval="INTERVAL 7 DAY"; break;
            default: $interval="INTERVAL 6 HOUR";
        }

        mysqli_query($conn, "
        UPDATE news 
        SET pinned=1,
            pinned_by='".$_SESSION['college_id']."',
            pinned_until = DATE_ADD(NOW(), $interval)
        WHERE news_id=$id
        ");

    } 
    elseif($action == "unpin"){

        mysqli_query($conn, "
        UPDATE news 
        SET pinned=0,
            pinned_by=NULL,
            pinned_until=NULL
        WHERE news_id=$id
        ");
    }
}

header("Location: manage_news.php");
exit();
?>