<?php
session_start();
include "KitchenProcess.php";
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Kitchen Dashboard</title>
    <link rel="stylesheet" href="Kitchen.css?v=20260411">
</head>
<body>

<div class="header">
    <div class="header-left">
        <img src="../Image/logo.png" alt="Kilimandjaro Logo">
        <div class="header-title">
            <h1>Kilimandjaro</h1>
            <p>KITCHEN DASHBOARD</p>
        </div>
    </div>
    <div class="tab-nav">
        <button class="tab-btn active">📦 Incoming Orders</button>
        <button class="tab-btn" onclick="window.location='KitchenMenu.php'">📋 Menu Management</button>
        <button class="tab-btn" onclick="window.location='KitchenFeedback.php'">💬 Feedback</button>
    </div>
    <div class="header-right">
        <span class="chef-name">Chef: <?php echo $_SESSION['First_name']; ?></span>
        <a href="../Auth/Logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</div>

<div class="main">

    <div class="stat-cards">
        <div class="stat-card clickable" onclick="toggleTodayOrders()">
            <h2><?php echo $total_today['cnt']; ?></h2>
            <p>Total Orders Today</p>
        </div>
        <div class="stat-card pending">
            <h2><?php echo $pending['cnt']; ?></h2>
            <p>Pending</p>
        </div>
        <div class="stat-card preparing">
            <h2><?php echo $preparing['cnt']; ?></h2>
            <p>Preparing</p>
        </div>
        <div class="stat-card ready">
            <h2><?php echo $ready['cnt']; ?></h2>
            <p>Ready for Pickup</p>
        </div>
    </div>

    <h2 class="section-title">Incoming Orders</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th><th>Customer</th><th>Items</th>
                    <th>Total</th><th>Status</th><th>Time</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $has_orders = false;
            while($o = mysqli_fetch_assoc($orders)){
                $has_orders = true;
                $status = $o['Status'];
                $order_items = mysqli_query($conn, "
                    SELECT m.Item, mo.Quantity, COUNT(mo.OptionID) AS cnt
                    FROM menu_option mo
                    JOIN menu m ON mo.ItemID = m.ItemID
                    WHERE mo.OrderID = " . intval($o['OrderID']) . "
                    GROUP BY m.Item, mo.Quantity
                ");
                $parts = [];
                if($order_items && mysqli_num_rows($order_items) > 0){
                    while($oi = mysqli_fetch_assoc($order_items)){
                        $parts[] = $oi['Item'] . ' (' . $oi['Quantity'] . ') x' . $oi['cnt'];
                    }
                }
            ?>
            <tr>
                <td>#<?php echo str_pad($o['OrderID'], 3, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo $o['First_name'] . ' ' . $o['Last_name']; ?></td>
                <td class="items-list"><?php echo !empty($parts) ? implode(', ', $parts) : '—'; ?></td>
                <td><?php echo $o['Total']; ?> Ksh</td>
                <td>
                    <?php
                    if($status == 'Paid')
                        echo '<span class="badge badge-paid">Paid</span>';
                    elseif($status == 'Preparing')
                        echo '<span class="badge badge-progress">Preparing</span>';
                    elseif($status == 'Ready')
                        echo '<span class="badge badge-ready">Ready</span>';
                    else
                        echo '<span class="badge badge-pending">Paid</span>';
                    ?>
                </td>
                <td><?php echo date('h:i A', strtotime($o['Time'])); ?></td>
                <td style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                    <?php if($status == 'Paid'){ ?>
                        <form action="Kitchen.php" method="POST" style="display:inline;">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="OrderID" value="<?php echo $o['OrderID']; ?>">
                            <input type="hidden" name="new_status" value="Preparing">
                            <button type="submit" class="btn-preparing">Preparing</button>
                        </form>
                        <form action="Kitchen.php" method="POST" style="display:inline;">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="OrderID" value="<?php echo $o['OrderID']; ?>">
                            <input type="hidden" name="new_status" value="Ready">
                            <button type="submit" class="btn-ready">Mark Ready</button>
                        </form>
                    <?php } elseif($status == 'Preparing'){ ?>
                        <form action="Kitchen.php" method="POST" style="display:inline;">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="OrderID" value="<?php echo $o['OrderID']; ?>">
                            <input type="hidden" name="new_status" value="Ready">
                            <button type="submit" class="btn-ready">Mark Ready</button>
                        </form>
                    <?php } elseif($status == 'Ready'){ ?>
                        <form action="Kitchen.php" method="POST" style="display:inline;">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="OrderID" value="<?php echo $o['OrderID']; ?>">
                            <input type="hidden" name="new_status" value="Picked Up">
                            <button type="submit" class="btn-pickedup">Picked Up</button>
                        </form>
                    <?php } ?>
                    <form action="Kitchen.php" method="POST" style="display:inline;"
                          onsubmit="return confirm('Cancel this order?')">
                        <input type="hidden" name="delete_order" value="1">
                        <input type="hidden" name="OrderID" value="<?php echo $o['OrderID']; ?>">
                        <button type="submit" class="btn-delete">Cancel</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            <?php if(!$has_orders){ ?>
            <tr>
                <td colspan="7" class="empty-state">
                    No orders assigned yet. Orders will appear here once customers start ordering.
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- All Orders Today collapsible section -->
    <div id="today-orders-section" class="today-orders-section">
        <div class="today-orders-header">
            <h2 class="section-title">All Orders Today</h2>
            <button class="today-orders-close" onclick="toggleTodayOrders()">X</button>
        </div>
        <div class="table-container">
            <div id="todayOrdersContent"><p class="empty-state">Loading...</p></div>
        </div>
    </div>

</div>

<script>
var isModalVisible = false;

function toggleTodayOrders(){
    console.log('Card clicked');
    var section = document.getElementById('today-orders-section');
    if(isModalVisible){
        section.classList.remove('open');
        isModalVisible = false;
    } else {
        section.classList.add('open');
        isModalVisible = true;
        fetchTodayOrders();
    }
}

function fetchTodayOrders(){
    document.getElementById('todayOrdersContent').innerHTML = '<p class="empty-state">Loading...</p>';
    fetch('Kitchen.php?fetch_today_orders=1')
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(data.length === 0){
                document.getElementById('todayOrdersContent').innerHTML = '<p class="empty-state">No orders recorded today.</p>';
                return;
            }
            var html = '<table><thead><tr>'
                + '<th>Order ID</th><th>Customer</th><th>Items</th>'
                + '<th>Total</th><th>Status</th><th>Time</th>'
                + '</tr></thead><tbody>';
            data.forEach(function(o){
                var badge = 'badge-paid';
                if(o.Status === 'Preparing')       badge = 'badge-progress';
                else if(o.Status === 'Ready')      badge = 'badge-ready';
                else if(o.Status === 'Picked Up')  badge = 'badge-pickedup';
                else if(o.Status === 'Pending')    badge = 'badge-pending';
                else if(o.Status === 'Cancelled')  badge = 'badge-cancelled';
                html += '<tr>'
                    + '<td>#' + o.OrderID + '</td>'
                    + '<td>' + o.Customer + '</td>'
                    + '<td class="items-list">' + o.Items + '</td>'
                    + '<td>' + o.Total + ' Ksh</td>'
                    + '<td><span class="badge ' + badge + '">' + o.Status + '</span></td>'
                    + '<td>' + o.Time + '</td>'
                    + '</tr>';
            });
            html += '</tbody></table>';
            document.getElementById('todayOrdersContent').innerHTML = html;
        })
        .catch(function(){
            document.getElementById('todayOrdersContent').innerHTML = '<p class="empty-state">Failed to load orders.</p>';
        });
}
</script>

</body>
</html>
