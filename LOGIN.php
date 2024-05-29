<?php
session_start();
include "bd_conn.php";

if(isset($_POST['uname']) && isset($_POST['password'])){
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    if(empty($uname)){
        header("Location: indexx.php?error=User Name is required");
        exit();
    }else if(empty($pass)){
        header("Location: indexx.php?error=Password is required");
        exit();
    }else{
        $sql = "SELECT * FROM employees WHERE Employee_ID = '$uname' AND Password = '$pass'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) === 1){
            $row = mysqli_fetch_assoc($result);
            if($row['Employee_ID'] === $uname && $row['Password'] === $pass){
               $_SESSION['Employee_ID'] = $row['Employee_ID'];
               $_SESSION['Name'] = $row['Name'];
               $_SESSION['Position'] = $row['Position'];
               header("Location: Withdraw.php");
               exit();
            }else{ 
                header("Location: indexx.php?error=Incorect User name or password");
                exit();
            }
            print_r($row);
        }else{
            header("Location: indexx.php?error=Incorect User name or password");
            exit();
        }
        }
}else{
    header("Location: indexx.php");
    exit();
}