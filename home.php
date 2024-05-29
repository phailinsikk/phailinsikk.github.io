<?php
session_start();

if (isset($_SESSION['Name']) && isset( $_SESSION['Employee_ID'])){
    

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <style>
    /* CSS styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #434343;
        color: #fff;
        padding: 315px 0;
        text-align: center;
    }

    nav ul {
        list-style-type: none;
        padding: 0;
    }

    nav ul li {
        display: inline;
        margin-right: 20px;
    }

    nav ul li a {
        color: #fff;
        text-decoration: none;
    }

    nav ul li a:hover {
        text-decoration: underline;
    }

    /* Additional styles can be added as needed */
    </style>

</head>

<body>
    <header>
        <nav id="home">
            <h1>Cutting Tool Withdraw</h1>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="Withdraw.php">Withdraw</a></li>
                <li><a href="Logout.php">Log out</a></li>
            </ul>
        </nav>
    </header>
</body>

</html>

<?php
}else{
    header("Location: indexx.php");
    exit();
}
?>