<?php
// ========== بارگذاری فایل‌های مورد نیاز ==========
require_once 'security.php';
require_once 'check-subscription.php';

// ========== هدرهای کنترل کش ==========
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

// ========== بررسی ورود کاربر ==========
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

// ========== بررسی دسترسی به دوره‌های ویدئویی ==========
$has_video_access = has_feature_access('video_courses');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8" />
    <title>دوره‌های تخصصی کیوان وب | آموزش برنامه نویسی، طراحی سایت، هوش مصنوعی</title>
    <meta name="description" content="دوره‌های آموزشی کیوان وب شامل آموزش برنامه نویسی، طراحی وب سایت، ساخت ربات تلگرام، زبان انگلیسی برای برنامه نویسان و هوش مصنوعی." />
    <meta name="keywords" content="دوره برنامه نویسی, آموزش طراحی سایت, ساخت ربات تلگرام, آموزش هوش مصنوعی, زبان انگلیسی برنامه نویسی, کیوان وب" />
    <meta name="author" content="کیوان وب | Keyvan Web" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="دوره‌های تخصصی کیوان وب | آموزش برنامه نویسی و طراحی سایت" />
    <meta property="og:description" content="بهترین دوره‌های آموزشی برنامه نویسی، طراحی وب، ربات تلگرام و هوش مصنوعی با پشتیبانی کامل و مدرک معتبر." />
    <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
    <meta property="og:url" content="https://keyvanwebsite.ir/courses.php" />
    <meta property="og:type" content="website" />
    
    <!-- ==========================================================
    تگ‌های سئو
    ========================================================== -->
    <link rel="canonical" href="https://keyvanwebsite.ir/courses.php" />
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "دوره‌های تخصصی کیوان وب",
        "description": "دوره‌های آموزشی برنامه نویسی، طراحی سایت و هوش مصنوعی",
        "url": "https://keyvanwebsite.ir/courses.php"
    }
    </script>
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" href="images/wolf-logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f8cff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
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
                <div class="logo-icon"><img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب" /></div>
                <div class="logo-text"><span class="fa">کیوان وب</span><span class="en">Keyvan Web</span></div>
            </div>
            <a href="ai.php" class="header-ai-btn" aria-label="دستیار هوش مصنوعی">
                <span class="header-ai-dot"></span>
                Ai
            </a>
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
    <main class="courses-main">
        <h1 style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;">
            دوره‌های تخصصی برنامه نویسی و هوش مصنوعی کیوان وب
        </h1>
        
        <!-- ===== انتخاب نوع دوره ===== -->
        <section class="course-mode-selector reveal">
            <div class="mode-card free-mode">
                <h2>آموزش متنی رایگان</h2>
                <p>شما میتونید از از آموزش های رایگان و متنی سایت ما استفاده کنید.</p>
                <button class="mode-btn" onclick="window.location.href='text-courses.php'">ورود به آموزش متنی</button>
            </div>
            <div class="mode-card video-mode">
                <h2>آموزش ویدئویی (اشتراکی)</h2>
                <p>دوره‌های ویدئویی حرفه‌ای با دسترسی ویژه و محتوای عمیق‌تر.</p>
                <button class="mode-btn premium" onclick="checkVideoAccess()">ورود به آموزش ویدئویی</button>
            </div>
        </section>

        <!-- ===== بخش هدر دوره‌ها ===== -->
        <section class="courses-hero reveal">
            <div class="courses-hero-content">
                <h1>یادگیری در نسل جدید وب</h1>
                <p>دوره‌ها و ویدیوهای کیوان وب، تجربه‌ای مدرن، سریع و هوشمند از آموزش آنلاین.</p>
                <div class="courses-hero-tags">
                    <span>زبان انگلیسی برای برنامه نویسی</span>
                    <span>طراحی وب</span>
                    <span>ربات تلگرام</span>
                    <span>برنامه‌نویسی</span>
                    <span>آموزش ترید (به زودی)</span>
                </div>
            </div>
            <div class="courses-hero-visual">
                <div class="courses-orbit"></div>
                <div class="courses-orbit second"></div>
                <div class="courses-orbit third"></div>
                <div class="courses-core">AI</div>
            </div>
        </section>

        <!-- ===== لیست دوره‌ها ===== -->
        <section class="courses-grid-section reveal">
            <div class="courses-grid-header">
                <h2>دوره‌ها و ویدیوهای کیوان وب</h2>
                <p>هر دوره و هر ویدیو، صفحه‌ی اختصاصی خودش را خواهد داشت.</p>
            </div>
            <div class="courses-grid">
                <article class="course-card">
                    <h3>آشنایی با زبان انگلیسی</h3>
                    <p>شروع کار با کلمات کلیدی و کاربرد های آن ها</p>
                    <button class="course-btn">مشاهده جزئیات</button>
                </article>
                <article class="course-card">
                    <h3>طراحی وب مدرن</h3>
                    <p>ساخت سایت‌های شخصی و فروشگاهی با ظاهر حرفه‌ای.</p>
                    <button class="course-btn">مشاهده جزئیات</button>
                </article>
                <article class="course-card">
                    <h3>ساخت ربات تلگرام</h3>
                    <p>از صفر تا ساخت ربات‌های هوشمند و کاربردی.</p>
                    <button class="course-btn">مشاهده جزئیات</button>
                </article>
                <article class="course-card">
                    <h3>آموزش زبان‌های برنامه‌نویسی</h3>
                    <p>شروع اصولی با زبان‌هایی مثل JavaScript، Python و ...</p>
                    <button class="course-btn">مشاهده جزئیات</button>
                </article>
            </div>
        </section>

        <!-- ===== آمار ===== -->
        <section class="courses-stats reveal">
            <div class="stat-box">
                <h4>+1200</h4>
                <span>بازدیدکننده</span>
            </div>
            <div class="stat-box">
                <h4>+100</h4>
                <span>بازخورد مثبت</span>
            </div>
            <div class="stat-box">
                <h4>در حال توسعه</h4>
                <span>دوره‌ها و امکانات جدید</span>
            </div>
        </section>
    </main>

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
        toggle.addEventListener("click", () => {
            toggle.classList.toggle("active");
            nav.classList.toggle("open");
        });
        nav.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                toggle.classList.remove("active");
                nav.classList.remove("open");
            });
        });

        // ===== انیمیشن اسکرول =====
        const revealItems = document.querySelectorAll(".reveal");
        if ("IntersectionObserver" in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) entry.target.classList.add("active");
                });
            }, { threshold: 0.15 });
            revealItems.forEach((el) => observer.observe(el));
        } else {
            revealItems.forEach((el) => el.classList.add("active"));
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
        
        // ===== بررسی دسترسی به ویدئو =====
        function checkVideoAccess() {
            <?php if($has_video_access): ?>
                window.location.href = "video-courses.php";
            <?php else: ?>
                if(confirm('🎬 برای دسترسی به دوره‌های ویدئویی نیاز به خرید اشتراک دارید. آیا به صفحه خرید اشتراک بروید؟')) {
                    window.location.href = "plans.php";
                }
            <?php endif; ?>
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>