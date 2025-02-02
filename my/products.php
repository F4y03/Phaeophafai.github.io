<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

$stmt = $conn->query("SELECT * FROM products WHERE stock_quantity > 0");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
    <h2 style="text-align: center; color: #2c3e50; margin: 40px 0; font-size: 2.8em; text-transform: uppercase; letter-spacing: 3px; font-family: 'Arial Rounded MT Bold', sans-serif;">
        🛍️ สินค้าทั้งหมด
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 35px; padding: 20px;">
        <?php foreach ($products as $product): ?>
            <div style="background: #ffffff; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); overflow: hidden; position: relative;">
                <?php if ($product['image_url']): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                         style="width: 100%; height: 220px; object-fit: cover; border-bottom: 3px solid #f8f9fa;">
                <?php else: ?>
                    <div style="height: 220px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; color: #6c757d;">
                        <i class="fas fa-image" style="font-size: 2.5em; opacity: 0.5;"></i>
                    </div>
                <?php endif; ?>
                
                <div style="padding: 25px; text-align: center;">
                    <h3 style="color: #2c3e50; margin: 0 0 15px; font-size: 1.3em; min-height: 60px; line-height: 1.4; font-weight: 600;">
                        <?= htmlspecialchars($product['name']) ?>
                    </h3>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 20px;">
                        <span style="color: #e74c3c; font-size: 1.6em; font-weight: 700; text-shadow: 0 2px 4px rgba(231, 76, 60, 0.1);">
                            ฿<?= number_format($product['price'], 2) ?>
                        </span>
                        <span style="color: #27ae60; font-size: 0.9em; background: rgba(39, 174, 96, 0.1); padding: 3px 8px; border-radius: 5px;">
                            <?= htmlspecialchars($product['stock_quantity']) ?> in stock
                        </span>
                    </div>
                    
                    <a href="product_detail.php?id=<?= $product['id'] ?>" 
                       style="display: inline-flex; align-items: center; gap: 8px; background: #3498db; color: white; padding: 12px 30px; border-radius: 30px; text-decoration: none; transition: all 0.3s ease; margin: 10px 0;"
                       onmouseover="this.style.background='#2980b9'; this.style.transform='translateY(-2px)'" 
                       onmouseout="this.style.background='#3498db'; this.style.transform='translateY(0)'">
                        <i class="fas fa-info-circle"></i>
                        ดูรายละเอียด
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<style>
    /* เพิ่ม Font Awesome สำหรับไอคอน */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    
    /* เอฟเฟกต์เงาเมื่อโฮเวอร์ */
    .product-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transform: translateY(-5px);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .product-title {
            font-size: 1.1em;
        }
    }
</style>