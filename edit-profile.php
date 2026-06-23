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
$success = '';
$error = '';

// ========== دریافت اطلاعات کاربر ==========
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// ========== پردازش فرم ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    
    $profile_image = $user['profile_image'];
    
    // ===== آپلود عکس پروفایل =====
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if ($profile_image && file_exists($profile_image)) unlink($profile_image);
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
        $upload_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
            $profile_image = $upload_path;
        }
    }
    
    // ===== بررسی یکتایی نام کاربری =====
    if ($username !== $user['username']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $error = '❌ این نام کاربری قبلاً ثبت شده است';
        }
    }
    
    // ===== ذخیره تغییرات =====
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE users SET fullname = ?, username = ?, phone = ?, bio = ?, profile_image = ?, profile_completed = 1 WHERE id = ?");
        $stmt->execute([$fullname, $username, $phone, $bio, $profile_image, $user_id]);
        
        $_SESSION['user_name'] = $fullname;
        $success = 'پروفایل شما با موفقیت به‌روزرسانی شد.';
        
        header('Location: edit-profile.php?success=1');
        exit;
    }
}

if (isset($_GET['success'])) {
    $success = 'پروفایل شما با موفقیت به‌روزرسانی شد.';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title>ویرایش پروفایل | کیوان وب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/wolf-logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f8cff">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
        #installPWA {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: none;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            transition: 0.3s;
        }
        #installPWA:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79,140,255,0.5);
        }
        .edit-profile-container {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(5,10,30,0.8);
            border-radius: 24px;
            border: 1px solid rgba(158,173,255,0.3);
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #dfe6ff;
            font-size: 14px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(158,173,255,0.3);
            background: rgba(3,6,18,0.8);
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #4f8cff;
            box-shadow: 0 0 0 3px rgba(79,140,255,0.2);
        }
        .form-group input::placeholder, .form-group textarea::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .form-note {
            font-size: 11px;
            opacity: 0.6;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .form-note svg {
            flex-shrink: 0;
        }
        .profile-image-wrapper {
            text-align: center;
            margin-bottom: 25px;
        }
        .profile-image-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #4f8cff;
            box-shadow: 0 0 20px rgba(79,140,255,0.3);
        }
        .file-input-label {
            display: inline-block;
            background: rgba(79,140,255,0.2);
            border: 1px solid rgba(79,140,255,0.5);
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
        }
        .file-input-label:hover { background: rgba(79,140,255,0.4); }
        input[type="file"] { display: none; }
        .btn-save {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border: none;
            border-radius: 30px;
            padding: 14px;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(79,140,255,0.4);
        }
        .btn-back {
            background: rgba(158,173,255,0.15);
            border: 1px solid rgba(158,173,255,0.4);
            border-radius: 30px;
            padding: 12px;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
            transition: 0.3s;
            margin-top: 15px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn-back:hover { background: rgba(158,173,255,0.25); }
        .success-msg {
            background: rgba(80,255,100,0.15);
            border: 1px solid #5aff78;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            color: #8affab;
            text-align: center;
        }
        .error-msg {
            background: rgba(255,80,100,0.15);
            border: 1px solid #ff5a78;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            color: #ff8fab;
            text-align: center;
        }
        @media (max-width: 600px) {
            .edit-profile-container { padding: 20px; margin: 0 15px; }
            .edit-profile-container h2 { font-size: 22px; }
            .profile-image-preview { width: 100px; height: 100px; }
        }
        @media (max-width: 480px) {
            main { padding: 120px 15px 60px !important; }
            .edit-profile-container { padding: 15px; }
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
    <main style="padding: 140px 7vw 80px; min-height: 70vh;">
        <div class="edit-profile-container">
            <h2>✏️ ویرایش پروفایل عمومی</h2>
            
            <?php if($success): ?>
                <div class="success-msg">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <!-- ===== عکس پروفایل ===== -->
                <div class="profile-image-wrapper">
                    <?php if($user['profile_image'] && file_exists($user['profile_image'])): ?>
                        <img src="<?php echo $user['profile_image']; ?>" class="profile-image-preview" id="profilePreview" alt="پروفایل">
                    <?php else: ?>
                        <img src="images/default-avatar.png" class="profile-image-preview" id="profilePreview" alt="پروفایل">
                    <?php endif; ?>
                    <div>
                        <label class="file-input-label">
                            📷 انتخاب عکس پروفایل
                            <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this)">
                        </label>
                        <small style="display: block; margin-top: 8px; opacity: 0.6;">jpg, png, gif (حداکثر 2MB)</small>
                    </div>
                </div>
                
                <!-- ===== نام و نام خانوادگی ===== -->
                <div class="form-group">
                    <label>نام و نام خانوادگی</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    <div class="form-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        این نام در پروفایل شما به دیگران نمایش داده می‌شود
                    </div>
                </div>
                
                <!-- ===== نام کاربری ===== -->
                <div class="form-group">
                    <label>نام کاربری (Username)</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" placeholder="مثال: keyvanweb" required>
                    <div class="form-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        نام کاربری شما در پروفایل عمومی نمایش داده می‌شود
                    </div>
                </div>
                
                <!-- ===== شماره تلفن ===== -->
                <div class="form-group">
                    <label>شماره تلفن همراه</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="مثال: 09123456789">
                    <div class="form-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.574 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        شماره تماس شما به هیچ کس نمایش داده نمی‌شود (فقط برای احراز هویت)
                    </div>
                </div>
                
                <!-- ===== بیوگرافی ===== -->
                <div class="form-group">
                    <label>درباره من (بیوگرافی)</label>
                    <textarea name="bio" rows="4" placeholder="کمی درباره خودتان بنویسید... تخصص‌ها، علایق، اهداف..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    <div class="form-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                        این متن در پروفایل عمومی شما به دیگران نمایش داده می‌شود
                    </div>
                </div>
                
                <!-- ===== دکمه‌ها ===== -->
                <button type="submit" class="btn-save">💾 ذخیره تغییرات</button>
                <a href="dashboard.php" class="btn-back">← بازگشت به داشبورد</a>
            </form>
        </div>
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
        if (toggle) toggle.addEventListener("click", () => { 
            toggle.classList.toggle("active"); 
            nav.classList.toggle("open"); 
        });
        if (nav) nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => { 
            if (toggle) toggle.classList.remove("active"); 
            nav.classList.remove("open"); 
        }));
        
        // ===== پیش‌نمایش عکس =====
        function previewImage(input) {
            const preview = document.getElementById('profilePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { 
                    preview.src = e.target.result; 
                }
                reader.readAsDataURL(input.files[0]);
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