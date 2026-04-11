<?php
include "../connection.php";

if(isset($_POST['Submit'])){

    $First_name       = trim($_POST['First_name']);
    $Last_name        = trim($_POST['Last_name']);
    $Gender           = $_POST['Gender'];
    $Role             = $_POST['Role'];
    $Password         = $_POST['Password'];
    $Confirm_Password = $_POST['Confirm_Password'];

    // VALIDATION 

    // Names must be at least 2 characters
    if(strlen($First_name) < 2 || strlen($Last_name) < 2){
        header('location:SignForm.php?error=name_short'); exit();
    }

    // Names must contain letters only (spaces allowed for double names)
    if(!preg_match('/^[a-zA-Z\s]+$/', $First_name) || !preg_match('/^[a-zA-Z\s]+$/', $Last_name)){
        header('location:SignForm.php?error=letters_only'); exit();
    }

    // Gender must be selected
    if(empty($Gender)){
        header('location:SignForm.php?error=no_gender'); exit();
    }

    // Role must be selected
    if(empty($Role)){
        header('location:SignForm.php?error=no_role'); exit();
    }

    // Chef registration not allowed through public form
    if($Role == 'Chef'){
        header('location:SignForm.php?error=chef_not_allowed'); exit();
    }

    // Password minimum 8 characters
    if(strlen($Password) < 8){
        header('location:SignForm.php?error=short_pass'); exit();
    }

    // Passwords must match
    if($Password !== $Confirm_Password){
        header('location:SignForm.php?error=pass_mismatch'); exit();
    }

    // DUPLICATE NAME CHECK

    $safe_first = mysqli_real_escape_string($conn, $First_name);
    $safe_last  = mysqli_real_escape_string($conn, $Last_name);
    $dup_query  = "SELECT UserID FROM registration WHERE First_name='$safe_first' AND Last_name='$safe_last'";
    $dup_result = mysqli_query($conn, $dup_query);
    if($dup_result && mysqli_num_rows($dup_result) > 0){
        header('location:SignForm.php?error=duplicate_name'); exit();
    }

    // INSERT INTO CORRECT TABLE

    if($Role == 'Customer'){
        $query = "INSERT INTO registration (First_name, Last_name, Gender, Password)
                  VALUES('$safe_first','$safe_last','$Gender','$Password')";
        mysqli_query($conn, $query);

        // Get the auto-generated ID
        $new_id = mysqli_insert_id($conn);

        // Redirect to login with the new ID so customer can see it
        header("location:Login.php?new_id=$new_id&role=Customer");
        exit();
    }
}
?>
