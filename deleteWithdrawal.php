<?php
session_start();

if (isset($_SESSION['Name']) && isset($_SESSION['Employee_ID'])) {
    include "bd_conn.php"; // เชื่อมต่อฐานข้อมูล

    if (isset($_GET['t_id'])) {
        $t_id = $_GET['t_id'];
        
        // ลบข้อมูลการถอน
        $sql_delete_withdrawal = "DELETE FROM withdraw WHERE T_ID = '$t_id'";
        if (mysqli_query($conn, $sql_delete_withdrawal)) {
            // ถ้าลบสำเร็จ
            header("Location: Withdraw.php"); // redirect กลับไปยังหน้า Withdraw
            exit();
        } else {
            // ถ้าเกิดข้อผิดพลาดในการลบ
            echo "เกิดข้อผิดพลาดในการลบข้อมูลการถอน: " . mysqli_error($conn);
        }
    } else {
        // ถ้าไม่มีพารามิเตอร์ t_id ที่ส่งมา
        echo "ไม่สามารถทำการลบข้อมูลได้เนื่องจากไม่มีข้อมูลที่ต้องการลบ";
    }
} else {
    header("Location: indexx.php");
    exit();
}
?>