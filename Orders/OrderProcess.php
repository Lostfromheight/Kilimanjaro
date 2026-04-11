<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

if(isset($_POST['place_order'])){
    $user_id = $_SESSION['UserID'];
    $cart    = $_SESSION['cart'];
    $phone   = isset($_POST['phone']) ? $_POST['phone'] : '';

    if(empty($cart)){
        header('location:Checkout.php?error=empty_cart');
        exit();
    }

    // Verify all cart items are still in stock before processing payment
    $oos_names = [];
    foreach($cart as $item){
        $check_id  = intval($item['ItemID']);
        $stock_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Status, Quantity FROM menu WHERE ItemID = $check_id"));
        if(!$stock_row || $stock_row['Status'] == 'Out of Stock' || $stock_row['Quantity'] <= 0){
            $oos_names[] = $item['Item'];
        }
    }
    if(!empty($oos_names)){
        $_SESSION['checkout_oos'] = implode(', ', $oos_names);
        header('location:Checkout.php?error=out_of_stock');
        exit();
    }

    // Auto assign first available chef
    $chef_result = mysqli_query($conn, "SELECT ChefID FROM chef LIMIT 1");
    $chef        = mysqli_fetch_assoc($chef_result);
    $chef_id     = $chef['ChefID'];

    // Calculate total
    $total = 0;
    foreach($cart as $item){
        $base_price = floatval($item['Price']);
        $size       = $item['size'];
        $count      = intval($item['count']);
        $item_price = ($size == 'Full') ? $base_price + 50 : $base_price;
        $total     += $item_price * $count;
    }

    // Insert order — status is Paid immediately after phone confirmation
    $phone_safe = mysqli_real_escape_string($conn, $phone);
    mysqli_query($conn, "INSERT INTO `order` (UserID,ChefID,Status,Total) VALUES('$user_id','$chef_id','Paid','$total')");
    $order_id = mysqli_insert_id($conn);

    // Insert items into menu_option
    foreach($cart as $item){
        $item_id     = intval($item['ItemID']);
        $size        = $item['size'];
        $base_price  = floatval($item['Price']);
        $count       = intval($item['count']);
        $final_price = ($size == 'Full') ? $base_price + 50 : $base_price;

        if(in_array($size, ['Small','Single','Half'])){
            $db_size = 'Half';
        } else {
            $db_size = 'Full';
        }

        for($i = 0; $i < $count; $i++){
            mysqli_query($conn, "INSERT INTO menu_option (OrderID,ItemID,Quantity,Price) VALUES($order_id,$item_id,'$db_size',$final_price)");
        }

        // Deduct stock
        $deduct = ($db_size == 'Full') ? $count * 2 : $count * 1;
        mysqli_query($conn, "UPDATE menu SET Quantity = Quantity - $deduct WHERE ItemID = $item_id");
        mysqli_query($conn, "UPDATE menu SET Status = 'Out of Stock' WHERE ItemID = $item_id AND Quantity <= 0");
        mysqli_query($conn, "UPDATE menu SET Quantity = 0 WHERE ItemID = $item_id AND Quantity < 0");
    }

    // Clear cart
    $_SESSION['cart'] = [];

    header("location:OrderStatus.php");
    exit();
}

// CUSTOMER CANCEL ORDER
if(isset($_POST['cancel_order'])){
    $order_id   = intval($_POST['OrderID']);
    $user_id    = intval($_SESSION['UserID']);
    $raw_reason = isset($_POST['cancellation_reason']) ? trim($_POST['cancellation_reason']) : '';

    if(empty($raw_reason)){
        header('location:OrderStatus.php?error=no_reason');
        exit();
    }

    // Verify order belongs to this customer and is still cancellable
    $check = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT OrderID FROM `order`
         WHERE OrderID=$order_id AND UserID=$user_id AND Status IN ('Paid','Preparing')"
    ));
    if(!$check){
        header('location:OrderStatus.php?error=invalid');
        exit();
    }

    // Mark order as Cancelled
    mysqli_query($conn, "UPDATE `order` SET Status='Cancelled' WHERE OrderID=$order_id");

    // Restore stock quantities
    $options = mysqli_query($conn, "SELECT ItemID, Quantity FROM menu_option WHERE OrderID=$order_id");
    while($opt = mysqli_fetch_assoc($options)){
        $item_id = intval($opt['ItemID']);
        $restore = ($opt['Quantity'] == 'Full') ? 2 : 1;
        mysqli_query($conn, "UPDATE menu SET Quantity = Quantity + $restore WHERE ItemID=$item_id");
        mysqli_query($conn, "UPDATE menu SET Status = 'In Stock' WHERE ItemID=$item_id AND Quantity > 0");
    }

    // Log cancellation as feedback to Chef
    $padded_id    = str_pad($order_id, 3, '0', STR_PAD_LEFT);
    $message_safe = mysqli_real_escape_string($conn, "Order #$padded_id cancelled. Reason: $raw_reason");
    mysqli_query($conn, "
        INSERT INTO feedback (UserID, Type, Message, GoesTo, Status)
        VALUES($user_id, 'Order Cancellation', '$message_safe', 'Chef', 'Unread')
    ");

    header('location:OrderStatus.php?cancelled=1');
    exit();
}
?>
