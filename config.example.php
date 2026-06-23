<?php
/**
 * ============================================================
 * config.example.php - نمونه تنظیمات دیتابیس
 * ============================================================
 * ⚠️ این فایل فقط برای نمایش ساختار است.
 * برای استفاده واقعی، نام را به config.php تغییر دهید
 * و اطلاعات واقعی را وارد کنید.
 * ============================================================
 */

// ===== اطلاعات اتصال به دیتابیس =====
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

// ===== اتصال به دیتابیس =====
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>