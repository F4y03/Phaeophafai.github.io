<?php
session_start();
include '../includes/db.php';

// เปิดแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// ฟังก์ชันดำเนินการ query
function executeQuery($conn, $sql) {
    try {
        return $conn->query($sql);
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}

// ดึงข้อมูลทั้งหมด
$todaySales = 0;
$newOrders = 0;
$totalProducts = 0;
$recentOrders = [];

// ดึงยอดขายวันนี้
$salesQuery = executeQuery($conn, 
    "SELECT COALESCE(SUM(total_amount), 0) AS today_sales 
    FROM orders 
    WHERE DATE(created_at) = CURDATE()
    AND status = 'completed'"
);

if ($salesQuery) {
    $salesData = $salesQuery->fetch(PDO::FETCH_ASSOC);
    $todaySales = (float)$salesData['today_sales'];
}

// ดึงคำสั่งซื้อวันนี้
$ordersQuery = executeQuery($conn, 
    "SELECT COUNT(*) AS new_orders 
    FROM orders 
    WHERE DATE(created_at) = CURDATE()"
);

if ($ordersQuery) {
    $ordersData = $ordersQuery->fetch(PDO::FETCH_ASSOC);
    $newOrders = (int)$ordersData['new_orders'];
}

// ดึงจำนวนสินค้า
$productsQuery = executeQuery($conn, "SELECT COUNT(*) AS total_products FROM products");

if ($productsQuery) {
    $productsData = $productsQuery->fetch(PDO::FETCH_ASSOC);
    $totalProducts = (int)$productsData['total_products'];
}

// ดึงคำสั่งซื้อล่าสุด
$recentQuery = executeQuery($conn, 
    "SELECT 
        o.id, 
        o.total_amount, 
        o.created_at, 
        COALESCE(c.name, 'ไม่ระบุชื่อ') AS customer_name 
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    ORDER BY o.created_at DESC 
    LIMIT 5"
);

if ($recentQuery) {
    $recentOrders = $recentQuery->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ดูแลระบบ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            margin-bottom: 1.5rem;
        }
        .sales-card { border-left: 4px solid #3498db; }
        .orders-card { border-left: 4px solid #e67e22; }
        .products-card { border-left: 4px solid #2ecc71; }
        .nav-link {
            color: #ecf0f1;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .nav-link:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

<header style="background-color: #2c3e50; padding: 1rem 2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <h1 style="color: white; margin: 0; font-size: 1.8rem;">แดชบอร์ดผู้ดูแลระบบ</h1>
    <nav style="margin-top: 1rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <a href="index.php" class="nav-link">📊 แดชบอร์ด</a>
        <a href="products/index.php" class="nav-link">🛍️ สินค้า</a>
        <a href="orders/index.php" class="nav-link">📦 คำสั่งซื้อ</a>
        <a href="customers/index.php" class="nav-link">👥 ลูกค้า</a>
        <a href="payments/index.php" class="nav-link">💳 การชำระเงิน</a>
        <a href="logout.php" class="nav-link" style="color: #e74c3c;">🚪 ออกจากระบบ</a>
    </nav>
</header>

<main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
    <div style="display: grid; gap: 2rem;">
        
        <!-- สถิติหลัก -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">

            <div class="card orders-card">
                <h3 style="margin: 0 0 0.5rem 0; color: #2c3e50;">📦 คำสั่งซื้อใหม่</h3>
                <p style="margin: 0; font-size: 1.5rem; color: #e67e22;">
                    <?= $newOrders ?> รายการ
                </p>
            </div>

            <div class="card products-card">
                <h3 style="margin: 0 0 0.5rem 0; color: #2c3e50;">📦 สินค้าทั้งหมด</h3>
                <p style="margin: 0; font-size: 1.5rem; color: #2ecc71;">
                    <?= $totalProducts ?> รายการ
                </p>
            </div>
        </div>

        <!-- กราฟและคำสั่งซื้อล่าสุด -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            
            <!-- กราฟยอดขาย -->
            <div class="card">

                <h3 style="margin-top: 0;">สถิติยอดขายรายสัปดาห์</h3>
                <canvas id="salesChart"></canvas>
            </div>

        
        </div>
    </div>
</main>

<script>
// กราฟยอดขาย
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัส', 'วันศุกร์', 'วันเสาร์', 'วันอาทิตย์'],
        datasets: [{
            label: 'ยอดขายรายวัน',
            data: [12000, 19000, 3000, 5000, 2000, 30000, 45000],
            borderColor: '#3498db',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { 
                position: 'top' 
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '฿' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>