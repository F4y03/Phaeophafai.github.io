<?php
session_start();
include 'includes/db.php';

// ตรวจสอบการล็อกอิน
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบตะกร้าสินค้า
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// ตั้งค่าไดเรกทอรีอัปโหลด
$upload_dir = 'uploads/slips/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ประกาศตัวแปรเริ่มต้น
$error = '';
$total_amount = 0;
$cart_items = [];

// ดึงข้อมูลสินค้าในตะกร้า
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $subtotal = $product['price'] * $quantity;
        $total_amount += $subtotal;
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// กรณีประมวลผลฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = $_POST['shipping_address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $slip_filename = null;

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($shipping_address) || empty($payment_method)) {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } else {
        // ตรวจสอบการอัปโหลดสลิปสำหรับโอนเงิน
        if ($payment_method === 'bank_transfer') {
            if (!isset($_FILES['payment_slip']) || $_FILES['payment_slip']['error'] !== UPLOAD_ERR_OK) {
                $error = "กรุณาอัปโหลดสลิปการโอนเงิน";
            } else {
                // ตรวจสอบประเภทและขนาดไฟล์
                $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
                $file_type = $_FILES['payment_slip']['type'];
                $file_size = $_FILES['payment_slip']['size'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($file_type, $allowed_types)) {
                    $error = "ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ JPEG, PNG, PDF)";
                } elseif ($file_size > $max_size) {
                    $error = "ขนาดไฟล์เกิน 5MB";
                } else {
                    // สร้างชื่อไฟล์ใหม่
                    $filename = uniqid() . '_' . basename($_FILES['payment_slip']['name']);
                    $target_path = $upload_dir . $filename;

                    if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], $target_path)) {
                        $error = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
                    } else {
                        $slip_filename = $filename;
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $conn->beginTransaction();

                // สร้างคำสั่งซื้อ (ต้องมีคอลัมน์ shipping_address ในตาราง orders)
                $stmt = $conn->prepare("INSERT INTO orders (customer_id, order_number, total_amount, shipping_address, status) VALUES (?, ?, ?, ?, 'pending')");
                $order_number = 'ORD' . uniqid();
                $stmt->execute([$_SESSION['user_id'], $order_number, $total_amount, $shipping_address]);
                $order_id = $conn->lastInsertId();

                // บันทึกรายการสินค้า
                foreach ($cart_items as $item) {
                    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['product']['id'], $item['quantity'], $item['product']['price'], $item['subtotal']]);
                }

                // สร้างใบเสร็จ
                $invoice_number = 'INV' . uniqid();
                $stmt = $conn->prepare("INSERT INTO invoices (order_id, invoice_number, total_amount, slip_filename) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $invoice_number, $total_amount, $slip_filename]);

                $conn->commit();

                // เก็บข้อมูลบางส่วนใน session (อาจใช้ในหน้า confirmation ได้)
                $_SESSION['order_success'] = [
                    'order_number'    => $order_number,
                    'invoice_number'  => $invoice_number,
                    'shipping_address'=> $shipping_address,
                ];

                // ล้างตะกร้า
                unset($_SESSION['cart']);

                // Redirect ไปยังหน้า order_success.php โดยส่ง order_id เป็น parameter
                header("Location: order_success.php?order_id=" . $order_id);
                exit();

            } catch (Exception $e) {
                $conn->rollBack();
                $error = "เกิดข้อผิดพลาดในการประมวลผล: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        main { max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffcdd2; color: #f44336; }
        form { display: flex; flex-direction: column; gap: 20px; margin-top: 30px; }
        textarea, select, input[type="file"] { width: 100%; padding: 12px; border: 1px solid #bdc3c7; border-radius: 6px; }
        button { background: #27ae60; color: white; padding: 15px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        button:hover { background: #219a52; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const slipUpload = document.getElementById('slip-upload-section');

            function toggleSlipUpload() {
                slipUpload.style.display = paymentMethod.value === 'bank_transfer' ? 'block' : 'none';
            }

            paymentMethod.addEventListener('change', toggleSlipUpload);
            toggleSlipUpload();
        });
    </script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <h2 style="text-align: center; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">ชำระเงิน</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" enctype="multipart/form-data">
            <div>
                <label>ที่อยู่จัดส่ง:</label>
                <textarea 
                    name="shipping_address" 
                    required
                    placeholder="กรุณากรอกที่อยู่จัดส่งของคุณ"
                ><?= htmlspecialchars($_POST['shipping_address'] ?? '') ?></textarea>
            </div>

            <div>
                <label>วิธีการชำระเงิน:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="" disabled selected>เลือกวิธีการชำระเงิน</option>
                    <option value="credit_card" <?= ($_POST['payment_method'] ?? '') === 'credit_card' ? 'selected' : '' ?>>บัตรเครดิต</option>
                    <option value="bank_transfer" <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>โอนเงินผ่านธนาคาร</option>
                </select>
            </div>

            <div id="slip-upload-section" style="display: none;">
                <label>อัปโหลดสลิปการโอนเงิน:</label>
                <input 
                    type="file" 
                    name="payment_slip" 
                    accept=".jpg,.jpeg,.png,.pdf"
                >
                <small>รองรับไฟล์ JPEG, PNG, PDF (ขนาดไม่เกิน 5MB)</small>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <p style="text-align: center; font-size: 1.2em; color: #27ae60; font-weight: 700;">
                    ยอดชำระเงินรวม: ฿<?= number_format($total_amount, 2) ?>
                </p>
            </div>

            <button type="submit">📦 ยืนยันการสั่งซื้อ</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
