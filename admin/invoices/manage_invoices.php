<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_invoice'])) {
    $order_id = $_POST['order_id'];
    $total_amount = $_POST['total_amount'];
    $invoice_status = 'pending'; // สถานะเริ่มต้น

    // สร้างหมายเลขใบเสร็จ (ตัวอย่างง่ายๆ)
    $invoice_number = 'INV-' . time();

    // บันทึกใบเสร็จลงฐานข้อมูล
    $stmt = $conn->prepare("
        INSERT INTO invoices (invoice_number, order_id, total_amount, invoice_status)
        VALUES (:invoice_number, :order_id, :total_amount, :invoice_status)
    ");
    $stmt->execute([
        ':invoice_number' => $invoice_number,
        ':order_id' => $order_id,
        ':total_amount' => $total_amount,
        ':invoice_status' => $invoice_status
    ]);

    header("Location: manage_invoices.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างใบเสร็จ</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>สร้างใบเสร็จ</h1>
        <nav>
            <a href="../index.php">แดชบอร์ด</a>
        </nav>
    </header>
    <main>
        <h2>สร้างใบเสร็จใหม่</h2>
        <form method="POST" action="">
            <label for="order_id">หมายเลขคำสั่งซื้อ:</label>
            <input type="text" id="order_id" name="order_id" required>
            <label for="total_amount">ยอดรวม:</label>
            <input type="number" step="0.01" id="total_amount" name="total_amount" required>
            <button type="submit" name="create_invoice">สร้างใบเสร็จ</button>
        </form>
    </main>
</body>
</html>