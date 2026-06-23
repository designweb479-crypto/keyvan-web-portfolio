<?php
// ========== بارگذاری فایل امنیت ==========
require_once 'security.php';

// ========== هدرهای کنترل کش ==========
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8" />
    <title>درباره کیوان وب | تیم متخصص هوش مصنوعی، طراحی سایت و آموزش برنامه نویسی</title>
    <meta name="description" content="کیوان وب از سال 1402 فعالیت خود را آغاز کرده است. ما با تیمی متخصص در حوزه هوش مصنوعی، طراحی وب سایت و آموزش برنامه نویسی، آماده ارائه خدمات به شما هستیم." />
    <meta name="keywords" content="درباره ما, کیوان وب, تیم متخصص, هوش مصنوعی, طراحی سایت, آموزش برنامه نویسی, عماد یگانه ترک" />
    <meta name="author" content="کیوان وب | Keyvan Web" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="درباره کیوان وب | تیم متخصص هوش مصنوعی و آموزش" />
    <meta property="og:description" content="کیوان وب با هدف ارائه خدمات مدرن هوش مصنوعی، طراحی وب سایت و آموزش برنامه نویسی فعالیت می‌کند." />
    <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
    <meta property="og:url" content="https://keyvanwebsite.ir/about-us.php" />
    <meta property="og:type" content="website" />
    
    <!-- ==========================================================
    تگ‌های سئو
    ========================================================== -->
    <link rel="canonical" href="https://keyvanwebsite.ir/about-us.php" />
    
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

<body class="page-about">
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
    <section class="about-cinema">
        <!-- ===== هدر تصویری ===== -->
        <div class="about-hero">
            <div class="about-hero-bg"></div>
            <div class="about-hero-content">
                <h1>با کیوان وب، وارد نسل جدید بشو</h1>
                <p>جایی که طراحی مدرن، هوش مصنوعی و خلاقیت کنار هم، تجربه‌ای متفاوت از وب می‌سازند.</p>
            </div>
        </div>

        <!-- ===== کارت‌های اطلاعاتی ===== -->
        <div class="about-info-cards">
            <div class="info-card reveal">
                <h3>زمان تأسیس</h3>
                <p>کیوان وب از سال 1402 فعالیت خود را در حوزه وب و هوش مصنوعی آغاز کرد.</p>
            </div>
            <div class="info-card reveal">
                <h3>توسعه‌دهندگان و صاحب امتیاز</h3>
                <p>توسعه و مدیریت کیوان وب توسط تیمی کوچک اما متخصص، با هدایت عماد یگانه ترک انجام می‌شود.</p>
            </div>
            <div class="info-card reveal">
                <h3>آمار آنلاین</h3>
                <p>بیش از ۱,۲۰۰ بازدید آنلاین، ده‌ها بازخورد مثبت و پروژه‌های در حال توسعه تا امروز.</p>
            </div>
            <div class="info-card reveal">
                <h3>هوش مصنوعی</h3>
                <p>کیوان وب دارای هوش مصنوعی رایگان برای کار های روزمره و بسیار حرفه‌ای است که شما میتوانید از آن استفاده کنید.</p>
            </div>
        </div>

        <!-- ===== گالری و خدمات ===== -->
        <div class="about-gallery-services">
            <div class="gallery-stack reveal">
                <div class="gallery-item">
                    <img src="images/pexels-kindelmedia-8566454.jpg" alt="گالری نمونه کارهای طراحی سایت کیوان وب">
                </div>
                <div class="gallery-item">
                    <img src="images/pexels-cottonbro-8721318.jpg" alt="گالری نمونه پروژه‌های هوش مصنوعی کیوان وب">
                </div>
                <div class="gallery-item">
                    <img src="images/Creating-image-with-artificial-intelligence.jpg" alt="گالری نمونه دوره‌های آموزشی کیوان وب">
                </div>
                <div class="gallery-item">
                    <img src="images/pexels-pavel-danilyuk-8294663.jpg" alt="گالری تیم توسعه کیوان وب">
                </div>
            </div>
            <div class="services-box reveal">
                <h2>خدمات کیوان وب</h2>
                <p>کیوان وب، ترکیبی از خلاقیت و فناوری است. ما با استفاده از هوش مصنوعی رایگان و ابزارهای مدرن، به شما کمک می‌کنیم حضور دیجیتال قدرتمندی بسازید.</p>
                <p>از طراحی و پیاده‌سازی وب‌سایت‌های شخصی، شرکتی و فروشگاهی گرفته تا ساخت ربات‌های تلگرامی اختصاصی، همه‌چیز را متناسب با نیاز شما توسعه می‌دهیم.</p>
                <p>علاوه بر این، کیوان وب در زمینه آموزش زبان‌های خارجی و آموزش زبان‌های برنامه‌نویسی نیز فعالیت می‌کند تا مسیر یادگیری و رشد شما را هموارتر کند.</p>
                <p>هدف ما این است که تکنولوژی، ساده، در دسترس و هوشمند در اختیار شما قرار بگیرد.</p>
            </div>
        </div>
    </section>

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
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>