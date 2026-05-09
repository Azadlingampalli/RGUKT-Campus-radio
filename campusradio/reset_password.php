<?php
include 'config.php';

if(!isset($_SESSION['reset_email'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['update_password'])){

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    // ✅ PASSWORD VALIDATION
    if(strlen($new_password) < 6 || 
       !preg_match("/[A-Z]/", $new_password) || 
       !preg_match("/[0-9]/", $new_password)){
        echo "<script>alert('Password must be 6+ chars, 1 uppercase, 1 number');</script>";
    }
    elseif($new_password !== $confirm_password){
        echo "<script>alert('Passwords do not match!');</script>";
    }
    else{

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE students 
                SET password='$hashed_password' 
                WHERE email='$email'";

        if(mysqli_query($conn,$sql)){
            unset($_SESSION['reset_email']);
            echo "<script>
                    alert('Password Updated Successfully!');
                    window.location='login.php';
                  </script>";
        }
        else{
            echo "<script>alert('Something went wrong!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(-45deg,#667eea,#764ba2,#6a11cb,#2575fc);
    background-size:400% 400%;
    animation: bg 10s ease infinite;
    height:100vh;
}

@keyframes bg{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.card{
    width:380px;
    border-radius:20px;
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.15);
    border:1px solid rgba(255,255,255,0.2);
    animation: fadeIn 0.7s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(-25px);}
    to{opacity:1; transform:translateY(0);}
}

h3{
    color:white;
    font-weight:bold;
}

label{
    color:white;
    font-size:14px;
}

input{
    background:transparent !important;
    border:none !important;
    border-bottom:2px solid #fff !important;
    color:white !important;
}

input::placeholder{
    color:#ddd;
}

input:focus{
    box-shadow:none !important;
    border-bottom:2px solid #00ffcc !important;
}

.btn-success{
    background:#00c6ff;
    border:none;
    transition:0.3s;
}

.btn-success:hover{
    background:#0072ff;
    transform:scale(1.05);
}

.toggle-eye{
    position:absolute;
    right:10px;
    top:38px;
    cursor:pointer;
    color:white;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="card p-4 shadow">

<h3 class="text-center mb-3">Reset Password</h3>

<form method="POST" onsubmit="return validateForm()">

<div class="mb-3 position-relative">
    <label>New Password</label>
    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
</div>

<div class="mb-3 position-relative">
    <label>Confirm Password</label>
    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
</div>

<button name="update_password" class="btn btn-success w-100">
    Update Password
</button>

</form>

</div>

<script>
function validateForm(){

    let pass = document.getElementById("new_password").value;
    let cpass = document.getElementById("confirm_password").value;

    let passPattern = /^(?=.*[A-Z])(?=.*[0-9]).{6,}$/;

    if(!pass.match(passPattern)){
        alert("Password must be 6+ chars, 1 uppercase, 1 number");
        return false;
    }

    if(pass !== cpass){
        alert("Passwords do not match!");
        return false;
    }

    return true;
}
</script>

</body>
</html>