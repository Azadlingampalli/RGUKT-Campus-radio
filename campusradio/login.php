<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){

    $college_id = trim($_POST['college_id']);
    $password   = $_POST['password'];
    $captcha    = trim($_POST['captcha']);

    // 🔴 1. EMPTY CHECK
    if(empty($college_id) || empty($password) || empty($captcha)){
        echo "<script>alert('All fields are required!');</script>";
        exit();
    }

    // 🔴 2. COLLEGE ID VALIDATION (B/b + 6 digits ONLY)
    if(!preg_match("/^[Bb][0-9]{6}$/", $college_id)){
        echo "<script>alert('College ID must be like B123456');</script>";
        exit();
    }

    // 🔴 3. PASSWORD VALIDATION
    if(strlen($password) < 6){
        echo "<script>alert('Password must be at least 6 characters!');</script>";
        exit();
    }

    // 🔴 4. CAPTCHA VALIDATION
    if(!isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']){
        echo "<script>alert('Wrong Captcha!'); window.location='login.php';</script>";
        exit();
    }

    // 🔐 Prevent captcha reuse
    unset($_SESSION['captcha']);

    // ✅ PREPARED STATEMENT
    $stmt = mysqli_prepare($conn, 
        "SELECT * FROM students WHERE college_id = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "s", $college_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result && mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        // 🔴 BLOCKED ADMIN CHECK
        $isAdminType = ($row['role'] == "admin" || $row['role'] == "ass_admin");

        if($isAdminType && $row['status'] == "blocked"){
            echo "<script>
                    alert('Your account is blocked. Contact Main Administrator.');
                    window.location='login.php';
                  </script>";
            exit();
        }

        // 🔐 PASSWORD VERIFY
        if(password_verify($password, $row['password'])){

            // 🔐 SESSION SECURITY
            session_regenerate_id(true);

            $_SESSION['user_id']     = $row['id'];
            $_SESSION['college_id'] = $row['college_id'];
            $_SESSION['role']       = $row['role'];

            // 🔥 ROLE REDIRECT
            if($row['role'] == "admin"){
                header("Location: admin/admin_dashboard.php");
            }
            else if($row['role'] =="ass_admin"){
                header("Location: ass_admin/admin_dashboard.php");
            }
            else {
                header("Location: student_dashboard.php");
            }
            exit();

        } else{
            echo "<script>alert('Wrong Password!');</script>";
        }

    } else{
        echo "<script>alert('College ID Not Found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
 
   
    <title>RGUKT Campus Radio</title>

    <meta name="description"
    content="RGUKT Campus Radio is a student-powered digital radio platform for campus news, live streaming, announcements, events, and student updates.">

    <meta name="keywords"
    content=" RGUKT Campus Radio,RGUKT, Campus Radio, Student News, Live Radio, College Radio,Student News Portal">

    <meta name="author" content="RGUKT Campus Radio">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    

<style>


      body {
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    overflow: hidden;
                    animation: bgMove 10s infinite alternate;
                }

                @keyframes bgMove {
                    0% { background-position: left; }
                    100% { background-position: right; }
                }

                /* Glass Card */
                .card {
                    backdrop-filter: blur(15px);
                    background: rgba(255,255,255,0.1);
                    border-radius: 20px;
                    border: 1px solid rgba(255,255,255,0.2);
                    transform-style: preserve-3d;
                    transition: transform 0.3s ease;
                }

                /* 3D Hover Effect */
                .card:hover {
                    transform: rotateY(8deg) rotateX(5deg) scale(1.02);
                }

                /* Inputs */
                .form-control {
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: #fff;
                    transition: 0.3s;
                }

                .form-control:focus {
                    background: rgba(255,255,255,0.3);
                    box-shadow: 0 0 10px #fff;
                    transform: scale(1.05);
                }

                /* Labels */
                label {
                    color: #fff;
                }

                /* Button */
                .btn-success {
                    background: linear-gradient(45deg, #00c9ff, #92fe9d);
                    border: none;
                    transition: 0.3s;
                }

                .btn-success:hover {
                    transform: scale(1.1);
                    box-shadow: 0 0 15px #00c9ff;
                }

                /* Heading */
                h3 {
                    color: #fff;
                    text-shadow: 0 0 10px #fff;
                }

                /* Links */
                a {
                    color: #fff;
                }
        /* Panda */
        #panda {
            position: fixed;
            left: -100px;
            bottom: 10px;
            font-size: 60px;
            animation: pandaMove 2s ease forwards;
        }

        /* FIXED animation */
        @keyframes pandaMove {
            0% { left: -100px; }
            80% { left: 40%; }
            100% { left: 35%; } /* final stop */
        }
</style>
</head>

<body class="bg-light">
<!-- 🐼 Panda -->
<div id="panda">🐼</div>
<div class="container mt-5">
    <div class="card mx-auto shadow p-4" style="max-width:400px;">

        <h3 class="text-center mb-3">Campus Radio Login</h3>
<!--Login to listen to live campus updates, student news, events, music, and announcements anytime, anywhere. Stay connected with your campus community in one place. -->
        <form method="POST" onsubmit="return validateForm()">

        <div class="mb-3">
            <label class="form-label">College ID</label>
            <input type="text" name="college_id" 
                   class="form-control"
                   pattern="[Bb][0-9]{6}"
                   maxlength="7"
                   title="Format: B123456"
                   placeholder="Enter ID"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" 
                   class="form-control"
                   minlength="6"
                        placeholder="Enter Password"
                   required>
        </div>

            

          

      <div class="mb-3">

    <div class="d-flex align-items-center gap-2">

        <!-- CAPTCHA TEXT -->
        <span id="captcha_text">
            <b><?php include 'captcha.php'; echo $_SESSION['captcha_question']; ?></b>
        </span>

        <!-- REFRESH BUTTON -->
        <button type="button" class="btn btn-outline-secondary btn-sm"
                onclick="refreshCaptcha()">
            🔄
        </button>
    </div>

    <input type="text" name="captcha" 
           class="form-control mt-2" 
           placeholder="Enter answer" required>

</div>

            <button type="submit" name="login" 
                    class="btn btn-success w-100">
                Login
            </button>

        </form>

        <div class="text-center mt-3">
            <a href="register.php">Register Here</a> |
            <a href="forgot_password.php">Forgot Password?</a>
        </div>

    </div>
</div>



<script>

setTimeout(() => {
    const panda = document.getElementById("panda");
    panda.style.animation = "none";   // stop animation
    panda.style.left = "35%";         // fix final position
}, 2000);


const card = document.querySelector(".card");

document.addEventListener("mousemove", (e) => {
    let x = (window.innerWidth / 2 - e.pageX) / 25;
    let y = (window.innerHeight / 2 - e.pageY) / 25;

    card.style.transform = `rotateY(${x}deg) rotateX(${y}deg)`;
});


function refreshCaptcha(){

    fetch('captcha.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById("captcha_text").innerHTML = "<b>" + data + "</b>";
    });

}

function validateForm(){

    let id = document.querySelector("[name='college_id']").value.trim();
    let pass = document.querySelector("[name='password']").value;
    let captcha = document.querySelector("[name='captcha']").value.trim();

    if(id === "" || pass === "" || captcha === ""){
        alert("All fields are required!");
        return false;
    }

    // B + 6 digits only
    if(!/^[Bb][0-9]{6}$/.test(id)){
        alert("College ID must be like B123456");
        return false;
    }

    if(pass.length < 6){
        alert("Password must be at least 6 characters!");
        return false;
    }

    return true;
}





</script>
</body>
</html>