<?php
// ========== بارگذاری فایل‌های مورد نیاز ==========
require_once 'config.php';
require_once 'security.php';

// ========== بررسی ورود کاربر ==========
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ========== تنظیمات صفحه‌بندی ==========
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 8;
$offset = ($page - 1) * $per_page;

// ========== دریافت کلمه جست‌وجو ==========
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ========== ساخت کوئری با جست‌وجو ==========
if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM text_courses WHERE status = 'active' AND (title LIKE ? OR description LIKE ?)");
    $stmt->execute([$search_term, $search_term]);
    $total_courses = $stmt->fetchColumn();
    
    $sql = "SELECT * FROM text_courses WHERE status = 'active' AND (title LIKE ? OR description LIKE ?) ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$search_term, $search_term]);
    $courses = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM text_courses WHERE status = 'active'");
    $stmt->execute();
    $total_courses = $stmt->fetchColumn();
    
    $sql = "SELECT * FROM text_courses WHERE status = 'active' ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $courses = $pdo->query($sql)->fetchAll();
}

$total_pages = ceil($total_courses / $per_page);

// ========== اگر صفحه درخواستی بیشتر از تعداد صفحات بود ==========
if ($page > $total_pages && $total_pages > 0) {
    header('Location: ?page=1' . (!empty($search) ? '&search=' . urlencode($search) : ''));
    exit;
}

