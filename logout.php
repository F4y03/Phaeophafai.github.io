<?php
session_start();
session_destroy(); // ลบ Session ทั้งหมด
header("Location: index.php"); // Redirect ไปหน้าหลัก
exit();
?>