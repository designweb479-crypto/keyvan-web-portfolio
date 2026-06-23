<?php
// ========== دریافت مسیر فایل PDF ==========
$pdf_path = $_GET['path'] ?? '';

// ========== اگر فایل وجود نداشت ==========
if (!$pdf_path || !file_exists($pdf_path)) {
    header('Content-Type: image/png');
    echo file_get_contents('images/pdf-placeholder.png');
    exit;
}

try {
    // ========== ایجاد شیء Imagick ==========
    $imagick = new Imagick();
    
    // ========== تنظیم کیفیت بالا ==========
    $imagick->setResolution(300, 300);
    $imagick->readImage($pdf_path . '[0]'); // فقط صفحه اول
    
    // ========== تنظیمات خروجی ==========
    $imagick->setImageFormat('png');
    $imagick->setImageCompressionQuality(100);
    $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
    
    // ========== تغییر اندازه برای وضوح بهتر ==========
    $imagick->resizeImage(800, 0, Imagick::FILTER_LANCZOS, 1);
    
    // ========== خروجی تصویر ==========
    header('Content-Type: image/png');
    echo $imagick->getImageBlob();
    
} catch(Exception $e) {
    // ========== در صورت خطا، تصویر جایگزین ==========
    header('Content-Type: image/png');
    echo file_get_contents('images/pdf-placeholder.png');
}
?>