// ========== اطلاعات سئو ==========
$page_title = !empty($search) ? "نتایج جست‌وجو: $search | کیوان وب" : "دوره‌های متنی رایگان | آموزش برنامه‌نویسی و هوش مصنوعی | کیوان وب";
$page_description = !empty($search) ? "نتایج جست‌وجو برای '$search' در دوره‌های کیوان وب" : "دسترسی رایگان به دوره‌های آموزشی برنامه‌نویسی، طراحی سایت، هوش مصنوعی و ساخت ربات تلگرام.";
$page_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$page_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/wolf-logo.jpg";
$keywords = "دوره رایگان برنامه نویسی, آموزش طراحی سایت, هوش مصنوعی رایگان, کیوان وب, آموزش ربات تلگرام";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $page_url; ?>">
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo $page_image; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="کیوان وب | Keyvan Web">
    
    <!-- ==========================================================
    Twitter Card
    ========================================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo $page_image; ?>">
    
    <!-- ==========================================================
    استایل‌ها
    ========================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/wolf-logo.png">

    <!-- ==========================================================
    استایل‌های داخلی
    ========================================================== -->
    <style>
        .main-courser {
            display: flex;
            flex-direction: column;
        }
            
        .courses-header { 
            text-align: center; 
            margin-bottom: 40px; 
        }
        .courses-header h1 { 
            font-size: 32px; 
            background: linear-gradient(135deg, #4f8cff, #c471ff); 
            -webkit-background-clip: text; 
            background-clip: text; 
            color: transparent; 
        }
        .courses-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 25px; 
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
            height: 180px; 
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
        .course-description { 
            font-size: 13px; 
            opacity: 0.8; 
            margin-bottom: 15px; 
            display: -webkit-box; 
            -webkit-line-clamp: 3; 
            -webkit-box-orient: vertical; 
            overflow: hidden; 
        }
        .course-btn { 
            display: inline-block; 
            background: linear-gradient(135deg, #4f8cff, #c471ff); 
            border-radius: 30px; 
            padding: 8px 20px; 
            color: white; 
            text-decoration: none; 
            font-size: 13px; 
        }
        
        /* ===== باکس جست‌وجو ===== */
        .search-box {
            max-width: 500px;
            margin: 0 auto 40px auto;
            position: relative;
        }
        .search-box form {
            display: flex;
            gap: 10px;
            background: rgba(5,10,30,0.7);
            border-radius: 60px;
            padding: 5px;
            border: 1px solid rgba(158,173,255,0.3);
            transition: 0.3s;
        }
        .search-box form:focus-within {
            border-color: #4f8cff;
            box-shadow: 0 0 20px rgba(79,140,255,0.3);
        }
        .search-input {
            flex: 1;
            background: transparent;
            border: none;
            padding: 14px 20px;
            color: white;
            font-size: 15px;
            outline: none;
            font-family: inherit;
        }
        .search-input::placeholder {
            color: rgba(255,255,255,0.5);
        }
        .search-btn {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border: none;
            border-radius: 50px;
            padding: 0 25px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .search-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(79,140,255,0.5);
        }
        .search-clear {
            position: absolute;
            left: 100px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 13px;
            background: rgba(255,255,255,0.1);
            padding: 5px 12px;
            border-radius: 30px;
            transition: 0.3s;
        }
        .search-clear:hover {
            background: rgba(255,80,100,0.3);
            color: #ff8fab;
        }
        .search-result-info {
            text-align: center;
            margin-bottom: 25px;
            padding: 10px 20px;
            background: rgba(79,140,255,0.1);
            border-radius: 30px;
            display: inline-block;
            width: auto;
            margin-left: auto;
            margin-right: auto;
        }
        .search-result-info span {
            color: #4f8cff;
            font-weight: bold;
        }
        .highlight {
            background: rgba(79,140,255,0.4);
            padding: 0 4px;
            border-radius: 6px;
            color: #fff;
        }
        .no-result {
            text-align: center;
            padding: 60px;
            background: rgba(5,10,30,0.5);
            border-radius: 24px;
        }
        
        /* ===== صفحه‌بندی ===== */
        .pagination { 
            display: flex; 
            justify-content: center; 
            gap: 10px; 
            margin-top: 50px; 
            flex-wrap: wrap; 
        }
        .pagination a, .pagination span { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            min-width: 40px; 
            height: 40px; 
            padding: 0 12px; 
            border-radius: 12px; 
            background: rgba(5,10,30,0.7); 
            border: 1px solid rgba(158,173,255,0.3); 
            color: white; 
            text-decoration: none; 
        }
        .pagination a:hover { 
            background: rgba(79,140,255,0.2); 
            border-color: #4f8cff; 
        }
        .pagination .active { 
            background: linear-gradient(135deg, #4f8cff, #c471ff); 
            border-color: transparent; 
        }
        
        /* ===== ریسپانسیو ===== */
        @media (max-width: 1000px) { 
            .courses-grid { 
                grid-template-columns: repeat(3, 1fr); 
            } 
        }
        @media (max-width: 768px) { 
            .courses-grid { 
                grid-template-columns: repeat(2, 1fr); 
            }
            .search-box form { 
                flex-direction: column; 
                border-radius: 24px; 
            }
            .search-btn { 
                padding: 12px; 
                justify-content: center; 
            }
            .search-clear { 
                position: static; 
                display: inline-block; 
                margin-top: 10px; 
                text-align: center; 
            }
        }
        @media (max-width: 480px) { 
            .courses-grid { 
                grid-template-columns: 1fr; 
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
    <main class="main-courser" style="padding: 140px 7vw 80px; min-height: 70vh;">
        
        <!-- ===== باکس جست‌وجو ===== -->
        <div class="search-box">
            <form method="get" action="">
                <input type="text" name="search" class="search-input" 
                       placeholder="🔍 جست‌وجو در دوره‌ها... (عنوان یا توضیحات)"
                       value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                <button type="submit" class="search-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    جست‌وجو
                </button>
            </form>
            <?php if(!empty($search)): ?>
                <a href="text-courses.php" class="search-clear">✖ پاک کردن فیلتر</a>
            <?php endif; ?>
        </div>
        
        <!-- ===== اطلاعات نتیجه جست‌وجو ===== -->
        <?php if(!empty($search)): ?>
            <div style="text-align: center;">
                <div class="search-result-info">
                    🔍 <span><?php echo $total_courses; ?></span> دوره برای "<strong><?php echo htmlspecialchars($search); ?></strong>" یافت شد
                </div>
            </div>
        <?php endif; ?>
        
        <!-- ===== لیست دوره‌ها ===== -->
        <?php if(empty($courses)): ?>
            <div class="no-result">
                <p style="font-size: 48px; margin-bottom: 20px;">🔍</p>
                <p>هیچ دوره‌ای برای جست‌وجوی "<strong><?php echo htmlspecialchars($search); ?></strong>" یافت نشد.</p>
                <p style="font-size: 13px; opacity: 0.7; margin-top: 10px;">لطفاً کلمه دیگری را امتحان کنید.</p>
                <a href="text-courses.php" class="course-btn" style="margin-top: 20px; display: inline-block;">← بازگشت به همه دوره‌ها</a>
            </div>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach($courses as $course): 
                    $display_title = htmlspecialchars($course['title']);
                    $display_desc = htmlspecialchars(mb_substr($course['description'] ?? '', 0, 100)) . '...';
                    
                    if(!empty($search)) {
                        $pattern = '/' . preg_quote($search, '/') . '/ui';
                        $display_title = preg_replace($pattern, '<span class="highlight">$1</span>', $display_title);
                        $display_desc = preg_replace($pattern, '<span class="highlight">$1</span>', $display_desc);
                    }
                ?>
                    <div class="course-card">
                        <?php if($course['image'] && file_exists($course['image'])): ?>
                            <img src="<?php echo $course['image']; ?>" class="course-image" alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php else: ?>
                            <div style="height: 180px; background: linear-gradient(135deg, #1e2a4a, #0a0f2a); display: flex; align-items: center; justify-content: center; font-size: 48px;">📖</div>
                        <?php endif; ?>
                        <div class="course-info">
                            <div class="course-title"><?php echo $display_title; ?></div>
                            <div class="course-description"><?php echo $display_desc; ?></div>
                            <a href="text-course.php?id=<?php echo $course['id']; ?>" class="course-btn">مشاهده دوره →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- ===== صفحه‌بندی ===== -->
        <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">‹ قبلی</a>
                <?php endif; ?>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">بعدی ›</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
        if(toggle) toggle.addEventListener("click", () => { 
            toggle.classList.toggle("active"); 
            nav.classList.toggle("open"); 
        });
        if(nav) nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => { 
            if(toggle) toggle.classList.remove("active"); 
            nav.classList.remove("open"); 
        }));
        
        // ===== افکت ستاره‌ها =====
        const container = document.getElementById("stars-container");
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if(!isMobile) {
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
            if(container) container.style.display = 'none';
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
                if(outcome === 'accepted') console.log('کاربر برنامه را نصب کرد');
                deferredPrompt = null;
            });
        });
        window.addEventListener('appinstalled', () => {
            installBtn.style.display = 'none';
            deferredPrompt = null;
            console.log('برنامه نصب شد');
        });
        if(window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
    </script>
</body>
</html>