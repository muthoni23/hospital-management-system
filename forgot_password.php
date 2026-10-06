<?php

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $message = "If that email exists, password reset instructions will be sent.";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:azure;
            font-family:Arial, sans-serif;
        }
        .box{
        width:400px;
        background:white;
        padding:35px;
        border-radius:12px;
        box-shadow:0 10px 25px silver;
    }
    .btn-main{
    background:darkcyan;
    color:white;
}
</style>
</head>
<body>

    <div class="box">
        <h3 class="mb-3 text-center">Forgot Password</h3>

        <?php if($message != "") { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <form method="POST">
            <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
            <button class="btn btn-main w-100">Send Reset Link</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

</body>
</html>
