<?php
session_start();
include 'includes/db.php';

// ตรวจสอบการล็อกอิน
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบ parameter order_id
if (!isset($_GET['order_id'])) {
    echo "ไม่มีหมายเลขคำสั่งซื้อ";
    exit();
}

$order_id = $_GET['order_id'];

// ดึงข้อมูลคำสั่งซื้อ
$stmt = $conn->prepare("SELECT o.*, i.invoice_number FROM orders o LEFT JOIN invoices i ON o.id = i.order_id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "ไม่พบข้อมูลคำสั่งซื้อ";
    exit();
}

// ดึงข้อมูลผู้ใช้ (สมมุติว่ามีข้อมูลชื่ออยู่ในตาราง users)
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$customer_name = $user['name'] ?? 'ลูกค้า';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสั่งซื้อ</title>
    <style>
        body { 
            margin: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f5f5f5; 
        }
        main { 
            max-width: 800px; 
            margin: 40px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        .success { 
            background: #e8f5e9; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 20px 0; 
            border: 1px solid #c8e6c9; 
            color: #4caf50; 
        }
        .order-info { 
            margin-top: 20px; 
        }
        .order-info p { 
            font-size: 1.1em; 
            margin: 8px 0; 
        }
        /* ตกแต่งปุ่มกลับหน้าหลัก */
        .btn-back-home {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin-top: 20px;
        }
        .btn-back-home:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h2 style="text-align: center; color: #2c3e50;">สั่งซื้อสำเร็จ!</h2>
        <div class="success">
            <p>ขอบคุณที่สั่งซื้อสินค้า <strong><?= htmlspecialchars($customer_name) ?></strong></p>
            <div class="order-info">
                <p>หมายเลขคำสั่งซื้อ: <strong><?= htmlspecialchars($order['order_number']) ?></strong></p>
                <p>หมายเลขใบเสร็จ: <strong><?= htmlspecialchars($order['invoice_number']) ?></strong></p>
                <p>ที่อยู่จัดส่ง: <strong><?= htmlspecialchars($order['shipping_address']) ?></strong></p>
            </div>
        </div>
        <div style="text-align: center;">
            <a href="index.php" class="btn-back-home">กลับหน้าหลัก</a>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
