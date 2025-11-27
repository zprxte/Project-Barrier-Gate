<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเข้า-ออกลานจอดรถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-box { width: 100%; max-width: 400px; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: white; }
        .btn-xl { padding: 15px; font-size: 1.2rem; width: 100%; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="card-box text-center">
        <h3 class="mb-4">🚗 ระบบจอดรถ HCU</h3>
        
        <form action="save_parking.php" method="POST">
            <div class="mb-3">
                <label for="car_id" class="form-label">กรอกป้ายทะเบียน</label>
                <input type="text" class="form-control form-control-lg text-center" 
                       name="car_id" placeholder="Ex. 7กข8486" required autofocus>
            </div>

            <hr>

            <button type="submit" name="action_type" value="IN" class="btn btn-success btn-xl">
                📥 เข้าจอด (IN)
            </button>
            
            <button type="submit" name="action_type" value="OUT" class="btn btn-danger btn-xl">
                📤 ออกรถ (OUT)
            </button>
            <a href="user_dashboard.php" style="display: block; margin-top: 10px; color: #888; text-decoration: none; font-size: 16px; font-weight: bold;">
                <i class="fas fa-arrow-left"></i> ย้อนกลับ
            </a>
        </form>
    </div>
</body>
</html>