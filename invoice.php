<?php
session_start();
include 'includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    die("ไม่พบใบเสร็จ");
}

// ดึงข้อมูลใบเสร็จ
$stmt = $conn->prepare("
    SELECT invoices.*, orders.order_number, customers.username, customers.email, customers.phone_number 
    FROM invoices 
    JOIN orders ON invoices.order_id = orders.id 
    JOIN customers ON orders.customer_id = customers.id 
    WHERE invoices.id = ?
");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("ไม่พบใบเสร็จ");
}

// ดึงรายการสินค้าในใบเสร็จ
$stmt = $conn->prepare("
    SELECT invoice_items.*, products.name 
    FROM invoice_items 
    JOIN products ON invoice_items.product_id = products.id 
    WHERE invoice_items.invoice_id = ?
");
$stmt->execute([$invoice_id]);
$invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จ</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h2>ใบเสร็จ</h2>
        <p>หมายเลขใบเสร็จ: <?= $invoice['invoice_number'] ?></p>
        <p>หมายเลขคำสั่งซื้อ: <?= $invoice['order_number'] ?></p>
        <p>ลูกค้า: <?= htmlspecialchars($invoice['username']) ?></p>
        <p>อีเมล: <?= htmlspecialchars($invoice['email']) ?></p>
        <p>เบอร์โทรศัพท์: <?= htmlspecialchars($invoice['phone_number']) ?></p>
        <p>วันที่ออกใบเสร็จ: <?= date('d/m/Y H:i', strtotime($invoice['invoice_date'])) ?></p>
        <p>ยอดรวม: <?= number_format($invoice['total_amount'], 2) ?> บาท</p>

        <h3>รายการสินค้า</h3>
        <table>
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ราคารวม</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['unit_price'], 2) ?> บาท</td>
                        <td><?= number_format($item['subtotal'], 2) ?> บาท</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button onclick="window.print()">พิมพ์ใบเสร็จ</button>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>