<?php
session_start();

if (isset($_SESSION['Name']) && isset($_SESSION['Employee_ID'])) {
    include "bd_conn.php"; // เชื่อมต่อฐานข้อมูล

    // ตรวจสอบว่ามีการส่งข้อมูลฟอร์มหรือไม่
    if (isset($_POST['submit']) && isset($_POST['quantity']) && isset($_POST['T_ID'])) {
        // รับค่าจำนวนและ T_ID จากฟอร์ม
        $quantity = $_POST['quantity'];
        $t_id = $_POST['T_ID'];
        $employee_id = $_SESSION['Employee_ID']; // รหัสพนักงานจาก session

        // ตรวจสอบว่าจำนวนที่ถอนไม่เกินสต็อกที่มีอยู่
        $sql_check_stock = "SELECT stock FROM cutting_tool WHERE T_ID = '$t_id'";
        $result_check_stock = mysqli_query($conn, $sql_check_stock);
        $row_check_stock = mysqli_fetch_assoc($result_check_stock);
        $current_stock = $row_check_stock['stock'];

        if ($quantity <= $current_stock) {
            // เพิ่มข้อมูลการถอนลงในตาราง withdraw
            $sql_insert_withdraw = "INSERT INTO withdraw (T_ID, Employee_ID, Out_qty) VALUES ('$t_id', '$employee_id', '$quantity')";

            // เพิ่มข้อมูลลงในตาราง summary_order
            $sql_insert_summary_order = "INSERT INTO summary_order (T_ID, Employee_ID, Out_qty) VALUES ('$t_id', '$employee_id', '$quantity')";

            // ทำการ execute คำสั่ง SQL
            if (mysqli_query($conn, $sql_insert_withdraw) && mysqli_query($conn, $sql_insert_summary_order)) {
                echo "บันทึกข้อมูลการถอนและ Summary Order เรียบร้อยแล้ว";
            } else {
                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
            }
        } else {
            echo "จำนวนที่ถอนเกินสต็อกที่มี";
        }
    } else {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
    
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DF Drill</title>
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

    /* Additional styles for popup */
    .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 999;
    }

    #popup-qrcode {
        margin-bottom: 20px;
    }

    .popup button {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .popup button:hover {
        background-color: #45a049;
    }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <header>
        <nav id="home">
            <h1>Cutting Tool Withdraw</h1>
            <ul>
                <li><a href="Logout.php">Log out</a></li>
            </ul>
        </nav>
    </header>
    <h1>Withdraw</h1>
    <form method="post">
        <label for="type">Select Type:</label>
        <select name="type" id="type">
            <option value="none">Please select type</option>
            <option value="CF Drill">CF Drill</option>
            <option value="C/B Drill">C/B Drill</option>
            <option value="Endmill Router">Endmill Router</option>
            <option value="CB Drill Reamer">CB Drill Reamer</option>
        </select>
        <button type="submit" name="submit_type">Select</button>
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
            <th>Latest Withdrawal Date</th>
            <th>Action</th>

        </tr>
        <?php
        // เลือกข้อมูลจากตาราง withdraw และจัดกลุ่มตาม T_ID
        $sql_summary = "SELECT withdraw.T_ID, cutting_tool.Type, SUM(withdraw.Out_qty) AS total_quantity, MAX(withdraw.Date) AS latest_withdrawal_date FROM withdraw LEFT JOIN cutting_tool ON withdraw.T_ID = cutting_tool.T_ID WHERE withdraw.Employee_ID = '" . $_SESSION['Employee_ID'] . "' GROUP BY withdraw.T_ID, DATE(withdraw.Date)";

        $result_summary = mysqli_query($conn, $sql_summary);

        if (mysqli_num_rows($result_summary) > 0) {
            while ($row_summary = mysqli_fetch_assoc($result_summary)) {
                echo "<tr>";
echo "<td>" . $row_summary["T_ID"] . "</td>";
echo "<td>" . $row_summary["Type"] . "</td>";
echo "<td>" . $row_summary["total_quantity"] . "</td>";
echo "<td>" . $row_summary["latest_withdrawal_date"] . "</td>";
echo "<td><button onclick=\"deleteWithdrawal('" . $row_summary["T_ID"] . "')\">Delete</button></td>"; 
                echo "</tr>";

            }
        } else {
            echo "<tr><td colspan='5'>No withdrawals yet</td></tr>";
        }
        ?>
    </table>

    <!-- Summary Order button -->
    <button type="button" onclick="summaryOrder()">Summary Order</button>

    <div id="qrcode"></div>
    <script>
    function deleteWithdrawal(t_id) {
        // ยืนยันการลบ
        if (confirm("คุณแน่ใจหรือไม่ที่ต้องการลบการถอนนี้?")) {
            // ส่ง T_ID ไปยัง deleteWithdrawal.php เพื่อลบข้อมูล
            window.location.href = 'deleteWithdrawal.php?t_id=' + t_id;
        }
    }
    </script>

    <script>
    function clearOrder(button) {
        // หาแถวของปุ่มที่ถูกคลิก
        const row = button.parentNode.parentNode;
        // ลบแถว
        row.parentNode.removeChild(row);
    }






    function showQRCodePopup(qrCodeData) {
        // สร้าง element div สำหรับ popup
        const popup = document.createElement('div');
        popup.classList.add('popup');

        // สร้าง element div สำหรับแสดง QR code
        const qrCodeDiv = document.createElement('div');
        qrCodeDiv.setAttribute('id', 'popup-qrcode');

        // สร้าง element button สำหรับปุ่ม OK
        const okButton = document.createElement('button');
        okButton.textContent = 'OK';

        // เพิ่ม QR code เข้าไปใน div ของ popup
        popup.appendChild(qrCodeDiv);

        // เพิ่มปุ่ม OK เข้าไปใน div ของ popup
        popup.appendChild(okButton);

        // เพิ่ม popup เข้าไปใน body
        document.body.appendChild(popup);

        // สร้าง QR code ใน div ที่เราสร้างขึ้นมา
        new QRCode(document.getElementById('popup-qrcode'), qrCodeData);

        // เมื่อคลิกที่ปุ่ม OK
        okButton.addEventListener('click', function() {
            // ลบ popup ออกจาก DOM
            document.body.removeChild(popup);
        });
    }

    function summaryOrder() {
        // สร้างข้อมูลจาก "Summary of Withdrawals" เพื่อสร้าง QR Code
        let summaryData = "";

        const rows = document.querySelectorAll("table")[1].rows; // ตาราง Summary of Withdrawals เป็นตารางที่สอง
        for (let i = 1; i < rows.length; i++) { // เริ่มจาก 1 เพื่อข้ามแถวหัวตาราง
            const cells = rows[i].cells;
            if (cells.length === 5) {
                const t_id = cells[0].innerText;
                const total_quantity = cells[2].innerText; // ค่าจำนวนที่ถูกถอนอยู่ในคอลัมน์ที่สาม
                summaryData +=
                    `${t_id} : ${total_quantity}\n`; // เพิ่มข้อมูลจำนวนที่ถูกถอนลงใน summaryData
            }
        }

        // แสดง popup ที่มี QR code
        showQRCodePopup(summaryData);

        // เรียกใช้ฟังก์ชันเพื่อเคลียร์ข้อมูลในตาราง "Summary of Withdrawals"
        clearSummary();
    }
    // เมื่อคลิกปุ่มส่งข้อมูล
    </script>



</body>

</html>
<?php
 } else {
    header("Location: indexx.php");
    exit();
 }
?>