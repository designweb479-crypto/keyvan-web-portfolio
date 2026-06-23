<?php
require_once 'config.php';
session_start();

/**
 * بررسی دسترسی کاربر به یک ویژگی خاص
 * @param string $feature نام ویژگی ('ai_advanced' یا 'video_courses')
 * @return bool آیا کاربر دسترسی دارد؟
 */
function has_feature_access($feature = 'ai_advanced') {
    // ===== کاربر وارد نشده = بدون دسترسی =====
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user_id = $_SESSION['user_id'];
    $pdo = $GLOBALS['pdo'];
    
    // ===== دریافت اشتراک فعال کاربر =====
    $stmt = $pdo->prepare("
        SELECT plan_type 
        FROM user_subscriptions 
        WHERE user_id = ? AND status = 'active' AND expires_at > NOW() 
        ORDER BY expires_at DESC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $sub = $stmt->fetch();
    
    // ===== بدون اشتراک = بدون دسترسی =====
    if (!$sub) {
        return false;
    }
    
    $plan_type = $sub['plan_type'];
    
    // ===== سطح دسترسی بر اساس پلن =====
    
    // هوش مصنوعی پیشرفته (ساخت عکس، تشخیص صدا، آپلود فایل)
    if ($feature == 'ai_advanced') {
        return ($plan_type == 'pro' || $plan_type == 'yearly');
    }
    
    // دوره‌های ویدئویی
    if ($feature == 'video_courses') {
        return ($plan_type == 'basic' || $plan_type == 'pro' || $plan_type == 'yearly');
    }
    
    // ===== ویژگی ناشناخته = بدون دسترسی =====
    return false;
}
?>