<?php
session_start();

// 1. ล้างตัวแปร session ทั้งหมด
session_unset(); 

// 2. ทำลาย session (เพื่อให้สถานะล็อกอินหายไป)
session_destroy(); 

// 3. ส่งกลับไปหน้าแรก (index.html)
header("Location: index.html");
exit();
?>