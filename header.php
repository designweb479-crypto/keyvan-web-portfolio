<?php
// اگر سشن شروع نشده، شروع کن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- ==========================================================
هدر سایت
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
    </nav>
</header>

<!-- ==========================================================
اسکریپت منوی موبایل و PWA
========================================================== -->
<script>
    /**
     * ============================================================
     * اسکریپت‌های هدر
     * ============================================================
     * 1. منوی موبایل
     * 2. نصب PWA
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
                if (toggle) toggle.classList.remove("active");
                nav.classList.remove("open");
            });
        });
    }

    // ===== 2. نصب PWA =====
    let deferredPrompt;
    let installBtn = document.getElementById('installPWA');

    // تشخیص قابلیت نصب PWA
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (installBtn) {
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
        }
    });

    // بعد از نصب، دکمه رو مخفی کن
    window.addEventListener('appinstalled', () => {
        if (installBtn) {
            installBtn.style.display = 'none';
        }
        deferredPrompt = null;
        console.log('برنامه نصب شد');
    });

    // اگر قبلاً نصب شده، دکمه رو نشون نده
    if (window.matchMedia('(display-mode: standalone)').matches) {
        if (installBtn) {
            installBtn.style.display = 'none';
        }
    }
</script>