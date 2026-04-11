<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "CU"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$user_id = $_SESSION['UserID'];

// Check customer has at least one order
$has_orders = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS cnt FROM `order` WHERE UserID='$user_id'"
));

// Handle form submission
$success = false;
$error   = '';

if(isset($_POST['submit_feedback'])){
    $type    = $_POST['Type'];
    $message = trim($_POST['Message']);

    if(empty($message)){
        $error = 'Please write your feedback before submitting.';
    } elseif($has_orders['cnt'] == 0){
        $error = 'You need to have placed at least one order before submitting feedback.';
    } else {
        // Route based on type
        $chef_types  = ['Quality of Service', 'Food Complaint'];
        $goes_to     = in_array($type, $chef_types) ? 'Chef' : 'Admin';

        $type_safe    = mysqli_real_escape_string($conn, $type);
        $message_safe = mysqli_real_escape_string($conn, $message);

        mysqli_query($conn, "INSERT INTO feedback (UserID, Type, Message, GoesTo)
                             VALUES('$user_id','$type_safe','$message_safe','$goes_to')");
        $success = true;
    }
}
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilimandjaro - Feedback</title>
    <link rel="stylesheet" href="../Menu/TestMenuCss.css">
    <style>
        body { margin:0; padding:0; font-family:'Segoe UI',Roboto,sans-serif; }
        .background-blur { position:fixed; inset:0; z-index:-1; filter:blur(8px); }
        .background-image {
            width:100vw; height:100vh;
            background-image:url('../Image/145.jpg');
            background-size:cover; background-position:left;
        }
        .menu-title {
            position:fixed; top:20px; left:50%; transform:translateX(-50%);
            z-index:100; color:white; font-size:24px; font-weight:bold;
            text-shadow:2px 2px 8px rgba(0,0,0,0.5); letter-spacing:4px;
        }
        .welcome-bar {
            position:fixed; top:10px; right:20px; z-index:200;
            color:white; font-size:13px; display:flex; gap:15px; align-items:center;
        }
        .btn-link {
            display:inline-block; padding:6px 14px; border-radius:20px;
            font-size:12px; font-weight:600; text-decoration:none;
            border:1.5px solid rgba(255,255,255,0.7); color:white;
            background:rgba(255,255,255,0.1); transition:background 0.25s;
        }
        .btn-link:hover { background:rgba(255,255,255,0.3); }
        .container {
            background:rgba(248,248,248,0.95); width:55%;
            margin:100px auto 40px auto; border-radius:20px;
            padding:36px; backdrop-filter:blur(10px);
        }
        .page-title {
            font-size:22px; font-weight:bold; color:#1a1a2e;
            margin-bottom:6px;
        }
        .page-sub { font-size:13px; color:#64748b; margin-bottom:28px; }
        .form-group { margin-bottom:20px; }
        .form-group label {
            display:block; font-size:13px; font-weight:600;
            color:#374151; margin-bottom:8px;
        }
        .form-group select, .form-group textarea {
            width:100%; padding:10px 14px;
            border:1.5px solid #e2e8f0; border-radius:8px;
            font-size:13px; font-family:inherit;
            box-sizing:border-box; transition:border 0.2s;
        }
        .form-group select:focus, .form-group textarea:focus {
            border-color:#1a1a2e; outline:none;
        }
        .form-group textarea { height:140px; resize:vertical; }
        .type-hint {
            font-size:11px; color:#94a3b8; margin-top:5px;
        }
        .btn-submit {
            background:#1a1a2e; color:white; border:none;
            padding:12px 32px; border-radius:8px; font-size:14px;
            cursor:pointer; transition:background 0.3s; letter-spacing:1px;
        }
        .btn-submit:hover { background:#2d2d4e; }
        .alert-success {
            background:#dcfce7; border:1px solid #86efac; color:#15803d;
            padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:13px;
        }
        .alert-error {
            background:#fee2e2; border:1px solid #fca5a5; color:#dc2626;
            padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:13px;
        }
        .no-orders-msg {
            text-align:center; padding:40px; color:#94a3b8;
        }
        .btn-dark {
            display:inline-block; padding:10px 24px; border-radius:8px;
            font-size:13px; font-weight:600; text-decoration:none;
            border:1.5px solid #1a1a2e; color:#1a1a2e;
            background:transparent; transition:background 0.25s;
        }
        .btn-dark:hover { background:#f1f5f9; }
    </style>
</head>
<body>

<div class="background-blur"><div class="background-image"></div></div>
<div class="menu-title">K I L I M A N D J A R O</div>

<div class="welcome-bar">
    <a href="../Menu/Menu.php" class="btn-link">🍽️ Menu</a>
    <a href="../Auth/Logout.php" class="btn-link">Logout</a>
</div>

<div class="container">
    <div class="page-title">💬 Give Feedback</div>
    <div class="page-sub">Your feedback helps us improve. Food issues go to the Chef, system issues go to the Admin.</div>

    <?php if($success){ ?>
        <div class="alert-success">
            ✅ Thank you! Your feedback has been submitted successfully.
        </div>
        <a href="../Menu/Menu.php" class="btn-dark" style="margin-bottom:16px;">← Back to Menu</a>
    <?php } else if($has_orders['cnt'] == 0){ ?>
        <div class="no-orders-msg">
            <p style="font-size:15px; color:#64748b;">You need to place an order before submitting feedback.</p><br>
            <a href="../Menu/Menu.php" class="btn-dark">← Browse Menu</a>
        </div>
    <?php } else { ?>

        <?php if($error){ ?>
            <div class="alert-error">⚠️ <?php echo $error; ?></div>
        <?php } ?>

        <form action="Feedback.php" method="POST">
            <input type="hidden" name="submit_feedback" value="1">

            <div class="form-group">
                <label>Feedback Type</label>
                <select name="Type" id="feedbackType" onchange="updateHint()">
                    <option value="Bug Report">🐛 Bug Report</option>
                    <option value="Feature Request">💡 Feature Request</option>
                    <option value="Quality of Service">⭐ Quality of Service</option>
                    <option value="Food Complaint">🍽️ Food Complaint</option>
                </select>
                <div class="type-hint" id="typeHint">This will be sent to the Admin team.</div>
            </div>

            <div class="form-group">
                <label>Your Message</label>
                <textarea name="Message" placeholder="Describe your feedback in detail..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Feedback</button>
            <a href="../Menu/Menu.php" class="btn-dark" style="margin-left:12px;">Cancel</a>
        </form>

    <?php } ?>
</div>

<script>
function updateHint(){
    var type = document.getElementById('feedbackType').value;
    var hint = document.getElementById('typeHint');
    var foodTypes = ['Quality of Service', 'Food Complaint'];
    if(foodTypes.includes(type)){
        hint.textContent = 'This will be sent to the Kitchen team.';
        hint.style.color = '#f59e0b';
    } else {
        hint.textContent = 'This will be sent to the Admin team.';
        hint.style.color = '#94a3b8';
    }
}
</script>
</body>
</html>
