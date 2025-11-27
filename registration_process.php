<?php
header('Content-Type: application/json');
require_once('connect.php'); // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

function sendResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

// รับค่าจากฟอร์ม
$user_type = $_POST['user_type'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// 1. ตรวจสอบค่าว่าง
if (empty($user_type) || empty($user_id) || empty($password)) {
    sendResponse(false, 'กรุณากรอกข้อมูลให้ครบถ้วน');
}

// 2. ตรวจสอบรหัสผ่านตรงกัน
if ($password !== $confirm) {
    sendResponse(false, 'รหัสผ่านไม่ตรงกัน');
}

// 3. ตรวจสอบว่า user_id ซ้ำหรือไม่
$stmt = $conn->prepare("SELECT user_id FROM user WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse(false, 'รหัสนี้ลงทะเบียนไปแล้ว');
}
$stmt->close();

// --- [จุดที่แก้ไข] สร้าง Hash Password ---
// ใช้ PASSWORD_DEFAULT (ปัจจุบันคือ bcrypt) ซึ่งปลอดภัยมาก
$password_hash = password_hash($password, PASSWORD_DEFAULT);


// 4. บันทึกข้อมูลลง Table: user
$sql = "INSERT INTO user (user_id, user_type, password) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    // --- [จุดที่แก้ไข] ส่งตัวแปร $password_hash แทน $password เดิม ---
    $stmt->bind_param("sss", $user_id, $user_type, $password_hash);
    
    if ($stmt->execute()) {
        sendResponse(true, 'ลงทะเบียนสำเร็จ');
    } else {
        sendResponse(false, 'SQL Error: ' . $stmt->error);
    }
    $stmt->close();
} else {
    sendResponse(false, 'Prepare Error: ' . $conn->error);
}

$conn->close();
?>