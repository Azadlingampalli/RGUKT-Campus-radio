<?php
session_start();
include 'config.php';

$lock_time = 600; // 10 minutes (in seconds)

// Initialize
if(!isset($_SESSION['email_attempts'])){
    $_SESSION['email_attempts'] = 0;
}

if(!isset($_SESSION['last_attempt_time'])){
    $_SESSION['last_attempt_time'] = time();
}

// 🔓 Auto unlock after time passes
if(time() - $_SESSION['last_attempt_time'] > $lock_time){
    $_SESSION['email_attempts'] = 0;
}

// 🚫 Check lock
if($_SESSION['email_attempts'] >= 5){
    $remaining = $lock_time - (time() - $_SESSION['last_attempt_time']);
    $error = "Too many attempts! Try again in ".ceil($remaining/60)." minutes.";
}

if(isset($_POST['check_email']) && $_SESSION['email_attempts'] < 5){

    $email = trim($_POST['email']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format!";
    } else{

        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 1){
            $_SESSION['reset_email'] = $email;
            $_SESSION['email_attempts'] = 0;
            header("Location: reset_password.php");
            exit();
        } else{
            $_SESSION['email_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $error = "Email not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
        }

        .card {
            animation: fadeIn 0.8s ease-in-out;
            border-radius: 15px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shake {
            animation: shake 0.3s;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="card p-4 shadow" style="width:350px;">

<h4 class="text-center mb-3">Forgot Password</h4>

<?php if(isset($error)): ?>
    <div class="alert alert-danger text-center shake">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" onsubmit="return validateEmail()">

    <div class="mb-3">
        <label>Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
        <small id="emailError" class="text-danger"></small>
    </div>

    <button name="check_email" class="btn btn-primary w-100">
        Continue
    </button>

</form>

<p class="text-center mt-3">
    <a href="login.php" class="text-white">Back to Login</a>
</p>

</div>

<script>
function validateEmail(){
    let email = document.getElementById("email").value;
    let error = document.getElementById("emailError");

    let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    if(!email.match(pattern)){
        error.innerHTML = "Enter valid email!";
        return false;
    } else{
        error.innerHTML = "";
        return true;
    }
}
</script>

</body>
</html>