<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$user_id = $_SESSION['UserID'];

$my_orders = mysqli_query($conn, "
    SELECT o.OrderID, o.Status, o.Total, o.Time, o.ChefID,
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
    <title>Kilimandjaro - My Orders</title>
    <link rel="stylesheet" href="../Menu/TestMenuCss.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .background-blur { position: fixed; inset: 0; z-index: -1; filter: blur(8px); }
        .background-image {
            width: 100vw; height: 100vh;
            background-image: url('../Image/145.jpg');
            background-size: cover; background-position: left;
        }
        .menu-title {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 100; color: white; font-size: 24px; font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: 4px;
        }
        .welcome-bar {
            position: fixed; top: 10px; right: 20px; z-index: 200;
            color: white; font-size: 13px; display: flex; gap: 15px; align-items: center;
        }
        .welcome-bar a { color: #ccc; text-decoration: none; font-size: 12px; }
        .welcome-bar a:hover { text-decoration: underline; }
        .orders-container {
            background: rgba(248,248,248,0.92); width: 80%;
            margin: 100px auto 40px auto; border-radius: 20px;
            padding: 30px; backdrop-filter: blur(10px);
        }
        .page-title {
            font-size: 22px; font-weight: bold; color: #333;
            margin-bottom: 24px; padding-bottom: 10px;
            border-bottom: 3px solid #8b6f47;
        }
        .order-card {
            background: white; border-radius: 12px;
            padding: 20px; margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .order-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 12px;
        }
        .order-id { font-size: 16px; font-weight: bold; color: #1e293b; }
        .order-time { font-size: 12px; color: #94a3b8; }
        .status-badge {
            padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .status-paid        { background: #e0f2fe; color: #075985; }
        .status-preparing   { background: #dbeafe; color: #1e40af; }
        .status-ready       { background: #dcfce7; color: #15803d; }
        .status-picked-up   { background: #f1f5f9; color: #64748b; }
        .order-details { display: flex; gap: 30px; flex-wrap: wrap; font-size: 13px; color: #475569; margin-top: 12px; }
        .items-list { margin-top: 10px; font-size: 13px; color: #475569; }
        .items-list span {
            background: #f1f5f9; padding: 3px 8px;
            border-radius: 4px; margin-right: 6px; margin-bottom: 4px;
            display: inline-block;
        }
        .progress-bar {
            display: flex; gap: 0; margin-top: 14px;
            border-radius: 8px; overflow: hidden;
        }
        .progress-step {
            flex: 1; padding: 6px; text-align: center;
            font-size: 10px; font-weight: 600;
            background: #f1f5f9; color: #94a3b8;
        }
        .progress-step.done   { background: #22c55e; color: white; }
        .progress-step.active { background: #3b82f6; color: white; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .btn-back {
            display: inline-block; background: #8b6f47; color: white;
            padding: 10px 20px; border-radius: 8px; text-decoration: none;
            font-size: 13px; margin-bottom: 20px;
        }
        .btn-back:hover { background: #6d5535; }
        .status-cancelled       { background: #fee2e2; color: #991b1b; }
        .cancelled-notice {
            margin-top: 14px; padding: 8px 12px; background: #fee2e2;
            border-radius: 6px; font-size: 12px; color: #991b1b; font-weight: 600;
        }
        .btn-cancel-order {
            display: inline-block; background: #ef4444; color: white;
            border: none; padding: 8px 16px; border-radius: 6px;
            font-size: 12px; font-weight: 600; cursor: pointer; margin-top: 12px;
        }
        .btn-cancel-order:hover { background: #dc2626; }
        .alert-success {
            background: rgba(34,197,94,0.15); border: 1px solid #22c55e;
            color: #166534; padding: 10px 16px; border-radius: 8px;
            margin-bottom: 16px; font-size: 13px;
        }
        .alert-error {
            background: rgba(239,68,68,0.12); border: 1px solid #ef4444;
            color: #991b1b; padding: 10px 16px; border-radius: 8px;
            margin-bottom: 16px; font-size: 13px;
        }
        .cancel-modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 1000;
            justify-content: center; align-items: center;
        }
        .cancel-modal-box {
            background: white; border-radius: 12px;
            width: 90%; max-width: 480px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.22);
        }
        .cancel-modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
        }
        .cancel-modal-header h3 { font-size: 17px; color: #1e293b; margin: 0; }
        .cancel-modal-close-btn {
            background: none; border: none; font-size: 18px; cursor: pointer;
            color: #64748b; padding: 4px 10px; border-radius: 4px; font-weight: 700;
        }
        .cancel-modal-close-btn:hover { background: #f1f5f9; color: #1e293b; }
        .cancel-modal-body { padding: 20px 24px; }
        .cancel-modal-body > p { font-size: 14px; color: #475569; margin-bottom: 16px; }
        .cancel-reason-options { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
        .cancel-reason-options label {
            font-size: 13px; color: #334155;
            display: flex; align-items: center; gap: 8px; cursor: pointer;
        }
        .cancel-other-textarea {
            width: 100%; box-sizing: border-box; padding: 8px 12px;
            border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;
            resize: vertical; min-height: 70px; margin-bottom: 4px;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .cancel-modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
        .btn-modal-back {
            background: #f1f5f9; color: #475569; border: none;
            padding: 9px 18px; border-radius: 6px; font-size: 13px; cursor: pointer;
        }
        .btn-modal-back:hover { background: #e2e8f0; }
        .btn-modal-confirm {
            background: #ef4444; color: white; border: none;
            padding: 9px 18px; border-radius: 6px; font-size: 13px;
            font-weight: 600; cursor: pointer;
        }
        .btn-modal-confirm:hover { background: #dc2626; }
    </style>
</head>
<body>

<div class="background-blur">
    <div class="background-image"></div>
</div>

<div class="menu-title">K I L I M A N D J A R O</div>

<div class="welcome-bar">
    <span>Welcome, <?php echo $_SESSION['First_name']; ?></span>
    <a href="../Menu/Menu.php">🍽️ Menu</a>
    <a href="../Auth/Logout.php">Logout</a>
</div>

<div class="orders-container">
    <div class="page-title">My Orders</div>
    <a href="../Menu/Menu.php" class="btn-back">Back to Menu</a>

    <?php if(isset($_GET['cancelled'])){ ?>
    <div class="alert-success">Your order has been cancelled successfully.</div>
    <?php } ?>
    <?php if(isset($_GET['error'])){ ?>
    <div class="alert-error">
        <?php
        if($_GET['error'] == 'no_reason') echo 'Please select a reason before cancelling.';
        elseif($_GET['error'] == 'invalid') echo 'This order cannot be cancelled.';
        else echo 'Something went wrong. Please try again.';
        ?>
    </div>
    <?php } ?>

    <?php
    $has_orders = false;
    while($order = mysqli_fetch_assoc($my_orders)){
        $has_orders = true;
        $status = $order['Status'];

        // Card border class
        $card_class = 'order-card';
        if($status == 'Paid')           $card_class .= ' paid';
        elseif($status == 'Preparing')  $card_class .= ' preparing';
        elseif($status == 'Ready')      $card_class .= ' ready';
        elseif($status == 'Picked Up')  $card_class .= ' picked-up';
        elseif($status == 'Cancelled')  $card_class .= ' cancelled';

        // Status badge class
        $badge_class = 'status-badge';
        if($status == 'Paid')           $badge_class .= ' status-paid';
        elseif($status == 'Preparing')  $badge_class .= ' status-preparing';
        elseif($status == 'Ready')      $badge_class .= ' status-ready';
        elseif($status == 'Picked Up')  $badge_class .= ' status-picked-up';
        elseif($status == 'Cancelled')  $badge_class .= ' status-cancelled';

        // Progress steps matching order flow
        $steps        = ['Paid', 'Preparing', 'Ready', 'Picked Up'];
        $current_step = array_search($status, $steps);
        if($current_step === false) $current_step = 0;

        // Fetch items for this order using intval — fixes the fatal error
        $items_result = mysqli_query($conn, "
            SELECT m.Item, mo.Quantity
            FROM menu_option mo
            JOIN menu m ON mo.ItemID = m.ItemID
            WHERE mo.OrderID = " . intval($order['OrderID'])
        );
    ?>
    <div class="<?php echo $card_class; ?>">
        <div class="order-header">
            <span class="order-id">Order #<?php echo str_pad($order['OrderID'], 3, '0', STR_PAD_LEFT); ?></span>
            <span class="<?php echo $badge_class; ?>"><?php echo $status; ?></span>
            <span class="order-time"><?php echo date('d M Y, h:i A', strtotime($order['Time'])); ?></span>
        </div>

        <!-- Progress bar or cancelled notice -->
        <?php if($status == 'Cancelled'){ ?>
        <div class="cancelled-notice">This order was cancelled.</div>
        <?php } else { ?>
        <div class="progress-bar">
            <?php foreach($steps as $i => $step){ ?>
            <div class="progress-step <?php echo $i < $current_step ? 'done' : ($i == $current_step ? 'active' : ''); ?>">
                <?php echo $step; ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

        <div class="order-details">
            <div><strong>Total:</strong> <?php echo $order['Total']; ?> Ksh</div>
            <?php if($order['chef_fname']){ ?>
            <div><strong>Chef:</strong> <?php echo $order['chef_fname'] . ' ' . $order['chef_lname']; ?></div>
            <?php } ?>
        </div>

        <!-- Items -->
        <div class="items-list">
            <strong>Items:</strong><br>
            <?php
            if($items_result && mysqli_num_rows($items_result) > 0){
                while($i = mysqli_fetch_assoc($items_result)){
                    echo '<span>' . $i['Item'] . ' (' . $i['Quantity'] . ')</span>';
                }
            } else {
                echo '<span style="color:#999;">No items found</span>';
            }
            ?>
        </div>

        <!-- Cancel button for Paid and Preparing orders only -->
        <?php if($status == 'Paid' || $status == 'Preparing'){ ?>
        <button class="btn-cancel-order" onclick="openCancelModal(<?php echo intval($order['OrderID']); ?>)">Cancel Order</button>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if(!$has_orders){ ?>
    <div class="empty-state">
        <p>You haven't placed any orders yet.</p><br>
        <a href="../Menu/Menu.php" class="btn-back">← Go to Menu</a>
    </div>
    <?php } ?>
</div>


<div id="cancelModal" class="cancel-modal-overlay" onclick="if(event.target===this)closeCancelModal()">
    <div class="cancel-modal-box">
        <div class="cancel-modal-header">
            <h3>Cancel Order</h3>
            <button class="cancel-modal-close-btn" onclick="closeCancelModal()">X</button>
        </div>
        <div class="cancel-modal-body">
            <p>Why are you cancelling this order?</p>
            <form id="cancelForm" action="OrderProcess.php" method="POST">
                <input type="hidden" name="cancel_order" value="1">
                <input type="hidden" name="OrderID" id="cancelOrderID" value="">
                <input type="hidden" name="cancellation_reason" id="cancelReasonFinal" value="">
                <div class="cancel-reason-options">
                    <label>
                        <input type="radio" name="reason_choice" value="Changed my mind" onchange="toggleOtherReason()">
                        Changed my mind
                    </label>
                    <label>
                        <input type="radio" name="reason_choice" value="Taking too long" onchange="toggleOtherReason()">
                        Taking too long
                    </label>
                    <label>
                        <input type="radio" name="reason_choice" value="Ordered by mistake" onchange="toggleOtherReason()">
                        Ordered by mistake
                    </label>
                    <label>
                        <input type="radio" name="reason_choice" value="other" onchange="toggleOtherReason()">
                        Other reason
                    </label>
                </div>
                <textarea id="otherReasonText" class="cancel-other-textarea" placeholder="Please describe your reason..." style="display:none;"></textarea>
                <div class="cancel-modal-actions">
                    <button type="button" class="btn-modal-back" onclick="closeCancelModal()">Go Back</button>
                    <button type="button" class="btn-modal-confirm" onclick="submitCancellation()">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(orderId){
    document.getElementById('cancelOrderID').value = orderId;
    document.getElementById('otherReasonText').style.display = 'none';
    var radios = document.getElementsByName('reason_choice');
    for(var i = 0; i < radios.length; i++) radios[i].checked = false;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal(){
    document.getElementById('cancelModal').style.display = 'none';
}

function toggleOtherReason(){
    var selected = document.querySelector('input[name="reason_choice"]:checked');
    var textarea = document.getElementById('otherReasonText');
    if(selected && selected.value === 'other'){
        textarea.style.display = 'block';
    } else {
        textarea.style.display = 'none';
    }
}

function submitCancellation(){
    var selected = document.querySelector('input[name="reason_choice"]:checked');
    if(!selected){
        alert('Please select a reason for cancelling.');
        return;
    }
    var reason = selected.value;
    if(reason === 'other'){
        var text = document.getElementById('otherReasonText').value.trim();
        if(!text){
            alert('Please describe your reason.');
            return;
        }
        reason = text;
    }
    document.getElementById('cancelReasonFinal').value = reason;
    document.getElementById('cancelForm').submit();
}
</script>

</body>
</html>
