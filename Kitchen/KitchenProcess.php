<?php
if(!isset($_SESSION['role']) || $_SESSION['role'] != "CH"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$chef_id = $_SESSION['UserID'];

//ORDER STATUS UPDATE 
if(isset($_POST['update_status'])){
    $order_id   = intval($_POST['OrderID']);
    $new_status = $_POST['new_status'];
    mysqli_query($conn, "UPDATE `order` SET Status='$new_status' WHERE OrderID=$order_id");
    header('location:Kitchen.php?section=orders');
    exit();
}

//CANCEL ORDER
if(isset($_POST['delete_order'])){
    $order_id = intval($_POST['OrderID']);

    // Get customer UserID for feedback entry
    $info = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT UserID FROM `order` WHERE OrderID=$order_id"
    ));
    $order_user_id = $info ? intval($info['UserID']) : 0;

    // Restore stock quantities before cancelling
    $options = mysqli_query($conn, "SELECT ItemID, Quantity FROM menu_option WHERE OrderID=$order_id");
    while($opt = mysqli_fetch_assoc($options)){
        $item_id = intval($opt['ItemID']);
        $restore = ($opt['Quantity'] == 'Full') ? 2 : 1;
        mysqli_query($conn, "UPDATE menu SET Quantity = Quantity + $restore WHERE ItemID=$item_id");
        mysqli_query($conn, "UPDATE menu SET Status = 'In Stock' WHERE ItemID=$item_id AND Quantity > 0");
    }

    // Mark order as Cancelled (preserve record for history)
    mysqli_query($conn, "UPDATE `order` SET Status='Cancelled' WHERE OrderID=$order_id");

    // Notify Admin via feedback
    if($order_user_id > 0){
        $padded_id    = str_pad($order_id, 3, '0', STR_PAD_LEFT);
        $message_safe = mysqli_real_escape_string($conn, "Order #$padded_id was cancelled by kitchen staff");
        mysqli_query($conn, "
            INSERT INTO feedback (UserID, Type, Message, GoesTo, Status)
            VALUES($order_user_id, 'Chef Cancellation', '$message_safe', 'Admin', 'Unread')
        ");
    }

    header('location:Kitchen.php?section=orders');
    exit();
}

//MENU: ADD ITEM
if(isset($_POST['add_menu'])){
    $item     = $_POST['Item'];
    $category = $_POST['Category'];
    $price    = floatval($_POST['Price']);
    $quantity = intval($_POST['Quantity']);
    $status   = $quantity > 0 ? 'In Stock' : 'Out of Stock';
    mysqli_query($conn, "INSERT INTO menu (Item,Category,Price,Quantity,Status)
                         VALUES('$item','$category','$price','$quantity','$status')");
    header('location:KitchenMenu.php?msg=added');
    exit();
}

// MENU: EDIT ITEM 
if(isset($_POST['edit_menu'])){
    $id       = intval($_POST['ItemID']);
    $item     = $_POST['Item'];
    $category = $_POST['Category'];
    $price    = floatval($_POST['Price']);
    $quantity = intval($_POST['Quantity']);
    $status   = $quantity > 0 ? 'In Stock' : 'Out of Stock';
    mysqli_query($conn, "UPDATE menu SET Item='$item', Category='$category',
                         Price='$price', Quantity='$quantity', Status='$status'
                         WHERE ItemID=$id");
    header('location:KitchenMenu.php?msg=updated');
    exit();
}

//MENU: DELETE ITEM 
if(isset($_GET['del_menu'])){
    $id = intval($_GET['del_menu']);
    mysqli_query($conn, "DELETE FROM menu WHERE ItemID=$id");
    header('location:KitchenMenu.php?msg=deleted');
    exit();
}

// FETCH TODAY'S ALL ORDERS (AJAX for modal)
if(isset($_GET['fetch_today_orders'])){
    $chef_id_int = intval($chef_id);
    $today_date  = date('Y-m-d');
    $res = mysqli_query($conn, "
        SELECT o.OrderID, o.Status, o.Total, o.Time,
               r.First_name, r.Last_name
        FROM `order` o
        LEFT JOIN registration r ON o.UserID = r.UserID
        WHERE o.ChefID = $chef_id_int
        AND DATE(o.Time) = '$today_date'
        ORDER BY o.Time DESC
    ");
    $rows = [];
    while($row = mysqli_fetch_assoc($res)){
        $oid = intval($row['OrderID']);
        $items_res = mysqli_query($conn, "
            SELECT m.Item, mo.Quantity, COUNT(mo.OptionID) AS cnt
            FROM menu_option mo
            JOIN menu m ON mo.ItemID = m.ItemID
            WHERE mo.OrderID = $oid
            GROUP BY m.Item, mo.Quantity
            ORDER BY m.ItemID ASC
        ");
        $parts = [];
        while($it = mysqli_fetch_assoc($items_res)){
            $parts[] = $it['Item'] . ' (' . $it['Quantity'] . ') x' . $it['cnt'];
        }
        $rows[] = [
            'OrderID'  => str_pad($oid, 3, '0', STR_PAD_LEFT),
            'Customer' => $row['First_name'] . ' ' . $row['Last_name'],
            'Items'    => !empty($parts) ? implode(', ', $parts) : '—',
            'Total'    => $row['Total'],
            'Status'   => $row['Status'],
            'Time'     => date('h:i A', strtotime($row['Time']))
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($rows);
    exit();
}

// FETCH ORDERS
$orders = mysqli_query($conn, "
    SELECT o.OrderID, o.Status, o.Total, o.Time,
           r.First_name, r.Last_name
    FROM `order` o
    LEFT JOIN registration r ON o.UserID = r.UserID
    WHERE o.ChefID = '$chef_id'
    AND o.Status IN ('Paid','Preparing','Ready')
    ORDER BY FIELD(o.Status,'Paid','Preparing','Ready'), o.Time ASC
");

//FETCH MENU ITEMS 
$menu_items = mysqli_query($conn, "SELECT * FROM menu ORDER BY ItemID ASC");

//STAT COUNTS 
$today = date('Y-m-d');

$total_today = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS cnt FROM `order`
     WHERE ChefID='$chef_id' AND DATE(Time)='$today'"));

$pending = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS cnt FROM `order`
     WHERE ChefID='$chef_id' AND Status='Pending' AND DATE(Time)='$today'"));

$preparing = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS cnt FROM `order`
     WHERE ChefID='$chef_id' AND Status='Preparing' AND DATE(Time)='$today'"));

$ready = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS cnt FROM `order`
     WHERE ChefID='$chef_id' AND Status='Ready' AND DATE(Time)='$today'"));
?>
