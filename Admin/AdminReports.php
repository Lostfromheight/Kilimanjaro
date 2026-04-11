<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "AD"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$total_orders   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM `order`"));
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM `order` WHERE Status='Pending'"));
$ready_orders   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM `order` WHERE Status='Ready'"));
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(Total) AS rev FROM `order`"));

$report_items = mysqli_query($conn,"
    SELECT m.Item, m.Category, COUNT(mo.OptionID) AS total_ordered, SUM(mo.Price) AS total_revenue
    FROM menu_option mo
    JOIN menu m ON mo.ItemID = m.ItemID
    GROUP BY mo.ItemID
    ORDER BY total_ordered DESC
    LIMIT 10
");

$monthly_orders = mysqli_query($conn,"
    SELECT DATE_FORMAT(Time, '%b %Y') AS month,
           DATE_FORMAT(Time, '%Y-%m') AS month_sort,
           COUNT(*) AS total
    FROM `order`
    GROUP BY month_sort
    ORDER BY month_sort ASC
    LIMIT 12
");
$months = []; $month_totals = [];
while($row = mysqli_fetch_assoc($monthly_orders)){
    $months[]       = $row['month'];
    $month_totals[] = $row['total'];
}
?>
<!doctype HTML>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Reports</title>
    <link rel="stylesheet" href="AdminInterface.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-wrapper {
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .chart-wrapper h3 {
            color: #1a1a2e;
            font-size: 16px;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>
        <a href="../Cafeteria.html" style="text-decoration:none; color:white; display:flex; align-items:center;">
            <img src="../Image/logo.png" width="150" height="50" alt="Kilimandjaro Logo" style="cursor:pointer;"/>
        </a>
        <span style="text-decoration:none; color:white; margin-left:20px; font-size:18px;">Admin Interface</span>
    </h1>
    <div style="display:flex; align-items:center; gap:20px;">
        <span style="font-size:14px; color:#ccc;">Welcome, <?php echo $_SESSION['First_name']; ?>!</span>
        <a href="../Auth/Logout.php"><button class="logout-btn">Log-out</button></a>
    </div>
</div>

<!-- Main Container -->
<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-item">
            <a href="AdminUsers.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">👥</span><span>Users</span>
            </a>
        </div>
        <div class="sidebar-item active">
            <a href="AdminReports.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">📊</span><span>Reports</span>
            </a>
        </div>
        <div class="sidebar-item">
            <a href="AdminFeedback.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">💬</span><span>Feedback</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h2>Reports</h2>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3><?php echo $total_orders['cnt']; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $pending_orders['cnt']; ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $ready_orders['cnt']; ?></h3>
                <p>Ready Orders</p>
            </div>
            <div class="summary-card">
                <h3><?php echo number_format($total_revenue['rev'] ?? 0); ?> Ksh</h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Orders Per Month Chart -->
        <div class="chart-wrapper">
            <h3>📈 Orders Per Month</h3>
            <canvas id="monthlyChart" height="120"></canvas>
        </div>

        <!-- Most Ordered Items Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Total Ordered</th>
                        <th>Revenue (Ksh)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $rank = 1; $has_data = false;
                while($r = mysqli_fetch_assoc($report_items)){
                    $has_data = true;
                ?>
                    <tr>
                        <td><strong><?php echo $rank++; ?></strong></td>
                        <td><?php echo $r['Item']; ?></td>
                        <td><?php echo $r['Category']; ?></td>
                        <td><?php echo $r['total_ordered']; ?></td>
                        <td><?php echo number_format($r['total_revenue']); ?></td>
                    </tr>
                <?php } ?>
                <?php if(!$has_data){ ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#999;">
                            No orders yet. Reports will appear once customers start ordering.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div><!-- end main-content -->
</div><!-- end container -->

<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($month_totals); ?>,
            backgroundColor: 'rgba(26,26,46,0.8)',
            borderColor: '#8b6f47',
            borderWidth: 2,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.y + ' orders'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
</body>
</HTML>
