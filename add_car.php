<?php
session_start();
require_once('connect.php'); // เรียกไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า Login หรือยัง
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ login ให้เด้งไปหน้า login หรือแจ้งเตือน
    header("Location: login.html"); 
    exit();
}

// รับค่า user_id ของคนที่ Login อยู่ปัจจุบัน
$current_user_id = $_SESSION['user_id'];

// --- ส่วนบันทึกข้อมูลเมื่อกดปุ่ม Submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cars'])) {
    
    $car_ids = $_POST['car_id'] ?? [];    // รับค่าทะเบียนรถ (Array)
    $car_types = $_POST['car_type'] ?? []; // รับค่าประเภทรถ (Array)

    $conn->begin_transaction(); // เริ่มต้น Transaction
    $success_count = 0;
    $error_msg = "";

    try {
        $sql = "INSERT INTO car_info (car_id, user_id, car_type, status_payment) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);

        for ($i = 0; $i < count($car_ids); $i++) {
            $plate = trim($car_ids[$i]);
            $type = $car_types[$i];

            if (!empty($plate)) {
                $stmt->bind_param("sss", $plate, $current_user_id, $type);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    // กรณี Error (เช่น ทะเบียนรถซ้ำ)
                    throw new Exception("บันทึกทะเบียน $plate ไม่สำเร็จ (อาจมีในระบบแล้ว)");
                }
            }
        }
        
        $conn->commit(); // ยืนยันการบันทึก
        echo "<script>alert('✅ เพิ่มรถสำเร็จจำนวน $success_count คัน'); window.location.href='user_dashboard.php';</script>";

    } catch (Exception $e) {
        $conn->rollback(); // ยกเลิกถ้ามีปัญหา
        $error_msg = $e->getMessage();
        echo "<script>alert('❌ เกิดข้อผิดพลาด: $error_msg');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนรถเพิ่ม - HCU</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ... CSS เดิมของคุณ ... */
        .dashboard-card {
            background-color: white;
            border-radius: 20px;
            padding: 40px;
            width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin: 50px auto; /* จัดกลางหน้าจอ */
        }
        .btn-confirm {
            background-color: #28a745;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-confirm:hover { background-color: #218838; }
        .btn-add-car-dashed {
            background-color: #f9f9f9;      /* พื้นหลังสีเทาอ่อนมาก */
            border: 2px dashed #aaa;        /* เส้นประสีเทา */
            color: #666;                    /* ตัวหนังสือสีเทาเข้ม */
            border-radius: 12px;            /* มุมโค้งมน */
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            margin-bottom: 15px;
        }

        .btn-add-car-dashed:hover {
            background-color: #fff;         /* พื้นหลังขาวตอนชี้ */
            border-color: #28a745;          /* เส้นขอบเปลี่ยนเป็นสีเขียว */
            color: #28a745;                 /* ตัวหนังสือเปลี่ยนเป็นสีเขียว */
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.15); /* เงาฟุ้งๆ */
            transform: translateY(-2px);    /* ลอยขึ้นนิดหน่อย */
        }

        .btn-add-car-dashed i {
            margin-right: 8px;              /* เว้นระยะไอคอนกับตัวหนังสือ */
        }
                /* การ์ดครอบข้อมูลรถ */
        .car-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* เงาฟุ้งๆ นุ่มๆ */
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
            position: relative;
            transition: all 0.3s ease;
        }

        .car-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); /* เงานูนขึ้นเมื่อเอาเมาส์ชี้ */
            transform: translateY(-2px);
        }

        /* หัวข้อ label */
        .form-label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-label i {
            color: #C00000; /* สีแดง HCU หรือสีธีม */
            margin-right: 5px;
            width: 20px;
            text-align: center;
        }

        /* ช่องกรอกข้อมูล Input & Select */
        .custom-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            background-color: #f9f9f9; /* พื้นหลังเทาอ่อน สบายตา */
            border-radius: 10px;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
            outline: none;
            box-sizing: border-box; /* สำคัญ: ไม่ให้ padding ดันกล่องจนล้น */
        }

        /* Effect ตอนคลิกกรอกข้อมูล */
        .custom-input:focus {
            background-color: #fff;
            border-color: #C00000; /* เปลี่ยนขอบเป็นสีธีม */
            box-shadow: 0 0 0 4px rgba(192, 0, 0, 0.1); /* แสงฟุ้งรอบๆ */
        }

        /* จัด Layout แนวนอนสำหรับจอใหญ่ (Optional) */
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-col {
            flex: 1;
            text-align: left; /* เพิ่มบรรทัดนี้ เพื่อให้ทุกอย่างในคอลัมน์ชิดซ้าย */
        }

        /* ปุ่มลบ (ถ้ามี) */
        .remove-car-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #ccc;
            cursor: pointer;
            transition: 0.2s;
        }
        .remove-car-btn:hover { color: red; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="hcu-header">
        <div class="logo-area">
            <img src="image/logoHCU.png" alt="โลโก้ HCU" class="hcu-logo">
        </div>
        <div class="system-title">ระบบลงทะเบียนยานพาหนะเพิ่มเติม</div>
    </header>

    <div class="main-container">
        <div class="dashboard-card">
            
            <h2 style="margin-bottom: 20px;">เพิ่มข้อมูลรถยนต์</h2>
            <p style="color: #666; margin-bottom: 30px;">
                ผู้ใช้งาน: <strong><?php echo htmlspecialchars($current_user_id); ?></strong>
            </p>

            <form action="" method="POST">
                
                <div id="car-container">
                    
                    <label style="display:block; font-size: 18px; font-weight:bold; margin-bottom:15px; color:#333;">
                        <i class="fas fa-list-ul"></i> ข้อมูลรถ
                    </label>
                    
                    <div class="car-group car-card">
                        
                        
                        <div class="form-col" style="text-align: left;">
                            <label class="form-label">
                                <i class="fas fa-car-side"></i> ทะเบียนรถ
                            </label>
                            <div class="form-row">
                                <input type="text" name="car_id[]" 
                                    placeholder="เช่น กก 1234" 
                                    class="custom-input" 
                                    required>
                            </div>
                        </div>

                        <div style="margin-top: 15px;"></div>
                            <div class="form-col">
                                <label class="form-label">
                                    <i class="fas fa-motorcycle"></i> ประเภทรถ
                                </label>
                                <div class="form-row">
                                <select name="car_type[]" class="custom-input">
                                    <option value="รถยนต์">รถยนต์</option>
                                    <option value="มอเตอร์ไซค์">มอเตอร์ไซค์</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <button type="button" onclick="addCarField()" class="btn-add-car-dashed">
                    <i class="fas fa-plus"></i> เพิ่มรถอีกคัน
                </button>

                <hr>

                <button type="submit" name="save_cars" class="btn-confirm">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
                
                <br><br>
                <a href="user_dashboard.php" style="color: #888; text-decoration: none;">ย้อนกลับ</a>

            </form>
            <script>
            function addCarField() {
                const newField = `
                    <div id="car-container">
                    
                    <div class="car-group car-card">
                        <label style="display:block; font-size: 18px; font-weight:bold; margin-bottom:15px; color:#333;">ข้อมูลรถเพิ่มเติม</label>
                        <div class="form-col">
                            <label class="form-label">
                                <i class="fas fa-car-side"></i> ทะเบียนรถ
                            </label>
                            <div class="form-row">
                                <input type="text" name="car_id[]" 
                                    placeholder="เช่น กก 1234" 
                                    class="custom-input" 
                                    required>
                            </div>
                        </div>

                        <div style="margin-top: 15px;"></div>
                            <div class="form-col">
                                <label class="form-label">
                                    <i class="fas fa-motorcycle"></i> ประเภทรถ
                                </label>
                                <div class="form-row">
                                <select name="car_type[]" class="custom-input">
                                    <option value="รถยนต์">รถยนต์</option>
                                    <option value="มอเตอร์ไซค์">มอเตอร์ไซค์</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: red; font-weight: bold; cursor: pointer;">
                            <i class="fas fa-trash"></i> ลบ
                        </button>

                    </div>
                </div>
                `;
                document.getElementById('car-container').insertAdjacentHTML('beforeend', newField);
            }
            </script>
        </div>
    </div>
</body>
</html>