<?php
session_start();
require_once('connect.php'); 
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $car_id = $_POST['car_id'];
    $action_type = $_POST['action_type']; 
    $current_time = date("Y-m-d H:i:s");

    if ($action_type == 'IN') {
        // --- กรณีขาเข้า (INSERT) ---
        
        // 1. ดึงข้อมูลผู้ใช้ (user_id) และสถานะการจ่ายเงินจากตาราง car_info
        // *** แก้ไข 1: เพิ่ม user_id เข้าไปใน SELECT ***
        $sql_check_car_info = "SELECT car_id, status_payment, user_id FROM car_info WHERE car_id = ?";
        
        $stmt_car_id = $conn->prepare($sql_check_car_info);
        $stmt_car_id->bind_param("s", $car_id);
        $stmt_car_id->execute();
        $result_car_id = $stmt_car_id->get_result();
        
        // 1.1 เช็คว่ามีรายชื่อในระบบไหม?
        if ($result_car_id->num_rows == 0) {
            echo "<script>
                    alert('❌ ไม่พบทะเบียนรถคันนี้ในระบบ (กรุณาลงทะเบียนก่อน)');
                    window.location.href='parking_form.php';
                  </script>";
            exit(); 
        }

        $row_car_id = $result_car_id->fetch_assoc();

        // *** แก้ไข 2: กำหนดค่าตัวแปร $user_id จากฐานข้อมูล ***
        $user_id = $row_car_id['user_id']; 

        // 1.2 เช็คว่าจ่ายเงินหรือยัง? 
        if ($row_car_id['status_payment'] != '1') {
            echo "<script>
                    alert('❌ สิทธิ์ถูกปฏิเสธ: คุณยังไม่ได้ชำระค่าบริการ');
                    window.location.href='parking_form.php';
                  </script>";
            exit(); 
        }
        
        // เช็คก่อนว่ารถคันนี้จอดค้างอยู่ไหม
        $check_sql = "SELECT id FROM parking_sessions WHERE car_id = ? AND status = 'PARKED'";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("s", $car_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('❌ รถคันนี้ จอดรถอยู่แล้ว (ยังไม่ได้กดออก)'); window.location.href='parking_form.php';</script>";
        } else {
            // 2. บันทึกเวลาเข้า (ตอนนี้ $user_id มีค่าแล้วจากการดึง DB)
            $sql = "INSERT INTO parking_sessions (user_id, car_id, time_in, status) VALUES (?, ?, ?, 'PARKED')";
            $stmt = $conn->prepare($sql);
            
            // sss = string, string, string
            $stmt->bind_param("sss", $user_id, $car_id, $current_time);
            
            if ($stmt->execute()) {
                echo "<script>alert('✅ บันทึกเวลาเข้าเรียบร้อย ($current_time)'); window.location.href='parking_form.php';</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
        }

    } elseif ($action_type == 'OUT') {
        // ... (โค้ดส่วนขาออก เหมือนเดิมถูกต้องแล้ว) ...
        $sql = "UPDATE parking_sessions 
                SET time_out = ?, status = 'COMPLETED' 
                WHERE car_id = ? AND status = 'PARKED'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $current_time, $car_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('✅ บันทึกเวลาออกเรียบร้อย ($current_time)'); window.location.href='parking_form.php';</script>";
        } else {
            echo "<script>alert('❌ ไม่พบข้อมูลการจอดรถของรหัสนี้ (อาจจะยังไม่กดเข้า หรือกดออกไปแล้ว)'); window.location.href='parking_form.php';</script>";
        }
    }

    $conn->close();
}
?>