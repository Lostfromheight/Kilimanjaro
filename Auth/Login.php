<?php
include "LoginProcess.php";
?>

<!doctype HTML>
<head>
    <title>Kilimandjaro - Login</title>
<style>
body {
  margin: 0;
  padding: 15;
  font-family: 'Times New Roman', Times, serif;
  background-color: #000000;
  color: #FFFFFF;
  text-align: left;
}
form {
    margin: auto;
    width: 35%;
    background-color: rgba(20, 20, 20, 0.5);
    border: 1px solid #ccc;
    padding-left: 10px;
    padding-top: 60px;
    padding-bottom: 5%;
    padding-right: 20px;
}
.error-msg {
    text-align: center;
    color: #ff4444;
    font-size: 13px;
    margin: 4px 0;
}
.success-msg {
    text-align: center;
    color: #44ff88;
    font-size: 13px;
    margin: 4px 0;
}
.field-error {
    display: none;
    color: #ff4444;
    font-size: 12px;
    margin: 2px 0 4px 0;
}
label {
    display: inline-block;
    width: 110px;
    text-align: right;
    margin-right: 8px;
}
input[type="text"], input[type="password"], select {
    padding: 6px 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 13px;
    width: 200px;
}
select { background: white; color: #333; }
</style>
</head>
<body>

<BR><BR><BR>
<div style="text-align:center;"><font color="White"><b>K I L I M A N D J A R O &nbsp; Login</b></font></div>
<BR>

<!-- Show new registration ID -->
<?php if(isset($_GET['new_id'])){ ?>
    <div class="success-msg">
        Registration successful! Your User ID is: <b><?php echo $_GET['new_id']; ?></b><br>
        <small>Please write this down — you will need it to log in.</small>
    </div>
<?php } ?>

<!-- Error messages -->
<?php if(isset($_GET['error'])){ ?>
    <?php if($_GET['error'] == 'wrong'){ ?>
        <div class="error-msg">Incorrect ID or Password. Please try again.</div>
    <?php } elseif($_GET['error'] == 'no_role'){ ?>
        <div class="error-msg">Please select your role before logging in.</div>
    <?php } elseif($_GET['error'] == 'no_access'){ ?>
        <div class="error-msg">You must be logged in to access that page.</div>
    <?php } ?>
<?php } ?>

<BR>
<form id="loginForm" action="LoginProcess.php" method="post" style="text-align:center;">

    <label for="Role">Role:</label>
    <select name="Role" id="Role">
        <option value="">-- Select Role --</option>
        <option value="Customer" <?php echo (isset($_GET['role']) && $_GET['role']=='Customer') ? 'selected' : ''; ?>>Customer</option>
        <option value="Chef">Chef</option>
        <option value="Admin">Admin</option>
    </select>
    <br><span class="field-error" id="err_role"></span><br>

    <label for="UserID">User ID:</label>
    <input type="text" name="UserID" id="UserID" placeholder="Your assigned ID" />
    <br><span class="field-error" id="err_userid"></span><br>

    <label for="Password">Password:</label>
    <input type="password" name="Password" id="Password" placeholder="Enter password" />
    <span onclick="toggleVisibility('Password','eyePass')" id="eyePass" style="cursor:pointer; font-size:16px; margin-left:6px; vertical-align:middle; user-select:none;" title="Show/hide password">👁</span>
    <br><span class="field-error" id="err_password"></span><br><br>

    <input type="Submit" name="Submit" value="Login" />
    <input type="Reset" name="Reset" value="Clear" />

    <br><br>
    <a href="SignForm.php" style="color:#aaa; font-size:13px;">New here? Register first</a>
</form>

<div style="position:fixed; inset:0; z-index:-1; filter:blur(8px);">
    <div style="width:100vw; height:100vh; background-image:url('../Image/150.jpg'); background-size:cover; background-position:left;"></div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    var valid = true;

    var role = document.getElementById('Role');
    var errRole = document.getElementById('err_role');
    if (!role.value) {
        errRole.textContent = 'Please select your role';
        errRole.style.display = 'block';
        valid = false;
    } else {
        errRole.style.display = 'none';
    }

    var userId = document.getElementById('UserID');
    var errUserId = document.getElementById('err_userid');
    if (!userId.value.trim()) {
        errUserId.textContent = 'Please enter your User ID';
        errUserId.style.display = 'block';
        valid = false;
    } else {
        errUserId.style.display = 'none';
    }

    var password = document.getElementById('Password');
    var errPassword = document.getElementById('err_password');
    if (!password.value.trim()) {
        errPassword.textContent = 'Please enter your password';
        errPassword.style.display = 'block';
        valid = false;
    } else if (password.value.length < 8) {
        errPassword.textContent = 'Password must be at least 8 characters';
        errPassword.style.display = 'block';
        valid = false;
    } else {
        errPassword.style.display = 'none';
    }

    if (!valid) e.preventDefault();
});

function toggleVisibility(fieldId, iconId) {
    var field = document.getElementById(fieldId);
    var icon  = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.style.opacity = '0.5';
    } else {
        field.type = 'password';
        icon.style.opacity = '1';
    }
}
</script>
</body>
</HTML>
