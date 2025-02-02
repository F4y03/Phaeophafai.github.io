<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

$stmt = $conn->query("SELECT * FROM products WHERE stock_quantity > 0 LIMIT 5");
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 30px; background: #f8f9fa; border-radius: 15px;">
    <h2 style="text-align: center; color: #2c3e50; margin-bottom: 50px; font-size: 2.8em; position: relative;">
        <span style="background: linear-gradient(135deg, #3498db, #9b59b6); -webkit-background-clip: text; color: transparent;">
            สินค้าแนะนำ
        </span>
        <div style="width: 60px; height: 4px; background: #3498db; margin: 15px auto; border-radius: 2px;"></div>
    </h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 35px; padding: 20px;">
        <?php foreach ($featured_products as $product): ?>
            <div style="background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative; overflow: hidden;">
                <div style="padding: 25px; text-align: center;">
                    <!-- Badge -->
                    <div style="position: absolute; top: 15px; right: -40px; background: #e74c3c; color: white; padding: 8px 40px; transform: rotate(45deg); font-size: 0.9em;">
                        HOT
                    </div>
                    
                    <h3 style="color: #2c3e50; margin: 0 0 15px; font-size: 1.4em; font-weight: 700; min-height: 60px;">
                        <?= htmlspecialchars($product['name']) ?>
                    </h3>

                    <div style="margin: 25px 0;">
                        <p style="color: #e74c3c; font-size: 1.8em; margin: 0; font-weight: 800;">
                            ฿<?= number_format($product['price'], 2) ?>
                        </p>
                        <p style="color: #95a5a6; font-size: 0.9em; margin: 5px 0;">
                            คงเหลือ <?= $product['stock_quantity'] ?> ชิ้น
                        </p>
                    </div>

                    <a href="product_detail.php?id=<?= $product['id'] ?>" 
                       style="display: inline-block; 
                              background: linear-gradient(135deg, #3498db, #2980b9); 
                              color: white; 
                              padding: 12px 35px; 
                              border-radius: 30px; 
                              text-decoration: none; 
                              transition: all 0.3s ease;
                              position: relative;
                              overflow: hidden;
                              border: none;
                              cursor: pointer;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(52, 152, 219, 0.4)';" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <span style="position: relative; z-index: 1;">
                            <i class="fas fa-eye" style="margin-right: 8px;"></i>
                            ดูรายละเอียด
                        </span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<style>
    /* เพิ่มเอฟเฟกต์เมื่อโฮเวอร์การ์ด */
    main > div > div:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    /* เพิ่มอนิเมชันให้กับ Badge HOT */
    main > div > div > div:first-child {
        transition: right 0.3s ease;
    }

    main > div > div:hover > div:first-child {
        right: -35px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        main {
            margin: 20px !important;
            padding: 20px !important;
        }
        
        h2 {
            font-size: 2.2em !important;
        }
    }

    /* เพิ่ม Font Awesome */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
</style>