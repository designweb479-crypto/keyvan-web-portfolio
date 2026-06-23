<?php
// ========== بارگذاری فایل‌های مورد نیاز ==========
require_once 'config.php';
require_once 'security.php';

// ========== بررسی ورود کاربر ==========
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ========== بررسی تکمیل پروفایل ==========
$stmt = $pdo->prepare("SELECT profile_completed FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user['profile_completed']) {
    header('Location: edit-profile.php?msg=complete_profile_first');
    exit;
}

// ========== دریافت اطلاعات دوره ==========
$stmt = $pdo->prepare("SELECT * FROM text_courses WHERE id = ? AND status = 'active'");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: text-courses.php');
    exit;
}

// ========== دریافت درس‌های دوره ==========
$stmt = $pdo->prepare("SELECT * FROM text_lessons WHERE course_id = ? ORDER BY order_number ASC, id ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

// ========== محاسبه پیشرفت کاربر ==========
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_completed_lessons WHERE user_id = ? AND lesson_id IN (SELECT id FROM text_lessons WHERE course_id = ?)");
$stmt->execute([$user_id, $course_id]);
$completed_count = $stmt->fetchColumn();
$total_lessons = count($lessons);
$progress_percent = $total_lessons > 0 ? round(($completed_count / $total_lessons) * 100) : 0;

// ========== اطلاعات سئو ==========
$page_title = htmlspecialchars($course['title']) . " | دوره آموزشی | کیوان وب";
$page_description = "دوره آموزشی " . htmlspecialchars($course['title']) . " - آموزش تخصصی برنامه‌نویسی، طراحی سایت و هوش مصنوعی. " . mb_substr(htmlspecialchars($course['description']), 0, 150) . "...";
$page_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$page_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/wolf-logo.jpg";
$keywords = htmlspecialchars($course['title']) . "، آموزش برنامه نویسی، طراحی سایت، کیوان وب، هوش مصنوعی، ربات تلگرام";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $page_url; ?>">
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo $page_image; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="کیوان وب | Keyvan Web">
    
    <!-- ==========================================================
    Twitter Card
    ========================================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="<?php echo $page_image; ?>">
    
    <!-- ==========================================================
    JSON-LD ساختاریافته
    ========================================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Course",
        "name": "<?php echo htmlspecialchars($course['title']); ?>",
        "description": "<?php echo htmlspecialchars($course['description']); ?>",
        "provider": {
            "@type": "Organization",
            "name": "کیوان وب",
            "url": "https://keyvanwebsite.ir"
        },
        "hasCourseInstance": {
            "@type": "CourseInstance",
            "courseMode": "online",
            "url": "<?php echo $page_url; ?>"
        }
    }
    </script>
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/wolf-logo.png">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: radial-gradient(circle at 20% 30%, #0a0f2a, #050814);
            color: #fff;
            min-height: 100vh;
        }
        
        .course-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 140px 20px 80px;
        }
        
        .course-hero {
            background: linear-gradient(135deg, rgba(79,140,255,0.15), rgba(196,113,255,0.1));
            border-radius: 32px;
            padding: 40px;
            margin-bottom: 40px;
            border: 1px solid rgba(79,140,255,0.3);
            backdrop-filter: blur(10px);
        }
        
        .course-badge {
            display: inline-block;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        
        .course-title {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        .course-description {
            opacity: 0.85;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        
        .progress-section {
            background: rgba(5,10,30,0.6);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 40px;
            border: 1px solid rgba(79,140,255,0.2);
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .progress-bar-wrapper {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            height: 10px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            background: linear-gradient(90deg, #4f8cff, #c471ff);
            height: 100%;
            border-radius: 20px;
            transition: width 0.5s ease;
        }
        
        .lessons-title {
            font-size: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .lessons-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .lesson-card {
            background: rgba(5,10,30,0.5);
            border-radius: 20px;
            border: 1px solid rgba(158,173,255,0.15);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .lesson-card:hover {
            border-color: rgba(79,140,255,0.4);
            transform: translateX(-5px);
        }
        
        .lesson-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 25px;
            cursor: pointer;
        }
        
        .lesson-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }
        
        .lesson-number {
            width: 40px;
            height: 40px;
            background: rgba(79,140,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .lesson-card.completed .lesson-number {
            background: rgba(80,255,100,0.2);
            color: #5dffb1;
        }
        
        .lesson-title {
            font-size: 18px;
            font-weight: 500;
        }
        
        .lesson-status-icon {
            font-size: 20px;
            margin-right: 15px;
        }
        
        .lesson-card.completed .lesson-status-icon {
            color: #5dffb1;
        }
        
        .lesson-toggle {
            background: none;
            border: none;
            color: #9fb4ff;
            cursor: pointer;
            font-size: 20px;
        }
        
        .lesson-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            padding: 0 25px;
            border-top: 1px solid transparent;
        }
        
        .lesson-content.open {
            max-height: 300px;
            padding: 20px 25px;
            border-top-color: rgba(79,140,255,0.2);
        }
        
        .lesson-text {
            line-height: 1.8;
            color: rgba(255,255,255,0.85);
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .lesson-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border-radius: 30px;
            padding: 8px 20px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }
        
        .lesson-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79,140,255,0.4);
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(158,173,255,0.1);
            border: 1px solid rgba(158,173,255,0.3);
            border-radius: 40px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
            .course-container { padding: 120px 15px 60px; }
            .course-hero { padding: 25px; }
            .course-title { font-size: 24px; }
            .lesson-header { padding: 15px; flex-wrap: wrap; }
            .lesson-info { flex-wrap: wrap; gap: 10px; }
            .lesson-title { font-size: 15px; }
            .lesson-number { width: 32px; height: 32px; font-size: 13px; }
            .lesson-content.open { max-height: 400px; }
        }

        #installPWA {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: none;
            align-items: center;
            gap: 10px;
            font-family: inherit;
        }
        #installPWA:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79,140,255,0.5);
        }
        @media (max-width: 768px) {
            #installPWA {
                bottom: 10px;
                right: 10px;
                padding: 10px 18px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <!-- ==========================================================
    هدر سایت
    ========================================================== -->
    <?php include 'header.php'; ?>
    
    <!-- ==========================================================
    محتوای اصلی
    ========================================================== -->
    <main>
        <div class="course-container">
            <!-- ===== هدر دوره ===== -->
            <div class="course-hero">
                <div class="course-badge">📚 دوره متنی رایگان</div>
                <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="course-description"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
            </div>
            
            <!-- ===== پیشرفت کاربر ===== -->
            <div class="progress-section">
                <div class="progress-header">
                    <span>📊 پیشرفت شما</span>
                    <span><?php echo $completed_count; ?> از <?php echo $total_lessons; ?> درس (<?php echo $progress_percent; ?>%)</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" style="width: <?php echo $progress_percent; ?>%"></div>
                </div>
            </div>
            
            <!-- ===== لیست درس‌ها ===== -->
            <div class="lessons-title">
                <span>📖</span>
                درس‌های این دوره
                <span style="font-size: 14px; opacity: 0.6;">(برای مشاهده کلیک کنید)</span>
            </div>
            
            <div class="lessons-grid">
                <?php foreach($lessons as $index => $lesson): ?>
                    <?php
                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM user_completed_lessons WHERE user_id = ? AND lesson_id = ?");
                    $stmt2->execute([$user_id, $lesson['id']]);
                    $is_completed = $stmt2->fetchColumn() > 0;
                    ?>
                    <div class="lesson-card <?php echo $is_completed ? 'completed' : ''; ?>" data-lesson-id="<?php echo $lesson['id']; ?>">
                        <div class="lesson-header" onclick="toggleLesson(this)">
                            <div class="lesson-info">
                                <div class="lesson-number"><?php echo $lesson['order_number'] + 1; ?></div>
                                <div class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            </div>
                            <div class="lesson-status-icon"><?php echo $is_completed ? '✅' : '📘'; ?></div>
                            <div class="lesson-toggle">▼</div>
                        </div>
                        <div class="lesson-content">
                            <div class="lesson-text"><?php echo mb_substr(strip_tags($lesson['content'] ?? ''), 0, 150); ?>...</div>
                            <a href="text-lesson.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="lesson-btn">📖 مشاهده و مطالعه درس →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- ===== دکمه بازگشت ===== -->
            <a href="text-courses.php" class="back-btn">← بازگشت به همه دوره‌های متنی</a>
        </div>
    </main>
    
    <!-- ==========================================================
    فوتر
    ========================================================== -->
    <?php include 'footer.php'; ?>
    
    <!-- ==========================================================
    اسکریپت‌های اصلی
    ========================================================== -->
    <script>
        // ===== باز و بسته کردن درس =====
        function toggleLesson(header) {
            const card = header.closest('.lesson-card');
            const content = card.querySelector('.lesson-content');
            const toggleIcon = header.querySelector('.lesson-toggle');
            
            content.classList.toggle('open');
            toggleIcon.textContent = content.classList.contains('open') ? '▲' : '▼';
        }

        // ===== منوی موبایل =====
        const toggle = document.querySelector(".menu-toggle");
        const nav = document.querySelector("nav");
        if(toggle) toggle.addEventListener("click", () => { 
            toggle.classList.toggle("active"); 
            nav.classList.toggle("open"); 
        });
        if(nav) nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => { 
            if(toggle) toggle.classList.remove("active"); 
            nav.classList.remove("open"); 
        }));

        // ===== PWA =====
        let deferredPrompt; 
        let installBtn = document.getElementById('installPWA');
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.style.display = 'flex';
            installBtn.addEventListener('click', async () => {
                installBtn.style.display = 'none';
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if(outcome === 'accepted') console.log('کاربر برنامه را نصب کرد');
                deferredPrompt = null;
            });
        });
        window.addEventListener('appinstalled', () => {
            installBtn.style.display = 'none';
            deferredPrompt = null;
            console.log('برنامه نصب شد');
        });
        if(window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
    </script>
</body>
</html>