<?php
// ========== بارگذاری فایل‌های مورد نیاز ==========
require_once 'security.php';
require_once 'config.php';

// ========== هدرهای کنترل کش ==========
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

// ========== بررسی ورود کاربر ==========
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ========== دریافت اطلاعات کاربر ==========
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// ========== دریافت وضعیت اشتراک ==========
$stmt = $pdo->prepare("SELECT * FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$subscription = $stmt->fetch();

$has_active_subscription = ($subscription !== false);
$expires_date = $subscription ? date('Y-m-d', strtotime($subscription['expires_at'])) : null;

// ========== آمار دوره‌ها ==========
// تعداد کل دوره‌های متنی فعال
$stmt = $pdo->query("SELECT COUNT(*) FROM text_courses WHERE status = 'active'");
$total_courses = $stmt->fetchColumn();

// تعداد دوره‌هایی که کاربر حداقل یک درس رو خونده
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT course_id) FROM text_lessons tl JOIN user_completed_lessons ucl ON tl.id = ucl.lesson_id WHERE ucl.user_id = ?");
$stmt->execute([$user_id]);
$enrolled_courses = $stmt->fetchColumn();

// تعداد دوره‌هایی که کاربر همه درس‌هاش رو کامل کرده
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM (
        SELECT tl.course_id, COUNT(*) as total, SUM(CASE WHEN ucl.lesson_id IS NOT NULL THEN 1 ELSE 0 END) as completed
        FROM text_lessons tl
        LEFT JOIN user_completed_lessons ucl ON tl.id = ucl.lesson_id AND ucl.user_id = ?
        GROUP BY tl.course_id
        HAVING total = completed
    ) as completed_courses
");
$stmt->execute([$user_id]);
$completed_courses = $stmt->fetchColumn();

// تعداد دوره‌های در حال اجرا
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM (
        SELECT tl.course_id, COUNT(*) as total, SUM(CASE WHEN ucl.lesson_id IS NOT NULL THEN 1 ELSE 0 END) as completed
        FROM text_lessons tl
        LEFT JOIN user_completed_lessons ucl ON tl.id = ucl.lesson_id AND ucl.user_id = ?
        GROUP BY tl.course_id
        HAVING completed > 0 AND completed < total
    ) as in_progress_courses
");
$stmt->execute([$user_id]);
$in_progress_courses = $stmt->fetchColumn();

// ===== مقادیر پیش‌فرض =====
$exams_taken = 0;
$certificates_count = 0;

