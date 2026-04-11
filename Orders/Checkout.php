<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../Menu/CartProcess.php";

if(isset($_POST['update_sizes'])){
    foreach($_SESSION['cart'] as $item_id => $item){
        if(isset($_POST['size_'.$item_id])){
            $_SESSION['cart'][$item_id]['size'] = $_POST['size_'.$item_id];
        }
    }
}

$cart  = $_SESSION['cart'];
$total = 0;
foreach($cart as $item){
    $item_price = ($item['size'] == 'Full') ? $item['Price'] + 50 : $item['Price'];
    $total += $item_price * $item['count'];
}
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Checkout</title>
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
        .checkout-container {
            background: rgba(248,248,248,0.92);
            width: 75%; margin: 100px auto 40px auto;
            border-radius: 20px; padding: 30px;
            backdrop-filter: blur(10px);
        }
        .checkout-title {
            font-size: 22px; font-weight: bold; color: #333;
            margin-bottom: 20px; padding-bottom: 10px;
            border-bottom: 3px solid #8b6f47;
        }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .cart-table th {
            background: #1a1a2e; color: white;
            padding: 12px 15px; text-align: left; font-size: 13px;
        }
        .cart-table td { padding: 12px 15px; border-bottom: 1px solid #ddd; color: #333; font-size: 13px; }
        .cart-table tbody tr:hover { background: #f5f5f5; }
        .size-select { padding: 6px 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 12px; }
        .remove-btn { color: #f44336; text-decoration: none; font-weight: 600; font-size: 12px; }
        .remove-btn:hover { color: #d32f2f; }
        .cart-summary {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 0; border-top: 2px solid #ddd; margin-top: 10px;
        }
        .total-text { font-size: 20px; font-weight: bold; color: #333; }
        .btn-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-place-order {
            background: #1a1a2e; color: white; border: none;
            padding: 12px 28px; border-radius: 8px; font-size: 14px;
            cursor: pointer; letter-spacing: 1px; transition: background 0.3s;
        }
        .btn-place-order:hover { background: #2d2d4e; }
        .btn-clear {
            background: #f44336; color: white; border: none;
            padding: 12px 20px; border-radius: 8px; font-size: 14px;
            cursor: pointer; transition: background 0.3s; text-decoration: none;
            display: inline-flex; align-items: center;
        }
        .btn-clear:hover { background: #d32f2f; }
        .btn-back {
            background: #8b6f47; color: white; border: none;
            padding: 12px 20px; border-radius: 8px; font-size: 14px;
            cursor: pointer; transition: background 0.3s; text-decoration: none;
            display: inline-flex; align-items: center;
        }
        .btn-back:hover { background: #6d5535; }
        .empty-cart { text-align: center; padding: 40px; color: #999; font-size: 16px; }
        .error-msg { color: #f44336; font-size: 13px; margin-bottom: 10px; }

                .processing-box.show { display: block; }
        .spinner {
            width: 40px; height: 40px; border: 4px solid #e2e8f0;
            border-top-color: #1a1a2e; border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 16px auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="background-blur">
    <div class="background-image"></div>
</div>

<div class="menu-title">K I L I M A N D J A R O &nbsp; C H E C K O U T</div>

<div class="welcome-bar">
    <span>Welcome, <?php echo $_SESSION['First_name']; ?></span>
    <a href="../Menu/Menu.php">🍽️ Menu</a>
    <a href="../Auth/Logout.php">Logout</a>
</div>

<div class="checkout-container">
    <div class="checkout-title">🛒 Your Cart</div>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'empty_cart'){ ?>
        <p class="error-msg">Your cart is empty. Please add items before placing an order.</p>
    <?php } ?>
    <?php if(isset($_GET['error']) && $_GET['error'] == 'out_of_stock'){
        $oos_msg = $_SESSION['checkout_oos'] ?? '';
        unset($_SESSION['checkout_oos']);
    ?>
        <p class="error-msg">Some items in your cart are no longer available<?php echo $oos_msg ? ': ' . htmlspecialchars($oos_msg) : ''; ?>. Please remove them and try again.</p>
    <?php } ?>

    <?php if(empty($cart)){ ?>
        <div class="empty-cart">
            <p>Your cart is empty!</p><br>
            <a href="../Menu/Menu.php" class="btn-back">← Back to Menu</a>
        </div>

    <?php } else { ?>

    <form action="Checkout.php" method="POST">
        <input type="hidden" name="update_sizes" value="1">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Size / Portion</th>
                    <th>Price (Ksh)</th>
                    <th>Qty</th>
                    <th>Subtotal (Ksh)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart as $item_id => $item){ ?>
                <tr>
                    <td><?php echo $item['Item']; ?></td>
                    <td>
                        <select name="size_<?php echo $item_id; ?>" class="size-select">
                            <option value="Half" <?php echo $item['size']=='Half' ? 'selected' : ''; ?>>Half</option>
                            <option value="Full" <?php echo $item['size']=='Full' ? 'selected' : ''; ?>>Full (+50 Ksh)</option>
                        </select>
                    </td>
                    <?php $display_price = ($item['size'] == 'Full') ? $item['Price'] + 50 : $item['Price']; ?>
                    <td><?php echo $display_price; ?></td>
                    <td><?php echo $item['count']; ?></td>
                    <td><?php echo $display_price * $item['count']; ?></td>
                    <td>
                        <a href="../Menu/CartProcess.php?remove_item=<?php echo $item_id; ?>" class="remove-btn">Remove</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" style="padding:8px 16px; background:#8b6f47; color:white; border:none; border-radius:6px; cursor:pointer; font-size:12px; margin-bottom:16px;">
            Update Sizes
        </button>
    </form>

    <!-- Order summary + action buttons -->
    <div class="cart-summary">
        <span class="total-text">Total: <?php echo $total; ?> Ksh</span>
        <div class="btn-row">
            <a href="../Menu/Menu.php" class="btn-back">← Continue Shopping</a>
            <a href="../Menu/CartProcess.php?clear_cart=1" class="btn-clear">🗑️ Clear Cart</a>
            <button type="button" class="btn-place-order" onclick="processPayment()">
                ✅ Place Order
            </button>
        </div>
    </div>

    <?php } ?>
</div>

<!-- Hidden form that submits the order -->
<form id="orderForm" action="OrderProcess.php" method="POST" style="display:none;">
    <input type="hidden" name="place_order" value="1">
    <input type="hidden" name="phone" id="hiddenPhone">
</form>

<script>
function processPayment(){
    var phone = prompt("Enter your M-Pesa phone number to complete payment:\nTotal: Ksh <?php echo $total; ?>");

    if(phone == null){
        return;
    }

    if(phone.length < 9){
        alert("Please enter a valid phone number.");
        return;
    }

    alert("Payment request sent to " + phone + ".\nClick OK to confirm your order.");

    document.getElementById('hiddenPhone').value = phone;
    document.getElementById('orderForm').submit();
}
</script>

</body>
</html>
