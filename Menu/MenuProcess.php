<?php
session_start();

include "../connection.php";

// Fetch Lunch items
$query_lunch = "SELECT * FROM Menu WHERE Category='Lunch'";
$result_lunch = mysqli_query($conn, $query_lunch);
if($result_lunch && mysqli_num_rows($result_lunch) > 0){
    $lunch_items = mysqli_fetch_all($result_lunch, MYSQLI_ASSOC);
    // DEBUG: Uncomment to see DB results
    // echo "<pre>DB Lunch Items: "; print_r($lunch_items); echo "</pre>";
} else {
    $lunch_items = [
        ['ItemID'=>101, 'Item'=>'Rice Beef', 'Price'=>180, 'Status'=>'In Stock'],
        ['ItemID'=>102, 'Item'=>'Githeri', 'Price'=>130, 'Status'=>'In Stock'],
        ['ItemID'=>103, 'Item'=>'Pilau', 'Price'=>150, 'Status'=>'In Stock'],
        ['ItemID'=>104, 'Item'=>'Chips', 'Price'=>120, 'Status'=>'In Stock'],
    ];
    // DEBUG: Uncomment to see fallback
    // echo "<pre>Fallback Lunch Items: "; print_r($lunch_items); echo "</pre>";
}

// Fetch Drinks items
$query_drinks = "SELECT * FROM Menu WHERE Category='Drinks'";
$result_drinks = mysqli_query($conn, $query_drinks);
if($result_drinks && mysqli_num_rows($result_drinks) > 0){
    $drink_items = mysqli_fetch_all($result_drinks, MYSQLI_ASSOC);
} else {
    $drink_items = [
        ['ItemID'=>201, 'Item'=>'Chocolate Milkshake', 'Price'=>150, 'Status'=>'In Stock'],
        ['ItemID'=>202, 'Item'=>'Fresh Juice', 'Price'=>120, 'Status'=>'In Stock'],
        ['ItemID'=>203, 'Item'=>'Milk', 'Price'=>100, 'Status'=>'In Stock'],
    ];
}

// Fetch Pastry items
$query_pastry = "SELECT * FROM Menu WHERE Category='Pastry'";
$result_pastry = mysqli_query($conn, $query_pastry);
if($result_pastry && mysqli_num_rows($result_pastry) > 0){
    $pastry_items = mysqli_fetch_all($result_pastry, MYSQLI_ASSOC);
} else {
    $pastry_items = [
        ['ItemID'=>301, 'Item'=>'Cake', 'Price'=>180, 'Status'=>'In Stock'],
        ['ItemID'=>302, 'Item'=>'Donut', 'Price'=>80, 'Status'=>'In Stock'],
        ['ItemID'=>303, 'Item'=>'Muffin', 'Price'=>90, 'Status'=>'In Stock'],
    ];
}
?>