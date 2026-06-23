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
    <title>تماس با کیوان وب | ارتباط با تیم پشتیبانی هوش مصنوعی و آموزش</title>
    <meta name="description" content="راه‌های ارتباطی با تیم کیوان وب: تلگرام، ایمیل، تماس تلفنی و اینستاگرام. ما پاسخگوی سوالات و درخواست‌های شما هستیم." />
    <meta name="keywords" content="تماس با ما, کیوان وب, پشتیبانی, تلگرام, ایمیل, اینستاگرام, ارتباط با تیم" />
    <meta name="author" content="کیوان وب | Keyvan Web" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="تماس با کیوان وب | ارتباط با تیم پشتیبانی" />
    <meta property="og:description" content="از طریق شبکه‌های اجتماعی، ایمیل و تلفن با ما در ارتباط باشید." />
    <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
    <meta property="og:url" content="https://keyvanwebsite.ir/contact-us.php" />
    <meta property="og:type" content="website" />
    
    <!-- ==========================================================
    تگ‌های سئو
    ========================================================== -->
    <link rel="canonical" href="https://keyvanwebsite.ir/contact-us.php" />
    
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
    <section id="contact" class="contact-section">
        <!-- ===== تصویر سمت راست ===== -->
        <div class="contact-image-side">
            <img src="images/contact-us.jpeg" alt="تماس با ما - صفحه ارتباط با تیم کیوان وب">
        </div>
        
        <!-- ===== دایره‌های ارتباطی ===== -->
        <div class="contact-left">
            <div id="contact-methods" class="contact-methods">
                <div class="contact-circle" onclick="showPreview('telegram')">
                    <img src="icons/telegram-svgrepo-com.svg" alt="ارتباط از طریق تلگرام">
                </div>
                <div class="contact-circle" onclick="showPreview('gmail')">
                    <img src="icons/gmail-svgrepo-com.svg" alt="ارتباط از طریق ایمیل">
                </div>
                <div class="contact-circle" onclick="showPreview('phone')">
                    <img src="icons/whatsapp-color-svgrepo-com.svg" alt="تماس تلفنی با پشتیبانی">
                </div>
                <div class="contact-circle" onclick="showPreview('instagram')">
                    <img src="icons/instagram-svgrepo-com.svg" alt="پیج اینستاگرام کیوان وب">
                </div>
            </div>
            <div id="preview-box" class="preview-box"></div>
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

        // ===== نمایش پیش‌نمایش =====
        function showPreview(type) {
            const methods = document.getElementById("contact-methods");
            const preview = document.getElementById("preview-box");
            methods.style.transform = "translateY(-120px)";
            
            const images = {
                telegram: "images/telegram.jpeg",
                gmail: "images/gmail.jpeg",
                phone: "images/call.jpeg",
                instagram: "images/instagram.jpeg"
            };
            
            const actions = {
                telegram: () => window.open("https://t.me/emadky13", "_blank"),
                gmail: () => alert("فرم ارسال ایمیل به زودی فعال می‌شود"),
                phone: () => window.location.href = "tel:+989306598875",
                instagram: () => window.open("https://instagram.com/yourinstagramid", "_blank")
            };
            
            preview.style.display = "flex";
            preview.innerHTML = "";
            let img = document.createElement("img");
            img.src = images[type];
            img.onclick = actions[type];
            preview.appendChild(img);
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