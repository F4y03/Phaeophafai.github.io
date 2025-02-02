<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id'];
    $newStatus = $_POST['status'];
    
    $allowedStatuses = ['pending', 'completed', 'cancelled'];
    if (!in_array($newStatus, $allowedStatuses)) {
        $_SESSION['error'] = "สถานะไม่ถูกต้อง";
        header("Location: manage_orders.php");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        
        $_SESSION['message'] = "อัปเดตสถานะเรียบร้อยแล้ว!";
        header("Location: ../index.php"); // เปลี่ยนเส้นทางไปหน้าแดชบอร์ดหลัก
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตสถานะ";
        header("Location: manage_orders.php");
        exit();
    }
} else {
    if (!isset($_GET['id'])) {
        header("Location: manage_orders.php");
        exit();
    }
    
    $orderId = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header("Location: manage_orders.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตสถานะคำสั่งซื้อ</title>
    <style>
        .update-form { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 500px; margin: 2rem auto; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 600; }
        select { width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 4px; }
        button { padding: 0.8rem 1.5rem; background-color: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #219a52; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">อัปเดตสถานะคำสั่งซื้อ</h1>
        <nav style="margin-top: 1rem;">
            <a href="index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่หน้ารายการคำสั่งซื้อ
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div class="update-form">
            <form method="POST">
                <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                
                <div class="form-group">
                    <label for="status">เลือกสถานะใหม่</label>
                    <select name="status" id="status" required>
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
                    </select>
                </div>
                
                <button type="submit">บันทึกการเปลี่ยนแปลง</button>
            </form>
        </div>
    </main>

</body>
</html>