// ===== محاسبه سطح کاربری =====
$user_level = 'سطح 1 (تازه کار)';
if ($has_active_subscription) {
    $user_level = 'سطح 3 (حرفه‌ای - اشتراک فعال)';
} elseif ($user['profile_completed']) {
    $user_level = 'سطح 2 (نیمه حرفه‌ای - تکمیل پروفایل)';
} else {
    $user_level = 'سطح 1 (تازه کار)';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title>داشبورد | کیوان وب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/wolf-logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f8cff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

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
        }

        #installPWA {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            display: none;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .dashboard-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .welcome-header {
            margin-bottom: 30px;
        }
        .welcome-header h1 {
            font-size: 28px;
            background: linear-gradient(135deg, #fff, #9fb4ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .welcome-header p {
            opacity: 0.7;
            margin-top: 5px;
        }

        .subscription-card {
            background: linear-gradient(135deg, rgba(79,140,255,0.2), rgba(196,113,255,0.15));
            border-radius: 24px;
            padding: 20px 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(79,140,255,0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            backdrop-filter: blur(10px);
        }
        .subscription-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .subscription-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .subscription-text h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .subscription-text p {
            font-size: 13px;
            opacity: 0.8;
        }
        .subscription-btn {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border: none;
            border-radius: 40px;
            padding: 10px 28px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
            white-space: nowrap;
        }
        .subscription-btn-outline {
            background: transparent;
            border: 1px solid #4f8cff;
        }
        .subscription-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(79,140,255,0.4);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
        }

        .profile-card {
            background: rgba(5,10,30,0.7);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 30px 25px;
            text-align: center;
            border: 1px solid rgba(158,173,255,0.2);
            transition: 0.3s;
        }
        .profile-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            margin: 0 auto 15px;
            border: 3px solid #4f8cff;
            overflow: hidden;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-avatar .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .profile-email {
            font-size: 13px;
            opacity: 0.7;
            margin-bottom: 10px;
        }
        .profile-level {
            display: inline-block;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            padding: 4px 16px;
            border-radius: 30px;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .profile-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }
        .btn-sm {
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 13px;
            border: 1px solid rgba(158,173,255,0.4);
            background: rgba(79,140,255,0.1);
            color: white;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }
        .btn-sm:hover {
            background: rgba(79,140,255,0.3);
            border-color: #4f8cff;
        }

        .quick-links {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .quick-link {
            background: rgba(79,140,255,0.08);
            border: 1px solid rgba(158,173,255,0.2);
            border-radius: 18px;
            padding: 15px 20px;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: 0.3s;
        }
        .quick-link:hover {
            background: rgba(79,140,255,0.15);
            transform: translateX(-5px);
            border-color: rgba(79,140,255,0.5);
        }
        .quick-link-icon {
            font-size: 28px;
        }
        .quick-link-text {
            flex: 1;
        }
        .quick-link-text strong {
            display: block;
            font-size: 15px;
        }
        .quick-link-text small {
            font-size: 11px;
            opacity: 0.6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: rgba(5,10,30,0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px 15px;
            text-align: center;
            border: 1px solid rgba(79,140,255,0.2);
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(79,140,255,0.5);
        }
        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .stat-label {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 5px;
        }

        .info-card {
            background: linear-gradient(135deg, rgba(79,140,255,0.1), rgba(196,113,255,0.05));
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(79,140,255,0.3);
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .info-card-icon {
            font-size: 48px;
        }
        .info-card-text h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .info-card-text p {
            font-size: 13px;
            opacity: 0.8;
        }
        .info-card-btn {
            margin-right: auto;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            color: white;
            font-size: 13px;
        }

        @media (max-width: 1000px) {
            .dashboard-grid {
                grid-template-columns: 280px 1fr;
                gap: 20px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-wrapper {
                padding: 15px;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .subscription-card {
                flex-direction: column;
                text-align: center;
            }
            .subscription-info {
                flex-direction: column;
            }
            .subscription-btn {
                width: 100%;
                text-align: center;
            }
            .info-card {
                flex-direction: column;
                text-align: center;
            }
            .info-card-btn {
                margin-right: 0;
            }
            .welcome-header h1 {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .profile-avatar {
                width: 90px;
                height: 90px;
            }
            .stat-number {
                font-size: 24px;
            }
        }
    </style>

    <!-- ==========================================================
    اسکریپت‌های امنیتی
    ========================================================== -->
    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () { history.go(1); };
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) || (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</head>

<body>
    <!-- ==========================================================
    هدر سایت
    ========================================================== -->
    <header>
        <div class="header-start">
            <div class="logo">
                <div class="logo-icon"><img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب"></div>
                <div class="logo-text"><span class="fa">کیوان وب</span><span class="en">Keyvan Web</span></div>
            </div>
            <a href="ai.php" class="header-ai-btn"><span class="header-ai-dot"></span>Ai</a>
        </div>
        <button class="menu-toggle" aria-label="باز کردن منو"><span></span></button>
        <nav>
            <a href="index.php">خانه</a>
            <a href="courses.php">دوره‌ها</a>
            <a href="contact-us.php">تماس با ما</a>
            <a href="about-us.php">درباره ما</a>
            <div class="nav-auth">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="nav-btn nav-btn-ghost">پنل کاربری</a>
                    <a href="logout.php" class="nav-btn nav-btn-primary">خروج</a>
                <?php else: ?>
                    <a href="sign-in.php" class="nav-btn nav-btn-ghost">ورود</a>
                    <a href="sign-up.php" class="nav-btn nav-btn-primary">ثبت نام</a>
                <?php endif; ?>
            </div>
            <button id="installPWA">📲 نصب برنامه کیوان وب</button>
        </nav>
    </header>

    <!-- ==========================================================
    محتوای اصلی
    ========================================================== -->
    <main>
        <div class="dashboard-wrapper">
            <!-- ===== هدر خوش‌آمدگویی ===== -->
            <div class="welcome-header">
                <h1>خوش آمدی، <?php echo htmlspecialchars($user['fullname']); ?>! 👋</h1>
                <p>به پنل کاربری کیوان وب خوش آمدی. از اینجا به همه امکانات دسترسی داری.</p>
            </div>

            <!-- ===== کارت اشتراک ===== -->
            <div class="subscription-card">
                <div class="subscription-info">
                    <div class="subscription-icon">
                        <?php if($has_active_subscription): ?>⭐<?php else: ?>⚠️<?php endif; ?>
                    </div>
                    <div class="subscription-text">
                        <?php if($has_active_subscription): ?>
                            <h3>✅ اشتراک فعال</h3>
                            <p>اعتبار اشتراک شما تا <strong><?php echo $expires_date; ?></strong> معتبر است</p>
                        <?php else: ?>
                            <h3>⚠️ شما اشتراک فعالی ندارید</h3>
                            <p>برای دسترسی به هوش مصنوعی نامحدود و دوره‌های ویژه، اشتراک تهیه کنید</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($has_active_subscription): ?>
                    <a href="plans.php" class="subscription-btn subscription-btn-outline">تمدید اشتراک</a>
                <?php else: ?>
                    <a href="plans.php" class="subscription-btn">خرید اشتراک</a>
                <?php endif; ?>
            </div>

            <!-- ===== گرید اصلی ===== -->
            <div class="dashboard-grid">
                <!-- ===== ستون چپ: پروفایل ===== -->
                <div>
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <?php if($user['profile_image'] && file_exists($user['profile_image'])): ?>
                                <img src="<?php echo $user['profile_image']; ?>" alt="پروفایل">
                            <?php else: ?>
                                <div class="avatar-placeholder">👤</div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-name"><?php echo htmlspecialchars($user['fullname']); ?></div>
                        <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
                        <div class="profile-level"><?php echo $user_level; ?></div>
                        <div class="profile-buttons">
                            <?php if(!$user['profile_completed']): ?>
                                <button onclick="toggleProfileForm()" class="btn-sm">✏️ تکمیل پروفایل</button>
                            <?php else: ?>
                                <a href="edit-profile.php" class="btn-sm">✏️ ویرایش پروفایل</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ===== لینک‌های سریع ===== -->
                    <div class="quick-links">
                        <a href="my-courses.php" class="quick-link">
                            <div class="quick-link-icon">📚</div>
                            <div class="quick-link-text">
                                <strong>دوره‌های من</strong>
                                <small><?php echo $enrolled_courses; ?> دوره ثبت‌نام شده</small>
                            </div>
                            <div>→</div>
                        </a>
                        <a href="ai.php" class="quick-link">
                            <div class="quick-link-icon">🤖</div>
                            <div class="quick-link-text">
                                <strong>هوش مصنوعی</strong>
                                <small>دستیار Keyvan AI</small>
                            </div>
                            <div>→</div>
                        </a>
                        <a href="my-certificates.php" class="quick-link">
                            <div class="quick-link-icon">🎓</div>
                            <div class="quick-link-text">
                                <strong>مدارک من</strong>
                                <small><?php echo $certificates_count; ?> گواهی دریافت شده</small>
                            </div>
                            <div>→</div>
                        </a>
                    </div>
                </div>

                <!-- ===== ستون راست: آمار ===== -->
                <div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📖</div>
                            <div class="stat-number"><?php echo $enrolled_courses; ?></div>
                            <div class="stat-label">دوره ثبت‌نام شده</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">✅</div>
                            <div class="stat-number"><?php echo $completed_courses; ?></div>
                            <div class="stat-label">دوره تمام شده</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">⚡</div>
                            <div class="stat-number"><?php echo $in_progress_courses; ?></div>
                            <div class="stat-label">در حال اجرا</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📝</div>
                            <div class="stat-number"><?php echo $exams_taken; ?></div>
                            <div class="stat-label">آزمون داده شده</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🎓</div>
                            <div class="stat-number"><?php echo $certificates_count; ?></div>
                            <div class="stat-label">گواهی دریافت شده</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🏆</div>
                            <div class="stat-number" style="font-size: 16px;"><?php echo $user_level; ?></div>
                            <div class="stat-label">سطح کاربری</div>
                        </div>
                    </div>

                    <!-- ===== کارت اطلاع‌رسانی ===== -->
                    <?php if($user['profile_completed']): ?>
                        <div class="info-card">
                            <div class="info-card-icon">🎉</div>
                            <div class="info-card-text">
                                <h3>آموزش متن رایگان فعال شد!</h3>
                                <p>تبریک! با تکمیل پروفایل، به دوره‌های متنی رایگان دسترسی پیدا کردی.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- ==========================================================
    مودال تکمیل پروفایل
    ========================================================== -->
    <div id="profileModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: rgba(5,10,30,0.98); border-radius: 28px; padding: 30px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; border: 1px solid rgba(79,140,255,0.3);">
            <h2 style="margin-bottom: 20px; text-align: center;">تکمیل پروفایل</h2>
            <form method="post" enctype="multipart/form-data" action="edit-profile.php">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px;">نام و نام خانوادگی</label>
                    <input type="text" name="fullname" class="form-input" value="<?php echo htmlspecialchars($user['fullname']); ?>" required style="width:100%; padding:12px; border-radius:12px; background:rgba(3,6,18,0.9); border:1px solid rgba(158,173,255,0.3); color:#fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px;">شماره تلفن</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone']); ?>" required style="width:100%; padding:12px; border-radius:12px; background:rgba(3,6,18,0.9); border:1px solid rgba(158,173,255,0.3); color:#fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px;">کد ملی</label>
                    <input type="text" name="national_code" class="form-input" value="<?php echo htmlspecialchars($user['national_code']); ?>" required style="width:100%; padding:12px; border-radius:12px; background:rgba(3,6,18,0.9); border:1px solid rgba(158,173,255,0.3); color:#fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px;">آدرس محل زندگی</label>
                    <textarea name="address" class="form-input" rows="3" required style="width:100%; padding:12px; border-radius:12px; background:rgba(3,6,18,0.9); border:1px solid rgba(158,173,255,0.3); color:#fff;"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">عکس پروفایل</label>
                    <input type="file" name="profile_image" accept="image/*" style="width:100%; padding:10px; border-radius:12px; background:rgba(3,6,18,0.9); border:1px solid rgba(158,173,255,0.3); color:#fff;">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex:1; background: linear-gradient(135deg, #4f8cff, #c471ff); border: none; border-radius: 30px; padding: 12px; color: #fff; font-weight: bold; cursor: pointer;">ذخیره تغییرات</button>
                    <button type="button" onclick="toggleProfileForm()" style="flex:1; background: rgba(255,80,100,0.2); border: 1px solid #ff5a78; border-radius: 30px; padding: 12px; color: #ff8fab; cursor: pointer;">بستن</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================================
    افکت ستاره‌ها
    ========================================================== -->
    <div id="stars-container"></div>

    <!-- ==========================================================
    اسکریپت‌های اصلی
    ========================================================== -->
    <script>
        // ===== منوی موبایل =====
        const toggle = document.querySelector(".menu-toggle");
        const nav = document.querySelector("nav");
        if (toggle) {
            toggle.addEventListener("click", () => {
                toggle.classList.toggle("active");
                nav.classList.toggle("open");
            });
        }
        if (nav) {
            nav.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    if (toggle) toggle.classList.remove("active");
                    nav.classList.remove("open");
                });
            });
        }

        // ===== مودال پروفایل =====
        function toggleProfileForm() {
            const modal = document.getElementById('profileModal');
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            } else {
                modal.style.display = 'flex';
            }
        }

        // ===== افکت ستاره‌ها =====
        const container = document.getElementById("stars-container");
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (!isMobile) {
            function createStar() {
                const star = document.createElement("div");
                star.classList.add("star");
                const size = Math.random() * 2 + 1;
                star.style.width = size + "px";
                star.style.height = size + "px";
                star.style.left = Math.random() * 100 + "vw";
                star.style.top = "100vh";
                const duration = Math.random() * 6 + 4;
                star.style.animationDuration = duration + "s";
                container.appendChild(star);
                setTimeout(() => star.remove(), duration * 1000);
            }
            function createSpark() {
                const spark = document.createElement("div");
                spark.classList.add("spark");
                spark.style.left = Math.random() * 100 + "vw";
                spark.style.top = Math.random() * 100 + "vh";
                container.appendChild(spark);
                setTimeout(() => spark.remove(), 1200);
            }
            setInterval(createStar, 120);
            setInterval(createSpark, 600);
        } else {
            if (container) container.style.display = 'none';
        }

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