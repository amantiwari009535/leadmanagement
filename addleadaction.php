<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: manageleads.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_SESSION['user_id'];
    
    $Contact_Name = $conn->real_escape_string($_POST['cname']);
    $Phone        = $conn->real_escape_string($_POST['phone']);
    $Email        = $conn->real_escape_string($_POST['email']);
    $Company      = isset($_POST['company']) ? $conn->real_escape_string($_POST['company']) : '';
    $Priority     = isset($_POST['priority']) ? $conn->real_escape_string($_POST['priority']) : '';
    
    // Status text ko Number mein convert kar rahe hain kyunki aapka DB integer maang raha hai
    $status_input = isset($_POST['status']) ? $_POST['status'] : '';
    if ($status_input == "Active") {
        $Status = 1;
    } elseif ($status_input == "Pending") {
        $Status = 2;
    } elseif ($status_input == "Closed") {
        $Status = 3;
    } else {
        $Status = 0; // Default ya select nahi kiya toh
    }

    $sql = "INSERT INTO `leads` (`sales_person_id`, `contact_name`, `phone`, `email`, `company`, `priority`, `status`) 
            VALUES ('$user_id', '$Contact_Name', '$Phone', '$Email', '$Company', '$Priority', '$Status')";
            
    if ($conn->query($sql)) {
        header("Location: manageleads.php?success=1");
        exit;
    } else {
        die("Database Query Error: " . $conn->error);
    }
} else {
    header("Location: manageleads.php");
    exit;
}
?>