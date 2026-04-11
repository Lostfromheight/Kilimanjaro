<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "AD"){
    header('location:../Auth/Login.php?error=no_access');
    exit();
}
// Default admin landing page — redirect to users
header('location:AdminUsers.php');
exit();
?>
