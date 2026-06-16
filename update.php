<?php

session_start(); 

// Debugging ke liye errors on rakhein
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Agar user logged in nahi hai toh login page par bhejen
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; 
}

include('config/db.php');

// Database connection check karein
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 2. Check karein ki URL mein ID aayi hai ya nahi
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $ID = $conn->real_escape_string($_GET['id']); 
    $user_id = $_SESSION['user_id'];
} else {
    die("<div style='font-family:sans-serif; margin:50px; text-align:center;'>
            <h2 style='color:red;'>Error: ID missing in URL!</h2>
            <p>Direct page open nahi hoga. Manage Leads page se Edit par click karein.</p>
         </div>");
}

// 3. Purana data fetch karein
$query = "SELECT * FROM leads WHERE id='$ID' AND sales_person_id='$user_id'";
$data = $conn->query($query);

if (!$data) {
    die("<div style='font-family:sans-serif; margin:50px; text-align:center;'>
            <h2 style='color:red;'>Database Query Failed!</h2>
            <p>Error Details: " . $conn->error . "</p>
         </div>");
}

$result = $data->fetch_assoc();

// Agar database mein is ID ka koi data nahi mile toh
if (!$result) {
    die("<div style='font-family:sans-serif; margin:50px; text-align:center;'>
            <h2 style='color:red;'>Data Not Found!</h2>
            <p>Ya toh ye lead exist nahi karti ya aapko ise edit karne ka permission nahi hai.</p>
         </div>");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Inputs ko secure banana aur fetch karna
    $contact_name = $conn->real_escape_string($_POST['cname']);
    $phone        = $conn->real_escape_string($_POST['phone']);
    $email        = $conn->real_escape_string($_POST['email']);
    $company      = $conn->real_escape_string($_POST['company']);
    $priority     = $conn->real_escape_string($_POST['priority']);
    $status       = $conn->real_escape_string($_POST['status']); // Yahan ab '1' ya '0' aayega

    // Validation (Status ke liye strict empty check kyunki '0' valid ho sakta hai)
    if ($contact_name != "" && $phone != "" && $email != "" && $priority != "" && $status !== "") {

        // UPDATE query
        $update_query = "UPDATE leads SET
            contact_name='$contact_name',
            phone='$phone',
            email='$email',
            company='$company',
            priority='$priority',
            status='$status'
            WHERE id='$ID' AND sales_person_id='$user_id'";

        if ($conn->query($update_query)) {
            echo "<script>
                    alert('Data Updated Successfully!');
                    if (window.opener) {
                        window.opener.location.reload(); // Peeche ka main page refresh ho jayega
                    }
                    window.close(); // Yeh edit tab automatic band ho jayega
                  </script>";
            exit;
        } else {
            echo "<div class='alert alert-danger m-3'>Failed to update: " . $conn->error . "</div>";
        }

    } else {
        echo "<script>alert('Please fill all required fields')</script>";
    }
}
// echo "<script>alert('Lead Updated Successfully!'); 
// window.close(); if (window.opener) { window.opener.location.reload(); }</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lead</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update Lead Details</h4>
        </div>

        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Name *</label>
                        <input type="text" class="form-control" name="cname" value="<?php echo htmlspecialchars($result['contact_name'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($result['phone'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($result['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control" name="company" value="<?php echo htmlspecialchars($result['company'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Priority *</label>
                        <select class="form-select" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="High" <?php if (($result['priority'] ?? '') == 'High') echo 'selected'; ?>>High</option>
                            <option value="Medium" <?php if (($result['priority'] ?? '') == 'Medium') echo 'selected'; ?>>Medium</option>
                            <option value="Low" <?php if (($result['priority'] ?? '') == 'Low') echo 'selected'; ?>>Low</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="1" <?php if (($result['status'] ?? '') == '1') echo 'selected'; ?>>Active</option>
                            <option value="0" <?php if (($result['status'] ?? '') == '0') echo 'selected'; ?>>Pending</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Update Lead</button>
                    <button type="button" class="btn btn-secondary" onclick="window.close();">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>