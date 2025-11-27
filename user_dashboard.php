<?php
session_start();
require_once('connect.php');

// 1. ถ้ายังไม่ล็อกอิน ให้ดีดกลับไปหน้าแรก
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- กำหนดค่าเริ่มต้น (Default Value) ---
$user_id_display = $user_id; 
$car_list_show = "-";
$cars = [];

// ตัวแปรสำหรับเช็คปุ่ม
$show_pay_button = false;   // ปุ่มจ่ายเงิน (Default: ปิด)
$has_paid_car = false;      // ปุ่มจอดรถ (Default: ปิด)

// 2. ดึงข้อมูลจากตาราง CAR_INFO
$sql = "SELECT car_id, status_payment FROM car_info WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // --- เริ่ม Loop รอบเดียว เช็คทุกเงื่อนไข ---
    while($row = $result->fetch_assoc()) {
        $cars[] = $row['car_id']; // เก็บทะเบียนรถ
        
        $status_db = $row['status_payment'];
        
        // ตรวจสอบว่าคันนี้จ่ายหรือยัง?
        $is_paid_item = ($status_db == '1' || $status_db == 'Paid' || $status_db == 'ชำระแล้ว');

        if (!$is_paid_item) {
            // ถ้ามีรถคันไหน "ยังไม่จ่าย" -> เปิดปุ่มจ่ายเงินทันที
            $show_pay_button = true;
        } else {
            // ถ้ามีรถคันไหน "จ่ายแล้ว" -> เปิดสิทธิ์ให้เห็นปุ่มจอดรถ
            $has_paid_car = true;
        }
    }
    
    // แปลง Array รถเป็น String เพื่อโชว์
    if (!empty($cars)) {
        $car_list_show = implode(", ", $cars);
    }
}

// 3. อัปเดตข้อความสถานะที่จะโชว์หน้าเว็บ
if ($show_pay_button) {
    // ถ้า $show_pay_button เป็น true แสดงว่ามียอดค้าง
    $status_show = "ยังไม่ชำระค่าบริการ (บางคัน/ทั้งหมด)";
    $status_class = "status-unpaid"; // สีแดง
} else {
    // ถ้าไม่มียอดค้าง (และอาจจะไม่มีรถเลย หรือจ่ายครบแล้ว)
    if (empty($cars)) {
        $status_show = "ไม่พบข้อมูลยานพาหนะ";
        $status_class = "status-unpaid"; // สีแดง (หรือสีอื่นตามชอบ)
    } else {
        $status_show = "ชำระค่าบริการแล้ว";
        $status_class = "status-paid"; // สีเขียว
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ข้อมูลผู้ใช้</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-card {
            background-color: white;
            border-radius: 20px;
            padding: 50px 80px;
            width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            min-height: 300px;
            text-align: center;
            margin: 0 auto;
        }
        .info-row {
            font-size: 24px;
            margin-bottom: 25px;
            color: #000;
            font-weight: bold;
            display: flex;
            justify-content: flex-start;
        }
        .info-label {
            width: 320px;
            text-align: right;
            margin-right: 20px;
        }
        .info-value {
            text-align: left;
            font-weight: normal;
        }
        .status-paid { color: #00b900; font-weight: bold; }
        .status-unpaid { color: red; font-weight: bold; }
        
        .logout-btn-container {
            position: absolute;
            bottom: 30px;
            right: 30px;
        }
        .logout-button {
            background-color: #C00000;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-button:hover { background-color: #a00000; }

        /* ปุ่มจ่ายเงิน */
        .pay-now-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: 0.3s;
        }
        .pay-now-btn:hover { background-color: #e0a800; transform: translateY(-2px);}
        
        /* ปุ่มเข้าจอด */
        .park-now-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #17a2b8; /* สีฟ้า */
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: 0.3s;
            margin-left: 10px;
        }
        .park-now-btn:hover { background-color: #138496; transform: translateY(-2px); }

        /* ปุ่มเพิ่มรถ */
        .add-car-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #28a745; /* สีเขียว */
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .add-car-btn:hover { background-color: #218838; transform: translateY(-2px);}
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
        <div class="dashboard-card">
            
            <div class="info-row">
                <div class="info-label">รหัสบุคลากร / นักศึกษา :</div>
                <div class="info-value"><?php echo htmlspecialchars($user_id_display); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">เลขทะเบียนยานพาหนะ :</div>
                <div class="info-value"><?php echo htmlspecialchars($car_list_show); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">สถานะ :</div>
                <div class="info-value <?php echo $status_class; ?>">
                    <?php echo $status_show; ?>
                </div>
            </div>

            <a href="add_car.php" class="add-car-btn">
                <i class="fas fa-plus-circle"></i> เพิ่มรถ
            </a>
            
            <span style="margin: 0 5px;"></span>

            <?php if ($show_pay_button): ?>
                <a href="payment_page.php" class="pay-now-btn">
                    <i class="fas fa-coins"></i> ไปที่หน้าชำระเงิน
                </a>
            <?php endif; ?>

            <?php if ($has_paid_car): ?>
                <a href="parking_form.php" class="park-now-btn">
                    <i class="fas fa-car"></i> เข้าสู่ระบบจอดรถ
                </a>
            <?php endif; ?>

            <div class="logout-btn-container">
                <a href="logout.php" class="logout-button">ออกจากระบบ</a>
            </div>

        </div>
    </div>

</body>
</html>