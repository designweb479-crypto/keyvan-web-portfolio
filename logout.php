<?php
// ========== بارگذاری فایل امنیت ==========
require_once 'security.php';

// ========== پاک‌سازی Session ==========
session_unset();
session_destroy();

// ========== حذف کوکی Session ==========
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ========== هدرهای کنترل کش ==========
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

// ========== هدایت به صفحه اصلی ==========
header('Location: index.php');
exit;
?>