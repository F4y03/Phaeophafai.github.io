<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลลูกค้าทั้งหมด
$stmt = $conn->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการลูกค้า</title>
    <style>
        .status-active { color: #27ae60; }
        .status-inactive { color: #e74c3c; }
        .address-column { max-width: 300px; word-wrap: break-word; }
    </style>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">

    <header style="background-color: #2c3e50; padding: 1.5rem 2rem; color: white;">
        <h1 style="margin: 0; font-size: 1.8rem;">จัดการลูกค้า</h1>
        <nav style="margin-top: 1rem; display: flex; gap: 1.5rem;">
            <a href="../index.php" style="color: #bdc3c7; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s;" 
               onmouseover="this.style.color='white'; this.style.backgroundColor='#34495e'" 
               onmouseout="this.style.color='#bdc3c7'; this.style.backgroundColor='transparent'">
               ← กลับสู่แดชบอร์ด
            </a>
        </nav>
    </header>

    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 1rem;">รายการลูกค้าทั้งหมด</h2>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">ID</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">ชื่อผู้ใช้</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">อีเมล</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #ecf0f1;">เบอร์โทรศัพท์</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #ecf0f1;">ที่อยู่</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr style="border-bottom: 1px solid #ecf0f1; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f8f9fa'">
                                <td style="padding: 1rem;"><?= $customer['id'] ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($customer['username']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($customer['email']) ?></td>
                                <td style="padding: 1rem; text-align: center;"><?= htmlspecialchars($customer['phone_number']) ?></td>
                                <td style="padding: 1rem; text-align: left;" class="address-column"><?= htmlspecialchars($customer['address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($customers)): ?>
                <div style="text-align: center; padding: 2rem; color: #7f8c8d;">
                    ไม่พบข้อมูลลูกค้าในระบบ
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>