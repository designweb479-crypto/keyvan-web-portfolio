<?php
require_once 'security.php';
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// بررسی اشتراک فعال
$stmt = $pdo->prepare("SELECT * FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$active_subscription = $stmt->fetch();

// پلن‌های اشتراک
$plans = [
    'basic' => [
        'name' => 'اشتراک پایه',
        'name_en' => 'Basic',
        'amount' => 299000,
        'days' => 30,
        'icon' => '📘',
        'color' => '#4f8cff',
        'features' => [
            '✅ دسترسی به همه دوره‌های ویدیویی',
            '✅ دسترسی به آموزش‌های متنی',
            '✅ پشتیبانی تیکت',
            '❌ هوش مصنوعی پیشرفته',
            '❌ ساخت عکس با AI',
            '❌ آپلود فایل و عکس'
        ]
    ],
    'pro' => [
        'name' => 'اشتراک حرفه‌ای',
        'name_en' => 'Pro',
        'amount' => 459000,
        'days' => 30,
        'icon' => '🚀',
        'color' => '#c471ff',
        'popular' => true,
        'features' => [
            '✅ همه امکانات اشتراک پایه',
            '✅ هوش مصنوعی نامحدود',
            '✅ ساخت عکس با AI',
            '✅ آپلود فایل و عکس',
            '✅ تحلیل تصویر با AI',
            '✅ اولویت در پشتیبانی'
        ]
    ],
    'yearly' => [
        'name' => 'اشتراک سالانه اقتصادی',
        'name_en' => 'Yearly',
        'amount' => 4900000,
        'days' => 365,
        'icon' => '⭐',
        'color' => '#ffaa00',
        'features' => [
            '✅ همه امکانات اشتراک حرفه‌ای',
            '✅ 12 ماه اشتراک با تخفیف ویژه',
            '✅ ذخیره‌سازی ابری نامحدود',
            '✅ پشتیبانی VIP',
            '✅ گواهی پایان دوره'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خرید اشتراک | کیوان وب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        .plans-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .plans-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .plans-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        .plan-card {
            background: rgba(5,10,30,0.8);
            border-radius: 24px;
            border: 1px solid rgba(158,173,255,0.3);
            padding: 30px;
            transition: 0.3s;
            position: relative;
        }
        .plan-card.popular {
            border: 2px solid #c471ff;
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(196,113,255,0.3);
        }
        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .plan-icon {
            font-size: 48px;
            margin-bottom: 15px;
            text-align: center;
        }
        .plan-name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }
        .plan-name-en {
            text-align: center;
            font-size: 12px;
            opacity: 0.6;
            margin-bottom: 15px;
        }
        .plan-price {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            color: #4f8cff;
            margin-bottom: 5px;
        }
        .plan-price small {
            font-size: 14px;
            color: #aaa;
        }
        .plan-period {
            text-align: center;
            font-size: 12px;
            opacity: 0.6;
            margin-bottom: 20px;
        }
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .plan-features li {
            padding: 8px 0;
            font-size: 13px;
            border-bottom: 1px solid rgba(158,173,255,0.1);
        }
        .btn-buy {
            background: linear-gradient(135deg, #4f8cff, #c471ff);
            border: none;
            border-radius: 30px;
            padding: 12px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            font-size: 16px;
        }
        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(79,140,255,0.4);
        }
        .active-subscription {
            background: rgba(80,255,100,0.1);
            border: 1px solid #5aff78;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        @media (max-width: 900px) {
            .plans-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .plan-card.popular {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main style="padding: 140px 7vw 80px;">
        <div class="plans-container">
            <div class="plans-header">
                <h1>🎁 اشتراک کیوان وب</h1>
                <p style="opacity: 0.8;">با تهیه اشتراک، به همه امکانات پیشرفته دسترسی پیدا کنید</p>
            </div>
            
            <?php if($active_subscription): ?>
                <div class="active-subscription">
                    <p>✅ شما هم‌اکنون دارای اشتراک فعال هستید!</p>
                    <p>نوع اشتراک: <strong><?php echo htmlspecialchars($active_subscription['plan_name']); ?></strong></p>
                    <p>تاریخ انقضا: <strong><?php echo date('Y-m-d', strtotime($active_subscription['expires_at'])); ?></strong></p>
                    <p style="margin-top: 10px;">برای خرید اشتراک جدید، اشتراک فعلی شما جایگزین خواهد شد.</p>
                </div>
            <?php endif; ?>
            
            <div class="plans-grid">
                <?php foreach($plans as $key => $plan): ?>
                    <div class="plan-card <?php echo isset($plan['popular']) ? 'popular' : ''; ?>">
                        <?php if(isset($plan['popular'])): ?>
                            <div class="popular-badge">⭐ محبوب‌ترین</div>
                        <?php endif; ?>
                        <div class="plan-icon"><?php echo $plan['icon']; ?></div>
                        <div class="plan-name"><?php echo $plan['name']; ?></div>
                        <div class="plan-name-en"><?php echo $plan['name_en']; ?></div>
                        <div class="plan-price"><?php echo number_format($plan['amount']); ?> <small>تومان</small></div>
                        <div class="plan-period"><?php echo $plan['days']; ?> روز</div>
                        <ul class="plan-features">
                            <?php foreach($plan['features'] as $feature): ?>
                                <li><?php echo $feature; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="payment.php?plan=<?php echo $key; ?>" class="btn-buy">خرید اشتراک</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>