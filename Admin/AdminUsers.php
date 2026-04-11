<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "AD"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";
include "AdminProcess.php";

$customers = mysqli_query($conn,"SELECT * FROM registration");
$chefs     = mysqli_query($conn,"SELECT * FROM chef");
$admins    = mysqli_query($conn,"SELECT * FROM admin");
?>
<!doctype HTML>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - User Management</title>
    <link rel="stylesheet" href="AdminInterface.css">
    <style>
        .add-form { display: none; }
        .add-form.open { display: block; background: rgba(255,255,255,0.95); color: #333; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .add-form input, .add-form select { padding: 8px; margin: 6px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; }
        .add-form button { padding: 8px 16px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 6px 4px; }
        .add-form button:hover { background: #333; }
        .user-section-title { font-size: 18px; letter-spacing: 1px; margin: 24px 0 12px 0; padding-bottom: 6px; border-bottom: 2px solid rgba(255,255,255,0.3); }
        .user-section-title:first-child { margin-top: 0; }
        .alert-success { background: rgba(76,175,80,0.2); border: 1px solid #4caf50; color: #fff; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
        .alert-info    { background: rgba(255,193,7,0.2);  border: 1px solid #ffc107; color: #fff; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
        .alert-danger  { background: rgba(244,67,54,0.2);  border: 1px solid #f44336; color: #fff; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>
        <a href="../Cafeteria.html" style="text-decoration:none; color:white; display:flex; align-items:center;">
            <img src="../Image/logo.png" width="150" height="50" alt="Kilimandjaro Logo" style="cursor:pointer;"/>
        </a>
        <span style="text-decoration:none; color:white; margin-left:20px; font-size:18px;">Admin Interface</span>
    </h1>
    <div style="display:flex; align-items:center; gap:20px;">
        <span style="font-size:14px; color:#ccc;">Welcome, <?php echo $_SESSION['First_name']; ?>!</span>
        <a href="../Auth/Logout.php"><button class="logout-btn">Log-out</button></a>
    </div>
</div>

<!-- Main Container -->
<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-item active">
            <a href="AdminUsers.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">👥</span><span>Users</span>
            </a>
        </div>
        <div class="sidebar-item">
            <a href="AdminReports.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">📊</span><span>Reports</span>
            </a>
        </div>
        <div class="sidebar-item">
            <a href="AdminFeedback.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">💬</span><span>Feedback</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h2>User Management</h2>
        </div>

        <!-- Messages -->
        <?php if(isset($_GET['msg'])){ ?>
            <?php if($_GET['msg'] == 'chef_added' && isset($_GET['chef_id'])){ ?>
                <div class="alert-info">
                    Chef account created. Their login ID is: <strong><?php echo $_GET['chef_id']; ?></strong> — share this with them directly.
                </div>
            <?php } elseif($_GET['msg'] == 'customer_added'){ ?>
                <div class="alert-success">Customer added successfully.</div>
            <?php } elseif($_GET['msg'] == 'admin_added'){ ?>
                <div class="alert-success">Admin account created successfully.</div>
            <?php } elseif($_GET['msg'] == 'updated'){ ?>
                <div class="alert-success">Record updated successfully.</div>
            <?php } elseif($_GET['msg'] == 'deleted'){ ?>
                <div class="alert-danger">Record deleted.</div>
            <?php } ?>
        <?php } ?>

        <!-- ── CUSTOMERS ── -->
        <div class="user-section-title">👤 Customers</div>
        <button class="add-btn" onclick="toggleForm('add-customer-form')" style="margin-bottom:10px;">+ Add Customer</button>
        <div class="add-form" id="add-customer-form">
            <strong>Add New Customer</strong><br><br>
            <form action="AdminUsers.php" method="POST">
                <input type="hidden" name="add_customer" value="1">
                <label>First Name:</label>
                <input type="text" name="First_name" required placeholder="First name">
                <label>Last Name:</label>
                <input type="text" name="Last_name" required placeholder="Last name">
                <label>Gender:</label>
                <select name="Gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
                <label>Password:</label>
                <input type="password" name="Password" required placeholder="Password">
                <button type="submit">Save</button>
                <button type="button" onclick="toggleForm('add-customer-form')">Cancel</button>
            </form>
        </div>

        <div class="table-container" style="margin-bottom:24px;">
            <table>
                <thead>
                    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php while($u = mysqli_fetch_assoc($customers)){ ?>
                    <tr>
                        <td><?php echo $u['UserID']; ?></td>
                        <td><?php echo $u['First_name']; ?></td>
                        <td><?php echo $u['Last_name']; ?></td>
                        <td><?php echo $u['Gender']=='M' ? 'Male' : 'Female'; ?></td>
                        <td class="action-links">
                            <a href="#" onclick="toggleEdit('cu-<?php echo $u['UserID']; ?>')">Edit</a>
                            <span> | </span>
                            <a href="AdminUsers.php?del_customer=<?php echo $u['UserID']; ?>"
                               class="delete" onclick="return confirm('Delete this customer?')">Delete</a>
                        </td>
                    </tr>
                    <tr id="cu-<?php echo $u['UserID']; ?>" style="display:none; background:#f0f4ff;">
                        <td colspan="5">
                            <form action="AdminUsers.php" method="POST"
                                  style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; padding:8px;">
                                <input type="hidden" name="edit_customer" value="1">
                                <input type="hidden" name="UserID" value="<?php echo $u['UserID']; ?>">
                                <input type="text" name="First_name" value="<?php echo $u['First_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <input type="text" name="Last_name" value="<?php echo $u['Last_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <select name="Gender" style="padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="M" <?php echo $u['Gender']=='M'?'selected':''; ?>>Male</option>
                                    <option value="F" <?php echo $u['Gender']=='F'?'selected':''; ?>>Female</option>
                                </select>
                                <button type="submit" style="padding:5px 12px; background:#2196f3; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Save</button>
                                <button type="button" onclick="toggleEdit('cu-<?php echo $u['UserID']; ?>')"
                                        style="padding:5px 12px; background:#999; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- ── CHEFS ── -->
        <div class="user-section-title">👨‍🍳 Chefs</div>
        <button class="add-btn" onclick="toggleForm('add-chef-form')" style="margin-bottom:10px;">+ Add Chef</button>
        <div class="add-form" id="add-chef-form">
            <strong>Add New Chef</strong><br>
            <small style="color:#666;">The Chef's login ID will appear after saving — share it with them.</small><br><br>
            <form action="AdminUsers.php" method="POST">
                <input type="hidden" name="add_chef" value="1">
                <label>First Name:</label>
                <input type="text" name="First_name" required placeholder="First name">
                <label>Last Name:</label>
                <input type="text" name="Last_name" required placeholder="Last name">
                <label>Gender:</label>
                <select name="Gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
                <label>Password:</label>
                <input type="password" name="Password" required placeholder="Password">
                <button type="submit">Save</button>
                <button type="button" onclick="toggleForm('add-chef-form')">Cancel</button>
            </form>
        </div>

        <div class="table-container" style="margin-bottom:24px;">
            <table>
                <thead>
                    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php while($c = mysqli_fetch_assoc($chefs)){ ?>
                    <tr>
                        <td><?php echo $c['ChefID']; ?></td>
                        <td><?php echo $c['First_name']; ?></td>
                        <td><?php echo $c['Last_name']; ?></td>
                        <td><?php echo $c['Gender']=='M' ? 'Male' : 'Female'; ?></td>
                        <td class="action-links">
                            <a href="#" onclick="toggleEdit('ch-<?php echo $c['ChefID']; ?>')">Edit</a>
                            <span> | </span>
                            <a href="AdminUsers.php?del_chef=<?php echo $c['ChefID']; ?>"
                               class="delete" onclick="return confirm('Delete this chef?')">Delete</a>
                        </td>
                    </tr>
                    <tr id="ch-<?php echo $c['ChefID']; ?>" style="display:none; background:#f0f4ff;">
                        <td colspan="5">
                            <form action="AdminUsers.php" method="POST"
                                  style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; padding:8px;">
                                <input type="hidden" name="edit_chef" value="1">
                                <input type="hidden" name="ChefID" value="<?php echo $c['ChefID']; ?>">
                                <input type="text" name="First_name" value="<?php echo $c['First_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <input type="text" name="Last_name" value="<?php echo $c['Last_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <select name="Gender" style="padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="M" <?php echo $c['Gender']=='M'?'selected':''; ?>>Male</option>
                                    <option value="F" <?php echo $c['Gender']=='F'?'selected':''; ?>>Female</option>
                                </select>
                                <button type="submit" style="padding:5px 12px; background:#2196f3; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Save</button>
                                <button type="button" onclick="toggleEdit('ch-<?php echo $c['ChefID']; ?>')"
                                        style="padding:5px 12px; background:#999; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- ── ADMINS ── -->
        <div class="user-section-title">🔑 Admins</div>
        <button class="add-btn" onclick="toggleForm('add-admin-form')" style="margin-bottom:10px;">+ Add Admin</button>
        <div class="add-form" id="add-admin-form">
            <strong>Add New Admin</strong><br><br>
            <form action="AdminUsers.php" method="POST">
                <input type="hidden" name="add_admin" value="1">
                <label>First Name:</label>
                <input type="text" name="First_name" required placeholder="First name">
                <label>Last Name:</label>
                <input type="text" name="Last_name" required placeholder="Last name">
                <label>Gender:</label>
                <select name="Gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
                <label>Password:</label>
                <input type="password" name="Password" required placeholder="Password">
                <button type="submit">Save</button>
                <button type="button" onclick="toggleForm('add-admin-form')">Cancel</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php while($a = mysqli_fetch_assoc($admins)){ ?>
                    <tr>
                        <td><?php echo $a['AdminID']; ?></td>
                        <td><?php echo $a['First_name']; ?></td>
                        <td><?php echo $a['Last_name']; ?></td>
                        <td><?php echo $a['Gender']=='M' ? 'Male' : 'Female'; ?></td>
                        <td class="action-links">
                            <a href="#" onclick="toggleEdit('ad-<?php echo $a['AdminID']; ?>')">Edit</a>
                            <span> | </span>
                            <a href="AdminUsers.php?del_admin=<?php echo $a['AdminID']; ?>"
                               class="delete" onclick="return confirm('Delete this admin?')">Delete</a>
                        </td>
                    </tr>
                    <tr id="ad-<?php echo $a['AdminID']; ?>" style="display:none; background:#f0f4ff;">
                        <td colspan="5">
                            <form action="AdminUsers.php" method="POST"
                                  style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; padding:8px;">
                                <input type="hidden" name="edit_admin" value="1">
                                <input type="hidden" name="AdminID" value="<?php echo $a['AdminID']; ?>">
                                <input type="text" name="First_name" value="<?php echo $a['First_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <input type="text" name="Last_name" value="<?php echo $a['Last_name']; ?>"
                                       style="width:100px; padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                <select name="Gender" style="padding:5px; font-size:12px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="M" <?php echo $a['Gender']=='M'?'selected':''; ?>>Male</option>
                                    <option value="F" <?php echo $a['Gender']=='F'?'selected':''; ?>>Female</option>
                                </select>
                                <button type="submit" style="padding:5px 12px; background:#2196f3; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Save</button>
                                <button type="button" onclick="toggleEdit('ad-<?php echo $a['AdminID']; ?>')"
                                        style="padding:5px 12px; background:#999; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div><!-- end main-content -->
</div><!-- end container -->

<script>
function toggleForm(id){
    document.getElementById(id).classList.toggle('open');
}
function toggleEdit(id){
    var r = document.getElementById(id);
    r.style.display = (r.style.display === 'none' || r.style.display === '') ? 'table-row' : 'none';
}
</script>
</body>
</HTML>
