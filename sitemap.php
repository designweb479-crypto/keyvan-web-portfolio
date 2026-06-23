<?php
// ========== تنظیم هدر XML ==========
header('Content-Type: application/xml');
require_once 'config.php';

$site_url = 'https://keyvanwebsite.ir';

// ========== شروع خروجی XML ==========
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// ========== صفحات ثابت ==========
// صفحه اصلی
echo '<url>';
echo '<loc>' . $site_url . '/</loc>';
echo '<priority>1.0</priority>';
echo '</url>';

// صفحه دوره‌ها
echo '<url>';
echo '<loc>' . $site_url . '/courses.php</loc>';
echo '<priority>0.9</priority>';
echo '</url>';

// صفحه هوش مصنوعی
echo '<url>';
echo '<loc>' . $site_url . '/ai.php</loc>';
echo '<priority>0.9</priority>';
echo '</url>';

// تماس با ما
echo '<url>';
echo '<loc>' . $site_url . '/contact-us.php</loc>';
echo '<priority>0.7</priority>';
echo '</url>';

// درباره ما
echo '<url>';
echo '<loc>' . $site_url . '/about-us.php</loc>';
echo '<priority>0.7</priority>';
echo '</url>';

// ========== صفحات داینامیک (دوره‌های متنی) ==========
$stmt = $pdo->query("SELECT id, title FROM text_courses WHERE status = 'active'");
$courses = $stmt->fetchAll();

foreach($courses as $course) {
    // صفحه دوره
    echo '<url>';
    echo '<loc>' . $site_url . '/text-course.php?id=' . $course['id'] . '</loc>';
    echo '<priority>0.8</priority>';
    echo '<changefreq>weekly</changefreq>';
    echo '</url>';
    
    // درس‌های هر دوره
    $stmt2 = $pdo->prepare("SELECT id FROM text_lessons WHERE course_id = ?");
    $stmt2->execute([$course['id']]);
    $lessons = $stmt2->fetchAll();
    
    foreach($lessons as $lesson) {
        echo '<url>';
        echo '<loc>' . $site_url . '/text-lesson.php?course_id=' . $course['id'] . '&lesson_id=' . $lesson['id'] . '</loc>';
        echo '<priority>0.7</priority>';
        echo '<changefreq>monthly</changefreq>';
        echo '</url>';
    }
}

// ========== پایان خروجی XML ==========
echo '</urlset>';
?>