<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$shipment_id = $_GET['id'] ?? null;

if (!$shipment_id) {
    die("ไม่พบข้อมูลการจัดส่ง");
}

// ดึงข้อมูลการจัดส่ง
$stmt = $conn->prepare("SELECT * FROM shipments WHERE id = ?");
$stmt->execute([$shipment_id]);
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shipment) {
    die("ไม่พบข้อมูลการจัดส่ง");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $shipped_at = $_POST['shipped_at'];
    $delivered_at = $_POST['delivered_at'];

    // อัปเดตสถานะการจัดส่ง
    $stmt = $conn->prepare("UPDATE shipments SET status = ?, shipped_at = ?, delivered_at = ? WHERE id = ?");
    if ($stmt->execute([$status, $shipped_at, $delivered_at, $shipment_id])) {
        $success = "อัปเดตสถานะการจัดส่งสำเร็จ!";
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดตสถานะการจัดส่ง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตสถานะการจัดส่ง</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>อัปเดตสถานะการจัดส่ง</h1>
        <nav>
            <a href="index.php">กลับไปรายการการจัดส่ง</a>
        </nav>
    </header>
    <main>
        <?php if ($error): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: green;"><?= $success ?></p>
        <?php endif; ?>
        <form action="update_status.php?id=<?= $shipment_id ?>" method="POST">
            <label for="status">สถานะ:</label>
            <select id="status" name="status" required>
                <option value="pending" <?= $shipment['status'] === 'pending' ? 'selected' : '' ?>>ยังไม่จัดส่ง</option>
                <option value="shipped" <?= $shipment['status'] === 'shipped' ? 'selected' : '' ?>>จัดส่งแล้ว</option>
            </select>

            <label for="shipped_at">วันที่จัดส่ง:</label>
            <input type="datetime-local" id="shipped_at" name="shipped_at" value="<?= $shipment['shipped_at'] ? date('Y-m-d\TH:i', strtotime($shipment['shipped_at'])) : '' ?>">

            <label for="delivered_at">วันที่ส่งถึง:</label>
            <input type="datetime-local" id="delivered_at" name="delivered_at" value="<?= $shipment['delivered_at'] ? date('Y-m-d\TH:i', strtotime($shipment['delivered_at'])) : '' ?>">

            <button type="submit">อัปเดตสถานะ</button>
        </form>
    </main>
</body>
</html>