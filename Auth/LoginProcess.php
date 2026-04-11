<?php
session_start();
include "../connection.php";

if(isset($_POST['Submit'])){

    $UserID   = $_POST['UserID'];
    $Password = $_POST['Password'];
    $Role     = $_POST['Role'];

    //CHECK CORRECT TABLE BASED ON ROLE DROPDOWN

    if($Role == 'Customer'){
        $query  = "SELECT * FROM registration WHERE UserID='$UserID' AND Password='$Password'";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
            $_SESSION['UserID']     = $user['UserID'];
            $_SESSION['role']       = 'CU';
            $_SESSION['First_name'] = $user['First_name'];
            header('location:../Menu/Menu.php'); exit();
        } else {
            header('location:Login.php?error=wrong'); exit();
        }

    } elseif($Role == 'Chef'){
        $query  = "SELECT * FROM chef WHERE ChefID='$UserID' AND Password='$Password'";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
            $_SESSION['UserID']     = $user['ChefID'];
            $_SESSION['role']       = 'CH';
            $_SESSION['First_name'] = $user['First_name'];
            header('location:../Kitchen/Kitchen.php'); exit();
        } else {
            header('location:Login.php?error=wrong'); exit();
        }

    } elseif($Role == 'Admin'){
        $query  = "SELECT * FROM admin WHERE AdminID='$UserID' AND Password='$Password'";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
            $_SESSION['UserID']     = $user['AdminID'];
            $_SESSION['role']       = 'AD';
            $_SESSION['First_name'] = $user['First_name'];
            header('location:../Admin/AdminInterface.php'); exit();
        } else {
            header('location:Login.php?error=wrong'); exit();
        }

    } else {
        header('location:Login.php?error=no_role'); exit();
    }
}
?>
