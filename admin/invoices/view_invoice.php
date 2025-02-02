<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $invoice_id = $_POST['invoice_id'];
    $invoice_status = $_POST['invoice_status'];

    $stmt = $conn->prepare("
        UPDATE invoices 
        SET invoice_status = :invoice_status 
        WHERE id = :invoice_id
    ");
    $stmt->execute([
        ':invoice_status' => $invoice_status,
        ':invoice_id' => $invoice_id
    ]);

    header("Location: manage_invoices.php");
    exit();
}

if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];

    $stmt = $conn->prepare("
        SELECT * FROM invoices 
        WHERE id = :invoice_id
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
    <title>อัปเดตสถานะใบเสร็จ</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>อัปเดตสถานะใบเสร็จ</h1>
        <nav>
            <a href="../index.php">แดชบอร์ด</a>
        </nav>
    </header>
    <main>
        <h2>อัปเดตสถานะใบเสร็จ</h2>
        <form method="POST" action="">
            <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
            <label for="invoice_status">สถานะ:</label>
            <select id="invoice_status" name="invoice_status" required>
                <option value="pending" <?= $invoice['invoice_status'] == 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                <option value="paid" <?= $invoice['invoice_status'] == 'paid' ? 'selected' : '' ?>>ชำระเงินแล้ว</option>
                <option value="cancelled" <?= $invoice['invoice_status'] == 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
            </select>
            <button type="submit" name="update_status">อัปเดตสถานะ</button>
        </form>
    </main>
</body>
</html>