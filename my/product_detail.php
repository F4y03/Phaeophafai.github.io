<?php
include 'includes/header.php';
include 'includes/db.php';

$product_id = $_GET['id'] ?? null;
$product = null; // กำหนดค่าเริ่มต้น

if ($product_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("เกิดข้อผิดพลาดในการดึงข้อมูลสินค้า");
    }
}

// ตรวจสอบข้อมูลสินค้าอย่างละเอียด
if (empty($product) || !is_array($product)) {
    header("Location: 404.php");
    exit();
}
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 30px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
        <!-- ส่วนรูปภาพ -->
        <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: sticky; top: 20px;">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>" 
                     style="width: 100%; height: auto; border-radius: 10px; aspect-ratio: 1/1; object-fit: cover;">
            <?php else: ?>
                <div style="background: linear-gradient(135deg, #ecf0f1 0%, #dfe6e9 100%); height: 400px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image" style="font-size: 3em; color: #bdc3c7;"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- ส่วนรายละเอียด -->
        <div>
            <h1 style="color: #2c3e50; font-size: 2.8em; margin: 0 0 15px; line-height: 1.2; position: relative;">
                <span style="background: linear-gradient(135deg, #3498db, #2980b9); -webkit-background-clip: text; color: transparent;">
                    <?= htmlspecialchars($product['name']) ?>
                </span>
                <div style="width: 60px; height: 4px; background: #3498db; margin: 20px 0; border-radius: 2px;"></div>
            </h1>

            <!-- ราคาและสต็อก -->
            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 30px;">
                <div style="background: #e74c3c; color: white; padding: 8px 20px; border-radius: 25px; font-size: 1.8em; font-weight: 800;">
                    ฿<?= number_format($product['price'] ?? 0, 2) ?>
                </div>
                <div style="background: rgba(52, 152, 219, 0.1); color: #3498db; padding: 8px 15px; border-radius: 20px; font-weight: 600;">
                    <i class="fas fa-box-open" style="margin-right: 8px;"></i>
                    คงเหลือ <?= $product['stock_quantity'] ?? 0 ?> ชิ้น
                </div>
            </div>

            <!-- คำอธิบาย -->
            <div style="background: #f8f9fa; padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                <h3 style="color: #2c3e50; margin: 0 0 15px; font-size: 1.3em; font-weight: 700;">
                    <i class="fas fa-file-alt" style="margin-right: 10px;"></i>
                    คำอธิบายสินค้า
                </h3>
                <p style="color: #7f8c8d; line-height: 1.6; margin: 0;">
                    <?= nl2br(htmlspecialchars($product['description'] ?? 'ไม่มีคำอธิบาย')) ?>
                </p>
            </div>

            <!-- ฟอร์มเพิ่มลงตะกร้า -->
            <form action="cart.php" method="POST" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <!-- เลือกไซส์ -->
                <div style="margin-bottom: 15px;">
                    <label for="size" style="display: block; color: #7f8c8d; margin-bottom: 8px; font-weight: 600;">
                        เลือกไซส์
                    </label>
                    <select name="size" id="size" style="width: 100%; padding: 12px 20px; border: 2px solid #3498db; border-radius: 25px; font-size: 1.1em; color: #2c3e50; transition: all 0.3s ease;">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>

                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="position: relative; flex-grow: 1;">
                        <label for="quantity" style="display: block; color: #7f8c8d; margin-bottom: 8px; font-weight: 600;">
                            <i class="fas fa-cart-plus" style="margin-right: 8px;"></i>
                            จำนวนที่ต้องการ
                        </label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               min="1" 
                               max="<?= $product['stock_quantity'] ?? 1 ?>" 
                               value="1"
                               style="width: 100%;
                                      padding: 12px 20px;
                                      border: 2px solid #3498db;
                                      border-radius: 25px;
                                      font-size: 1.1em;
                                      color: #2c3e50;
                                      transition: all 0.3s ease;"
                               onfocus="this.style.borderColor='#2980b9'; this.style.boxShadow='0 0 0 3px rgba(52, 152, 219, 0.2)';"
                               onblur="this.style.borderColor='#3498db'; this.style.boxShadow='none';">
                    </div>
                    
                    <button type="submit" 
                            style="background: linear-gradient(135deg, #27ae60, #219a52);
                                   color: white;
                                   padding: 15px 35px;
                                   border: none;
                                   border-radius: 25px;
                                   font-size: 1.1em;
                                   font-weight: 700;
                                   cursor: pointer;
                                   transition: all 0.3s ease;
                                   align-self: flex-end;
                                   display: flex;
                                   align-items: center;
                                   gap: 10px;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(39, 174, 96, 0.3)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-shopping-cart"></i>
                        เพิ่มลงตะกร้า
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
