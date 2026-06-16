<?php
session_start();
include('config/db.php');

$email = $_POST['username'];
$password1 = $_POST['password'];


// CHECK USER EXISTS
// $sql = "SELECT * FROM users 
//         WHERE email='$email'";

// $result = $conn->query($sql);


// // IF USER DOES NOT EXIST → INSERT
// if ($result->num_rows == 0) {

//     $insert = "INSERT INTO users(Name,email, password)
//                VALUES('$Name','$email', '$password1')";

//     if ($conn->query($insert) === TRUE) {

//         header("Location: login2.php");
//         exit();

//     } else {

//         echo "Insert Error: " . $conn->error;
//     }

// }


// IF USER EXISTS → LOGIN CHECK
//else {

    $login = "SELECT * FROM users
              WHERE email='$email'
              AND password='$password1' and status='0'";
              //echo $login;exit;

    $loginResult = $conn->query($login);

    if ($loginResult->num_rows > 0) {
        $row = $loginResult->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];

        header("Location: dashboard.php");
        exit();

    } else {

        header("Location: login.php?loginfail=1");
        exit();
    }


?>