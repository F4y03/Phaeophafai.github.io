<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// จัดการการกระทำในตะกร้า
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $product_id = $_POST['product_id'] ?? null;

        // ลบสินค้า
        if ($action === 'delete' && $product_id) {
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        // อัปเดตจำนวน
        elseif ($action === 'update' && $product_id && isset($_POST['quantity'])) {
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    } else {
        // เพิ่มสินค้า
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}

// ดึงข้อมูลสินค้าในตะกร้า
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity
            ];
        }
    }
}
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 30px; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="font-size: 32px; color: #2c3e50; text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #3498db;">ตะกร้าสินค้า</h2>
    
    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 30px; background: #f8f9fa; border-radius: 8px; margin: 20px 0;">
            <p style="font-size: 18px; color: #7f8c8d; margin: 0;">⛔️ ตะกร้าสินค้าว่าง</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #3498db; color: white;">
                        <th style="padding: 16px 20px; text-align: left; border: 1px solid #2980b9;">สินค้า</th>
                        <th style="padding: 16px 20px; text-align: center; border: 1px solid #2980b9; min-width: 200px;">จำนวน</th>
                        <th style="padding: 16px 20px; text-align: center; border: 1px solid #2980b9; min-width: 150px;">การจัดการ</th>
                        <th style="padding: 16px 20px; text-align: right; border: 1px solid #2980b9; min-width: 150px;">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $index => $item): ?>
                        <tr style="background: <?= $index % 2 === 0 ? '#ffffff' : '#f8f9fa' ?>;">
                            <td style="padding: 15px 20px; border: 1px solid #ecf0f1; color: #34495e; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <?php if (!empty($item['product']['image'])): ?>
                                        <img src="<?= $item['product']['image'] ?>" alt="<?= $item['product']['name'] ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #ecf0f1; border-radius: 8px;"></div>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($item['product']['name']) ?>
                                </div>
                            </td>
                            <td style="padding: 15px 20px; border: 1px solid #ecf0f1; text-align: center; vertical-align: middle;">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width: 70px; padding: 8px; margin-right: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="submit" class="update-btn">อัปเดต</button>
                                </form>
                            </td>
                            <td style="padding: 15px 20px; border: 1px solid #ecf0f1; text-align: center; vertical-align: middle;">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                                    <button type="submit" class="delete-btn">ลบ</button>
                                </form>
                            </td>
                            <td style="padding: 15px 20px; border: 1px solid #ecf0f1; text-align: right; color: #34495e; vertical-align: middle; font-weight: 600;">
                                <?= number_format($item['subtotal'], 2) ?> บาท
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 30px;">
            <a href="checkout.php" style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 40px; background: #27ae60; color: white; text-decoration: none; border-radius: 8px; transition: all 0.3s; border: 2px solid #219a52; font-size: 16px; font-weight: 500;">
                <svg style="width: 20px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
                ดำเนินการชำระเงิน
            </a>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<style>
    a[href="checkout.php"]:hover {
        background: #219a52 !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
    }

    .update-btn {
        padding: 8px 15px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .update-btn:hover {
        background: #2980b9;
        transform: translateY(-1px);
    }

    .delete-btn {
        padding: 8px 15px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .delete-btn:hover {
        background: #c0392b;
        transform: translateY(-1px);
    }
</style>