<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login page</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            height: 100vh;
            background: #f2f2f2;

            background-image: url("assets/image/background2.jpg");
            /* Replace with your path */
            background-repeat: no-repeat;
            background-size: cover;
            /* Ensures the image fills the screen */
            background-position: center;

        }

        .login-box {
            width: 100%;
            max-width: 380px;
            border-radius: 12px;
        }

        .error {
            color: red;
            font-size: 14px;
            display: none;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <form action="actionpage.php" method="POST" onsubmit="return validateForm()">
      


        <div class="card shadow p-4 login-box">
            <img src="assets/image/download.png" alt="" style="width: 80px;">
            <h2 class="text-center mb-4">Login</h2>
            <?php
            if($_GET['loginfail'] == 1){
                
                echo '<p class="text-danger">Invalid email or password</p>';
            }

            ?>
            <p class="error mt-1" id=userError>user name cannot be empty

            </p>
            <p class="error mt-1" id="passError">
                password cannot be empty
            </p>
            <!-- <div class="mb-3">
                <input name="Name" type="text" id="Name" class="form-control" placeholder="Enter Name" />
                <div></div>
            </div> -->


            <div class="mb-3">
                <input name="username" type="text" id="username" class="form-control" placeholder="Enter email" />
                <div></div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <input name="password" type="password" id="password" class="form-control"
                    placeholder="Enter Password" />
                <div></div>
            </div>

            <!-- Button -->
            <button class="btn btn-primary w-100" type="submit">
                Login
            </button>
        </div>
    </form>

    <script>
        function validateForm() {
            let username = document.getElementById("username").value;
            let password = document.getElementById("password").value;

            let userError = document.getElementById("userError");
            let passError = document.getElementById("passError");

            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Hide previous errors
            userError.style.display = "none";
            passError.style.display = "none";

            // Validation
            if (username === "") {
                userError.style.display = "block";
                return false;

            }
            else if (!emailPattern.test(username)) {
                userError.innerText = "Enter a valid email eg.(ajs12@gmail.com)";
                userError.style.display = "block";
                return false;
            }
            else {

                if (password === "") {
                    passError.style.display = "block";
                    return false;
                }
            }

            // Success
            if (username !== "" && password !== "" && emailPattern.test(username)) {
                return true;
            }
        }
    </script>
    <!-- Test -->
</body>

</html>
