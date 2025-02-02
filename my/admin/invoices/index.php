<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลใบแจ้งหนี้ทั้งหมด
$stmt = $conn->query("
    SELECT invoices.*, orders.order_number 
    FROM invoices 
    JOIN orders ON invoices.order_id = orders.id
");
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการใบแจ้งหนี้</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>จัดการใบแจ้งหนี้</h1>
        <nav>
            <a href="../index.php">แดชบอร์ด</a>
        </nav>
    </header>
    <main>
        <h2>รายการใบแจ้งหนี้</h2>
        <table>
            <thead>
                <tr>
                    <th>หมายเลขใบแจ้งหนี้</th>
                    <th>หมายเลขคำสั่งซื้อ</th>
                    <th>ยอดรวม</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?= $invoice['invoice_number'] ?></td>
                        <td><?= $invoice['order_number'] ?></td>
                        <td><?= number_format($invoice['total_amount'], 2) ?> บาท</td>
                        <td><?= $invoice['invoice_status'] ?></td>
                        <td>
                            <a href="view.php?id=<?= $invoice['id'] ?>">ดูรายละเอียด</a>
                            <a href="update_status.php?id=<?= $invoice['id'] ?>">อัปเดตสถานะ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>