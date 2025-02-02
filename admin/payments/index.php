<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->query("
    SELECT payments.*, orders.order_number 
    FROM payments 
    JOIN orders ON payments.order_id = orders.id
    ORDER BY payments.payment_date DESC
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการชำระเงิน</title>
    <style>
        .status-paid { color: #27ae60; }
        .status-pending { color: #e67e22; }
        .status-failed { color: #e74c3c; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">จัดการการชำระเงิน</h1>
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
            <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 1rem;">รายการการชำระเงินทั้งหมด</h2>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">หมายเลขการชำระเงิน</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">หมายเลขคำสั่งซื้อ</th>
                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #ecf0f1;">ยอดชำระ</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #ecf0f1;">สถานะ</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr style="border-bottom: 1px solid #ecf0f1; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f8f9fa'">
                                <td style="padding: 1rem;"><?= htmlspecialchars($payment['payment_number']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($payment['order_number']) ?></td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($payment['amount'], 2) ?> บาท</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span class="status-<?= htmlspecialchars(strtolower($payment['payment_status'])) ?>">
                                        <?= htmlspecialchars($payment['payment_status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="update_status.php?id=<?= $payment['id'] ?>" 
                                       style="padding: 0.5rem 1rem; background-color: #27ae60; color: white; text-decoration: none; border-radius: 4px;">
                                        อัปเดตสถานะ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($payments)): ?>
                <div style="text-align: center; padding: 2rem; color: #7f8c8d;">
                    ไม่พบรายการชำระเงิน
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>