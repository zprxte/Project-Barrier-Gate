<?php
// Project_Y3/login_process.php
header('Content-Type: application/json');
session_start(); 

require_once('connect.php'); 

// ฟังก์ชันสำหรับส่ง Error Message
function sendError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    sendError("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
}

// 1. รับข้อมูลจากฟอร์ม
$username = $_POST['username'] ?? ''; 
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    sendError("กรุณากรอกรหัสบุคลากร/นักศึกษา และรหัสผ่าน");
}

// ----------------------------------------------------
// 2. ตรวจสอบว่าเป็น Admin (ส่วนนี้ยังคงใช้แบบ Plain Text ตามเดิม)
// ----------------------------------------------------
// หมายเหตุ: ถ้า Admin ไม่ได้ถูก Hash ตอนสร้าง ให้ใช้โค้ดเดิมตรงนี้
$stmt = $conn->prepare("SELECT admin_id FROM admin WHERE admin_id = ? AND password = ?");
$stmt->bind_param("ss", $username, $password); 
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($admin_id);
    $stmt->fetch();
    
    $_SESSION['user_id'] = $admin_id;
    $_SESSION['user_role'] = 'admin';

    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true, 
        'message' => 'เข้าสู่ระบบ Admin สำเร็จ',
        'redirect' => 'admin_dashboard.php'
    ]);
    exit();
}
$stmt->close();

$stmt = $conn->prepare("SELECT exec_id FROM executive WHERE exec_id = ? AND password = ?");
$stmt->bind_param("ss", $username, $password); 
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($exec_id);
    $stmt->fetch();
    
    $_SESSION['user_id'] = $exec_id;
    $_SESSION['user_role'] = 'executive';

    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true, 
        'message' => 'เข้าสู่ระบบ executive สำเร็จ',
        'redirect' => 'executive_dashboard.php'
    ]);
    exit();
}
$stmt->close();

// ----------------------------------------------------
// 3. ตรวจสอบ User ทั่วไป (ใช้ password_verify)
// ----------------------------------------------------

// *** แก้ไข SQL: ดึง password (ที่เป็น hash) ออกมาด้วย และลบเงื่อนไข AND password = ? ออก ***
$stmt = $conn->prepare("SELECT user_id, user_type, password FROM user WHERE user_id = ?");
$stmt->bind_param("s", $username); 
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    // พบ User ID นี้ในระบบ -> ดึงค่าออกมาตรวจสอบ
    $stmt->bind_result($user_id, $user_type, $hashed_password);
    $stmt->fetch();

    // *** ตรวจสอบรหัสผ่านที่กรอก ($password) กับ Hash ในฐานข้อมูล ($hashed_password) ***
    if (password_verify($password, $hashed_password)) {
        
        // รหัสถูกต้อง: ตั้งค่า Session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_role'] = 'user';
        $_SESSION['user_type'] = $user_type;

        $stmt->close();
        $conn->close();
        
        echo json_encode([
            'success' => true, 
            'message' => 'เข้าสู่ระบบสำเร็จ',
            'redirect' => 'user_dashboard.php' 
        ]);
        exit();

    } else {
        // รหัสผ่านไม่ถูกต้อง (User ID ถูก แต่ Password ผิด)
        $stmt->close();
        $conn->close();
        sendError("รหัสผ่านไม่ถูกต้อง");
    }

} else {
    // ไม่พบ User ID นี้เลย
    $stmt->close();
    $conn->close();
    sendError("ไม่พบข้อมูลผู้ใช้งาน หรือ รหัสผ่านไม่ถูกต้อง");
}
?>