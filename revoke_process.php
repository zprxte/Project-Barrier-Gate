<?php
session_start();
require_once('connect.php');

// ตรวจสอบ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.html");
    exit();
}

// *** แก้ไข: รับค่า car_id และ user_id (เอา user_id ไว้เพื่อ Redirect กลับถูกหน้า) ***
if (isset($_GET['car_id']) && isset($_GET['user_id'])) {
    
    $target_car_id = $_GET['car_id'];
    $redirect_user_id = $_GET['user_id'];

    // *** แก้ไข SQL: เปลี่ยน WHERE จาก user_id เป็น car_id ***
    // เพื่อให้กระทบแค่รถคันเดียวที่เลือก ไม่ใช่รถทุกคันของคนนั้น
    $sql = "UPDATE car_info SET status_payment = 'Pending', privilege_parking = '0' WHERE car_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $target_car_id);
    
    if ($stmt->execute()) {
        // สำเร็จ: เด้งกลับไปหน้าค้นหา โดยส่ง keyword เป็น user_id เดิมกลับไปเพื่อให้หน้าเว็บแสดงผลคนเดิม
        header("Location: admin_check_data.php?keyword=" . urlencode($redirect_user_id)); 
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();

} else {
    // ถ้าข้อมูลไม่ครบ ให้กลับไปหน้าหลัก
    header("Location: admin_check_data.php");
}
$conn->close();
?>