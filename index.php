<?php
// ========== بارگذاری فایل امنیت ==========
require_once 'security.php';

// ========== هدرهای کنترل کش (جلوگیری از ذخیره‌سازی مرورگر) ==========
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
    <title>کیوان وب - هوش مصنوعی</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    
    <!-- ==========================================================
    آیکون و PWA (Progressive Web App)
    ========================================================== -->
    <link rel="icon" type="image/png" href="images/wolf-logo.png">
    <link rel="shortcut icon" href="/images/wolf-logo.jpg" type="image/jpeg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f8cff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- ==========================================================
    استایل‌های داخلی (دکمه نصب PWA)
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
            transition: transform 0.2s ease, box-shadow 0.2s ease;
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
    اسکریپت‌های امنیتی (جلوگیری از بازرسی کد)
    ========================================================== -->
    <script>
        // جلوگیری از بازگشت با دکمه Back (امنیت)
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // جلوگیری از کلیک راست
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // جلوگیری از کلیدهای میانبر برای بازرسی
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
                return false;
            }
        });

        // تشخیص ابزار توسعه (DevTools)
        setInterval(function() {
            var before = new Date();
            debugger;
            var after = new Date();
            if (after - before > 100) {
                document.body.innerHTML = '<h1 style="text-align:center; margin-top:50px;">🔒 دسترسی غیرمجاز!</h1><p style="text-align:center;">امکان مشاهده کدهای سایت وجود ندارد.</p>';
                alert('دسترسی به ابزار توسعه ممنوع است!');
            }
        }, 1000);
    </script>
</head>

