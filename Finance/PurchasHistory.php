<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";
$user_id = $_SESSION['UserID'];

$orders = mysqli_query($conn, "
    SELECT o.OrderID, o.Status, o.Total, o.Time,
           c.First_name AS chef_fname, c.Last_name AS chef_lname
    FROM `order` o
    LEFT JOIN chef c ON o.ChefID = c.ChefID
    WHERE o.UserID = '$user_id'
    ORDER BY o.Time DESC
");
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Purchase History</title>
    <link rel="stylesheet" href="../Menu/TestMenuCss.css">
    <style>
        body { margin:0; padding:0; font-family:'Segoe UI',Roboto,sans-serif; }
        .background-blur { position:fixed; inset:0; z-index:-1; filter:blur(8px); }
        .background-image {
            width:100vw; height:100vh; background-image:url('../Image/145.jpg');
            background-size:cover; background-position:left;
        }
        .menu-title {
            position:fixed; top:20px; left:50%; transform:translateX(-50%);
            z-index:100; color:white; font-size:24px; font-weight:bold;
            text-shadow:2px 2px 8px rgba(0,0,0,0.5); letter-spacing:4px;
        }
        .welcome-bar {
            position:fixed; top:10px; right:20px; z-index:200;
            color:white; font-size:13px; display:flex; gap:15px; align-items:center;
        }
        .btn-link {
            display:inline-block; padding:6px 14px; border-radius:20px;
            font-size:12px; font-weight:600; text-decoration:none;
            border:1.5px solid rgba(255,255,255,0.7); color:white;
            background:rgba(255,255,255,0.1); transition:background 0.25s;
        }
        .btn-link:hover { background:rgba(255,255,255,0.3); }
        .container {
            background:rgba(248,248,248,0.95); width:80%;
            margin:100px auto 40px auto; border-radius:20px;
            padding:30px; backdrop-filter:blur(10px);
        }
        .page-title {
            font-size:22px; font-weight:bold; color:#1a1a2e;
            margin-bottom:24px; padding-bottom:10px;
            border-bottom:3px solid #8b6f47;
            display:flex; justify-content:space-between; align-items:center;
        }
        table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; }
        thead { background:#1a1a2e; }
        th { padding:13px 16px; text-align:left; color:white; font-size:13px; }
        td { padding:13px 16px; border-bottom:1px solid #f1f5f9; font-size:13px; color:#334155; }
        tbody tr:hover { background:#f8fafc; }
        .badge {
            padding:3px 10px; border-radius:20px;
            font-size:11px; font-weight:700; display:inline-block;
        }
        .badge-paid       { background:#e0f2fe; color:#075985; }
        .badge-preparing  { background:#dbeafe; color:#1e40af; }
        .badge-ready      { background:#dcfce7; color:#15803d; }
        .badge-picked-up  { background:#f1f5f9; color:#64748b; }
        .badge-pending    { background:#fef9c3; color:#854d0e; }
        .btn-receipt {
            background:#8b6f47; color:white; border:none;
            padding:5px 12px; border-radius:6px; font-size:12px;
            cursor:pointer; text-decoration:none; display:inline-block;
        }
        .btn-receipt:hover { background:#6d5535; }
        .empty-state { text-align:center; padding:48px; color:#94a3b8; }
        .items-cell { font-size:12px; color:#475569; }
        .items-cell span {
            background:#f1f5f9; padding:2px 7px;
            border-radius:4px; margin-right:4px; display:inline-block;
        }
        .badge { position: static; display: inline-block; }
    </style>
</head>
<body>

<div class="background-blur"><div class="background-image"></div></div>
<div class="menu-title">K I L I M A N D J A R O</div>

<div class="welcome-bar">
    <a href="../Menu/Menu.php" class="btn-link">Menu</a>
    <a href="../Auth/Logout.php" class="btn-link">Logout</a>

</div>

<div class="container">
    <div class="page-title">
        🕒 Purchase History
        <span style="font-size:13px; color:#64748b; font-weight:normal;">
            All your orders
        </span>
    </div>

    <?php
    $has = false;
    $all_orders = [];
    while($o = mysqli_fetch_assoc($orders)){
        $has = true;
        $items_result = mysqli_query($conn, "
            SELECT m.Item, mo.Quantity
            FROM menu_option mo
            JOIN menu m ON mo.ItemID = m.ItemID
            WHERE mo.OrderID = " . intval($o['OrderID'])
        );
        $items = [];
        if($items_result && mysqli_num_rows($items_result) > 0){
            while($i = mysqli_fetch_assoc($items_result)){
                $items[] = $i['Item'] . ' (' . $i['Quantity'] . ')';
            }
        }
        $o['items'] = $items;
        $all_orders[] = $o;
    }
    ?>

    <?php if($has){ ?>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date & Time</th>
                <th>Items</th>
                <th>Chef</th>
                <th>Total (Ksh)</th>
                <th>Status</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($all_orders as $o){
            $status = $o['Status'];
            $badge_class = 'badge ';
            if($status == 'Paid')        $badge_class .= 'badge-paid';
            elseif($status == 'Preparing')$badge_class .= 'badge-preparing';
            elseif($status == 'Ready')   $badge_class .= 'badge-ready';
            elseif($status == 'Picked Up')$badge_class .= 'badge-picked-up';
            else                          $badge_class .= 'badge-pending';
        ?>
        <tr>
            <td><strong>#<?php echo str_pad($o['OrderID'],3,'0',STR_PAD_LEFT); ?></strong></td>
            <td><?php echo date('d M Y, h:i A', strtotime($o['Time'])); ?></td>
            <td class="items-cell">
                <?php
                if(!empty($o['items'])){
                    foreach($o['items'] as $item){
                        echo '<span>' . $item . '</span>';
                    }
                } else {
                    echo '—';
                }
                ?>
            </td>
            <td><?php echo $o['chef_fname'] ? $o['chef_fname'] . ' ' . $o['chef_lname'] : '—'; ?></td>
            <td><?php echo number_format($o['Total']); ?></td>
            <td><span class="<?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
            <td>
                <a href="Receipt.php?order_id=<?php echo $o['OrderID']; ?>" class="btn-receipt">
                    Receipt
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
    <div class="empty-state">
        <p>You have no order history yet.</p><br>
        <a href="../Menu/Menu.php" class="btn-link">← Browse Menu</a>
    </div>
    <?php } ?>
</div>

</body>
</html>
