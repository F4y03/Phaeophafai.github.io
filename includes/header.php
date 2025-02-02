<?php 
// session_start(); // เปิดคอมเมนต์หากต้องการใช้ session
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phaeophafai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        h1 {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        nav a:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        nav a:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            nav {
                gap: 0.8rem;
                padding: 0 1rem;
            }
            
            nav a {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            nav {
                flex-direction: column;
                align-items: center;
            }
            
            nav a {
                width: 100%;
                justify-content: center;
            }
        }

        /* สไตล์ไอคอน (สามารถเพิ่มไอคอน Font Awesome ได้ในภายหลัง) */
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <header>
        <h1> Phaeophafai</h1>
        <nav>
            <a href="index.php"><span class="icon">🏠</span>สินค้าแนะนำ</a>
            <a href="products.php"><span class="icon">📦</span>สินค้า</a>
            <a href="cart.php"><span class="icon">🛒</span>ตะกร้าสินค้า</a>
            <a href="order_tracking.php"><span class="icon">📮</span>สถานะคำสั่งซื้อ</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user_account.php"><span class="icon">👤</span>บัญชีผู้ใช้</a>
                <a href="logout.php"><span class="icon">🚪</span>ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php"><span class="icon">🔑</span>เข้าสู่ระบบ</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>