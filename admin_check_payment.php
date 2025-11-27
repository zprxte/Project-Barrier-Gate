<?php
session_start();
require_once('connect.php');

// ตรวจสอบว่าเป็น Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.html");
    exit();
}

// รับค่าคำค้นหา (ถ้ามี)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// สร้าง Query
if ($search != '') {
    // กรณีมีการค้นหา: ค้นหาจาก user_id ที่มีตัวเลขตามที่พิมพ์ (ใช้ LIKE)
    $sql = "SELECT car_id, user_id, status_payment FROM car_info WHERE user_id LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search . "%"; // เติม % หน้าหลังเพื่อให้หาบางส่วนได้
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // กรณีไม่มีการค้นหา: ดึงข้อมูลทั้งหมด
    $sql = "SELECT car_id, user_id, status_payment FROM car_info";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คประวัติการชำระเงิน</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="admin-title-container">
            <span class="admin-title">ประวัติการชำระเงิน</span>
        </div>
    </header>

    <div class="main-container">
        <div class="table-card">
            
            <div class="table-header-row">
                <a href="admin_dashboard.php" class="back-link"> < ย้อนกลับ</a>
                
                <form action="admin_check_payment.php" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="ค้นหารหัสนักศึกษา..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                    <?php if ($search != ''): ?>
                        <a href="admin_check_payment.php" class="clear-search-btn">ดูทั้งหมด</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <h2>รายชื่อผู้ใช้งานและสถานะการชำระเงิน</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>เลขทะเบียนรถ</th>
                        <th>รหัสผู้ใช้</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <?php 
                                $status = $row['status_payment'];
                                $status_text = ($status == '1') ? "ชำระแล้ว" : "ยังไม่ชำระ";
                                $status_class = ($status == '1') ? "status-paid-badge" : "status-unpaid-badge";
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['car_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td>
                                    <span class="<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color: #888; padding: 30px;">
                                ไม่พบข้อมูลที่ค้นหา
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>