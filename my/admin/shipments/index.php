<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลการจัดส่งทั้งหมด
$stmt = $conn->query("
    SELECT shipments.*, orders.order_number 
    FROM shipments 
    JOIN orders ON shipments.order_id = orders.id
");
$shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการจัดส่ง</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>จัดการการจัดส่ง</h1>
        <nav>
            <a href="../index.php">แดชบอร์ด</a>
        </nav>
    </header>
    <main>
        <h2>รายการการจัดส่ง</h2>
        <table>
            <thead>
                <tr>
                    <th>หมายเลขติดตาม</th>
                    <th>หมายเลขคำสั่งซื้อ</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shipments as $shipment): ?>
                    <tr>
                        <td><?= $shipment['tracking_number'] ?></td>
                        <td><?= $shipment['order_number'] ?></td>
                        <td><?= $shipment['status'] ?></td>
                        <td>
                            <a href="view.php?id=<?= $shipment['id'] ?>">ดูรายละเอียด</a>
                            <a href="update_status.php?id=<?= $shipment['id'] ?>">อัปเดตสถานะ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>