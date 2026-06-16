<?php
session_start();

// Saare session variables ko khali karein
$_SESSION = array();

// Session ko poori tarah destroy krne ke liye,
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();


header("Location: login1.php");
exit;