<?php
session_start();

// Demo username & password
$valid_username = "aman";
$valid_password = "123456";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username == $valid_username && $password == $valid_password) {

        $_SESSION['user_id'] = 1;
        $_SESSION['sales_person_id'] = 101;
        $_SESSION['username'] = $username;

        header("Location: manageleads_api.php");
        exit;

    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f6fa;
}

.login-box{
    width:400px;
    margin:auto;
    margin-top:100px;
}

.card{
    border:none;
    box-shadow:0px 4px 15px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<div class="login-box">

    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3>Lead Management Login</h3>
        </div>

        <div class="card-body">

            <?php if(!empty($error)){ ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100">
                    Login
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>