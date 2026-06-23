<?php
/**
 * ایجاد فایل جدید برای کاربر
 * @param int $user_id شناسه کاربر
 * @param string $fullname نام کامل
 * @param string $username نام کاربری
 * @param string $email ایمیل
 * @param string $password رمز عبور
 * @param string $phone تلفن
 * @param string $bio بیوگرافی
 * @param string $profile_image عکس پروفایل
 * @return string مسیر فایل ایجاد شده
 */
function createUserFile($user_id, $fullname, $username, $email, $password, $phone = '', $bio = '', $profile_image = '') {
    // ===== ایجاد پوشه در صورت نبود =====
    $dir = 'users_data/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // ===== ساخت نام فایل =====
    $clean_name = preg_replace('/[^a-zA-Z0-9آ-ی]/u', '_', $fullname);
    $filename = $dir . $clean_name . '_' . $user_id . '.txt';
    
    // ===== محتوای فایل =====
    $content = "═══════════════════════════════════════════════════════════\n";
    $content .= "                    اطلاعات کاربر کیوان وب                    \n";
    $content .= "═══════════════════════════════════════════════════════════\n\n";
    $content .= "┌─────────────────────────────────────────────────────────┐\n";
    $content .= "│  مشخصات کاربر                                           │\n";
    $content .= "├─────────────────────────────────────────────────────────┤\n";
    $content .= "│  شناسه کاربر     : " . str_pad($user_id, 40) . "│\n";
    $content .= "│  نام و نام خانوادگی : " . str_pad($fullname, 35) . "│\n";
    $content .= "│  نام کاربری      : " . str_pad($username, 40) . "│\n";
    $content .= "│  ایمیل           : " . str_pad($email, 40) . "│\n";
    $content .= "│  رمز عبور        : " . str_pad($password, 40) . "│\n";
    $content .= "│  شماره تلفن      : " . str_pad($phone ?: 'ثبت نشده', 35) . "│\n";
    $content .= "│  بیوگرافی        : " . str_pad(mb_substr($bio ?: 'ثبت نشده', 0, 35), 35) . "│\n";
    $content .= "│  عکس پروفایل     : " . str_pad($profile_image ?: 'ثبت نشده', 35) . "│\n";
    $content .= "│  تاریخ ثبت نام   : " . str_pad(date('Y-m-d H:i:s'), 35) . "│\n";
    $content .= "└─────────────────────────────────────────────────────────┘\n";
    $content .= "═══════════════════════════════════════════════════════════\n";
    
    // ===== ذخیره فایل =====
    file_put_contents($filename, $content);
    return $filename;
}

/**
 * بروزرسانی فایل کاربر
 * @param int $user_id شناسه کاربر
 * @param string $fullname نام کامل
 * @param string $username نام کاربری
 * @param string $email ایمیل
 * @param string $password رمز عبور
 * @param string $phone تلفن
 * @param string $bio بیوگرافی
 * @param string $profile_image عکس پروفایل
 * @return string مسیر فایل
 */
function updateUserFile($user_id, $fullname, $username, $email, $password, $phone, $bio, $profile_image) {
    // ===== ساخت نام فایل =====
    $dir = 'users_data/';
    $clean_name = preg_replace('/[^a-zA-Z0-9آ-ی]/u', '_', $fullname);
    $filename = $dir . $clean_name . '_' . $user_id . '.txt';
    
    // ===== اگر فایل وجود نداشت، ایجاد کن =====
    if (!file_exists($filename)) {
        return createUserFile($user_id, $fullname, $username, $email, $password, $phone, $bio, $profile_image);
    }
    
    // ===== محتوای جدید =====
    $content = "═══════════════════════════════════════════════════════════\n";
    $content .= "                    اطلاعات کاربر کیوان وب                    \n";
    $content .= "═══════════════════════════════════════════════════════════\n\n";
    $content .= "┌─────────────────────────────────────────────────────────┐\n";
    $content .= "│  مشخصات کاربر                                           │\n";
    $content .= "├─────────────────────────────────────────────────────────┤\n";
    $content .= "│  شناسه کاربر     : " . str_pad($user_id, 40) . "│\n";
    $content .= "│  نام و نام خانوادگی : " . str_pad($fullname, 35) . "│\n";
    $content .= "│  نام کاربری      : " . str_pad($username, 40) . "│\n";
    $content .= "│  ایمیل           : " . str_pad($email, 40) . "│\n";
    $content .= "│  رمز عبور        : " . str_pad($password, 40) . "│\n";
    $content .= "│  شماره تلفن      : " . str_pad($phone ?: 'ثبت نشده', 35) . "│\n";
    $content .= "│  بیوگرافی        : " . str_pad(mb_substr($bio ?: 'ثبت نشده', 0, 35), 35) . "│\n";
    $content .= "│  عکس پروفایل     : " . str_pad($profile_image ?: 'ثبت نشده', 35) . "│\n";
    $content .= "│  آخرین به‌روزرسانی : " . str_pad(date('Y-m-d H:i:s'), 32) . "│\n";
    $content .= "└─────────────────────────────────────────────────────────┘\n";
    $content .= "═══════════════════════════════════════════════════════════\n";
    
    // ===== ذخیره فایل =====
    file_put_contents($filename, $content);
    return $filename;
}
?>