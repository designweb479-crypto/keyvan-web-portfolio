<!DOCTYPE html>
<?php
require_once 'security.php';
session_regenerate_id(true);
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');
$errors = $_SESSION['login_errors'] ?? [];
$old = $_SESSION['login_data'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['login_data']);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>ورود به حساب کاربری | کیوان وب - هوش مصنوعی و آموزش برنامه نویسی</title>
  <meta name="description" content="به حساب کاربری کیوان وب وارد شوید و به هوش مصنوعی پیشرفته، دوره‌های برنامه نویسی، طراحی سایت و آموزش زبان انگلیسی دسترسی پیدا کنید." />
  <meta name="keywords" content="ورود, کیوان وب, حساب کاربری, هوش مصنوعی, آموزش برنامه نویسی, طراحی سایت" />
  <meta name="author" content="کیوان وب | Keyvan Web" />
  <meta name="robots" content="index, follow" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:title" content="ورود به حساب کاربری | کیوان وب" />
  <meta property="og:description" content="به پلتفرم تخصصی هوش مصنوعی و آموزش خوش آمدید. وارد شوید و از خدمات کیوان وب استفاده کنید." />
  <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
  <meta property="og:url" content="https://keyvanwebsite.ir/sign-in.php" />
  <meta property="og:type" content="website" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="ورود به حساب کاربری | کیوان وب" />
  <meta name="twitter:description" content="وارد شوید و از هوش مصنوعی و دوره‌های آموزشی کیوان وب استفاده کنید." />
  <link rel="canonical" href="https://keyvanwebsite.ir/sign-in.php" />
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
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
      <img src="images/wolf-logo.jpg" alt="کیوان وب - ورود به حساب" />
      <div class="auth-visual-overlay">
        <span class="auth-visual-kicker">KEYVAN WEB</span>
        <h2>خوش برگشتی!</h2>
        <p>با ورود به حساب، به دوره‌ها، ویدیوها و ابزارهای هوش مصنوعی کیوان وب دسترسی داشته باش.</p>
      </div>
    </div>
    <div class="auth-form-side">
      <div class="auth-box">
        <div class="auth-box-header">
          <h1>ورود</h1>
          <p>به حساب کاربری خود وارد شوید</p>
        </div>
        <?php if (!empty($errors)): ?>
          <div style="background: rgba(255, 80, 100, 0.15); border: 1px solid #ff5a78; border-radius: 16px; padding: 16px 20px; margin-bottom: 20px;">
            <?php foreach($errors as $error): ?>
              <p style="color: #ff8fab; margin: 5px 0;"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form class="auth-form" action="signin_process.php" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
          <label class="auth-field"><span>ایمیل</span><input type="email" name="email" placeholder="example@email.com" required /></label>
          <label class="auth-field"><span>رمز عبور</span><input type="password" name="password" placeholder="رمز عبور خود را وارد کنید" required /></label>
          <div class="auth-options">
            <label class="auth-checkbox"><input type="checkbox" name="remember" /><span>مرا به خاطر بسپار</span></label>
            <a href="forgot-password.php" class="auth-link">فراموشی رمز عبور</a>
          </div>
          <button type="submit" class="auth-submit">ورود</button>
        </form>
        <p class="auth-switch">حساب کاربری ندارید؟ <a href="sign-up.php">ثبت نام</a></p>
      </div>
    </div>
  </main>
  <div id="stars-container"></div>
  <script>
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