<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary of Withdrawals</title>
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
        padding: 10px 0;
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
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>
    <header>
        <nav id="home">
            <h1>Summary of Withdrawals</h1>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="Withdraw.php">Withdraw</a></li>
                <li><a href="Logout.php">Log out</a></li>
            </ul>
        </nav>
    </header>
    <!-- Summary of withdrawals -->
    <h2>Summary of Withdrawals</h2>
    <table border="1">
        <tr>
            <th>T_ID</th>
            <th>Type</th>
            <th>Total Quantity Withdrawn</th>
        </tr>
        <?php
        // Include database connection
        include "bd_conn.php";

        session_start();

        // Select data from withdraw table and group by T_ID
        $sql_summary = "SELECT withdraw.T_ID, cutting_tool.Type, SUM(withdraw.Out_qty) AS total_quantity FROM withdraw LEFT JOIN cutting_tool ON withdraw.T_ID = cutting_tool.T_ID WHERE withdraw.Employee_ID = '".$_SESSION['Employee_ID']."' GROUP BY withdraw.T_ID";
        $result_summary = mysqli_query($conn, $sql_summary);

        if ($result_summary && mysqli_num_rows($result_summary) > 0) {
            while ($row_summary = mysqli_fetch_assoc($result_summary)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row_summary["T_ID"]) . "</td>";
                echo "<td>" . htmlspecialchars($row_summary["Type"]) . "</td>";
                echo "<td>" . htmlspecialchars($row_summary["total_quantity"]) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No withdrawals yet</td></tr>";
        }
        ?>
    </table>
</body>

</html>