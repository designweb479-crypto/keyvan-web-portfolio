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

// ========== دریافت دوره‌های متنی کاربر ==========
$stmt = $pdo->prepare("
    SELECT DISTINCT c.*, 
           (SELECT COUNT(*) FROM user_completed_lessons ucl 
            JOIN text_lessons tl ON ucl.lesson_id = tl.id 
            WHERE tl.course_id = c.id AND ucl.user_id = ?) as completed_lessons,
           (SELECT COUNT(*) FROM text_lessons WHERE course_id = c.id) as total_lessons
    FROM text_courses c
    WHERE c.status = 'active'
    ORDER BY c.id DESC
");
$stmt->execute([$user_id]);
$text_courses = $stmt->fetchAll();

// ========== دوره‌های ویدئویی (فعلاً خالی) ==========
$video_courses = [];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title>دوره‌های من | کیوان وب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/wolf-logo.png">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
        .mycourses-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 140px 20px 80px;
        }
        .section-title {
            font-size: 28px;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 50px;
        }
        .course-card {
            background: rgba(5,10,30,0.7);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(158,173,255,0.2);
            transition: 0.3s;
        }
        .course-card:hover {
            transform: translateY(-5px);
            border-color: rgba(79,140,255,0.5);
        }
        .course-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        .course-info {
            padding: 20px;
        }
        .course-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .course-progress {
            margin: 15px 0;
        }
        .progress-bar {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            height: 6px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #4f8cff, #c471ff);
            width: 0%;
            height: 100%;
            border-radius: 10px;
        }
        .course-stats {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.7;
        }
        .course-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border-radius: 30px;
            padding: 8px 20px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            margin-top: 15px;
            width: 100%;
            text-align: center;
        }
        .empty-state {
            text-align: center;
            padding: 60px;
            background: rgba(5,10,30,0.5);
            border-radius: 24px;
        }
        @media (max-width: 900px) {
            .courses-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .mycourses-container {
                padding: 120px 15px 60px;
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
        <div class="mycourses-container">
            <h1 class="section-title">📚 دوره‌های من</h1>
            
            <?php if(empty($text_courses) && empty($video_courses)): ?>
                <!-- ===== حالت خالی ===== -->
                <div class="empty-state">
                    <p style="font-size: 48px; margin-bottom: 20px;">📖</p>
                    <p>شما هنوز در هیچ دوره‌ای ثبت‌نام نکرده‌اید.</p>
                    <a href="courses.php" class="course-btn" style="width: auto; display: inline-block; margin-top: 20px;">مشاهده دوره‌ها</a>
                </div>
            <?php else: ?>
                
                <?php if(!empty($text_courses)): ?>
                    <!-- ===== دوره‌های متنی ===== -->
                    <h2 class="section-title" style="font-size: 22px;">📘 دوره‌های متنی</h2>
                    <div class="courses-grid">
                        <?php foreach($text_courses as $course): 
                            $progress = $course['total_lessons'] > 0 ? round(($course['completed_lessons'] / $course['total_lessons']) * 100) : 0;
                        ?>
                            <div class="course-card">
                                <?php if($course['image'] && file_exists($course['image'])): ?>
                                    <img src="<?php echo $course['image']; ?>" class="course-image" alt="<?php echo htmlspecialchars($course['title']); ?>">
                                <?php else: ?>
                                    <div style="height: 160px; background: linear-gradient(135deg, #1e2a4a, #0a0f2a); display: flex; align-items: center; justify-content: center; font-size: 48px;">📖</div>
                                <?php endif; ?>
                                <div class="course-info">
                                    <div class="course-title"><?php echo htmlspecialchars($course['title']); ?></div>
                                    <div class="course-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <div class="course-stats">
                                            <span><?php echo $course['completed_lessons']; ?> از <?php echo $course['total_lessons']; ?> درس</span>
                                            <span><?php echo $progress; ?>%</span>
                                        </div>
                                    </div>
                                    <a href="text-course.php?id=<?php echo $course['id']; ?>" class="course-btn">ادامه مطالعه →</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($video_courses)): ?>
                    <!-- ===== دوره‌های ویدئویی ===== -->
                    <h2 class="section-title" style="font-size: 22px;">🎥 دوره‌های ویدئویی</h2>
                    <div class="courses-grid">
                        <?php foreach($video_courses as $course): ?>
                            <!-- بعداً تکمیل می‌شه -->
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
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
        if (toggle) toggle.addEventListener("click", () => { 
            toggle.classList.toggle("active"); 
            nav.classList.toggle("open"); 
        });
        if (nav) nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => { 
            if (toggle) toggle.classList.remove("active"); 
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
                if (outcome === 'accepted') console.log('کاربر برنامه را نصب کرد');
                deferredPrompt = null;
            });
        });
        window.addEventListener('appinstalled', () => {
            installBtn.style.display = 'none';
            deferredPrompt = null;
            console.log('برنامه نصب شد');
        });
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
    </script>
</body>
</html>