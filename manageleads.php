<?php
session_start();

// Agar user logged in nahi hai toh login page par aa jayega
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; 
}

include('config/db.php');
$user_id = $_SESSION['user_id'];

// Fetch Leads Data (id ko bhi fetch kiya taaki links me use ho sake)
$sql = "SELECT id, contact_name, phone, email, company, priority FROM leads 
        WHERE sales_person_id='" . $conn->real_escape_string($user_id) . "'";
$result = $conn->query($sql);
// echo "<script>alert('Lead Added Successfully!'); 
// window.close(); if (window.opener) { window.opener.location.reload(); }</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leads</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center bg-primary text-white p-4 rounded shadow-sm mb-4">
        <div>
            <h1 class="m-0 h3">Add New Lead</h1>
        </div>
        <div class="d-flex align-items-center gap-3">
            <h3 class="m-0 h5"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h3>
            <a href="logout.php" class="btn btn-light fw-bold text-primary">Logout</a>
        </div>
    </div>

    <div class="card shadow mb-5">
        <div class="card-body">
            <form action="addleadaction.php" method="post">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Name *</label>
                        <input type="text" class="form-control" placeholder="Enter Contact Name" name="cname" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" class="form-control" placeholder="Enter Phone Number" name="phone" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" placeholder="Enter Email" name="email" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control" placeholder="Enter Company Name" name="company">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority">
                            <option value="">Select Priority</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save Lead</button>
                <button type="reset" class="btn btn-secondary">Clear</button>
            </form>
        </div>
    </div>

    <div class="card p-4 shadow-sm mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h4 class="m-0">Leads Details</h4>
            <div style="max-width: 300px; width: 100%;">
                <input type="text" id="searchInput" onkeyup="searchLeads()" class="form-control" placeholder="🔍 Search name, phone or email...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="leadsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Contact Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Priority</th>
                        <th class="text-center">Action</th> </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0) { 
                        while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['contact_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <?php 
                                $priority = $row["priority"];
                                if ($priority == "High") {
                                    echo '<span class="badge rounded-pill bg-danger px-3 py-2">High</span>';
                                } elseif ($priority == "Medium") {
                                    echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2">Medium</span>';
                                } else {
                                    echo '<span class="badge rounded-pill bg-success px-3 py-2">Low</span>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="update.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    Edit
                                </a>
                                
                                <a href="delete.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-danger" onclick="return confirm('Kya aap sach me is lead ko delete karna chahte hain?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php } 
                    } else { ?>
                        <tr id="noDataRow"><td colspan="5" class="text-center text-muted">No Leads Found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function searchLeads() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("leadsTable");
    let tr = table.getElementsByTagName("tr");
    let visibleRows = 0;

    // Loop through all table rows, except the first (header) row
    for (let i = 1; i < tr.length; i++) {
        // Agar pehle se "No Leads Found" wala text hai toh use skip karein
        if(tr[i].id === "noDataRow") continue;

        let row = tr[i];
        let matchFound = false;
        
        let nameCell = row.getElementsByTagName("td")[0];
        let phoneCell = row.getElementsByTagName("td")[1];
        let emailCell = row.getElementsByTagName("td")[2];

        if (nameCell || phoneCell || emailCell) {
            let nameText = nameCell ? nameCell.textContent || nameCell.innerText : "";
            let phoneText = phoneCell ? phoneCell.textContent || phoneCell.innerText : "";
            let emailText = emailCell ? emailCell.textContent || emailCell.innerText : "";

            if (
                nameText.toLowerCase().indexOf(filter) > -1 || 
                phoneText.toLowerCase().indexOf(filter) > -1 || 
                emailText.toLowerCase().indexOf(filter) > -1
            ) {
                matchFound = true;
            }
        }

        if (matchFound) {
            row.style.display = "";
            visibleRows++;
        } else {
            row.style.display = "none";
        }
    }
}
</script>

</body>
</html>