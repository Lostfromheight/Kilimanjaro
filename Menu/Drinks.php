<?php
include "MenuProcess.php";
include "CartProcess.php";

$cart_count = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $item){
        $cart_count += $item['count'];
    }
}
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro Cafe Menu - Drinks</title>
    <link rel="stylesheet" href="TestMenuCss.css">
    <link rel="stylesheet" href="Drinks.css">
    
</head>
<body>

<div class="background-blur">
    <div class="background-image"></div>
</div>

<div class="menu-title">K I L I M A N D J A R O &nbsp; M E N U</div>

<?php if(isset($_SESSION['First_name'])): ?>
<div style="position:fixed; top:10px; left:20px; z-index:200; color:white; font-size:13px; font-weight:600;">
    Welcome, <?php echo $_SESSION['First_name']; ?>
</div>
<?php endif; ?>
<div class="welcome-bar">
<?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'CU'): ?>
    <a href="../Orders/Checkout.php" class="cart-badge">Cart (<?php echo $cart_count; ?>)</a>
    <a href="../Orders/OrderStatus.php" class="btn-link">My Orders</a>
    <a href="../Auth/Logout.php" class="btn-link">Logout</a>
<?php else: ?>
    <a href="../Auth/Login.php" class="btn-link">Login</a>
    <a href="../Auth/SignForm.php" class="btn-link">Register</a>
<?php endif; ?>
</div>

<div class="app-container">
    <header class="category-tabs">
        <a href="Menu.php"   class="tab">Lunch</a>
        <a href="Drinks.php" class="tab active">Drinks</a>
        <a href="Pastry.php" class="tab">Pastry</a>
    </header>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'out_of_stock'){ ?>
    <p class="oos-error">This item is currently out of stock.</p>
    <?php } ?>

    <section class="menu-section">
        <h2 class="section-title">Drinks</h2>
        <main class="menu-grid">

        <?php foreach($drink_items as $item){
            $status_class = $item['Status'] == 'In Stock' ? 'in-stock' : 'out-of-stock';
        ?>
        <div class="menu-card <?php echo $status_class; ?>">
            <div class="image-container">
                <?php if($item['Item'] == 'Chocolate Milkshake'){ ?>
                    <img src="../Image/164.jpg" alt="Chocolate Milkshake">
                <?php } elseif($item['Item'] == 'Milk'){ ?>
                    <img src="../Image/166.jpg" alt="Milk">
                <?php } elseif($item['Item'] == 'Fresh Juice'){ ?>
                    <img src="../Image/165.jpg" alt="Fresh Juice">
                <?php } else { ?>
                    <img src="../Image/164.jpg" alt="<?php echo $item['Item']; ?>">
                <?php } ?>

                <form action="CartProcess.php" method="POST">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="hidden" name="ItemID" value="<?php echo $item['ItemID']; ?>">
                    <input type="hidden" name="Item" value="<?php echo $item['Item']; ?>">
                    <input type="hidden" name="Price" value="<?php echo $item['Price']; ?>">
                    <input type="hidden" name="redirect" value="Drinks.php">
                    <button type="submit" class="add-btn"<?php echo ($item['Status'] == 'Out of Stock') ? ' disabled' : ''; ?>>+</button>
                </form>

                <?php if($item['Status'] == 'Out of Stock'){ ?>
                <span class="stock-badge out-of-stock">Out of Stock</span>
                <?php } ?>
            </div>
            <div class="card-info">
                <p class="price"><?php echo $item['Price']; ?> Ksh
                    <span class="status-label <?php echo $status_class; ?>">
                        <?php echo $item['Status']; ?>
                    </span>
                </p>
                <h3 class="title"><?php echo $item['Item']; ?></h3>
                <div class="portion-buttons">
                    <button class="portion-btn">Small</button>
                    <button class="portion-btn">Large</button>
                </div>
            </div>
        </div>
        <?php } ?>

        </main>
    </section>
</div>

</body>
</html>