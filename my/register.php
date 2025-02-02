<?php
session_start();
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$conn) {
            throw new Exception("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
        }

        // ตรวจสอบว่าอีเมลซ้ำหรือไม่
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        if (!$stmt) {
            throw new Exception("เตรียมคำสั่ง SQL ไม่สำเร็จ: " . $conn->errorInfo()[2]);
        }
        
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "อีเมลนี้มีผู้ใช้งานแล้ว";
        } else {
            // Hash รหัสผ่าน
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // บันทึกข้อมูลผู้ใช้ใหม่
            $insert_stmt = $conn->prepare("INSERT INTO customers (username, email, password_hash, phone_number, address) VALUES (?, ?, ?, ?, ?)");
            
            if (!$insert_stmt) {
                throw new Exception("เตรียมคำสั่ง INSERT ไม่สำเร็จ: " . $conn->errorInfo()[2]);
            }

            $insert_stmt->execute([$username, $email, $password_hash, $phone_number, $address]);

            // Redirect ไปหน้า Login
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa;">
    <?php include 'includes/header.php'; ?>
    
    <main style="max-width: 800px; margin: 2rem auto; padding: 2.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #2c3e50; margin-bottom: 2rem; padding-bottom: 0.5rem; border-bottom: 3px solid #27ae60;">📝 สมัครสมาชิก</h2>

        <?php if ($error): ?>
            <div style="background: #fee; color: #721c24; padding: 1rem; border-radius: 6px; border: 1px solid #f5c6cb; margin-bottom: 1.5rem;">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" style="display: grid; gap: 1.5rem;">
            <!-- Username & Email Row -->
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label for="username" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">👤 ชื่อผู้ใช้:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#27ae60'; this.style.boxShadow='0 0 0 3px rgba(39,174,96,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">📧 อีเมล:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#27ae60'; this.style.boxShadow='0 0 0 3px rgba(39,174,96,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>
            </div>

            <!-- Password & Phone Row -->
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">🔒 รหัสผ่าน (อย่างน้อย 6 ตัวอักษร):</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="6"
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#27ae60'; this.style.boxShadow='0 0 0 3px rgba(39,174,96,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label for="phone_number" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">📱 เบอร์โทรศัพท์:</label>
                    <input 
                        type="text" 
                        id="phone_number" 
                        name="phone_number" 
                        required
                        placeholder="ตัวอย่าง: 0812345678"
                        style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='#27ae60'; this.style.boxShadow='0 0 0 3px rgba(39,174,96,0.1)'"
                        onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                    >
                </div>
            </div>

            <!-- Address Field -->
            <div>
                <label for="address" style="display: block; margin-bottom: 0.5rem; color: #34495e; font-weight: 600;">🏠 ที่อยู่:</label>
                <textarea 
                    id="address" 
                    name="address" 
                    required
                    style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 6px; min-height: 120px; resize: vertical; transition: all 0.3s;"
                    onfocus="this.style.borderColor='#27ae60'; this.style.boxShadow='0 0 0 3px rgba(39,174,96,0.1)'"
                    onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                ></textarea>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                style="padding: 0.8rem 2rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s; width: fit-content;"
                onmouseover="this.style.background='#219a52'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.background='#27ae60'; this.style.transform='none'"
            >
                🎉 สมัครสมาชิก
            </button>

            <!-- Login Link -->
            <p style="color: #7f8c8d; margin-top: 1.5rem;">
                มีบัญชีอยู่แล้ว? 
                <a 
                    href="login.php" 
                    style="color: #3498db; text-decoration: none; font-weight: 600;"
                    onmouseover="this.style.textDecoration='underline'"
                    onmouseout="this.style.textDecoration='none'"
                >
                    เข้าสู่ระบบที่นี่ →
                </a>
            </p>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>