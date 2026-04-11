<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "AD"){
    header('location:../Auth/Login.php?error=no_access'); exit();
}
include "../connection.php";

// Mark as read
if(isset($_GET['mark_read'])){
    $id = intval($_GET['mark_read']);
    mysqli_query($conn, "UPDATE feedback SET Status='Read' WHERE FeedbackID=$id");
    header('location:AdminFeedback.php'); exit();
}

$feedbacks = mysqli_query($conn, "
    SELECT f.*, r.First_name, r.Last_name
    FROM feedback f
    JOIN registration r ON f.UserID = r.UserID
    WHERE f.GoesTo = 'Admin'
    ORDER BY f.CreatedAt DESC
");
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Kilimandjaro - Admin Feedback</title>
    <link rel="stylesheet" href="AdminInterface.css">
    <style>
        .add-form { display:none; }
        .feedback-card {
            background: rgba(255,255,255,0.95); border-radius: 8px;
            padding: 18px 20px; margin-bottom: 14px;
            border-left: 4px solid #3b82f6;
        }
        .feedback-card.unread { border-left-color: #f59e0b; }
        .feedback-card.read   { border-left-color: #94a3b8; opacity: 0.8; }
        .fb-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
        .fb-type { font-size:13px; font-weight:700; color:#1a1a2e; }
        .fb-meta { font-size:11px; color:#64748b; }
        .fb-message { font-size:13px; color:#334155; margin-top:6px; line-height:1.6; }
        .fb-from { font-size:12px; color:#8b6f47; margin-top:8px; font-weight:600; }
        .badge-unread { background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
        .badge-read   { background:#f1f5f9; color:#64748b; padding:2px 8px; border-radius:10px; font-size:11px; }
        .mark-read-btn {
            background:#1a1a2e; color:white; border:none; padding:5px 12px;
            border-radius:4px; font-size:12px; cursor:pointer; text-decoration:none;
            display:inline-block; margin-top:10px;
        }
        .mark-read-btn:hover { background:#2d2d4e; }
        .empty-state { text-align:center; padding:40px; color:#94a3b8; }
    </style>
</head>
<body>
<div class="header">
    <h1>
        <a href="../Cafeteria.html" style="text-decoration:none; color:white; display:flex; align-items:center;">
            <img src="../Image/logo.png" width="150" height="50" alt="Logo">
        </a>
        <span style="margin-left:20px; font-size:18px;">Admin Interface</span>
    </h1>
    <div style="display:flex; align-items:center; gap:20px;">
        <span style="font-size:14px; color:#ccc;">Welcome, <?php echo $_SESSION['First_name']; ?>!</span>
        <a href="../Auth/Logout.php"><button class="logout-btn">Log-out</button></a>
    </div>
</div>

<div class="container">
    <div class="sidebar">
        <div class="sidebar-item">
            <a href="AdminUsers.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">👥</span><span>Users</span>
            </a>
        </div>
        <div class="sidebar-item">
            <a href="AdminReports.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">📊</span><span>Reports</span>
            </a>
        </div>
        <div class="sidebar-item active">
            <a href="AdminFeedback.php" style="text-decoration:none; color:white; display:flex; align-items:center; gap:15px; width:100%;">
                <span class="sidebar-icon">💬</span><span>Feedback</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h2>System Feedback</h2>
        </div>

        <?php
        $has = false;
        while($f = mysqli_fetch_assoc($feedbacks)){
            $has = true;
            $card_class = 'feedback-card ' . strtolower($f['Status']);
        ?>
        <div class="<?php echo $card_class; ?>">
            <div class="fb-header">
                <span class="fb-type">🐛 <?php echo $f['Type']; ?></span>
                <span>
                    <?php if($f['Status'] == 'Unread'){ ?>
                        <span class="badge-unread">Unread</span>
                    <?php } else { ?>
                        <span class="badge-read">Read</span>
                    <?php } ?>
                </span>
            </div>
            <div class="fb-message"><?php echo htmlspecialchars($f['Message']); ?></div>
            <div class="fb-from">From: <?php echo $f['First_name'] . ' ' . $f['Last_name']; ?></div>
            <div class="fb-meta"><?php echo date('d M Y, h:i A', strtotime($f['CreatedAt'])); ?></div>
            <?php if($f['Status'] == 'Unread'){ ?>
                <a href="AdminFeedback.php?mark_read=<?php echo $f['FeedbackID']; ?>" class="mark-read-btn">
                     Mark as Read
                </a>
            <?php } ?>
        </div>
        <?php } ?>

        <?php if(!$has){ ?>
        <div class="empty-state">No system feedback yet.</div>
        <?php } ?>
    </div>
</div>
</body>
</html>
