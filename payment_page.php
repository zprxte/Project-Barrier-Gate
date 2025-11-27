<?php
session_start();
require_once('connect.php');

// กำหนดจำนวนเงินที่ต้องชำระ
$required_amount = 200.00;
$error_message = '';

// 1. ตรวจสอบ Session
$session_user_id = $_SESSION['user_id'] ?? null; 
$is_test_mode = empty($session_user_id); // ถ้าไม่มี session = โหมดทดสอบ

// --- ส่วนเสริม: ดึงรายชื่อรถ (แก้ไข: ดึงเฉพาะคันที่ยังไม่จ่าย) ---
$my_cars = [];
if (!$is_test_mode) {
    // เพิ่มเงื่อนไข SQL: เลือกเฉพาะที่ status_payment ไม่ใช่ '1' และไม่ใช่ 'Paid'
    $sql_cars = "SELECT car_id FROM car_info WHERE user_id = ? AND (status_payment != '1' AND status_payment != 'Paid')";
    $stmt_cars = $conn->prepare($sql_cars);
    $stmt_cars->bind_param("s", $session_user_id);
    $stmt_cars->execute();
    $res_cars = $stmt_cars->get_result();
    while ($row = $res_cars->fetch_assoc()) {
        $my_cars[] = $row['car_id'];
    }
    $stmt_cars->close();
}

// 2. ตรวจสอบการกดปุ่มชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? 0;
    $target_car_id = trim($_POST['target_car_id'] ?? ''); // ใช้ trim ตัดช่องว่าง

    if (empty($target_car_id)) {
        $error_message = "กรุณาระบุเลขทะเบียนรถที่ต้องการชำระเงิน";
    } 
    // เงื่อนไข: ต้องใส่เลข 200 เท่านั้น
    elseif (floatval($amount) == $required_amount) {
        
        // 2.1 ตรวจสอบว่ามีทะเบียนรถนี้ในระบบจริงไหม AND ตรวจสอบสถานะการจ่าย
        // ดึง status_payment ออกมาดูด้วย
        $check = $conn->prepare("SELECT car_id, status_payment FROM car_info WHERE car_id = ?");
        $check->bind_param("s", $target_car_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = "ไม่พบทะเบียนรถ: " . htmlspecialchars($target_car_id) . " ในระบบ";
        } else {
            // ดึงข้อมูลมาเช็คก่อนอัปเดต
            $row = $result->fetch_assoc();
            $current_status = $row['status_payment'];

            // *** เพิ่ม LOGIC ป้องกันการจ่ายซ้ำตรงนี้ ***
            if ($current_status == '1' || $current_status == 'Paid') {
                $error_message = "รถทะเบียน " . htmlspecialchars($target_car_id) . " ได้ชำระค่าบริการไปแล้ว ไม่สามารถชำระซ้ำได้";
            } else {
                // 2.2 อัปเดตสถานะการจ่ายเงิน (เมื่อยังไม่เคยจ่าย)
                $sql = "UPDATE car_info SET status_payment = '1' WHERE car_id = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die("SQL Error: " . $conn->error); 
                }

                $stmt->bind_param("s", $target_car_id);
                
                if ($stmt->execute()) {
                    // ... ส่วนที่สำเร็จ ...
                    // (Optional) อาจจะเคลียร์ session ชั่วคราวถ้าจำเป็น
                    if(isset($_SESSION['current_user_id'])) { unset($_SESSION['current_user_id']); }
                    
                    header("Location: payment_success.php?amount=" . number_format($required_amount, 2));
                    exit();
                } else {
                    $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
                }
                $stmt->close();
            }
        }
        $check->close();
        
    } else {
        $error_message = "ยอดเงินไม่ถูกต้อง กรุณาชำระยอด 200.00 บาท เท่านั้น";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระค่าบริการ - HCU Parking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="system-title">ระบบสแกนป้ายทะเบียนยานพาหนะ HCU</div>
    </header>

    <div class="main-container">
        <div class="registration-box-label-input">
            
            <div class="payment-content">
                <div class="payment-title">การชำระค่าบริการ (รายคัน)</div>
                
                <div class="amount-highlight">200.00</div>
                <div class="amount-label">จำนวนเงินที่ต้องชำระ</div>

                <form method="POST" action="payment_page.php">
                    
                    <?php if ($is_test_mode): ?>
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 18px; color: #C00000; font-weight: bold;">ระบุเลขทะเบียนรถ </label><br>
                            <input type="text" name="target_car_id" class="payment-input" 
                                   placeholder="เช่น กก 1234" required 
                                   style="margin-top: 5px; border-color: #C00000;">
                        </div>

                    <?php else: ?>
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 18px; color: #000; font-weight: bold;">เลือกรถที่ต้องการชำระเงิน </label><br>
                            
                            <?php if (count($my_cars) > 0): ?>
                                <select name="target_car_id" class="payment-input" style="margin-top: 5px; width: 220px; height: 40px;">
                                    <?php foreach ($my_cars as $car): ?>
                                        <option value="<?php echo htmlspecialchars($car); ?>">
                                            <?php echo htmlspecialchars($car); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <p style="color:green; font-weight:bold; margin-top:10px;">
                                    คุณชำระค่าบริการครบทุกคันแล้ว หรือยังไม่มีข้อมูลรถ
                                </p>
                                <input type="hidden" name="target_car_id" value="">
                            <?php endif; ?>
                            
                            <div style="margin-top:5px; font-size:14px; color:gray;">
                                (ผู้ใช้: <?php echo htmlspecialchars($session_user_id); ?>)
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="payment-form-row">
                        <?php if (!$is_test_mode && count($my_cars) == 0): ?>
                            <button type="button" class="green-button" disabled style="background-color: gray; cursor: not-allowed;">ชำระเงิน</button>
                        <?php else: ?>
                            <input type="number" name="amount" class="payment-input" placeholder="กรอกจำนวนเงิน" required style="width: 200px;">
                            <button type="submit" class="green-button">ชำระเงิน</button>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if ($error_message): ?>
                    <p class="error-msg" style="color:red; margin-top:10px; font-weight:bold;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                
                <br>
                <a href="user_dashboard.php" style="color: #888; text-decoration: none;">ย้อนกลับ</a>
            </div>

        </div>
    </div>

</body>
</html>