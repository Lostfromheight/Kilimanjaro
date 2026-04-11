<?php
session_start();
include "KitchenProcess.php";

$menu_items = mysqli_query($conn, "SELECT * FROM menu ORDER BY Category, Item");
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Menu Management</title>
    <link rel="stylesheet" href="Kitchen.css?v=20260411b">
</head>
<body class="bg-blur">

<div class="header">
    <div class="header-left">
        <img src="../Image/logo.png" alt="Kilimandjaro Logo">
        <div class="header-title">
            <h1>Kilimandjaro</h1>
            <p>KITCHEN DASHBOARD</p>
        </div>
    </div>
    <div class="tab-nav">
        <button class="tab-btn" onclick="window.location='Kitchen.php'">📦 Incoming Orders</button>
        <button class="tab-btn active">📋 Menu Management</button>
        <button class="tab-btn" onclick="window.location='KitchenFeedback.php'">💬 Feedback</button>
    </div>
    <div class="header-right">
        <span class="chef-name">Chef: <?php echo $_SESSION['First_name']; ?></span>
        <a href="../Auth/Logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</div>

<div class="main">

    <?php if(isset($_GET['msg'])){ ?>
        <?php if($_GET['msg'] == 'added'){ ?>
            <div class="alert-success">✅ Menu item added successfully.</div>
        <?php } elseif($_GET['msg'] == 'updated'){ ?>
            <div class="alert-success">✅ Menu item updated successfully.</div>
        <?php } elseif($_GET['msg'] == 'deleted'){ ?>
            <div class="alert-danger">🗑 Menu item deleted.</div>
        <?php } ?>
    <?php } ?>

    <div style="margin-bottom:16px;">
        <button onclick="toggleMenuForm()" class="btn-preparing" style="padding:10px 20px; font-size:13px;">
            + Add New Menu Item
        </button>
    </div>

    <div id="menu-add-form" style="display:none; background:white; border-radius:10px; padding:24px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        <h3 style="color:#1a1a2e; font-size:16px; margin-bottom:20px; letter-spacing:1px;">Add New Menu Item</h3>
        <form action="KitchenMenu.php" method="POST">
            <input type="hidden" name="add_menu" value="1">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px;">Item Name</label>
                    <input type="text" name="Item" placeholder="e.g. Chips" required
                           style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px;">Category</label>
                    <select name="Category"
                            style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                        <option value="Lunch">Lunch</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Pastry">Pastry</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px;">Price (Ksh)</label>
                    <input type="number" name="Price" placeholder="e.g. 150" step="0.01" required
                           style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px;">Quantity</label>
                    <input type="number" name="Quantity" placeholder="e.g. 30" required
                           style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                </div>
            </div>
            <button type="submit" class="btn-ready" style="padding:8px 20px; margin-right:8px;">Save Item</button>
            <button type="button" onclick="toggleMenuForm()"
                    style="padding:8px 20px; background:#94a3b8; color:white; border:none; border-radius:6px; cursor:pointer; font-size:13px;">Cancel</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Item</th><th>Category</th>
                    <th>Price (Ksh)</th><th>Quantity</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $has_menu = false;
            while($item = mysqli_fetch_assoc($menu_items)){
                $has_menu = true;
            ?>
            <tr>
                <td><?php echo $item['ItemID']; ?></td>
                <td><?php echo $item['Item']; ?></td>
                <td><?php echo $item['Category']; ?></td>
                <td><?php echo $item['Price']; ?></td>
                <td><?php echo $item['Quantity']; ?></td>
                <td>
                    <?php if($item['Status'] == 'In Stock'){ ?>
                        <span class="badge-instock">In Stock</span>
                    <?php } else { ?>
                        <span class="badge-outofstock">Out of Stock</span>
                    <?php } ?>
                </td>
                <td style="display:flex; gap:6px; align-items:center;">
                    <button class="btn-edit-sm"
                            onclick="toggleEditRow('edit-<?php echo $item['ItemID']; ?>')">Edit</button>
                    <a href="KitchenMenu.php?del_menu=<?php echo $item['ItemID']; ?>"
                       class="btn-del-sm"
                       onclick="return confirm('Delete this menu item?')"
                       style="text-decoration:none; padding:5px 12px; display:inline-block;">Delete</a>
                </td>
            </tr>
            <tr id="edit-<?php echo $item['ItemID']; ?>" style="display:none; background:#f0f9ff;">
                <td colspan="7">
                    <form action="KitchenMenu.php" method="POST"
                          style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; padding:8px;">
                        <input type="hidden" name="edit_menu" value="1">
                        <input type="hidden" name="ItemID" value="<?php echo $item['ItemID']; ?>">
                        <input type="text" name="Item" value="<?php echo $item['Item']; ?>"
                               style="width:110px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                        <select name="Category" style="padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                            <option value="Lunch"  <?php echo $item['Category']=='Lunch'  ?'selected':''; ?>>Lunch</option>
                            <option value="Drinks" <?php echo $item['Category']=='Drinks' ?'selected':''; ?>>Drinks</option>
                            <option value="Pastry" <?php echo $item['Category']=='Pastry' ?'selected':''; ?>>Pastry</option>
                        </select>
                        <input type="number" name="Price" value="<?php echo $item['Price']; ?>" step="0.01"
                               style="width:80px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                        <input type="number" name="Quantity" value="<?php echo $item['Quantity']; ?>"
                               style="width:70px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                        <button type="submit" class="btn-save-sm">Save</button>
                        <button type="button" class="btn-del-sm"
                                onclick="toggleEditRow('edit-<?php echo $item['ItemID']; ?>')">Cancel</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            <?php if(!$has_menu){ ?>
            <tr>
                <td colspan="7" class="empty-state">No menu items yet. Add your first item above.</td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleMenuForm(){
    var f = document.getElementById('menu-add-form');
    f.style.display = (f.style.display === 'none' || f.style.display === '') ? 'block' : 'none';
}
function toggleEditRow(id){
    var r = document.getElementById(id);
    r.style.display = (r.style.display === 'none' || r.style.display === '') ? 'table-row' : 'none';
}
</script>
</body>
</html>
