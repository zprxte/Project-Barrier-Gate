<?php
session_start();
// ตรวจสอบว่าเป็น Admin หรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management - HCU Parking</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="admin-title-container">
            <span class="admin-title">Admin management</span>
        </div>
    </header>

    <div class="main-container">
        <div class="admin-dashboard-card">
            
            <div class="admin-button-container">
                <a href="admin_check_payment.php" class="admin-big-btn">
                    ประวัติการชำระเงิน
                </a>

               <a 
               href="admin_check_data.php" class="admin-big-btn">เช็คข้อมูล
            </a>
               
            </div>
            <div class="logout-area">
                <a href="logout.php" class="logout-pill-btn">ออกจากระบบ</a>
            </div>

        </div>
    </div>

</body>
</html>