<?php
if(!isset($_SESSION['role']) || $_SESSION['role'] != "AD"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}

include "../connection.php";

$section = isset($_GET['section']) ? $_GET['section'] : 'users';

// USERS
if($section == 'users' || isset($_POST['add_customer']) || isset($_POST['edit_customer']) || isset($_GET['del_customer'])
                        || isset($_POST['add_chef'])     || isset($_POST['edit_chef'])     || isset($_GET['del_chef'])
                        || isset($_POST['add_admin'])    || isset($_POST['edit_admin'])    || isset($_GET['del_admin'])){

    // Customer CRUD
    if(isset($_POST['add_customer'])){
        $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender']; $p=$_POST['Password'];
        mysqli_query($conn,"INSERT INTO registration (First_name,Last_name,Gender,Password) VALUES('$fn','$ln','$g','$p')");
        header('location:AdminUsers.php?msg=customer_added'); exit();
    }
    if(isset($_POST['edit_customer'])){
        $id=$_POST['UserID']; $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender'];
        mysqli_query($conn,"UPDATE registration SET First_name='$fn',Last_name='$ln',Gender='$g' WHERE UserID='$id'");
        header('location:AdminUsers.php?msg=updated'); exit();
    }
    if(isset($_GET['del_customer'])){
        mysqli_query($conn,"DELETE FROM registration WHERE UserID='".$_GET['del_customer']."'");
        header('location:AdminUsers.php?msg=deleted'); exit();
    }

    // Chef CRUD
    if(isset($_POST['add_chef'])){
        $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender']; $p=$_POST['Password'];
        mysqli_query($conn,"INSERT INTO chef (First_name,Last_name,Gender,Password) VALUES('$fn','$ln','$g','$p')");
        $new_chef_id = mysqli_insert_id($conn);
        header("location:AdminUsers.php?msg=chef_added&chef_id=$new_chef_id"); exit();
    }
    if(isset($_POST['edit_chef'])){
        $id=$_POST['ChefID']; $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender'];
        mysqli_query($conn,"UPDATE chef SET First_name='$fn',Last_name='$ln',Gender='$g' WHERE ChefID='$id'");
        header('location:AdminUsers.php?msg=updated'); exit();
    }
    if(isset($_GET['del_chef'])){
        mysqli_query($conn,"DELETE FROM chef WHERE ChefID='".$_GET['del_chef']."'");
        header('location:AdminUsers.php?msg=deleted'); exit();
    }

    // Admin CRUD
    if(isset($_POST['add_admin'])){
        $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender']; $p=$_POST['Password'];
        mysqli_query($conn,"INSERT INTO admin (First_name,Last_name,Gender,Password) VALUES('$fn','$ln','$g','$p')");
        header('location:AdminUsers.php?msg=admin_added'); exit();
    }
    if(isset($_POST['edit_admin'])){
        $id=$_POST['AdminID']; $fn=$_POST['First_name']; $ln=$_POST['Last_name']; $g=$_POST['Gender'];
        mysqli_query($conn,"UPDATE admin SET First_name='$fn',Last_name='$ln',Gender='$g' WHERE AdminID='$id'");
        header('location:AdminUsers.php?msg=updated'); exit();
    }
    if(isset($_GET['del_admin'])){
        mysqli_query($conn,"DELETE FROM admin WHERE AdminID='".$_GET['del_admin']."'");
        header('location:AdminUsers.php?msg=deleted'); exit();
    }

    $customers = mysqli_query($conn,"SELECT * FROM registration");
    $chefs     = mysqli_query($conn,"SELECT * FROM chef");
    $admins    = mysqli_query($conn,"SELECT * FROM admin");
}

// Reports are handled in AdminReports.php

?>
