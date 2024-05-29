<?php
session_start();

if (isset($_SESSION['Name']) && isset($_SESSION['Employee_ID'])) {
    include "bd_conn.php"; // เชื่อมต่อฐานข้อมูล

    if (isset($_POST['submit'])) { // ตรวจสอบว่ามีการส่งข้อมูลผ่านฟอร์มหรือไม่
        $quantity = $_POST['quantity']; // รับค่า quantity จากฟอร์ม
        $t_id = $_POST['T_ID']; // รับค่า T_ID จากฟอร์ม

        // เพิ่มข้อมูลการเบิกลงในตาราง withdraw
        $employee_id = $_SESSION['Employee_ID'];
        $withdraw_date = date("Y-m-d");
        $sql_insert_withdraw = "INSERT INTO withdraw (T_ID, Employee_ID, Out_qty, Date) VALUES ('$t_id', '$employee_id', '$quantity', '$withdraw_date')";

        if (mysqli_query($conn, $sql_insert_withdraw)) {
            echo "บันทึกข้อมูลการเบิกสำเร็จ";
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูลการเบิก: " . mysqli_error($conn);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DF Drill</title>
    <!-- CSS styles -->
    <style>
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

    form input[type='number'] {
        width: 60px;
    }

    form button {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    form button:hover {
        background-color: #45a049;
    }
    </style>
</head>

<body>
    <header>
        <nav id="home">
            <h1>Cutting Tool Withdraw</h1>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="Withdraw.php">Withdraw</a></li>
                <li><a href="summary_order.php">Summary Order</a></li>
                <li><a href="Logout.php">Log out</a></li>
            </ul>
        </nav>
    </header>
    <h1>Withdraw</h1>
    <form method="post">
        <label for="type">เลือกประเภท:</label>
        <select name="type" id="type">
            <option value="none">โปรดเลือกประเภท</option>
            <option value="CF Drill">CF Drill</option>
            <option value="C/B Drill">C/B Drill</option>
            <option value="Endmill Router">Endmill Router</option>
            <option value="CB Drill Reamer">CB Drill Reamer</option>
        </select>
        <button type="submit" name="submit_type">เลือก</button>
    </form>
    <table border="1">
        <tr>
            <th>T_ID</th>
            <th>Type</th>
            <th>Size</th>
            <th>Stock</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
        <?php
        if (isset($_POST['submit_type'])) {
            $selected_type = $_POST['type'];
            $sql = "SELECT * FROM cutting_tool WHERE Type = '$selected_type'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row["T_ID"] . "</td>";
                    echo "<td>" . $row["Type"] . "</td>";
                    echo "<td>" . $row["Size"] . "</td>";
                    echo "<td>" . $row["stock"] . "</td>";
                    echo "<td><form method='post'><input type='hidden' name='T_ID' value='" . $row["T_ID"] . "'><input type='number' name='quantity' min='1' max='" . $row["stock"] . "'></td>";
                    echo "<td><button type='submit' name='submit'>Submit</button></form></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No data found</td></tr>";
            }
        }
        ?>
    </table>

    <!-- Summary of withdrawals -->
    <h2>Summary of Withdrawals</h2>
    <table border="1">
        <tr>
            <th>T_ID</th>
            <th>Type</th>
            <th>Total Quantity Withdrawn</th>
        </tr>
        <?php
        // Select data from withdraw table and group by T_ID
        $sql_summary = "SELECT T_ID, SUM(Out_qty) AS total_quantity FROM withdraw GROUP BY T_ID";
        $result_summary = mysqli_query($conn, $sql_summary);

        if (mysqli_num_rows($result_summary) > 0) {
            while ($row_summary = mysqli_fetch_assoc($result_summary)) {
                // Select cutting tool type for each T_ID
                $t_id = $row_summary["T_ID"];
                $sql_type = "SELECT Type FROM cutting_tool WHERE T_ID = '$t_id'";
                $result_type = mysqli_query($conn, $sql_type);
                $row_type = mysqli_fetch_assoc($result_type);

                echo "<tr>";
                echo "<td>" . $row_summary["T_ID"] . "</td>";
                echo "<td>" . $row_type["Type"] . "</td>";
                echo "<td>" . $row_summary["total_quantity"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No withdrawals yet</td></tr>";
        }
        ?>
    </table>
</body>

</html>
<?php
}else{
    header("Location: indexx.php");
    exit();
}
?>