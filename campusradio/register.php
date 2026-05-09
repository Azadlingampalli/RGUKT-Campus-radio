<?php
include 'config.php';

if(isset($_POST['register'])){

    $name = trim($_POST['name']);
    $college_id = trim($_POST['college_id']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ VALIDATIONS

    // Name
    if(strlen($name) < 4 || !preg_match("/^[a-zA-Z ]+$/", $name)){
        echo "<script>alert('Name must be at least 4 letters!');</script>";
    }

    // College ID (B123456)
    elseif(!preg_match("/^[bB][0-9]{6}$/", $college_id)){
        echo "<script>alert('College ID must be like B2xxxxx');</script>";
    }

    // Email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "<script>alert('Invalid email format!');</script>";
    }

    // Password
    elseif(strlen($password) < 6 || 
           !preg_match("/[A-Z]/", $password) || 
           !preg_match("/[0-9]/", $password)){
        echo "<script>alert('Password must be 6+ chars, 1 uppercase, 1 number');</script>";
    }

    else{

        // 🔐 ESCAPE INPUTS
        $name = mysqli_real_escape_string($conn,$name);
        $college_id = mysqli_real_escape_string($conn,$college_id);
        $email = mysqli_real_escape_string($conn,$email);

        // CHECK COLLEGE ID
        $check_id = "SELECT * FROM students WHERE college_id='$college_id'";
        $result_id = mysqli_query($conn,$check_id);

        if(mysqli_num_rows($result_id) > 0){
            echo "<script>alert('College ID already exists!');</script>";
        }
        else{

            // CHECK EMAIL
            $check_email = "SELECT * FROM students WHERE email='$email'";
            $result_email = mysqli_query($conn,$check_email);

            if(mysqli_num_rows($result_email) > 0){
                echo "<script>alert('Email already registered!');</script>";
            }
            else{

                // HASH PASSWORD
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // INSERT
                $sql = "INSERT INTO students(name, college_id, email, password)
                        VALUES('$name','$college_id','$email','$hashed_password')";

                if(mysqli_query($conn,$sql)){
                    echo "<script>
                          alert('Registration Successful!');
                          window.location='login.php';
                          </script>";
                }
                else{
                    echo "<script>alert('Something went wrong!');</script>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
     body{
    background: linear-gradient(-45deg, #667eea, #764ba2, #6a11cb, #2575fc);
    background-size: 400% 400%;
    animation: gradientBG 10s ease infinite;
    height:100vh;
}

@keyframes gradientBG {
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.card{
    border-radius:20px;
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    animation: fadeIn 0.8s ease-in-out;
    transition: 0.3s;
}

.card:hover{
    transform: scale(1.02);
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(-30px);}
    to{opacity:1; transform:translateY(0);}
}

input{
    background: transparent !important;
    border: none !important;
    border-bottom: 2px solid #fff !important;
    color: white !important;
    border-radius: 0 !important;
}

input::placeholder{
    color: #ddd;
}

input:focus{
    box-shadow: none !important;
    border-bottom: 2px solid #00ffcc !important;
}

label{
    color: white;
    font-size: 14px;
}

h4{
    color: white;
    font-weight: bold;
}

.btn-primary{
    background: #00c6ff;
    border: none;
    transition: 0.3s;
}

.btn-primary:hover{
    background: #0072ff;
    transform: scale(1.05);
}
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="card p-4 shadow" style="width:400px;">

<h4 class="text-center">Student Registration</h4>

<form method="POST" onsubmit="return validateForm()">

<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" required>
</div>

<div class="mb-3">
    <label>College ID</label>
    <input type="text" name="college_id" id="college_id" class="form-control" placeholder="B2xxxxx" required>
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
</div>

<div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
</div>

<button name="register" class="btn btn-primary w-100 mt-2">Register</button>

</form>

<p class="text-center mt-2">
    <a href="login.php" class="text-white">Already have an account?</a>
</p>

</div>

<script>
function validateForm(){

    let name = document.getElementById("name").value;
    let id = document.getElementById("college_id").value;
    let email = document.getElementById("email").value;
    let pass = document.getElementById("password").value;

    let idPattern = /^[bB][0-9]{6}$/;
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    let passPattern = /^(?=.*[A-Z])(?=.*[0-9]).{6,}$/;

    if(name.length < 4){
        alert("Name must be at least 4 characters");
        return false;
    }

    if(!id.match(idPattern)){
        alert("College ID must be B123456 format");
        return false;
    }

    if(!email.match(emailPattern)){
        alert("Invalid email format");
        return false;
    }

    if(!pass.match(passPattern)){
        alert("Password must be 6+ chars, 1 uppercase, 1 number");
        return false;
    }

    return true;
}
</script>

</body>
</html>