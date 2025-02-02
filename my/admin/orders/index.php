
<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// แก้ไขชื่อคอลัมน์เป็น created_at (หรือชื่อที่ถูกต้องในฐานข้อมูลของคุณ)
$stmt = $conn->query("
    SELECT orders.*, customers.username 
    FROM orders 
    JOIN customers ON orders.customer_id = customers.id
    ORDER BY orders.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ</title>
    <style>
        .status-pending { color: #e67e22; }
        .status-completed { color: #27ae60; }
        .status-cancelled { color: #e74c3c; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">จัดการคำสั่งซื้อ</h1>
        <nav style="margin-top: 1rem; display: flex; gap: 1.5rem;">
            <a href="../index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่แดชบอร์ด
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 1rem;">รายการคำสั่งซื้อทั้งหมด</h2>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">หมายเลขคำสั่งซื้อ</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">ลูกค้า</th>
                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #ecf0f1;">ยอดรวม</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #ecf0f1;">สถานะ</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #ecf0f1;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #ecf0f1; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f8f9fa'">
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['order_number']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['username']) ?></td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($order['total_amount'], 2) ?> บาท</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span class="status-<?= htmlspecialchars(strtolower($order['status'])) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="view.php?id=<?= $order['id'] ?>" 
                                       style="padding: 0.5rem 1rem; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; margin-right: 0.5rem;">
                                        ดูรายละเอียด
                                    </a>
                                    <a href="update_status.php?id=<?= $order['id'] ?>" 
                                       style="padding: 0.5rem 1rem; background-color: #27ae60; color: white; text-decoration: none; border-radius: 4px;">
                                        อัปเดตสถานะ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 2rem; color: #7f8c8d;">
                    ไม่พบคำสั่งซื้อในระบบ
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>