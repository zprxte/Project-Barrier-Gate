<?php
session_start();
require_once('connect.php');

// ตรวจสอบ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.html");
    exit();
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$error_msg = "";
$result = null; 

// ถ้ามีการค้นหา
if (!empty($keyword)) {
    $sql = "SELECT * FROM car_info WHERE user_id = ? OR car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error_msg = "ไม่พบข้อมูลในระบบ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คข้อมูล - HCU Parking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- CSS จัดระเบียบการ์ด --- */
        .main-container {
            display: flex;
            flex-direction: column; 
            align-items: center;    
            padding-top: 120px;     
            padding-bottom: 50px;
            min-height: 100vh;      
            box-sizing: border-box;
        }

        .info-card {
            position: relative;     
            top: auto; left: auto; transform: none; 
            margin-bottom: 30px;    
            background-color: white;
            border-radius: 20px;
            padding: 40px 60px;
            width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .check-data-card {
            position: relative;
            top: auto; left: auto; transform: none;
            margin-top: 50px;
            background-color: white;
            border-radius: 20px;
            padding: 50px 80px;
            width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        /* --- CSS เพิ่มใหม่: จัดปุ่มค้นหาให้อยู่ข้างช่องกรอก --- */
        .search-center-box form {
            display: flex;          /* สั่งให้ลูกๆ (input, button) เรียงแนวนอน */
            justify-content: center; /* จัดกึ่งกลางแนวนอน */
            align-items: center;    /* จัดกึ่งกลางแนวตั้ง */
            gap: 10px;              /* ระยะห่างระหว่างช่องกรอกกับปุ่ม */
            width: 100%;
        }

        /* ปรับขนาดช่องกรอกให้พอดี */
        .search-center-box input.big-input {
            flex-grow: 1;           /* ให้ช่องกรอกขยายเต็มพื้นที่ที่เหลือ */
            margin: 0;              /* ลบ margin เดิมที่อาจจะดันบรรทัด */
            height: 25px;           /* กำหนดความสูงให้เท่าปุ่ม (ปรับตามต้องการ) */
        }

        /* ปรับปุ่มให้ไม่หดและสูงเท่าช่องกรอก */
        .search-center-box button.red-square-btn {
            flex-shrink: 0;         /* ห้ามปุ่มหดตัว */
            height: 45px;           /* ความสูงเท่า input */
            margin: 0;              /* ลบ margin เดิม */
            display: flex; align-items: center; justify-content: center; /* จัดตัวหนังสือกลางปุ่ม */
        }
    </style>
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="admin-title-container">
            <span class="admin-title">ระบบสแกนป้ายทะเบียนยานพาหนะ HCU</span>
        </div>
    </header>

    <div class="main-container">
        
        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
            
                <div class="info-card"> 
                    <div class="detail-row">
                        <div class="detail-label">รหัสบุคลากร / นักศึกษา :</div>
                        <div class="detail-value"><?php echo htmlspecialchars($row['user_id']); ?></div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">เลขทะเบียนยานพาหนะ :</div>
                        <div class="detail-value"><?php echo htmlspecialchars($row['car_id']); ?></div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">ประเภทรถ :</div>
                        <div class="detail-value"><?php echo htmlspecialchars($row['car_type']); ?></div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">การชำระค่าบริการ :</div>
                        <div class="detail-value">
                            <?php if ($row['status_payment'] == '1' || $row['status_payment'] == 'Paid'): ?>
                                <span class="text-paid">ชำระค่าบริการแล้ว</span>
                            <?php else: ?>
                                <span class="text-unpaid">ยังไม่ชำระค่าบริการ</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="revoke-btn-container">
                        <a href="revoke_process.php?car_id=<?php echo $row['car_id']; ?>&user_id=<?php echo $row['user_id']; ?>" 
                           class="revoke-button"
                           onclick="return confirm('คุณต้องการเพิกถอนสิทธิ์รถทะเบียน <?php echo $row['car_id']; ?> ใช่หรือไม่?');">
                            เพิกถอนสิทธิ์
                        </a>
                    </div>

                </div>

            <?php endwhile; ?>
            
            <div class="bottom-right-container" style="text-align: center; margin-top: 20px;">
                <a href="admin_check_data.php" class="back-pill-btn">ค้นหาใหม่</a>
            </div>

        <?php else: ?>
            
            <div class="check-data-card">
                <h2 class="page-title-left" style="text-align: center;">เช็คข้อมูล</h2>

                <div class="search-center-box">
                    <form method="GET" action="admin_check_data.php">
                        <input type="text" name="keyword" class="big-input" placeholder="เลขทะเบียน/รหัสนักศึกษา" value="<?php echo htmlspecialchars($keyword); ?>" required>
                        <button type="submit" class="back-pill-btn">ค้นหา</button>
                    </form>
                </div>

                <?php if ($error_msg): ?>
                    <p style="text-align:center; color:red; margin-top:20px; font-size:18px; font-weight:bold;">
                        <?php echo $error_msg; ?>
                    </p>
                <?php endif; ?>

                <div class="bottom-right-container">
                    <a href="admin_dashboard.php" class="back-pill-btn">กลับ</a>
                </div>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>