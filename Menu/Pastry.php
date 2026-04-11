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
    <title>Kilimandjaro Cafe Menu - Pastry</title>
    <link rel="stylesheet" href="TestMenuCss.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .menu-title {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 100; color: white; font-size: 24px; font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: 4px;
        }
        .background-blur { position: fixed; inset: 0; z-index: -1; filter: blur(8px); }
        .background-image {
            width: 100vw; height: 100vh;
            background-image: url('../Image/145.jpg');
            background-size: cover; background-position: left;
        }
        .welcome-bar {
            position: fixed; top: 10px; right: 20px; z-index: 200;
            color: white; font-size: 13px; display: flex; gap: 15px; align-items: center;
        }
        .welcome-bar a { color: #ccc; text-decoration: none; font-size: 12px; }
        .welcome-bar a:hover { text-decoration: underline; }
        .cart-badge {
            background: #8b6f47; color: white; padding: 5px 12px;
            border-radius: 20px; font-size: 12px; font-weight: bold;
            text-decoration: none; transition: background 0.3s;
        }
        .cart-badge:hover { background: #6d5535; color: white; }
        .btn-link {
            display: inline-block; padding: 6px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 600; text-decoration: none;
            border: 1.5px solid rgba(255,255,255,0.7); color: white;
            background: rgba(255,255,255,0.1); transition: background 0.25s;
        }
        .btn-link:hover { background: rgba(255,255,255,0.3); }
        .welcome-name { font-weight: 600; }
    </style>
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
        <a href="Drinks.php" class="tab">Drinks</a>
        <a href="Pastry.php" class="tab active">Pastry</a>
    </header>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'out_of_stock'){ ?>
    <p class="oos-error">This item is currently out of stock.</p>
    <?php } ?>

    <section class="menu-section">
        <h2 class="section-title">Pastry</h2>
        <main class="menu-grid">

        <?php foreach($pastry_items as $item){
            $status_class = $item['Status'] == 'In Stock' ? 'in-stock' : 'out-of-stock';
        ?>
        <div class="menu-card <?php echo $status_class; ?>">
            <div class="image-container">
                <?php if($item['Item'] == 'Cake'){ ?>
                    <img src="../Image/163.jpg" alt="Cake">
                <?php } elseif($item['Item'] == 'Donut'){ ?>
                    <img src="../Image/162.jpg" alt="Donut">
                <?php } elseif($item['Item'] == 'Muffin'){ ?>
                    <img src="../Image/161.jpg" alt="Muffin">
                <?php } else { ?>
                    <img src="../Image/163.jpg" alt="<?php echo $item['Item']; ?>">
                <?php } ?>

                <form action="CartProcess.php" method="POST">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="hidden" name="ItemID"   value="<?php echo $item['ItemID']; ?>">
                    <input type="hidden" name="Item"     value="<?php echo $item['Item']; ?>">
                    <input type="hidden" name="Price"    value="<?php echo $item['Price']; ?>">
                    <input type="hidden" name="redirect" value="Pastry.php">
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
                    <button class="portion-btn">Single</button>
                    <button class="portion-btn">Pack</button>
                </div>
            </div>
        </div>
        <?php } ?>

        </main>
    </section>
</div>

</body>
</html>