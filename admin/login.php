<?php
session_start();
include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูลผู้ดูแลระบบ (ตัวอย่างใช้ email = admin@example.com, password = admin123)
    if ($email === 'admin@aa.com' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
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
    <title>เข้าสู่ระบบผู้ดูแลระบบ</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh;">

    <main style="width: 100%; max-width: 400px; padding: 2rem;">
        <div style="background: white; padding: 2.5rem; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="color: #2c3e50; margin: 0 0 1.5rem 0; font-size: 1.8rem;">เข้าสู่ระบบผู้ดูแลระบบ</h2>
                <img src="../assets/images/1.jpg" alt="Logo" style="height: 80px; margin-bottom: 1.5rem;">
            </div>

            <?php if ($error): ?>
                <div style="background: #fee; padding: 1rem; border-radius: 6px; border: 1px solid #f5c6cb; color: #721c24; margin-bottom: 1.5rem;">
                    ⚠️ <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" style="display: grid; gap: 1.5rem;">
                <div style="display: grid; gap: 0.6rem;">
                    <label style="font-weight: 600; color: #34495e;">อีเมล</label>
                    <input type="email" name="email" required 
                           style="padding: 0.9rem; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 1rem; transition: border-color 0.3s;"
                           onfocus="this.style.borderColor='#3498db'"
                           onblur="this.style.borderColor='#e0e0e0'">
                </div>

                <div style="display: grid; gap: 0.6rem;">
                    <label style="font-weight: 600; color: #34495e;">รหัสผ่าน</label>
                    <input type="password" name="password" required 
                           style="padding: 0.9rem; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 1rem; transition: border-color 0.3s;"
                           onfocus="this.style.borderColor='#3498db'"
                           onblur="this.style.borderColor='#e0e0e0'">
                </div>

                <button type="submit" 
                        style="background-color: #27ae60; color: white; padding: 1rem; border: none; border-radius: 6px; 
                               font-size: 1.1rem; cursor: pointer; transition: all 0.3s; font-weight: 600;"
                        onmouseover="this.style.backgroundColor='#219a52'; this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.backgroundColor='#27ae60'; this.style.transform='translateY(0)'">
                    🔑 เข้าสู่ระบบ
                </button>
            </form>
        </div>
    </main>

</body>
</html>