<?php
session_start();
require_once('connect.php');

// 1. ตรวจสอบล็อกอิน (ควรเช็คว่าเป็น Admin ด้วย ถ้าหน้านี้สำหรับ Admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// 2. แก้ไข SQL: ลบ WHERE ออก เพื่อดึงข้อมูล "ทั้งหมด" ในตาราง
// เรียงจากเวลาล่าสุดไปเก่าสุด
$sql = "SELECT * FROM parking_sessions ORDER BY time_in DESC";

$stmt = $conn->prepare($sql);
// $stmt->bind_param... ไม่ต้องใช้แล้ว เพราะไม่มีเครื่องหมาย ? ใน SQL
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการเข้า-ออกทั้งหมด - HCU Parking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* สไตล์เดิมของคุณ */
        .history-card {
            background-color: white;
            border-radius: 20px;
            padding: 40px;
            width: 900px; /* ขยายความกว้างหน่อยเพราะข้อมูลเยอะขึ้น */
            max-width: 95%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 0 auto;
            text-align: center;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        .history-table th {
            background-color: #C00000;
            color: white;
            padding: 12px;
            text-align: left;
            border-radius: 5px 5px 0 0;
        }
        
        .history-table th:first-child { border-top-left-radius: 10px; }
        .history-table th:last-child { border-top-right-radius: 10px; }
        .history-table th { border-radius: 0; } 

        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            color: #333;
        }

        .history-table tr:hover { background-color: #f5f5f5; }

        .status-parked {
            color: #28a745; font-weight: bold; background-color: #e8f5e9;
            padding: 4px 10px; border-radius: 20px; display: inline-block; font-size: 14px;
        }

        .status-exited {
            color: #666; font-weight: bold; background-color: #eee;
            padding: 4px 10px; border-radius: 20px; display: inline-block; font-size: 14px;
        }
        
        .empty-state { padding: 40px; color: #888; font-size: 18px; }
        
        .main-container {
            display: flex; justify-content: center;
            padding-top: 120px; padding-bottom: 50px; min-height: 100vh;
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

    <div class="main-container">
        <div class="history-card">
            
            <h2 style="color: #333; margin-bottom: 30px; text-align: left;">
                <i class="fas fa-list-alt"></i> ประวัติการเข้า-ออก (ทั้งหมด)
            </h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <div style="overflow-x: auto;"> 
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th width="15%">วันที่</th>
                                <th width="15%">User ID</th> <th width="15%">ทะเบียนรถ</th>
                                <th width="15%">เวลาเข้า</th>
                                <th width="15%">เวลาออก</th>
                                <th width="15%">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): 
                                $date_show = date("d/m/Y", strtotime($row['time_in']));
                                $time_in_show = date("H:i", strtotime($row['time_in']));
                                $time_out_show = $row['time_out'] ? date("H:i", strtotime($row['time_out'])) : "-";
                            ?>
                                <tr>
                                    <td><?php echo $date_show; ?></td>
                                    <td style="color:#007bff; font-weight:bold;">
                                        <?php echo htmlspecialchars($row['user_id']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['car_id']); ?></td>
                                    <td><?php echo $time_in_show; ?> น.</td>
                                    <td><?php echo $time_out_show; ?> <?php echo $row['time_out'] ? "น." : ""; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'PARKED' || $row['status'] == 'Parked'): ?>
                                            <span class="status-parked">กำลังจอด</span>
                                        <?php else: ?>
                                            <span class="status-exited">ออกแล้ว</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open" style="font-size: 50px; margin-bottom: 10px;"></i><br>
                    ยังไม่มีประวัติการเข้า-ออกในระบบ
                </div>
            <?php endif; ?>

            <div style="margin-top: 40px; text-align: right;">
                <a href="executive_dashboard.php" class="back-pill-btn">กลับหน้าหลัก</a>
            </div>

        </div>
    </div>

</body>
</html>