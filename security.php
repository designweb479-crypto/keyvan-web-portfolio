<?php
// ========== تنظیمات امنیتی Session ==========
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_only_cookies', 1);

// ========== هدرهای امنیتی HTTP ==========
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-Permitted-Cross-Domain-Policies: none");
header("Referrer-Policy: strict-origin-when-cross-origin");

// ========== شروع Session ==========
session_start();

// ========== محافظت از Session با اثر انگشت (Fingerprint) ==========
// جلوگیری از دزدیده شدن Session
if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
} elseif ($_SESSION['fingerprint'] !== md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'])) {
    session_destroy();
    header('Location: sign-in.php');
    exit;
}

// ========== تولید توکن CSRF (Cross-Site Request Forgery) ==========
// جلوگیری از حملات CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * تولید توکن CSRF برای فرم‌ها
 * @return string توکن CSRF
 */
function generate_csrf_token() {
    return $_SESSION['csrf_token'];
}

/**
 * بررسی توکن CSRF
 * @param string $token توکن دریافتی از فرم
 * @return bool معتبر بودن توکن
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ========== جلوگیری از نمایش خطاهای PHP به کاربر ==========
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// ========== پاک‌سازی ورودی‌ها (Sanitize) ==========
/**
 * پاک‌سازی و ایمن‌سازی ورودی‌های کاربر
 * @param string $data ورودی کاربر
 * @return string ورودی پاک‌سازی‌شده
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>