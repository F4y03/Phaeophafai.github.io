<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_orders.php");
    exit();
}

$orderId = $_GET['id'];

// ปรับปรุง SQL query โดยใช้ LEFT JOIN กับตาราง invoices เพื่อดึงข้อมูล slip_filename (ถ้ามี)
$stmt = $conn->prepare("
    SELECT orders.*, customers.username, invoices.slip_filename 
    FROM orders 
    JOIN customers ON orders.customer_id = customers.id
    LEFT JOIN invoices ON invoices.order_id = orders.id
    WHERE orders.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: manage_orders.php");
    exit();
}

// ดึงรายการสินค้าที่สั่งซื้อ (รวมข้อมูลไซส์จาก order_items)
$stmtItems = $conn->prepare("
    SELECT oi.*, p.name AS product_name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$orderId]);
$orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ</title>
    <style>
        .order-details { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .detail-item { margin: 1rem 0; padding: 1rem; border-bottom: 1px solid #ecf0f1; }
        .label { font-weight: 600; color: #2c3e50; min-width: 150px; display: inline-block; }
        .value { color: #7f8c8d; }
        .payment-slip { margin-top: 1rem; }
        .payment-slip img { max-width: 400px; width: 100%; height: auto; border: 1px solid #ccc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        table th, table td { padding: 12px 15px; border: 1px solid #ecf0f1; text-align: left; }
        table th { background-color: #f8f9fa; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">รายละเอียดคำสั่งซื้อ</h1>
        <nav style="margin-top: 1rem;">
            <a href="index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่หน้ารายการ
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div class="order-details">
            <h2 style="color: #2c3e50;">คำสั่งซื้อ #<?= htmlspecialchars($order['order_number']) ?></h2>
            
            <div class="detail-item">
                <span class="label">ลูกค้า:</span>
                <span class="value"><?= htmlspecialchars($order['username']) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">วันที่สั่งซื้อ:</span>
                <span class="value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">สถานะ:</span>
                <span class="value"><?= htmlspecialchars($order['status']) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">ยอดรวม:</span>
                <span class="value"><?= number_format($order['total_amount'], 2) ?> บาท</span>
            </div>
            
            <?php if (!empty($order['slip_filename'])): ?>
                <div class="detail-item payment-slip">
                    <span class="label">สลิปการโอนเงิน:</span>
                    <div class="value">
                        <img src="../../uploads/slips/<?= htmlspecialchars($order['slip_filename']) ?>" alt="Payment Slip">
                    </div>
                </div>
            <?php else: ?>
                <div class="detail-item">
                    <span class="label">สลิปการโอนเงิน:</span>
                    <span class="value">ไม่มีสลิปการโอนเงิน</span>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 2rem;">
                <a href="update_status.php?id=<?= $order['id'] ?>" 
                   style="padding: 0.8rem 1.5rem; background-color: #27ae60; color: white; text-decoration: none; border-radius: 4px;">
                    อัปเดตสถานะ
                </a>
            </div>
        </div>
        
        <!-- แสดงรายการสินค้าที่สั่งซื้อพร้อมข้อมูลไซส์ -->
        <?php if (!empty($orderItems)): ?>
            <div class="order-details">
                <h3 style="color: #2c3e50; margin-bottom: 1rem;">รายการสินค้าที่สั่งซื้อ</h3>
                <table>
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>ไซส์</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อชิ้น (บาท)</th>
                            <th>ราคารวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= htmlspecialchars($item['size'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= number_format($item['unit_price'], 2) ?></td>
                                <td><?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #7f8c8d;">ไม่พบรายการสินค้าที่สั่งซื้อ</p>
        <?php endif; ?>
    </main>

</body>
</html>
