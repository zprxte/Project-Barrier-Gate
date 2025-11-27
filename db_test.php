<?php
// Project_Y3/db_test.php

// เรียกใช้ไฟล์เชื่อมต่อ
require_once('connect.php'); 

// โค้ดสำหรับตรวจสอบผลลัพธ์
if ($conn) {
    echo "<h1>✅ เชื่อมต่อฐานข้อมูลสำเร็จ!</h1>";
    echo "<p>ยินดีด้วย! ฐานข้อมูลพร้อมใช้งาน ชื่อฐานข้อมูล: " . $conn->host_info . "</p>";
    $conn->close();
} else {
    // ในกรณีที่ connect.php มีปัญหา แต่โค้ดนี้ยังทำงานต่อได้
    echo "<h1>❌ เชื่อมต่อฐานข้อมูลล้มเหลว</h1>";
    echo "<p>โปรดตรวจสอบไฟล์ connect.php และสถานะ MySQL ใน XAMPP/WAMP</p>";
}
?>