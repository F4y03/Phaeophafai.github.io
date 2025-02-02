<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];

    $stmt = $conn->prepare("
        SELECT invoices.*, orders.order_number 
        FROM invoices 
        JOIN orders ON invoices.order_id = orders.id
        WHERE invoices.id = :invoice_id
    ");
    $stmt->execute([':invoice_id' => $invoice_id]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        header("Location: manage_invoices.php");
        exit();
    }
} else {
    header("Location: manage_invoices.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูใบเสร็จ</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>ดูใบเสร็จ</h1>
        <nav>
            <a href="../index.php">แดชบอร์ด</a>
        </nav>
    </header>
    <main>
        <h2>รายละเอียดใบเสร็จ</h2>
        <p><strong>หมายเลขใบเสร็จ:</strong> <?= $invoice['invoice_number'] ?></p>
        <p><strong>หมายเลขคำสั่งซื้อ:</strong> <?= $invoice['order_number'] ?></p>
        <p><strong>ยอดรวม:</strong> <?= number_format($invoice['total_amount'], 2) ?> บาท</p>
        <p><strong>สถานะ:</strong> <?= $invoice['invoice_status'] ?></p>
        <button onclick="window.print()">พิมพ์ใบเสร็จ</button>
    </main>
</body>
</html>