<body>
    <!-- ==========================================================
    هدر سایت (Header)
    ========================================================== -->
    <header>
        <!-- ===== بخش چپ هدر: لوگو + دکمه AI ===== -->
        <div class="header-start">
            <div class="logo">
                <div class="logo-icon">
                    <img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب" />
                </div>
                <div class="logo-text">
                    <span class="fa">کیوان وب</span>
                    <span class="en">Keyvan Web</span>
                </div>
            </div>
            <a href="ai.php" class="header-ai-btn" aria-label="دستیار هوش مصنوعی">
                <span class="header-ai-dot"></span>
                Ai
            </a>
        </div>

        <!-- ===== دکمه منو برای موبایل ===== -->
        <button class="menu-toggle" aria-label="باز کردن منو">
            <span></span>
        </button>

        <!-- ===== ناوبری اصلی ===== -->
        <nav>
            <a href="index.php">خانه</a>
            <a href="courses.php">دوره‌ها</a>
            <a href="contact-us.php">تماس با ما</a>
            <a href="about-us.php">درباره ما</a>
            
            <!-- ===== دکمه‌های ورود/ثبت نام / پنل کاربری ===== -->
            <div class="nav-auth">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- کاربر وارد شده -->
                    <a href="dashboard.php" class="nav-btn nav-btn-ghost">پنل کاربری</a>
                    <a href="logout.php" class="nav-btn nav-btn-primary">خروج</a>
                <?php else: ?>
                    <!-- کاربر وارد نشده -->
                    <a href="sign-in.php" class="nav-btn nav-btn-ghost">ورود</a>
                    <a href="sign-up.php" class="nav-btn nav-btn-primary">ثبت نام</a>
                <?php endif; ?>
            </div>
            
            <!-- ===== دکمه نصب PWA ===== -->
            <button id="installPWA">📲 نصب برنامه کیوان وب</button>
        </nav>
    </header>

    <!-- ==========================================================
    محتوای اصلی (Main)
    ========================================================== -->
    <main id="home" style="padding: 140px 7vw 80px; min-height: 100vh;">
        <section class="hero">
            <!-- ===== بخش متن (سمت چپ) ===== -->
            <div class="hero-text">
                <div class="hero-kicker">AI • WEB • FUTURE</div>
                <h1 class="hero-title">
                    هوش مصنوعی در خدمت<br />
                    <span>کسب‌وکار و خلاقیت تو</span>
                </h1>
                <p class="hero-subtitle">
                    با ترکیب طراحی مدرن، وب‌سایت حرفه‌ای و هوش مصنوعی، 
                    کیوان وب بهت کمک می‌کنه وارد نسل جدید حضور آنلاین بشی.
                </p>

                <!-- ===== دکمه‌های Call To Action ===== -->
                <div class="hero-cta-group">
                    <button class="btn-primary" onclick="window.location.href='ai.php'">
                        هوش مصنوعی
                    </button>
                    <button class="btn-ghost" onclick="window.location.href='courses.php'">
                        مشاهده دوره‌ها
                    </button>
                </div>

                <p class="hero-tagline">
                    <strong>با کیوان وب، وارد دنیای جدید شو</strong>
                </p>
            </div>

            <!-- ===== بخش تصویر (سمت راست) ===== -->
            <div class="hero-visual">
                <div class="brain-orbit">
                    <!-- حلقه‌های انرژی متحرک -->
                    <div class="energy-ring"></div>
                    <div class="energy-ring"></div>
                    <div class="energy-ring"></div>
                    
                    <!-- لوگوی اصلی با افکت -->
                    <img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب" class="wolf-logo" />
                    
                    <!-- برچسب هوش مصنوعی -->
                    <div class="ai-chip-tag">
                        <span class="dot"></span>
                        <span>پردازش هوشمند کیوان وب</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ==========================================================
    افکت ستاره‌ها (پس‌زمینه متحرک)
    ========================================================== -->
    <div id="stars-container"></div>

    <!-- ==========================================================
    اسکریپت‌های اصلی
    ========================================================== -->
    <script>
        /**
         * ============================================================
         * اسکریپت‌های اصلی صفحه
         * ============================================================
         * 1. منوی موبایل
         * 2. دکمه‌های CTA
         * 3. افکت ستاره‌ها
         * 4. نصب PWA
         * ============================================================
         */

        // ===== 1. منوی موبایل =====
        const toggle = document.querySelector(".menu-toggle");
        const nav = document.querySelector("nav");

        if (toggle && nav) {
            toggle.addEventListener("click", () => {
                toggle.classList.toggle("active");
                nav.classList.toggle("open");
            });

            // بستن منو با کلیک روی هر لینک
            nav.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    toggle.classList.remove("active");
                    nav.classList.remove("open");
                });
            });
        }

        // ===== 2. دکمه‌های CTA =====
        const primaryBtn = document.querySelector(".btn-primary");
        if (primaryBtn) {
            primaryBtn.addEventListener("click", () => {
                window.location.href = "ai.php";
            });
        }

        const ghostBtn = document.querySelector(".btn-ghost");
        if (ghostBtn) {
            ghostBtn.addEventListener("click", () => {
                window.location.href = "courses.php";
            });
        }

        // ===== 3. افکت ستاره‌ها (فقط دسکتاپ) =====
        const container = document.getElementById("stars-container");
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (!isMobile && container) {
            /**
             * ساخت یک ستاره جدید
             */
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

            /**
             * ساخت یک جرقه (اسپارک)
             */
            function createSpark() {
                const spark = document.createElement("div");
                spark.classList.add("spark");
                spark.style.left = Math.random() * 100 + "vw";
                spark.style.top = Math.random() * 100 + "vh";
                container.appendChild(spark);
                setTimeout(() => spark.remove(), 1200);
            }

            // اجرای تابع‌ها با فاصله
            setInterval(createStar, 120);
            setInterval(createSpark, 600);
        } else {
            if (container) container.style.display = 'none';
        }

        // ===== 4. نصب PWA =====
        let deferredPrompt;
        let installBtn = document.getElementById('installPWA');

        // تشخیص قابلیت نصب PWA
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.style.display = 'flex';
            
            // کلیک روی دکمه نصب
            installBtn.addEventListener('click', async () => {
                installBtn.style.display = 'none';
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('کاربر برنامه را نصب کرد');
                }
                deferredPrompt = null;
            });
        });

        // بعد از نصب، دکمه رو مخفی کن
        window.addEventListener('appinstalled', () => {
            installBtn.style.display = 'none';
            deferredPrompt = null;
            console.log('برنامه نصب شد');
        });

        // اگر قبلاً نصب شده، دکمه رو نشون نده
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
    </script>

    <!-- ==========================================================
    فوتر (Footer)
    ========================================================== -->
    <?php include 'footer.php'; ?>
</body>
</html>