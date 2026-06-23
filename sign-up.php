<!DOCTYPE html>
<?php
require_once 'security.php';
session_regenerate_id(true);
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');
$errors = $_SESSION['signup_errors'] ?? [];
$old = $_SESSION['signup_data'] ?? [];
unset($_SESSION['signup_errors'], $_SESSION['signup_data']);
?>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>ثبت نام در کیوان وب | هوش مصنوعی، آموزش برنامه نویسی و طراحی سایت</title>
  <meta name="description" content="همین حالا در کیوان وب ثبت نام کنید و به دوره‌های تخصصی هوش مصنوعی، آموزش برنامه نویسی، طراحی وب سایت و ساخت ربات تلگرام دسترسی پیدا کنید." />
  <meta name="keywords" content="ثبت نام, کیوان وب, هوش مصنوعی, آموزش برنامه نویسی, طراحی سایت, ربات تلگرام, عضویت" />
  <meta name="author" content="کیوان وب | Keyvan Web" />
  <meta name="robots" content="index, follow" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:title" content="ثبت نام در کیوان وب | عضویت در پلتفرم هوش مصنوعی و آموزش" />
  <meta property="og:description" content="با ثبت نام در کیوان وب، به هوش مصنوعی پیشرفته، دوره‌های برنامه نویسی و طراحی سایت دسترسی پیدا کنید." />
  <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
  <meta property="og:url" content="https://keyvanwebsite.ir/sign-up.php" />
  <meta property="og:type" content="website" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="ثبت نام در کیوان وب" />
  <meta name="twitter:description" content="به جمع دانشجویان کیوان وب بپیوندید و از آموزش‌های حرفه‌ای بهره‌مند شوید." />
  <link rel="canonical" href="https://keyvanwebsite.ir/sign-up.php" />
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
  <link rel="icon" type="image/png" href="images/wolf-logo.png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#4f8cff">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
<body class="page-auth">
  <?php if (!empty($errors)): ?>
    <div style="background: rgba(255, 80, 100, 0.15); border: 1px solid #ff5a78; border-radius: 16px; padding: 16px 20px; margin: 100px 7vw 0 7vw; direction: rtl;">
      <?php foreach($errors as $error): ?>
        <p style="color: #ff8fab; margin: 5px 0;">❌ <?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <header>
    <div class="header-start">
      <div class="logo">
        <div class="logo-icon"><img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب" /></div>
        <div class="logo-text"><span class="fa">کیوان وب</span><span class="en">Keyvan Web</span></div>
      </div>
      <a href="ai.php" class="header-ai-btn" aria-label="دستیار هوش مصنوعی"><span class="header-ai-dot"></span>Ai</a>
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
  <main class="auth-page">
    <div class="auth-visual">
      <img src="images/contact-us.jpeg" alt="ثبت نام در کیوان وب" />
      <div class="auth-visual-overlay">
        <span class="auth-visual-kicker">JOIN KEYVAN WEB</span>
        <h2>به جمع ما بپیوند</h2>
        <p>با ثبت نام، مسیر یادگیری وب، هوش مصنوعی و برنامه‌نویسی را با کیوان وب شروع کن.</p>
      </div>
    </div>
    <div class="auth-form-side">
      <div class="auth-box">
        <div class="auth-box-header">
          <h1>ثبت نام</h1>
          <p>حساب کاربری جدید بسازید</p>
        </div>
        <form class="auth-form" action="signup_process.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <label class="auth-field"><span>نام و نام خانوادگی</span><input type="text" name="fullname" placeholder="نام کامل خود را وارد کنید" required /></label>
    
    <label class="auth-field"><span>نام کاربری (Username)</span><input type="text" name="username" placeholder="نام کاربری دلخواه" required /></label>
    
    <label class="auth-field"><span>ایمیل</span><input type="email" name="email" placeholder="example@email.com" required /></label>
    
    <label class="auth-field"><span>شماره تلفن همراه</span><input type="tel" name="phone" placeholder="مثال: 09123456789" required /></label>
    
    <label class="auth-field"><span>رمز عبور</span><input type="password" name="password" placeholder="حداقل ۸ کاراکتر" required /></label>
    
    <label class="auth-field"><span>تکرار رمز عبور</span><input type="password" name="password_confirm" placeholder="رمز عبور را دوباره وارد کنید" required /></label>
    
    <label class="auth-field">
        <span>سوال امنیتی</span>
        <select name="security_question" required style="width:100%; padding:12px; border-radius:10px; background:#0a0f2a; color:#fff; border:1px solid #4f8cff;">
            <option value="">یک سوال انتخاب کنید</option>
            <option value="نام مادر شما چیست؟">نام مادر شما چیست؟</option>
            <option value="نام پدر شما چیست؟">نام پدر شما چیست؟</option>
            <option value="شهر محل تولد شما؟">شهر محل تولد شما؟</option>
            <option value="نام اولین مدرسه‌تان؟">نام اولین مدرسه‌تان؟</option>
            <option value="نام بهترین دوست شما؟">نام بهترین دوست شما؟</option>
            <option value="مدل اولین موبایلتان؟">مدل اولین موبایلتان؟</option>
        </select>
    </label>
    
    <label class="auth-field">
        <span>پاسخ سوال امنیتی</span>
        <input type="text" name="security_answer" placeholder="پاسخ را وارد کنید" required />
        <small style="font-size:11px; opacity:0.7;">این پاسخ برای بازیابی رمز استفاده می‌شود</small>
    </label>
    
    <label class="auth-checkbox auth-checkbox-block"><input type="checkbox" name="terms" required /><span>قوانین و مقررات کیوان وب را می‌پذیرم</span></label>
    
    <button type="submit" class="auth-submit">ثبت نام</button>
</form>
        <p class="auth-switch">قبلاً ثبت نام کرده‌اید؟ <a href="sign-in.php">ورود</a></p>
      </div>
    </div>
  </main>
  <div id="stars-container"></div>
  <script>
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