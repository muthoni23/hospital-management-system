<?php

require_once 'db.php';

$message = "Invalid verification link.";
$type = "danger";

if(isset($_GET['token'])){
    $token = trim($_GET['token']);

    $sql = "UPDATE users
            SET email_verified = 1,
                verification_token = NULL
            WHERE verification_token = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    if(mysqli_stmt_affected_rows($stmt) > 0){
        $message = "Email verified successfully. You can now continue using your account.";
        $type = "success";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Verification</title>
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
        max-width:520px;
        background:white;
        padding:35px;
        border-radius:12px;
        box-shadow:0 10px 25px silver;
    }
</style>
</head>
<body>

    <div class="box text-center">
        <div class="alert alert-<?php echo $type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>

</body>
</html>
