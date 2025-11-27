<?php
session_start();
require_once('connect.php');

// 1. ตรวจสอบสิทธิ์ Executive
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'executive') {
    header("Location: admin_login.html"); // หรือหน้า Login ที่คุณใช้
    exit();
}

// 2. ดึงข้อมูลสรุป (Stats) เพื่อโชว์ให้ผู้บริหารเห็นภาพรวม
// - จำนวนรถที่กำลังจอดอยู่ (Status = Parked)
$sql_parked = "SELECT COUNT(*) as count FROM parking_sessions WHERE status = 'PARKED'";
$res_parked = $conn->query($sql_parked);
$row_parked = $res_parked->fetch_assoc();
$current_parked = $row_parked['count'];

// - จำนวนรถเข้าทั้งหมดของวันนี้
$today = date('Y-m-d');
$sql_today = "SELECT COUNT(*) as count FROM parking_sessions WHERE DATE(time_in) = '$today'";
$res_today = $conn->query($sql_today);
$row_today = $res_today->fetch_assoc();
$total_today = $row_today['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผู้บริหาร - HCU Parking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .exec-card {
            background-color: white;
            border-radius: 20px;
            padding: 50px;
            width: 700px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }

        /* กล่องแสดงสถิติ (Stats Box) */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 20px;
        }
        .stat-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 20px;
            flex: 1;
            transition: 0.3s;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #C00000;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #C00000;
            margin: 10px 0;
        }
        .stat-label {
            color: #555;
            font-size: 16px;
        }

        /* ปุ่มเมนู */
        .menu-btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-history {
            background-color: #007bff; /* สีน้ำเงิน */
            color: white;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .btn-history:hover { background-color: #0056b3; transform: translateY(-2px); }

        .btn-logout {
            background-color: #dc3545;
            color: white;
        }
        .btn-logout:hover { background-color: #a71d2a; }

        .welcome-text {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
        }
    </style>
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="system-title">ระบบสแกนป้ายทะเบียนยานพาหนะ HCU</div>
    </header>

    <div class="main-container" style="display:flex; justify-content:center; padding-top:120px;">
        <div class="exec-card">
            
            <div class="welcome-text">
                ยินดีต้อนรับ, <strong>ผู้บริหาร (Executive)</strong>
            </div>

            <div class="stats-container">
                <div class="stat-box">
                    <i class="fas fa-car" style="font-size: 30px; color: #888;"></i>
                    <div class="stat-number"><?php echo $current_parked; ?></div>
                    <div class="stat-label">รถที่กำลังจอด (คัน)</div>
                </div>
                <div class="stat-box">
                    <i class="fas fa-clock" style="font-size: 30px; color: #888;"></i>
                    <div class="stat-number"><?php echo $total_today; ?></div>
                    <div class="stat-label">รถเข้าวันนี้ (ครั้ง)</div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

            <a href="parking_sessions.php" class="menu-btn btn-history">
                <i class="fas fa-list-alt"></i> ดูประวัติการเข้า-ออก (ทั้งหมด)
            </a>

            <a href="logout.php" class="menu-btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
            </a>

        </div>
    </div>

</body>
</html>