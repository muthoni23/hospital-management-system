<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

$error = "";
$saved_username = "";

if(isset($_COOKIE['remembered_username'])){
    $saved_username = $_COOKIE['remembered_username'];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    $sql = "SELECT * FROM users
            WHERE username=?
            AND password=?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result && mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if($remember){
            setcookie("remembered_username", $username, time() + (86400 * 30), "/");
        } else {
            setcookie("remembered_username", "", time() - 3600, "/");
        }

        if($user['role'] == "admin" || $user['role'] == "doctor" || $user['role'] == "employee"){
            header("Location: admin_dashboard.php");
            exit();
        }

        header("Location: patient.php");
        exit();

    } else {

        $error = "Invalid details! No account found.";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Tumaini Login</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>

            *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
        height:100vh;
        display:flex;
        justify-content:center;
        align-items:center;
        font-family:Arial, sans-serif;
        background:black url('https://images.unsplash.com/photo-1584982751601-97dcc096659c') center/cover no-repeat;
        background-blend-mode:multiply;
    }

    .login-box{
    width:400px;
    background:white;
    padding:40px;
    border-radius:18px;
    box-shadow:0 12px 35px dimgray;
    animation:fadeIn 0.7s ease;
}

.login-box h2{
text-align:center;
margin-bottom:10px;
font-size:30px;
color:midnightblue;
}

.subtitle{
text-align:center;
margin-bottom:25px;
color:gray;
font-size:15px;
}

.form-control{
height:48px;
border-radius:8px;
margin-bottom:18px;
font-size:16px;
}

.btn-login{
width:100%;
height:48px;
border:none;
border-radius:8px;
background:dodgerblue;
color:white;
font-size:18px;
transition:0.3s;
}

.btn-login:hover{
background:royalblue;
}

.extra-links{
margin-top:18px;
text-align:center;
}

.extra-links a{
text-decoration:none;
color:dodgerblue;
font-size:15px;
}

.extra-links a:hover{
text-decoration:underline;
}

.show-password{
margin-bottom:15px;
font-size:14px;
}

.popup-banner{
position:fixed;
top:20px;
right:20px;
background:crimson;
color:white;
padding:15px 25px;
border-radius:10px;
box-shadow:0 5px 15px gray;
z-index:9999;
animation:slideIn 0.5s ease;
}

@keyframes slideIn{
from{
opacity:0;
transform:translateX(100px);
}
to{
opacity:1;
transform:translateX(0);
}
}

@keyframes fadeIn{
from{
opacity:0;
transform:translateY(30px);
}
to{
opacity:1;
transform:translateY(0);
}
}

</style>

</head>

<body>

    <div class="login-box">

        <h2>Tumaini Login</h2>

        <div class="subtitle">
            Welcome Back
        </div>

        <form method="POST">

            <input type="text"
            name="username"
            class="form-control"
            placeholder="Username"
            value="<?php echo htmlspecialchars($saved_username); ?>"
            required>

            <input type="password"
            name="password"
            id="password"
            class="form-control"
            placeholder="Password"
            required>

            <div class="show-password">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">Show Password</label>
            </div>

            <div class="show-password">
                <input type="checkbox" id="remember" name="remember" <?php if($saved_username != ""){ echo "checked"; } ?>>
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-login">
                Login
            </button>

        </form>

        <div class="extra-links">

            <p>
                <a href="createaccount.php">
                    Create Account
                </a>
            </p>

            <p>
                <a href="forgot_password.php">
                    Forgot Password?
                </a>
            </p>

        </div>

    </div>

    <?php if($error != "") { ?>

        <div class="popup-banner" id="popupBanner">
            <?php echo htmlspecialchars($error); ?>
        </div>

        <script>
            setTimeout(function(){
                let createAccount = confirm(
                    "No account found with those details.\n\nWould you like to create a patient account?"
                );

                if(createAccount){
                    window.location.href = "createaccount.php";
                }
            },1000);

            setTimeout(function(){
                let popup = document.getElementById("popupBanner");

                if(popup){
                    popup.style.display = "none";
                }
            },5000);
        </script>

    <?php } ?>

    <script>
        let showPassword = document.getElementById("showPassword");
        let password = document.getElementById("password");

        if(showPassword && password){
            showPassword.addEventListener("change", function(){
                if(showPassword.checked){
                    password.type = "text";
                } else {
                    password.type = "password";
                }
            });
        }
    </script>

</body>
</html>
