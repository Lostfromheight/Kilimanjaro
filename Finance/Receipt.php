<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$order_id = intval($_GET['order_id'] ?? 0);
$user_id  = $_SESSION['UserID'];

// Fetch order — must belong to this customer
$order = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT o.*, r.First_name, r.Last_name,
           c.First_name AS chef_fname, c.Last_name AS chef_lname
    FROM `order` o
    JOIN registration r ON o.UserID = r.UserID
    LEFT JOIN chef c ON o.ChefID = c.ChefID
    WHERE o.OrderID = $order_id AND o.UserID = '$user_id'
"));

if(!$order){
    header('location:PurchasHistory.php');
    exit();
}

// Fetch items
$items_result = mysqli_query($conn, "
    SELECT m.Item, m.Category, mo.Quantity, mo.Price
    FROM menu_option mo
    JOIN menu m ON mo.ItemID = m.ItemID
    WHERE mo.OrderID = $order_id
");
$items = [];
while($i = mysqli_fetch_assoc($items_result)){
    $items[] = $i;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - Order #<?php echo str_pad($order_id,3,'0',STR_PAD_LEFT); ?></title>
    <style>
        /* Screen styles */
        body {
            font-family: 'Courier New', monospace;
            background: #f0f2f5;
            display: flex; flex-direction: column;
            align-items: center; padding: 40px 20px;
        }
        .print-btn {
            background: #1a1a2e; color: white; border: none;
            padding: 12px 32px; border-radius: 8px; font-size: 14px;
            cursor: pointer; margin-bottom: 24px; letter-spacing: 1px;
        }
        .print-btn:hover { background: #2d2d4e; }
        .back-btn {
            background: #8b6f47; color: white; border: none;
            padding: 12px 24px; border-radius: 8px; font-size: 14px;
            cursor: pointer; margin-bottom: 24px; margin-right: 12px;
            text-decoration: none; display: inline-block; letter-spacing: 1px;
        }
        .back-btn:hover { background: #6d5535; }

        /* Receipt card */
        .receipt {
            background: white;
            width: 380px;
            padding: 32px 28px;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .receipt-header { text-align: center; margin-bottom: 20px; }
        .receipt-header h1 { font-size: 22px; letter-spacing: 4px; color: #1a1a2e; margin: 0; }
        .receipt-header p  { font-size: 11px; color: #64748b; margin: 4px 0 0; letter-spacing: 2px; }
        .divider {
            border: none; border-top: 1px dashed #cbd5e1;
            margin: 14px 0;
        }
        .info-row {
            display: flex; justify-content: space-between;
            font-size: 12px; color: #475569; margin-bottom: 6px;
        }
        .info-row strong { color: #1e293b; }
        .items-table { width: 100%; margin: 8px 0; }
        .items-table th {
            font-size: 11px; color: #94a3b8; font-weight: 600;
            padding: 4px 0; border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .items-table th:last-child { text-align: right; }
        .items-table td {
            font-size: 12px; color: #334155;
            padding: 6px 0; border-bottom: 1px solid #f8fafc;
        }
        .items-table td:last-child { text-align: right; font-weight: 600; }
        .total-row {
            display: flex; justify-content: space-between;
            font-size: 16px; font-weight: bold; color: #1a1a2e;
            margin-top: 10px;
        }
        .status-paid {
            text-align: center; background: #dcfce7; color: #15803d;
            padding: 6px; border-radius: 4px; font-size: 12px;
            font-weight: bold; margin-top: 12px; letter-spacing: 1px;
        }
        .receipt-footer {
            text-align: center; font-size: 11px; color: #94a3b8;
            margin-top: 16px; line-height: 1.8;
        }

        /* Print styles — hide buttons, clean layout */
        @media print {
            body { background: white; padding: 0; }
            .print-btn, .back-btn { display: none; }
            .receipt {
                box-shadow: none; border-radius: 0;
                width: 100%; padding: 20px;
            }
        }
    </style>
</head>
<body>

<a href="PurchasHistory.php" class="back-btn">← Back to History</a>
<button class="print-btn" onclick="window.print()">Print / Save as PDF</button>

<div class="receipt">

    <div class="receipt-header">
        <h1>KILIMANDJARO</h1>
        <p>UNIVERSITY CAFETERIA</p>
    </div>

    <hr class="divider">

    <div class="info-row">
        <span>Order ID</span>
        <strong>#<?php echo str_pad($order['OrderID'],3,'0',STR_PAD_LEFT); ?></strong>
    </div>
    <div class="info-row">
        <span>Customer</span>
        <strong><?php echo $order['First_name'] . ' ' . $order['Last_name']; ?></strong>
    </div>
    <div class="info-row">
        <span>Date</span>
        <strong><?php echo date('d M Y, h:i A', strtotime($order['Time'])); ?></strong>
    </div>
    <div class="info-row">
        <span>Chef</span>
        <strong><?php echo $order['chef_fname'] ? $order['chef_fname'] . ' ' . $order['chef_lname'] : '—'; ?></strong>
    </div>
    <div class="info-row">
        <span>Payment</span>
        <strong>M-Pesa (Simulated)</strong>
    </div>

    <hr class="divider">

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Portion</th>
                <th>Amount (Ksh)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($items as $item){ ?>
            <tr>
                <td><?php echo $item['Item']; ?></td>
                <td><?php echo $item['Quantity']; ?></td>
                <td><?php echo number_format($item['Price']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <hr class="divider">

    <div class="total-row">
        <span>TOTAL</span>
        <span>Ksh <?php echo number_format($order['Total']); ?></span>
    </div>

    <div class="status-paid">✓ PAYMENT CONFIRMED</div>

    <hr class="divider">

    <div class="receipt-footer">
        Thank you for dining with us!<br>
        Strathmore Institute — Kilimandjaro Cafe<br>
        <?php echo date('Y'); ?>
    </div>

</div>

</body>
</html>
