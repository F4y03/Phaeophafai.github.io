<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// ตรวจสอบ ID ลูกค้า
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_customers.php");
    exit();
}

$customer_id = $_GET['id'];

// ดึงข้อมูลลูกค้า
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีลูกค้าหรือไม่
if (!$customer) {
    $error = "ไม่พบข้อมูลลูกค้า";
}

// ดึงประวัติการสั่งซื้อ
$order_stmt = $conn->prepare("
    SELECT o.id, o.order_date, o.total_amount, os.status_name 
    FROM orders o
    JOIN order_status os ON o.status_id = os.id
    WHERE o.customer_id = ?
    ORDER BY o.order_date DESC
");
$order_stmt->execute([$customer_id]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดลูกค้า</title>
    <style>
        .status-active { color: #27ae60; }
        .status-inactive { color: #e74c3c; }
        .info-section { margin-bottom: 2rem; }
        .order-table { margin-top: 2rem; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">รายละเอียดลูกค้า</h1>
        <nav style="margin-top: 1rem; display: flex; gap: 1.5rem;">
            <a href="manage_customers.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่หน้าจัดการลูกค้า
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <?php if(isset($error)): ?>
            <div style="background: #ffebee; padding: 1.5rem; border-radius: 8px; color: #c62828; margin-bottom: 2rem;">
                <?= $error ?>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                
                <!-- ข้อมูลลูกค้า -->
                <div class="info-section">
                    <h2 style="color: #2c3e50; margin-top: 0;">ข้อมูลทั่วไป</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div>
                            <p><strong>ชื่อผู้ใช้:</strong> <?= htmlspecialchars($customer['username']) ?></p>
                            <p><strong>อีเมล:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                            <p><strong>เบอร์โทรศัพท์:</strong> <?= $customer['phone_number'] ? htmlspecialchars($customer['phone_number']) : '-' ?></p>
                        </div>
                        <div>
                            <p><strong>สถานะ:</strong> 
                                <span class="status-<?= $customer['is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $customer['is_active'] ? 'ใช้งานปกติ' : 'ระงับการใช้งาน' ?>
                                </span>
                            </p>
                            <p><strong>วันที่สมัครสมาชิก:</strong> <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?></p>
                            <p><strong>อัปเดตล่าสุด:</strong> <?= date('d/m/Y H:i', strtotime($customer['updated_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- ประวัติการสั่งซื้อ -->
                <div class="order-table">
                    <h2 style="color: #2c3e50;">ประวัติการสั่งซื้อ</h2>
                    <?php if(!empty($orders)): ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">เลขที่คำสั่งซื้อ</th>
                                        <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #ecf0f1;">วันที่สั่งซื้อ</th>
                                        <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #ecf0f1;">ยอดรวม (บาท)</th>
                                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #ecf0f1;">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                    <tr style="border-bottom: 1px solid #ecf0f1;">
                                        <td style="padding: 1rem;">#<?= $order['id'] ?></td>
                                        <td style="padding: 1rem; text-align: right;"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                        <td style="padding: 1rem; text-align: right;"><?= number_format($order['total_amount'], 2) ?></td>
                                        <td style="padding: 1rem; text-align: center;"><?= htmlspecialchars($order['status_name']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2rem; color: #7f8c8d;">
                            ไม่พบประวัติการสั่งซื้อ
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        <?php endif; ?>
    </main>

</body>
</html>