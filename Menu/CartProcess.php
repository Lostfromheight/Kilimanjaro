<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Initialize cart if it doesn't exist
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Add item to cart
if(isset($_POST['add_to_cart'])){
    if(!isset($_SESSION['UserID'])){
        header('location: ../Auth/Login.php');
        exit();
    }
    // Load DB connection if not already available
    if(!isset($conn)){
        include "../connection.php";
    }
    $item_id = intval($_POST['ItemID']);
    // Block add if item is out of stock or quantity is zero
    $stock_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Status, Quantity FROM menu WHERE ItemID = $item_id"));
    if(!$stock_row || $stock_row['Status'] == 'Out of Stock' || $stock_row['Quantity'] <= 0){
        $redirect = $_POST['redirect'] ?? 'Menu.php';
        header("location: $redirect?error=out_of_stock");
        exit();
    }
    $item_name = $_POST['Item'];
    $price     = $_POST['Price'];

    // If item already in cart increase quantity count
    if(isset($_SESSION['cart'][$item_id])){
        $_SESSION['cart'][$item_id]['count']++;
    } else {
        $_SESSION['cart'][$item_id] = [
            'ItemID' => $item_id,
            'Item'   => $item_name,
            'Price'  => $price,
            'size'   => 'Full', // default size, customer changes on checkout
            'count'  => 1
        ];
    }

    // Redirect back to same page they were on
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'Menu.php';
    header("location: $redirect");
    exit();
}

// Remove single item from cart
if(isset($_GET['remove_item'])){
    $item_id = $_GET['remove_item'];
    if(isset($_SESSION['cart'][$item_id])){
        unset($_SESSION['cart'][$item_id]);
    }
    header("location: ../Orders/Checkout.php");
    exit();
}

// Clear entire cart
if(isset($_GET['clear_cart'])){
    $_SESSION['cart'] = [];
    header("location: ../Orders/Checkout.php");
    exit();
}
?>