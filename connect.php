<?php
// กำหนดตัวแปรสำหรับเชื่อมต่อฐานข้อมูล
$servername = "localhost";        // Hostname: โดยทั่วไปคือ localhost สำหรับ XAMPP
$username = "root";               // Username: เริ่มต้นของ XAMPP
$password = "";                   // Password: เริ่มต้นของ XAMPP (มักเป็นค่าว่าง)
$dbname = "barrier_gate_system";  // ชื่อฐานข้อมูล: ตามที่ระบุในไฟล์ user.sql

// สร้างการเชื่อมต่อแบบ Object-Oriented (MySQLi)
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    // ถ้าเชื่อมต่อไม่ได้ ให้หยุดการทำงานและแสดงข้อความ error
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าภาษา (เพื่อให้รองรับภาษาไทย)
$conn->set_charset("utf8");