<?php
session_start();
// $servername="localhost";
// $username="root";
// $password="";
// $dbname="mits";

//create connection
// $conn=new mysqli($servername, $username, $password, $dbname);
//check connection
// if ($conn->connect_error) {
//     die("Connection failed". $conn->connect_error);
// }

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    //exit;
}
include('config/db.php');
$user_id = $_SESSION['user_id'];

// Fetch Leads Data
$sql = "SELECT id, contact_name, phone, email, company, priority FROM leads 
        WHERE sales_person_id='" . $conn->real_escape_string($user_id) . "'";
$result = $conn->query($sql);

// Fetch Summary Metric Data
$sql_summary ="SELECT COUNT(0) as total,
               IFNULL(SUM(`priority`='High'), 0) as 'high_priority',
               IFNULL(SUM(`priority`='Medium'), 0) as 'medium_priority',
               IFNULL(SUM(`priority`='Low'), 0) as 'low_priority'
               FROM `leads` 
               WHERE `sales_person_id`='" . $conn->real_escape_string($user_id) . "'";
$resultSummary = $conn->query($sql_summary);
$row2Summary = $resultSummary->fetch_assoc();

// Percentages nikalne ke liye formula (Avoid division by zero)
$total_leads = $row2Summary['total'] > 0 ? $row2Summary['total'] : 1;
$high_pct = round(($row2Summary['high_priority'] / $total_leads) * 100);
$med_pct  = round(($row2Summary['medium_priority'] / $total_leads) * 100);
$low_pct  = round(($row2Summary['low_priority'] / $total_leads) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>
<div>
  <h1 style="text-align: center;" >Hii 
    <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>, Welcome to  the Dashboard</h1>
</div>
<body class="bg-light">

  <div class="container mt-5">
   
    <div class="d-flex justify-content-between align-items-center bg-primary text-white p-4 rounded shadow-sm mb-4">
        <div>
            <h1 class="m-0 h3">Dashboard</h1>
        </div>
        <div class="d-flex align-items-center gap-3">
            <h3 class="m-0 h5"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h3>
            <a href="logout.php" class="btn btn-warning fw-bold">Logout</a>
            <a href="addleadaction.php" class="btn btn-primary fw-bold">Next-Page</a>
        </div>
        
    </div>

    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 bg-success text-white h-100 shadow-sm">
          <h5>Total Leads: <?php echo $row2Summary['total']; ?></h5>
          <p class="m-0"><?php echo ($row2Summary['total'] > 0) ? 100 : 0; ?>% Performance</p>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card p-3 bg-primary text-white h-100 shadow-sm">
          <h5>High Priority: <?php echo $row2Summary['high_priority']; ?></h5>
          <p class="m-0"><?php echo $high_pct; ?>% Performance</p>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card p-3 bg-info text-white h-100 shadow-sm">
          <h5>Medium Priority: <?php echo $row2Summary['medium_priority']; ?></h5>
          <p class="m-0"><?php echo $med_pct; ?>% Performance</p>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card p-3 bg-secondary text-white h-100 shadow-sm">
          <h5>Low Priority: <?php echo $row2Summary['low_priority']; ?></h5>
          <p class="m-0"><?php echo $low_pct; ?>% Performance</p>
        </div>
      </div>
    </div>
    
<!-- chart ko alag row me rakhne ke liye -->


    <div class="row g-4 mb-5">
      <div class="col-12 col-xl-4">
        <div class="card p-3 shadow-sm">
          <div id="chart1" style="width:100%; height:350px;"></div>
        </div>
      </div>
      <div class="col-12 col-xl-4">
         <div class="card p-3 shadow-sm">
            <div id="chart" style="width:100%; height:350px;"></div>
         </div>
      </div>
      <div class="col-12 col-xl-4">
          <div class="card p-3 shadow-sm">
            <div id="chart2" style="width:100%; height:350px;"></div>
          </div>
      </div>
    </div>

    <div class="card p-4 shadow-sm mb-5">
      <h4 class="mb-3">Leads Details</h4>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Contact Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Priority</th>
            </tr>
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
              </tr>
            <?php } 
            } else { ?>
               <tr><td colspan="4" class="text-center text-muted">No Leads Found</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    // PHP variables to JS variables for clean usage
    const highCount = <?php echo $row2Summary['high_priority']; ?>;
    const medCount = <?php echo $row2Summary['medium_priority']; ?>;
    const lowCount = <?php echo $row2Summary['low_priority']; ?>;
    
    const highPct = <?php echo $high_pct; ?>;
    const medPct = <?php echo $med_pct; ?>;
    const lowPct = <?php echo $low_pct; ?>;

    // 1. Column Chart (Shows Count)
    Highcharts.chart('chart1', {
        chart: { type: 'column' },
        title: { text: 'Leads Count by Priority', style: { fontSize: '15px' } },
        xAxis: {
            categories: ['High', 'Medium', 'Low'],
            crosshair: true
        },
        yAxis: { min: 0, title: { text: 'Number of Leads' } },
        credits: { enabled: false },
        series: [{
            name: 'Leads Count',
            colorByPoint: true,
            colors: ['#dc3545', '#ffc107', '#198754'], // Bootstrap Danger, Warning, Success colors
            data: [highCount, medCount, lowCount]
        }]
    });

    // 2. Pie Chart (Shows Percentage Share)
    Highcharts.chart('chart', {
        chart: { type: 'pie' },
        title: { text: 'Leads Share by Priority', style: { fontSize: '15px' } },
        credits: { enabled: false },
        tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                colors: ['#dc3545', '#ffc107', '#198754'],
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                    distance: -30
                }
            }
        },
        series: [{
            name: 'Priority Share',
            data: [
                { name: 'High', y: highPct },
                { name: 'Medium', y: medPct },
                { name: 'Low', y: lowPct }
            ]
        }]
    });

    // 3. Line Chart (Shows Distribution Trend)
    Highcharts.chart('chart2', {
        chart: { type: 'line' },
        title: { text: 'Priority Trend Analysis', style: { fontSize: '15px' } },
        xAxis: {
            categories: ['High', 'Medium', 'Low']
        },
        yAxis: { min: 0, title: { text: 'Volume' } },
        credits: { enabled: false },
        series: [{
            name: 'Leads Volume',
            data: [highCount, medCount, lowCount],
            color: '#0d6efd' // Bootstrap Primary Blue
        }]
    });
  </script>
</body>

</html>