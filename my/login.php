<?php
session_start();
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ค้นหาผู้ใช้จากอีเมล
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // บันทึกข้อมูลผู้ใช้ใน Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); // Redirect ไปหน้าหลัก
        exit();
    } else {
        $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f5f6fa;">
    <?php include 'includes/header.php'; ?>
    
    <main style="max-width: 500px; margin: 50px auto; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #2c3e50; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 3px solid #3498db;">🔑 เข้าสู่ระบบ</h2>

        <?php if ($error): ?>
            <div style="background: #fee; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Email Field -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label for="email" style="font-weight: 600; color: #34495e;">📧 อีเมล:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    style="padding: 12px 16px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: 16px; transition: all 0.3s;"
                    onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                    onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                >
            </div>

            <!-- Password Field -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label for="password" style="font-weight: 600; color: #34495e;">🔒 รหัสผ่าน:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    style="padding: 12px 16px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: 16px; transition: all 0.3s;"
                    onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                    onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
                >
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                style="padding: 14px 20px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 10px;"
                onmouseover="this.style.background='#2980b9'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.background='#3498db'; this.style.transform='none'"
            >
                เข้าสู่ระบบ
            </button>
        </form>

        <!-- Registration Link -->
        <p style="text-align: center; margin-top: 25px; color: #7f8c8d;">
            ยังไม่มีบัญชี? 
            <a 
                href="register.php" 
                style="color: #3498db; text-decoration: none; font-weight: 600;"
                onmouseover="this.style.textDecoration='underline'"
                onmouseout="this.style.textDecoration='none'"
            >
                สมัครสมาชิกที่นี่
            </a>
        </p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>