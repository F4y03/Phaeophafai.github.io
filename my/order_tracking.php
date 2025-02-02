<?php
session_start();
include 'includes/db.php';

$error = '';
$order = null;
$order_items = [];
$payments = [];
$shipments = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = $_POST['order_number'];

    // ดึงข้อมูลคำสั่งซื้อ
    $stmt = $conn->prepare("
        SELECT orders.*, customers.username, customers.email, customers.phone_number 
        FROM orders 
        JOIN customers ON orders.customer_id = customers.id 
        WHERE orders.order_number = ?
    ");
    $stmt->execute([$order_number]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // ดึงรายการสินค้าในคำสั่งซื้อ
        $stmt = $conn->prepare("
            SELECT order_items.*, products.name, products.image_url 
            FROM order_items 
            JOIN products ON order_items.product_id = products.id 
            WHERE order_items.order_id = ?
        ");
        $stmt->execute([$order['id']]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ดึงข้อมูลการชำระเงิน
        $stmt = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
        $stmt->execute([$order['id']]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ดึงข้อมูลการจัดส่ง
        $stmt = $conn->prepare("SELECT * FROM shipments WHERE order_id = ?");
        $stmt->execute([$order['id']]);
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = "ไม่พบคำสั่งซื้อที่ตรงกับหมายเลขนี้";
    }
}
?>


<?php include 'includes/header.php'; ?>
    <main style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; box-shadow: 0 2px 15px rgba(0,0,0,0.1); border-radius: 8px;">
        <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">ตรวจสอบสถานะคำสั่งซื้อ</h2>
        
        <form action="order_tracking.php" method="POST" style="margin-bottom: 30px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label for="order_number" style="font-weight: bold; color: #34495e; min-width: 120px;">หมายเลขคำสั่งซื้อ:</label>
                <input 
                    type="text" 
                    id="order_number" 
                    name="order_number" 
                    required
                    style="padding: 10px 15px; border: 1px solid #bdc3c7; border-radius: 4px; flex-grow: 1; font-size: 16px;"
                >
                <button 
                    type="submit" 
                    style="background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; transition: all 0.3s; font-weight: bold;"
                    onmouseover="this.style.background='#2980b9'" 
                    onmouseout="this.style.background='#3498db'"
                >
                    ตรวจสอบ
                </button>
            </div>
        </form>

        <?php if ($error): ?>
            <div style="background: #fee; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                ⚠️ <?= $error ?>
            </div>
        <?php elseif ($order): ?>
            <!-- Order Information Section -->
            <div style="margin-bottom: 35px; padding: 20px; background: #f8fafc; border-radius: 8px;">
                <h3 style="color: #2c3e50; margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">ข้อมูลคำสั่งซื้อ</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div style="background: white; padding: 15px; border-radius: 6px;">
                        <p style="margin: 8px 0;"><strong>📦 หมายเลขคำสั่งซื้อ:</strong><br><?= $order['order_number'] ?></p>
                        <p style="margin: 8px 0;"><strong>👤 ลูกค้า:</strong><br><?= htmlspecialchars($order['username']) ?></p>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 6px;">
                        <p style="margin: 8px 0;"><strong>📧 อีเมล:</strong><br><?= htmlspecialchars($order['email']) ?></p>
                        <p style="margin: 8px 0;"><strong>📱 เบอร์โทรศัพท์:</strong><br><?= htmlspecialchars($order['phone_number']) ?></p>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 6px;">
                        <p style="margin: 8px 0;"><strong>💵 ยอดรวม:</strong><br><span style="color: #27ae60;"><?= number_format($order['total_amount'], 2) ?> บาท</span></p>
                        <p style="margin: 8px 0;"><strong>🔄 สถานะ:</strong><br><span style="color: #e67e22; font-weight: bold;"><?= $order['status'] ?></span></p>
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div style="margin-bottom: 35px;">
                <h3 style="color: #2c3e50; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">📦 รายการสินค้า</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">สินค้า</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; width: 120px;">รูปภาพ</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ecf0f1; width: 80px;">จำนวน</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 2px solid #ecf0f1; width: 120px;">ราคาต่อหน่วย</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 2px solid #ecf0f1; width: 120px;">ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr style="border-bottom: 1px solid #ecf0f1;">
                                    <td style="padding: 12px;"><?= htmlspecialchars($item['name']) ?></td>
                                    <td style="padding: 12px;">
                                        <?php if ($item['image_url']): ?>
                                            <img 
                                                src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                alt="<?= htmlspecialchars($item['name']) ?>" 
                                                style="width: 100px; height: 100px; object-fit: contain; border-radius: 4px; border: 1px solid #ddd;"
                                            >
                                        <?php else: ?>
                                            <div style="width: 100px; height: 100px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px; color: #95a5a6;">
                                                ไม่มีรูปภาพ
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: center;"><?= $item['quantity'] ?></td>
                                    <td style="padding: 12px; text-align: right;"><?= number_format($item['unit_price'], 2) ?> บาท</td>
                                    <td style="padding: 12px; text-align: right; color: #27ae60;"><?= number_format($item['subtotal'], 2) ?> บาท</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Section -->
            <div style="margin-bottom: 35px;">
                <h3 style="color: #2c3e50; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">💳 ข้อมูลการชำระเงิน</h3>
                <?php if (!empty($payments)): ?>
                    <div style="overflow-x: auto; margin-top: 15px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">หมายเลขการชำระเงิน</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #ecf0f1;">ยอดชำระ</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">วิธีการชำระเงิน</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ecf0f1; width: 100px;">สถานะ</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ecf0f1; width: 120px;">สลิป</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr style="border-bottom: 1px solid #ecf0f1;">
                                        <td style="padding: 12px;"><?= $payment['payment_number'] ?></td>
                                        <td style="padding: 12px; text-align: right;"><?= number_format($payment['amount'], 2) ?> บาท</td>
                                        <td style="padding: 12px;"><?= $payment['payment_method'] ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 15px; background: <?= $payment['payment_status'] === 'สำเร็จ' ? '#d4edda' : '#fff3cd' ?>; color: <?= $payment['payment_status'] === 'สำเร็จ' ? '#155724' : '#856404' ?>;">
                                                <?= $payment['payment_status'] ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <?php if ($payment['slip_url']): ?>
                                                <a 
                                                    href="<?= htmlspecialchars($payment['slip_url']) ?>" 
                                                    target="_blank"
                                                    style="display: inline-block; padding: 6px 15px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; transition: all 0.3s;"
                                                    onmouseover="this.style.background='#2980b9'" 
                                                    onmouseout="this.style.background='#3498db'"
                                                >
                                                    ดูสลิป
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #95a5a6;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #95a5a6;">
                        มีการชำระเงิน
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipping Section -->
            <div style="margin-bottom: 20px;">
                <h3 style="color: #2c3e50; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">🚚 ข้อมูลการจัดส่ง</h3>
                <?php if (!empty($shipments)): ?>
                    <div style="overflow-x: auto; margin-top: 15px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">หมายเลขติดตาม</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; width: 120px;">สถานะ</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">ที่อยู่จัดส่ง</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; width: 150px;">วันที่จัดส่ง</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; width: 150px;">วันที่ส่งถึง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shipments as $shipment): ?>
                                    <tr style="border-bottom: 1px solid #ecf0f1;">
                                        <td style="padding: 12px;"><?= $shipment['tracking_number'] ?></td>
                                        <td style="padding: 12px;">
                                            <?php
                                            $status_color = match($shipment['status']) {
                                                'จัดส่งแล้ว' => ['#d4edda', '#155724'],
                                                'กำลังจัดส่ง' => ['#fff3cd', '#856404'],
                                                default => ['#f8d7da', '#721c24']
                                            };
                                            ?>
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 15px; background: <?= $status_color[0] ?>; color: <?= $status_color[1] ?>;">
                                                <?= $shipment['status'] ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($shipment['shipping_address']) ?></td>
                                        <td style="padding: 12px;"><?= $shipment['shipped_at'] ? date('d/m/Y H:i', strtotime($shipment['shipped_at'])) : '⏳ ยังไม่จัดส่ง' ?></td>
                                        <td style="padding: 12px;"><?= $shipment['delivered_at'] ? date('d/m/Y H:i', strtotime($shipment['delivered_at'])) : '⏳ ยังไม่ส่งถึง' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #95a5a6;">
                        🚫 ยังไม่มีการจัดส่ง
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php include 'includes/footer.php'; ?>