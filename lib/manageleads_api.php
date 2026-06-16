<?php
// 1. Error Reporting On (Taki agar fir bhi koi dikkat ho to error samne dikhe)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Correct Path Configuration

if (file_exists('api_helper.php')) {
    include 'api_helper.php';
} else if (file_exists('lib/api_helper.php')) {
    include 'lib/api_helper.php';
} else {
    die("Error: api_helper.php file nahi mili! Apne folder structure ko check karein.");
}

session_start();

// Check user authentication (Path fix: login page parent folder me hai)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login1.php");
    exit;
}

// Sales person ID safely handle krne ke liye 
$sales_person_id = $_SESSION['sales_person_id'] ?? '';

// ---------------------- ADD or UPDATE ----------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $payload = [
        "action" => empty($_POST['id']) ? "add" : "update",
        "sales_person_id" => $sales_person_id,
        "contact_name" => $_POST['contact_name'] ?? '',
        "phone" => $_POST['phone'] ?? '',
        "email" => $_POST['email'] ?? '',
        "company" => $_POST['company'] ?? '',
        "priority" => $_POST['priority'] ?? '',
        "status" => $_POST['status'] ?? ''
    ];

    if (!empty($_POST['id'])) {
        $payload['id'] = $_POST['id'];
    }

    callawsAPI($payload, 'mitsleadcrud');

    header("Location: manageleads_api.php");
    exit;
}

// ---------------------- DELETE ----------------------
if (isset($_GET['delete'])) {

    callawsAPI([
        "action" => "delete",
        "id" => $_GET['delete']
    ], 'mitsleadcrud');

    header("Location: manageleads_api.php");
    exit;
}

// ---------------------- EDIT FETCH ----------------------
$rowData = [];
if (isset($_GET['edit'])) {

    $response = callawsAPI([
        "action" => "getOne",
        "id" => $_GET['edit']
    ], 'mitsleadcrud');

    // Agar API array inside array de rahi hai (like $response[0])
    if (is_array($response)) {
        $rowData = isset($response[0]) ? $response[0] : $response;
    }
}

// ---------------------- FETCH LEADS ----------------------
$leadList = callawsAPI([
    "action" => "list",
    "sales_person_id" => $sales_person_id
], 'mitsleadcrud');

if (!is_array($leadList)) {
    $leadList = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Management CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f5f6fa; }
        .card { border:none; box-shadow:0px 3px 10px rgba(0,0,0,.1); }
        .sidebar { background:#212529; min-height:100vh; }
        .sidebar .logo { background:#0d6efd; color:#fff; text-align:center; padding:20px; font-size:24px; font-weight:bold; }
        .sidebar .nav-link { color:#fff; padding:12px 20px; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar .nav-link:hover { background:#0d6efd; color:#fff; }
        .main-content { padding:20px; }
    </style>
</head>
<body>

<div class="container-fluid">
<div class="row">

    <div class="col-md-2 sidebar p-0">
        <div class="logo">Lead Management</div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../dashboard.php" class="nav-link">🏠 Dashboard</a></li>
            <li class="nav-item"><a href="manageleads_api.php" class="nav-link active bg-primary">📋 Manage Leads</a></li>
            <li class="nav-item"><a href="../logout1.php" class="nav-link text-danger">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="col-md-10 main-content">

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?php echo !empty($rowData) ? "Edit Lead" : "Add New Lead"; ?></h4>
        </div>

        <div class="card-body">
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($rowData['id'] ?? ''); ?>">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Name *</label>
                        <input type="text" name="contact_name" class="form-control" required value="<?php echo htmlspecialchars($rowData['contact_name'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" required value="<?php echo htmlspecialchars($rowData['phone'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($rowData['email'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($rowData['company'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="High" <?php if(($rowData['priority'] ?? '') == 'High') echo 'selected'; ?>>High</option>
                            <option value="Medium" <?php if(($rowData['priority'] ?? '') == 'Medium') echo 'selected'; ?>>Medium</option>
                            <option value="Low" <?php if(($rowData['priority'] ?? '') == 'Low') echo 'selected'; ?>>Low</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="0" <?php if(($rowData['status'] ?? '') == '0') echo 'selected'; ?>>Active</option>
                            <option value="1" <?php if(($rowData['status'] ?? '') == '1') echo 'selected'; ?>>In-Active</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="save" class="btn btn-success px-4">Save Lead</button>
                <a href="manageleads_api.php" class="btn btn-secondary px-4">Clear</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">Lead List</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Sales Person</th>
                            <th>Contact Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                        if (empty($leadList)) {
                            echo "<tr><td colspan='9' class='text-center py-3 text-muted'>Koi data nahi mila ya API fail ho gayi.</td></tr>";
                        } else {
                            $i = 1;
                            foreach($leadList as $row){
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['sales_person'] ?? $row['sales_person_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['company'] ?? ''); ?></td>
                            <td>
                                <span class="badge <?php 
                                    echo (($row['priority'] ?? '') == 'High') ? 'bg-danger' : ((($row['priority'] ?? '') == 'Medium') ? 'bg-warning text-dark' : 'bg-secondary'); 
                                ?>">
                                    <?php echo htmlspecialchars($row['priority'] ?? 'Low'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (isset($row['status']) && $row['status'] == 0): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">In-Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?php echo urlencode($row['id'] ?? ''); ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                <a href="?delete=<?php echo urlencode($row['id'] ?? ''); ?>" onclick="return confirm('Kya aap sach me is lead ko delete karna chahte hain?')" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>