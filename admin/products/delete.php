<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: index.php");
    exit();
}

try {
    // เริ่ม Transaction
    $conn->beginTransaction();

    // ลบข้อมูลใน order_items ที่เกี่ยวข้องก่อน
    $stmt = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // ดึงข้อมูลรูปภาพสินค้า
    $stmt = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // ลบรูปภาพถ้ามี
        if ($product['image_url']) {
            $image_path = "../../" . $product['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // ลบข้อมูลจากตาราง products
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
    }

    // Commit Transaction
    $conn->commit();

} catch (Exception $e) {
    // Rollback Transaction หากเกิดข้อผิดพลาด
    $conn->rollBack();
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}

// ส่งกลับไปหน้ารายการสินค้า
header("Location: index.php");
exit();
?>