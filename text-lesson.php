<?php
// ========== بارگذاری فایل‌های مورد نیاز ==========
require_once 'security.php';
require_once 'config.php';

// ========== بررسی ورود کاربر ==========
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

// ========== دریافت اطلاعات درس ==========
$stmt = $pdo->prepare("SELECT * FROM text_lessons WHERE id = ? AND course_id = ?");
$stmt->execute([$lesson_id, $course_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    header('Location: text-courses.php');
    exit;
}

// ========== دریافت اطلاعات دوره ==========
$stmt = $pdo->prepare("SELECT * FROM text_courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

// ========== ثبت پیشرفت کاربر ==========
$stmt = $pdo->prepare("SELECT * FROM user_text_progress WHERE user_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
$progress = $stmt->fetch();

if (!$progress) {
    $total_lessons = $pdo->prepare("SELECT COUNT(*) FROM text_lessons WHERE course_id = ?");
    $total_lessons->execute([$course_id]);
    $total = $total_lessons->fetchColumn();
    
    $stmt = $pdo->prepare("INSERT INTO user_text_progress (user_id, course_id, total_lessons, started_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $course_id, $total]);
    $progress = ['completed_lessons' => 0, 'total_lessons' => $total];
}

// ========== بررسی کامل بودن درس ==========
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_completed_lessons WHERE user_id = ? AND lesson_id = ?");
$stmt->execute([$user_id, $lesson_id]);
$already_completed = $stmt->fetchColumn();

if (!$already_completed) {
    $stmt = $pdo->prepare("INSERT INTO user_completed_lessons (user_id, lesson_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $lesson_id]);
    
    $new_count = $progress['completed_lessons'] + 1;
    $stmt = $pdo->prepare("UPDATE user_text_progress SET completed_lessons = ? WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$new_count, $user_id, $course_id]);
    
    if ($new_count >= $progress['total_lessons']) {
        $stmt = $pdo->prepare("UPDATE user_text_progress SET status = 'completed', completed_at = NOW() WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE user_text_progress SET status = 'in_progress' WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
    }
}

// ========== دریافت درس قبلی و بعدی ==========
$stmt = $pdo->prepare("SELECT id, title FROM text_lessons WHERE course_id = ? AND order_number < (SELECT order_number FROM text_lessons WHERE id = ?) ORDER BY order_number DESC LIMIT 1");
$stmt->execute([$course_id, $lesson_id]);
$prev_lesson = $stmt->fetch();

$stmt = $pdo->prepare("SELECT id, title FROM text_lessons WHERE course_id = ? AND order_number > (SELECT order_number FROM text_lessons WHERE id = ?) ORDER BY order_number ASC LIMIT 1");
$stmt->execute([$course_id, $lesson_id]);
$next_lesson = $stmt->fetch();

// ========== آدرس‌های فایل PDF ==========
$pdf_full_url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $lesson['pdf_file'];
$thumbnail_url = 'pdf_thumbnail.php?path=' . urlencode($lesson['pdf_file']);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($lesson['title']); ?> | <?php echo htmlspecialchars($course['title']); ?> | کیوان وب</title>
    <meta name="description" content="درس <?php echo htmlspecialchars($lesson['title']); ?> از دوره <?php echo htmlspecialchars($course['title']); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, viewport-fit=cover">
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
        /* ===== ریست کامل ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            overflow-x: hidden;
            width: 100%;
        }
        
        .lesson-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .lesson-header {
            background: linear-gradient(135deg, rgba(79,140,255,0.15), rgba(196,113,255,0.1));
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(79,140,255,0.3);
        }
        
        .lesson-title-main {
            font-size: 22px;
            margin-bottom: 8px;
            word-break: break-word;
            line-height: 1.4;
        }
        
        .lesson-meta {
            display: flex;
            gap: 12px;
            opacity: 0.7;
            font-size: 12px;
            flex-wrap: wrap;
        }
        
        .pdf-preview {
            background: rgba(5,10,30,0.5);
            border-radius: 20px;
            padding: 15px;
            text-align: center;
            border: 1px solid rgba(158,173,255,0.2);
        }
        
        .pdf-preview-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(79,140,255,0.3);
            margin-bottom: 20px;
            border: 1px solid rgba(79,140,255,0.3);
        }
        
        .download-box {
            background: linear-gradient(135deg, rgba(79,140,255,0.1), rgba(196,113,255,0.05));
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            width: 100%;
            max-width: 280px;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(79,140,255,0.4);
        }
        
        .download-note {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 12px;
        }
        
        .lesson-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            background: rgba(79,140,255,0.15);
            border: 1px solid rgba(79,140,255,0.4);
            border-radius: 40px;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            font-size: 13px;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }
        
        .nav-btn:hover {
            background: rgba(79,140,255,0.3);
        }
        
        .course-btn {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border-radius: 40px;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s;
            font-size: 13px;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }
        
        .course-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79,140,255,0.4);
        }
        
        .disabled-nav {
            background: rgba(79,140,255,0.08);
            border: 1px solid rgba(79,140,255,0.2);
            border-radius: 40px;
            padding: 10px 18px;
            opacity: 0.5;
            cursor: not-allowed;
            font-size: 13px;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }
        
        main {
            padding: 110px 0 60px 0 !important;
            min-height: 70vh;
            width: 100%;
            overflow-x: hidden;
        }
        
        /* ===== موبایل ===== */
        @media (max-width: 768px) {
            .lesson-title-main {
                font-size: 18px;
            }
            .lesson-header {
                padding: 15px;
            }
            .pdf-preview {
                padding: 12px;
            }
            .download-btn {
                padding: 10px 16px;
                font-size: 13px;
            }
            .nav-btn, .course-btn, .disabled-nav {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 100px;
            }
            .lesson-navigation {
                flex-direction: column;
                gap: 8px;
            }
            .nav-btn svg, .course-btn svg {
                width: 14px;
                height: 14px;
            }
            .lesson-meta span {
                font-size: 11px;
            }
        }
        
        /* ===== موبایل خیلی کوچک ===== */
        @media (max-width: 480px) {
            main {
                padding: 100px 0 50px 0 !important;
            }
            .lesson-container {
                padding: 0 12px;
            }
            .lesson-title-main {
                font-size: 16px;
            }
            .lesson-header {
                padding: 12px;
            }
            .pdf-preview-image {
                margin-bottom: 12px;
            }
            .download-box {
                padding: 12px;
            }
            .download-btn {
                padding: 8px 12px;
                font-size: 12px;
            }
            .download-note {
                font-size: 10px;
            }
        }
        
        /* ===== تبلت ===== */
        @media (min-width: 769px) and (max-width: 1024px) {
            .lesson-container {
                max-width: 750px;
            }
            .lesson-title-main {
                font-size: 24px;
            }
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
        <div class="lesson-container">
            <!-- ===== هدر درس ===== -->
            <div class="lesson-header">
                <h1 class="lesson-title-main">📖 <?php echo htmlspecialchars($lesson['title']); ?></h1>
                <div class="lesson-meta">
                    <span>📘 <?php echo htmlspecialchars($course['title']); ?></span>
                    <span>📚 درس <?php echo htmlspecialchars($lesson['order_number'] + 1); ?></span>
                </div>
            </div>
            
            <!-- ===== پیش‌نمایش PDF ===== -->
            <div class="pdf-preview">
                <?php if($lesson['pdf_file'] && file_exists($lesson['pdf_file'])): ?>
                    <img src="<?php echo $thumbnail_url; ?>" alt="پیش‌نمایش درس <?php echo htmlspecialchars($lesson['title']); ?>" class="pdf-preview-image" loading="lazy">
                    
                    <div class="download-box">
                        <a href="<?php echo $pdf_full_url; ?>" download class="download-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3v12m0 0-3-3m3 3 3-3M5 17v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2"/>
                            </svg>
                            📥 دانلود PDF درس
                        </a>
                        <p class="download-note">
                            پس از دانلود، فایل در مرورگر شما باز می‌شود.
                        </p>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 20px;">
                        <p style="font-size: 48px; margin-bottom: 15px;">📄</p>
                        <p>فایل PDF برای این درس یافت نشد.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- ===== ناوبری بین درس‌ها ===== -->
            <div class="lesson-navigation">
                <?php if($prev_lesson): ?>
                    <a href="text-lesson.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $prev_lesson['id']; ?>" class="nav-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                        درس قبلی
                    </a>
                <?php else: ?>
                    <span class="disabled-nav">← اولین درس</span>
                <?php endif; ?>
                
                <?php if($next_lesson): ?>
                    <a href="text-lesson.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $next_lesson['id']; ?>" class="nav-btn">
                        درس بعدی
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="text-course.php?id=<?php echo $course_id; ?>" class="course-btn">
                        ✅ اتمام دوره
                    </a>
                <?php endif; ?>
            </div>
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