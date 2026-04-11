<?php
include "CafeConnection.php";
?>

<!doctype HTML>
<head>
    <title>Kilimandjaro - Register</title>
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
select {
    background: white;
    color: #333;
}
</style>
</head>
<body>

<BR><BR><BR>
<div style="text-align:center;"><font color="White"><b>K I L I M A N D J A R O &nbsp; Registration</b></font></div>

<!-- Error messages -->
<?php if(isset($_GET['error'])){ ?>
    <?php if($_GET['error'] == 'letters_only'){ ?>
        <div class="error-msg" style="text-align:center;">Names must contain letters only — no numbers or special characters.</div>
    <?php } elseif($_GET['error'] == 'name_short'){ ?>
        <div class="error-msg" style="text-align:center;">First and Last name must be at least 2 characters each.</div>
    <?php } elseif($_GET['error'] == 'no_gender'){ ?>
        <div class="error-msg" style="text-align:center;">Please select a gender.</div>
    <?php } elseif($_GET['error'] == 'no_role'){ ?>
        <div class="error-msg" style="text-align:center;">Please select a role.</div>
    <?php } elseif($_GET['error'] == 'short_pass'){ ?>
        <div class="error-msg" style="text-align:center;">Password must be at least 6 characters.</div>
    <?php } elseif($_GET['error'] == 'chef_not_allowed'){ ?>
        <div class="error-msg" style="text-align:center;">Chef accounts can only be created by an Administrator.</div>
    <?php } elseif($_GET['error'] == 'duplicate_name'){ ?>
        <div class="error-msg" style="text-align:center;">An account with this name already exists. Please contact admin if this is your account.</div>
    <?php } ?>
<?php } ?>

<BR>
<form id="signForm" action="CafeConnection.php" method="post" style="text-align:center;">

    <label for="First_name">First Name:</label>
    <input type="text" name="First_name" id="First_name" placeholder="Enter first name">
    <br><span class="field-error" id="err_first_name"></span><br>

    <label for="Last_name">Last Name:</label>
    <input type="text" name="Last_name" id="Last_name" placeholder="Enter last name">
    <br><span class="field-error" id="err_last_name"></span><br>

    <label for="Gender">Gender:</label>
    <input type="radio" name="Gender" value="M" /> Male &nbsp;
    <input type="radio" name="Gender" value="F" /> Female
    <br><span class="field-error" id="err_gender"></span><br>

    <label for="Role">Role:</label>
    <select name="Role" id="Role">
        <option value="">-- Select Role --</option>
        <option value="Customer">Customer</option>
    </select>
    <br><span class="field-error" id="err_role"></span><br>

    <label for="Password">Password:</label>
    <input type="password" name="Password" id="Password" placeholder="Min. 8 characters">
    <span onclick="toggleVisibility('Password','eyePass')" id="eyePass" style="cursor:pointer; font-size:16px; margin-left:6px; vertical-align:middle; user-select:none;" title="Show/hide password">👁</span>
    <br><span class="field-error" id="err_password"></span><br>

    <label for="Confirm_Password">Confirm:</label>
    <input type="password" name="Confirm_Password" id="Confirm_Password" placeholder="Repeat password">
    <span onclick="toggleVisibility('Confirm_Password','eyeConfirm')" id="eyeConfirm" style="cursor:pointer; font-size:16px; margin-left:6px; vertical-align:middle; user-select:none;" title="Show/hide password">👁</span>
    <br><span class="field-error" id="err_confirm"></span><br><br>

    <input type="Submit" name="Submit" value="Register" />
    <input type="Reset" name="Reset" value="Clear" />

    <br><br>
    <a href="Login.php" style="color:#aaa; font-size:13px;">Already registered? Login here</a>
</form>

<div style="position:fixed; inset:0; z-index:-1; filter:blur(8px);">
    <div style="width:100vw; height:100vh; background-image:url('../Image/150.jpg'); background-size:cover; background-position:left;"></div>
</div>

<script>
document.getElementById('signForm').addEventListener('submit', function(e) {
    var valid = true;

    var firstName = document.getElementById('First_name');
    var errFirstName = document.getElementById('err_first_name');
    if (!firstName.value.trim()) {
        errFirstName.textContent = 'Please enter your first name';
        errFirstName.style.display = 'block';
        valid = false;
    } else {
        errFirstName.style.display = 'none';
    }

    var lastName = document.getElementById('Last_name');
    var errLastName = document.getElementById('err_last_name');
    if (!lastName.value.trim()) {
        errLastName.textContent = 'Please enter your last name';
        errLastName.style.display = 'block';
        valid = false;
    } else {
        errLastName.style.display = 'none';
    }

    var genderChecked = document.querySelector('input[name="Gender"]:checked');
    var errGender = document.getElementById('err_gender');
    if (!genderChecked) {
        errGender.textContent = 'Please select a gender';
        errGender.style.display = 'block';
        valid = false;
    } else {
        errGender.style.display = 'none';
    }

    var role = document.getElementById('Role');
    var errRole = document.getElementById('err_role');
    if (!role.value) {
        errRole.textContent = 'Please select a role';
        errRole.style.display = 'block';
        valid = false;
    } else {
        errRole.style.display = 'none';
    }

    var password = document.getElementById('Password');
    var errPassword = document.getElementById('err_password');
    if (!password.value.trim()) {
        errPassword.textContent = 'Please enter a password';
        errPassword.style.display = 'block';
        valid = false;
    } else if (password.value.length < 8) {
        errPassword.textContent = 'Password must be at least 8 characters';
        errPassword.style.display = 'block';
        valid = false;
    } else {
        errPassword.style.display = 'none';
    }

    var confirm = document.getElementById('Confirm_Password');
    var errConfirm = document.getElementById('err_confirm');
    if (!confirm.value.trim()) {
        errConfirm.textContent = 'Please confirm your password';
        errConfirm.style.display = 'block';
        valid = false;
    } else {
        errConfirm.style.display = 'none';
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
