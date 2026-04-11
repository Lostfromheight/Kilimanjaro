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
    <title>Kilimandjaro Cafe Menu</title>
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
            color: white; font-size: 13px; display: flex; gap: 12px; align-items: center;
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

        /* ── Profile dropdown ── */
        .profile-wrap { position: relative; }
        .profile-btn {
            background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.7);
            color: white; padding: 6px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 6px;
            transition: background 0.25s;
        }
        .profile-btn:hover { background: rgba(255,255,255,0.3); }
        .dropdown-menu {
            display: none; position: absolute; top: 36px; right: 0;
            background: white; border-radius: 10px; min-width: 180px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15); overflow: hidden; z-index: 300;
        }
        .dropdown-menu.open { display: block; }
        .dropdown-menu a {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; font-size: 13px; color: #334155;
            text-decoration: none; transition: background 0.15s;
        }
        .dropdown-menu a:hover { background: #f8fafc; color: #1a1a2e; }
        .dropdown-divider { border: none; border-top: 1px solid #f1f5f9; margin: 0; }
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

    <!-- Profile dropdown -->
    <div class="profile-wrap">
        <button class="profile-btn" onclick="toggleDropdown()">
            <?php echo $_SESSION['First_name']; ?> &#9662;
        </button>
        <div class="dropdown-menu" id="profileDropdown">
            <a href="../Orders/OrderStatus.php">My Orders</a>
            <a href="../Finance/PurchasHistory.php">Purchase History</a>
            <a href="../Orders/Feedback.php">Give Feedback</a>
            <hr class="dropdown-divider">
            <a href="../Auth/Logout.php" style="color:#ef4444;">Logout</a>
        </div>
    </div>
<?php else: ?>
    <a href="../Auth/Login.php" class="btn-link">Login</a>
    <a href="../Auth/SignForm.php" class="btn-link">Register</a>
<?php endif; ?>
</div>

<div class="app-container">
    <header class="category-tabs">
        <a href="Menu.php"   class="tab active">Lunch</a>
        <a href="Drinks.php" class="tab">Drinks</a>
        <a href="Pastry.php" class="tab">Pastry</a>
    </header>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'out_of_stock'){ ?>
    <p class="oos-error">This item is currently out of stock.</p>
    <?php } ?>

    <section class="menu-section">
        <h2 class="section-title">Lunch</h2>
        <main class="menu-grid">

        <?php foreach($lunch_items as $item){
            $status_class = $item['Status'] == 'In Stock' ? 'in-stock' : 'out-of-stock';
        ?>
        <div class="menu-card <?php echo $status_class; ?>">
            <div class="image-container">
                <?php if($item['Item'] == 'Rice Beef'){ ?>
                    <img src="../Image/160.jpg" alt="Rice Beef">
                <?php } elseif($item['Item'] == 'Githeri'){ ?>
                    <img src="../Image/159.jpg" alt="Githeri">
                <?php } elseif($item['Item'] == 'Chips'){ ?>
                    <img src="../Image/158.jpg" alt="Chips">
                <?php } elseif($item['Item'] == 'Pilau'){ ?>
                    <img src="../Image/151.jpg" alt="Pilau">
                <?php } else { ?>
                    <img src="../Image/160.jpg" alt="<?php echo $item['Item']; ?>">
                <?php } ?>

                <form action="CartProcess.php" method="POST">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="hidden" name="ItemID"   value="<?php echo $item['ItemID']; ?>">
                    <input type="hidden" name="Item"     value="<?php echo $item['Item']; ?>">
                    <input type="hidden" name="Price"    value="<?php echo $item['Price']; ?>">
                    <input type="hidden" name="redirect" value="Menu.php">
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
                    <button class="portion-btn">Half</button>
                    <button class="portion-btn">Full</button>
                </div>
            </div>
        </div>
        <?php } ?>

        </main>
    </section>
</div>

<script>
function toggleDropdown(){
    document.getElementById('profileDropdown').classList.toggle('open');
}
// Close dropdown when clicking outside
document.addEventListener('click', function(e){
    var wrap = document.querySelector('.profile-wrap');
    if(wrap && !wrap.contains(e.target)){
        document.getElementById('profileDropdown').classList.remove('open');
    }
});
</script>

</body>
</html>