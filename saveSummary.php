<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "bd_conn.php"; // เชื่อมต่อฐานข้อมูล

    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents('php://input'), true);
    $employee_id = $_SESSION['Employee_ID']; // รหัสพนักงานจาก session

    foreach ($data as $withdrawal) {
        $t_id = $withdrawal['t_id'];
        $quantity = $withdrawal['total_quantity'];

        // ตรวจสอบว่าจำนวนที่ถอนไม่เกินสต็อกที่มีอยู่
        $sql_check_stock = "SELECT stock FROM cutting_tool WHERE T_ID = '$t_id'";
        $result_check_stock = mysqli_query($conn, $sql_check_stock);
        $row_check_stock = mysqli_fetch_assoc($result_check_stock);
        $current_stock = $row_check_stock['stock'];

        if ($quantity <= $current_stock) {
            // เพิ่มข้อมูลการถอนลงในตาราง withdraw
            $sql_insert_withdraw = "INSERT INTO withdraw (T_ID, Employee_ID, Out_qty) VALUES ('$t_id', '$employee_id', '$quantity')";
            if (!mysqli_query($conn, $sql_insert_withdraw)) {
                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูลการถอน: " . mysqli_error($conn);
            } else {
                // อัปเดตสต็อกในตาราง cutting_tool
                $new_stock = $current_stock - $quantity;
                $sql_update_stock = "UPDATE cutting_tool SET stock = '$new_stock' WHERE T_ID = '$t_id'";
                if (!mysqli_query($conn, $sql_update_stock)) {
                    echo "เกิดข้อผิดพลาดในการอัปเดตสต็อก: " . mysqli_error($conn);
                }
            }
        } else {
            echo "จำนวนที่ถอนเกินสต็อกที่มีสำหรับ T_ID: $t_id";
        }
    }
    echo "Summary saved successfully.";
} else {
    echo "Invalid request method.";
}
?>