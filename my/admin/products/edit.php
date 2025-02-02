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

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // ตรวจสอบข้อมูลพื้นฐาน
    if (empty($name) || empty($price) || empty($stock)) {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        try {
            $image_url = $product['image_url'];

            // จัดการอัปโหลดรูปภาพ
            if ($image['error'] === UPLOAD_ERR_OK) {
                // ลบรูปเก่าถ้ามี
                if ($image_url) {
                    $old_image_path = "../../$image_url";
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }

                // อัปโหลดรูปใหม่
                $upload_dir = '../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                $file_name = uniqid('product_') . '.' . $file_ext;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($image['tmp_name'], $target_path)) {
                    $image_url = 'uploads/' . $file_name;
                } else {
                    throw new Exception('อัปโหลดรูปภาพไม่สำเร็จ');
                }
            }

            // อัปเดตข้อมูลในฐานข้อมูล
            $stmt = $conn->prepare("UPDATE products SET 
                name = ?, 
                price = ?, 
                stock_quantity = ?, 
                description = ?, 
                image_url = ? 
                WHERE id = ?");
            $stmt->execute([$name, $price, $stock, $description, $image_url, $product_id]);

            $success = 'อัปเดตสินค้าสำเร็จแล้ว';
        } catch (Exception $e) {
            $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background-color: #f8f9fa; 
        }
        .container { 
            max-width: 800px; 
            margin: 2rem auto; 
            padding: 2rem; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .form-group { 
            margin-bottom: 1rem; 
        }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            color: #2c3e50; 
            font-weight: 500;
        }
        input, textarea, select { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        button { 
            background-color: #3498db; 
            color: white; 
            padding: 0.75rem 1.5rem; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        button:hover { 
            background-color: #2980b9; 
        }
        .error { 
            color: #e74c3c; 
            padding: 1rem; 
            background: #fdeded; 
            border-radius: 6px; 
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
        }
        .success { 
            color: #27ae60; 
            padding: 1rem; 
            background: #edf7ee; 
            border-radius: 6px; 
            margin-bottom: 1.5rem;
            border: 1px solid #c3e6cb;
        }
        .current-image {
            margin-top: 1rem;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .current-image img {
            max-width: 200px;
            height: auto;
            border-radius: 4px;
            border: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #2c3e50; margin-top: 0;">✏️ แก้ไขสินค้า</h1>
        <a href="index.php" style="display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; color: #3498db; text-decoration: none; font-weight: 500;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            กลับสู่การจักการสินค้า
        </a>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">ชื่อสินค้า</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="price">ราคา (บาท)</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">จำนวนสต็อก</label>
                <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock_quantity']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">รายละเอียดสินค้า</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">อัปโหลดรูปภาพใหม่</label>
                <input type="file" id="image" name="image" accept="image/*">
                
                <?php if ($product['image_url']): ?>
                    <div class="current-image">
                        <p style="margin: 0 0 0.5rem 0; color: #7f8c8d;">รูปภาพปัจจุบัน:</p>
                        <img src="../../<?= htmlspecialchars($product['image_url']) ?>" alt="Current Image">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" style="margin-top: 1rem;">
                บันทึกการเปลี่ยนแปลง
            </button>
        </form>
    </div>
</body>
</html>