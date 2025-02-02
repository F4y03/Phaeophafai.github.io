<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$error = '';
$success = '';

// ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// หากฟอร์มถูกส่งมา (แก้ไขข้อมูล)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // อัปเดตข้อมูลผู้ใช้
    $stmt = $conn->prepare("UPDATE customers SET username = ?, email = ?, phone_number = ?, address = ? WHERE id = ?");
    if ($stmt->execute([$username, $email, $phone_number, $address, $customer_id])) {
        $success = "อัปเดตข้อมูลสำเร็จ!";
        // อัปเดต Session ด้วยข้อมูลใหม่
        $_SESSION['username'] = $username;
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัญชีผู้ใช้</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f5f6fa;">
    <?php include 'includes/header.php'; ?>
    
    <main style="max-width: 800px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <h2 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 0.5rem; margin-bottom: 2rem;">👤 บัญชีผู้ใช้</h2>

        <!-- Messages -->
        <?php if ($error): ?>
            <div style="background: #fee; color: #721c24; padding: 1rem; border-radius: 6px; border: 1px solid #f5c6cb; margin-bottom: 1.5rem;">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="background: #e9f7ef; color: #155724; padding: 1rem; border-radius: 6px; border: 1px solid #c3e6cb; margin-bottom: 1.5rem;">
                ✅ <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="user_account.php" method="POST" style="display: grid; gap: 1.5rem;">
            <!-- Form Row -->
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <!-- Username Field -->
                <div style="flex: 1; min-width: 250px;">
                    <label for="username" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">ชื่อผู้ใช้:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?= htmlspecialchars($customer['username']) ?>"
                        required
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>

                <!-- Email Field -->
                <div style="flex: 1; min-width: 250px;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">📧 อีเมล:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($customer['email']) ?>"
                        required
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>
            </div>

            <!-- Phone Number Field -->
            <div>
                <label for="phone_number" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">📱 เบอร์โทรศัพท์:</label>
                <input 
                    type="text" 
                    id="phone_number" 
                    name="phone_number" 
                    value="<?= htmlspecialchars($customer['phone_number']) ?>"
                    required
                    style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                    onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                    onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                >
            </div>

            <!-- Address Field -->
            <div>
                <label for="address" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">🏠 ที่อยู่:</label>
                <textarea 
                    id="address" 
                    name="address" 
                    required
                    style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; min-height: 120px; resize: vertical; transition: all 0.3s;"
                    onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                    onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                ><?= htmlspecialchars($customer['address']) ?></textarea>
            </div>

            <!-- Buttons Container -->
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button 
                    type="submit" 
                    style="padding: 0.8rem 2rem; background: #3498db; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.background='#2980b9'; this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.background='#3498db'; this.style.transform='none'"
                >
                    🔄 อัปเดตข้อมูล
                </button>
                
                <a 
                    href="products.php" 
                    style="padding: 0.8rem 2rem; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s;"
                    onmouseover="this.style.background='#7f8c8d'; this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.background='#95a5a6'; this.style.transform='none'"
                >
                    ← กลับไปเลือกสินค้า
                </a>
            </div>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>