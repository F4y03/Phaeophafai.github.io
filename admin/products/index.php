<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลสินค้าทั้งหมด
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1rem 2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h1 style="color: white; margin: 0; font-size: 1.8rem;">จัดการสินค้า</h1>
        <nav style="margin-top: 1rem; display: flex; gap: 1rem;">
            <a href="../index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่แดชบอร์ด
            </a>
            <a href="add.php" style="background-color: #27ae60; color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.backgroundColor='#219a52'; transform: translateY(-2px)" 
               onmouseout="this.style.backgroundColor='#27ae60'; transform: none">
               ➕ เพิ่มสินค้า
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 1rem;">📦 รายการสินค้า</h2>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">ID</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; width: 120px;">รูปภาพ</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1;">ชื่อสินค้า</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #ecf0f1;">ราคา</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ecf0f1;">สต็อก</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ecf0f1;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid #ecf0f1;">
                                <td style="padding: 12px; color: #7f8c8d;">#<?= $product['id'] ?></td>
                                <td style="padding: 12px;">
                                    <?php if ($product['image_url']): ?>
                                        <img 
                                            src="../../<?= htmlspecialchars($product['image_url']) ?>" 
                                            alt="<?= htmlspecialchars($product['name']) ?>" 
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"
                                        >
                                    <?php else: ?>
                                        <div style="width: 100px; height: 100px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px; color: #95a5a6;">
                                            ไม่มีรูปภาพ
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($product['name']) ?></td>
                                <td style="padding: 12px; text-align: right; color: #27ae60;"><?= number_format($product['price'], 2) ?> บาท</td>
                                <td style="padding: 12px; text-align: center; color: <?= $product['stock_quantity'] > 0 ? '#2ecc71' : '#e74c3c' ?>;">
                                    <?= $product['stock_quantity'] ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a 
                                            href="edit.php?id=<?= $product['id'] ?>" 
                                            style="padding: 6px 12px; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; transition: all 0.3s;"
                                            onmouseover="this.style.backgroundColor='#2980b9'" 
                                            onmouseout="this.style.backgroundColor='#3498db'"
                                        >
                                            แก้ไข
                                        </a>
                                        <a 
                                            href="delete.php?id=<?= $product['id'] ?>" 
                                            style="padding: 6px 12px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 4px; transition: all 0.3s;"
                                            onmouseover="this.style.backgroundColor='#c0392b'" 
                                            onmouseout="this.style.backgroundColor='#e74c3c'"
                                            onclick="return confirm('คุณแน่ใจหรือไม่?')"
                                        >
                                            ลบ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>