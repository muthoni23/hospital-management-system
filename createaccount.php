<?php
session_start();

require_once 'db.php';

$message = "";

if(isset($_POST['register'])){

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $token = bin2hex(random_bytes(16));

    $role = "patient";

    $check = "SELECT * FROM users
              WHERE username=?
              OR email=?
              LIMIT 1";

    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){

        $message = "Account already exists!";

    } else {

        $sql = "INSERT INTO users
                (username,email,phone,password,role,email_verified,verification_token)
                VALUES
                (?,?,?,?,?,0,?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $phone, $password, $role, $token);

        if(mysqli_stmt_execute($stmt)){

            $user_id = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? "https" : "http";
            $verify_link = $scheme . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify_email.php?token=" . $token;
            $subject = "Verify your Tumaini account";
            $body = "Hello $username,\n\nPlease verify your Tumaini account using this link:\n$verify_link";
            @mail($email, $subject, $body);

            header("Location: patient.php?created=1");
            exit();

        } else {

            $message = 'Error creating account';

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Create Patient Account</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

        <style>

            body{
            margin:0;
            padding:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            font-family:Arial,sans-serif;
            background:black url('https://images.unsplash.com/photo-1584982751601-97dcc096659c') center/cover no-repeat;
            background-blend-mode:multiply;
        }

        .register-box{
        width:420px;
        background:white;
        padding:40px;
        border-radius:18px;
        box-shadow:0 12px 35px dimgray;
    }

    .register-box h2{
    text-align:center;
    margin-bottom:25px;
    color:midnightblue;
    font-size:30px;
}

.form-control{
height:48px;
border-radius:8px;
margin-bottom:18px;
}

.btn-register{
width:100%;
height:48px;
border:none;
border-radius:8px;
background:dodgerblue;
color:white;
font-size:18px;
transition:0.3s;
}

.btn-register:hover{
background:royalblue;
}

.login-link{
text-align:center;
margin-top:20px;
}

.login-link a{
text-decoration:none;
}

.role-box{
background:aliceblue;
border:1px solid lightskyblue;
padding:12px;
border-radius:8px;
margin-bottom:18px;
text-align:center;
font-weight:bold;
color:dodgerblue;
}

.show-password{
margin-bottom:15px;
font-size:14px;
}

.popup-error{
position:fixed;
top:20px;
right:20px;
background:crimson;
color:white;
padding:15px 25px;
border-radius:10px;
box-shadow:0 5px 15px gray;
z-index:9999;
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

</style>

</head>

<body>

    <div class="register-box">

        <h2>Create Account</h2>

        <form method="POST">

            <input type="text"
            name="username"
            class="form-control"
            placeholder="Enter Username"
            required>

            <input type="email"
            name="email"
            class="form-control"
            placeholder="Enter Email Address"
            required>

            <input type="text"
            name="phone"
            class="form-control"
            placeholder="Enter Phone Number"
            required>

            <input type="password"
            name="password"
            id="password"
            class="form-control"
            placeholder="Enter Password"
            required>

            <div class="show-password">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">Show Password</label>
            </div>

            <div class="role-box">
                Role: Patient
            </div>

            <button type="submit"
                name="register"
                class="btn-register">

                Create Account

            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login Here</a>
        </div>

    </div>

    <?php if($message != "") { ?>

        <div class="popup-error" id="popupError">
            <?php echo $message; ?>
        </div>

        <script>
            setTimeout(function(){
                document.getElementById("popupError").style.display = "none";
            },4000);
        </script>

    <?php } ?>

    <script>
        let showPassword = document.getElementById("showPassword");
        let password = document.getElementById("password");

        if(showPassword && password){
            showPassword.addEventListener("change", function(){
                password.type = showPassword.checked ? "text" : "password";
            });
        }
    </script>

</body>
</html>
