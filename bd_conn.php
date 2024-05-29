<?php

$hostname = "localhost";
$uname = "b6308193";
$password = "Irin32613";

$db_name="b6308193db";

$conn = mysqli_connect($hostname, $uname, $password, $db_name);

if(!$conn){
    echo "Connection failed!";
}