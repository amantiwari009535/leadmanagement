<?php
session_start();

// Safety Check: Agar logged in nahi hai
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; 
}

include('config/db.php');

// URL se ID check krte hain
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $ID = $conn->real_escape_string($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Query: Sirf wahi lead delete ho jo is logged-in user ki ho (Security)
    $query = "DELETE FROM leads WHERE id = '$ID' AND sales_person_id = '$user_id'";
    
    if ($conn->query($query)) {
        echo "<script>
                alert('Lead Deleted Successfully!');
                window.close(); // Naya tab automatically band ho jayega
                if (window.opener) {
                    window.opener.location.reload(); // Main manageleads.php page refresh ho jayega
                }
              </script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid Request. ID missing.";
}
echo "<script>alert('Lead Deleted Successfully!'); 
window.close(); if (window.opener) { window.opener.location.reload(); }</script>";