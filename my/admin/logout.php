<?php
session_start();
session_destroy(); // ลบ Session ทั้งหมด
header("Location: login.php"); // Redirect ไปหน้า Login
exit();
?>