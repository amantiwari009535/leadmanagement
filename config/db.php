<?php
//action page connection

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lead_managment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
// dashboard connection
$servername1 = "localhost";
$username1= "root";
$password1= "";
$dbname1 = "lead_managment";

// Create connection
$conn1 = new mysqli($servername1, $username1, $password1, $dbname1);

// Check connection
if ($conn1->connect_error) {
    die("Connection failed: " . $conn1->connect_error);
}

?>
