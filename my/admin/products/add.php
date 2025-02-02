<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];

    // อัปโหลดรูปภาพ
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/products/'; // โฟลเดอร์เก็บรูปภาพ
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // สร้างโฟลเดอร์หากไม่มี
        }

        $file_name = basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;

        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            $error = "ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ JPEG, PNG, GIF)";
        } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image_url = 'assets/images/products/' . $file_name; // บันทึก URL ของรูปภาพ
        } else {
            $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
        }
    } else {
        $image_url = null; // หากไม่มีการอัปโหลดรูปภาพ
    }

    if (empty($error)) {
        // เพิ่มสินค้าใหม่
        $stmt = $conn->prepare("INSERT INTO products (sku, name, type, price, description, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$sku, $name, $type, $price, $description, $stock_quantity, $image_url])) {
            $success = "เพิ่มสินค้าสำเร็จ!";
        } else {
            $error = "เกิดข้อผิดพลาดในการเพิ่มสินค้า";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">เพิ่มสินค้าใหม่</h1>
        <nav style="margin-top: 1rem; display: flex; gap: 1.5rem;">
            <a href="index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่รายการสินค้า
            </a>
        </nav>
    </header>

    <main style="max-width: 800px; margin: 2rem auto; padding: 0 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            
            <?php if ($error): ?>
                <div style="background: #fee; padding: 1rem; border-radius: 4px; border: 1px solid #f5c6cb; color: #721c24; margin-bottom: 1.5rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: #efffed; padding: 1rem; border-radius: 4px; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 1.5rem;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="add.php" method="POST" enctype="multipart/form-data" style="display: grid; gap: 1.2rem;">
                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">SKU</label>
                    <input type="text" name="sku" required 
                           style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">ชื่อสินค้า</label>
                    <input type="text" name="name" required 
                           style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">ประเภท</label>
                    <input type="text" name="type" required 
                           style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">ราคา</label>
                    <input type="number" name="price" step="0.01" required 
                           style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">คำอธิบาย</label>
                    <textarea name="description" rows="4" required 
                              style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;"></textarea>
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">สต็อก</label>
                    <input type="number" name="stock_quantity" required 
                           style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                </div>

                <div style="display: grid; gap: 0.5rem;">
                    <label style="font-weight: 500; color: #2c3e50;">รูปภาพสินค้า</label>
                    <input type="file" name="image" accept="image/*" 
                           style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <button type="submit" 
                        style="background-color: #27ae60; color: white; padding: 1rem 2rem; border: none; border-radius: 4px; 
                               font-size: 1rem; cursor: pointer; transition: background-color 0.3s; margin-top: 1rem;"
                        onmouseover="this.style.backgroundColor='#219a52'"
                        onmouseout="this.style.backgroundColor='#27ae60'">
                    📦 เพิ่มสินค้า
                </button>
            </form>
        </div>
    </main>

</body>
</html>