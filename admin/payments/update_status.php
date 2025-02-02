<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$payment_id = $_GET['id'];

try {
    // Fetch current payment details
    $stmt = $conn->prepare("
        SELECT payments.*, orders.order_number 
        FROM payments 
        JOIN orders ON payments.order_id = orders.id
        WHERE payments.id = :payment_id
    ");
    $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        die("ไม่พบข้อมูลการชำระเงิน");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_status = $_POST['payment_status'];

        $update_stmt = $conn->prepare("
            UPDATE payments 
            SET payment_status = :payment_status 
            WHERE id = :payment_id
        ");
        $update_stmt->bindParam(':payment_status', $new_status, PDO::PARAM_STR);
        $update_stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
        $update_stmt->execute();

        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตสถานะการชำระเงิน</title>
    <style>
        /* Added modern styling */
        body {
            font-family: 'Segoe UI', 'Noto Sans Thai', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            color: #2c3e50;
        }

        header {
            background-color: #2c3e50;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        header h1 {
            color: white;
            margin: 0;
            font-size: 1.8rem;
        }

        nav {
            margin-top: 1rem;
        }

        nav a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav a:hover {
            background-color: #34495e;
            color: white;
        }

        main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        h2 {
            color: #27ae60;
            margin-top: 0;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #34495e;
        }

        select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #bdc3c7;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background-color: white;
        }

        select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }

        button[type="submit"] {
            background-color: #27ae60;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        button[type="submit"]:hover {
            background-color: #219a52;
        }

        .status-indicator {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .status-pending { background-color: #f39c12; color: white; }
        .status-completed { background-color: #2ecc71; color: white; }
        .status-failed { background-color: #e74c3c; color: white; }
    </style>
</head>
<body>
    <header>
        <h1>อัปเดตสถานะการชำระเงิน</h1>
        <nav>
            <a href="index.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                กลับไปยังรายการชำระเงิน
            </a>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <h2>อัปเดตสถานะการชำระเงิน #<?= htmlspecialchars($payment['id']) ?></h2>
            
            <div style="margin-bottom: 2rem;">
                <p style="margin: 0.5rem 0;">
                    <strong>หมายเลขคำสั่งซื้อ:</strong> 
                    <?= htmlspecialchars($payment['order_number']) ?>
                </p>
                <p style="margin: 0.5rem 0;">
                    <strong>สถานะปัจจุบัน:</strong> 
                    <span class="status-indicator status-<?= $payment['payment_status'] ?>">
                        <?php 
                        $statusText = [
                            'pending' => 'รอดำเนินการ',
                            'completed' => 'สำเร็จ',
                            'failed' => 'ล้มเหลว'
                        ];
                        echo $statusText[$payment['payment_status']];
                        ?>
                    </span>
                </p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="payment_status">เลือกสถานะใหม่:</label>
                    <select name="payment_status" id="payment_status" required>
                        <option value="pending" <?= $payment['payment_status'] === 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                        <option value="completed" <?= $payment['payment_status'] === 'completed' ? 'selected' : '' ?>>สำเร็จ</option>
                        <option value="failed" <?= $payment['payment_status'] === 'failed' ? 'selected' : '' ?>>ล้มเหลว</option>
                    </select>
                </div>
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                    อัปเดตสถานะ
                </button>
            </form>
        </div>
    </main>
</body>
</html>