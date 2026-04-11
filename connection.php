<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "cafeteriareg";
$conn = mysqli_connect($host,$user,$password,$db);
if(!$conn){
    echo "Connection unsuccessful";
}
