<?php
session_start();
include("config/db.php");

// Insert Data
if(isset($_POST['teams']))
{
    $teams = mysqli_real_escape_string($conn,$_POST['teams']);
    $amount = mysqli_real_escape_string($conn,$_POST['amount']);
    $sale_date = mysqli_real_escape_string($conn,$_POST['sale_date']);

    $query = "INSERT INTO sales(teams,amount,sale_date)
              VALUES('$teams','$amount','$sale_date')";

    mysqli_query($conn,$query);

    echo "<script>
            alert('Sale Added Successfully');
            window.location='sales_manage.php';
          </script>";
}

// Fetch Data
$query = "SELECT * FROM sales ORDER BY id DESC";
$data = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sales Management</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
    margin:0;
    padding:0;
}

.sidebar{
    width:220px;
    min-height:100vh;
    background:#0d6efd;
}

.sidebar h2{
    padding:30px 15px;
    text-align:center;
    color:white;
    font-weight:bold;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px 20px;
    border-bottom:1px solid rgba(255,255,255,0.2);
}

.sidebar a:hover{
    background:#212529;
}

.active-menu{
    background:#212529;
}

.content{
    flex:1;
    padding:25px;
}

.card{
    border:none;
    border-radius:10px;
}

.card-header{
    font-weight:bold;
}

.table td,
.table th{
    vertical-align:middle;
}

</style>
</head>

<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar">

        <h2>Sales Management</h2>

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="sales_manage.php" class="active-menu">
            📋 Manage Sales
        </a>

        <a href="logout.php" style="color:#ff4d4d;">
            🚪 Logout
        </a>

    </div>

    <!-- Main Content -->
    <div class="content">

        <!-- Add Sale Card -->
        <div class="card shadow mb-4">

            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Add New Sale</h3>
            </div>

            <div class="card-body">

                <form action="" method="POST">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Team *
                            </label>

                            <select name="teams"
                                class="form-control"
                                required>

                                <option value="">
                                    Select Team
                                </option>

                                <option value="Closing Crew">
                                    Closing Crew
                                </option>

                                <option value="Peak Performer">
                                    Peak Performer
                                </option>

                                <option value="Revenue Rocket">
                                    Revenue Rocket
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Amount *
                            </label>

                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Sale Date *
                            </label>

                            <input type="date"
                                   name="sale_date"
                                   class="form-control"
                                   required>

                        </div>

                    </div>

                    <button type="submit"
                            class="btn btn-success">
                        Save Sale
                    </button>

                    <button type="reset"
                            class="btn btn-secondary">
                        Clear
                    </button>

                </form>

            </div>

        </div>

        <!-- Sales List Card -->
        <div class="card shadow">

            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Sales List</h3>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>
                                <th>ID</th>
                                <th>Team</th>
                                <th>Amount</th>
                                <th>Sale Date</th>
                                <th width="150">
                                    Action
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php
                        if(mysqli_num_rows($data)>0)
                        {
                            while($row=mysqli_fetch_assoc($data))
                            {
                        ?>

                            <tr>

                                <td>
                                    <?php echo $row['id']; ?>
                                </td>

                                <td>
                                    <?php echo $row['teams']; ?>
                                </td>

                                <td>
                                    ₹<?php echo $row['amount']; ?>
                                </td>

                                <td>
                                    <?php echo $row['sale_date']; ?>
                                </td>

                                <td>

                                    <a href="edit.php?id=<?php echo $row['id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this sale?')">
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php
                            }
                        }
                        else
                        {
                        ?>

                            <tr>
                                <td colspan="5" class="text-center">
                                    No Sales Found
                                </td>
                            </tr>

                        <?php
                        }
                        ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>