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

// ========== بررسی دسترسی به هوش مصنوعی پیشرفته ==========
$has_advanced_ai = has_feature_access('ai_advanced');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- ==========================================================
    متا تگ‌های اصلی
    ========================================================== -->
    <meta charset="UTF-8" />
    <title>دستیار هوش مصنوعی Keyvan AI | کیوان وب</title>
    <meta name="description" content="با Keyvan AI، دستیار هوشمند کیوان وب، می‌توانید سوالات خود را بپرسید، عکس بسازید، فایل ارسال کنید و از مدل‌های مختلف هوش مصنوعی استفاده کنید." />
    <meta name="keywords" content="هوش مصنوعی, Keyvan AI, چت آنلاین, ساخت عکس با هوش مصنوعی, تحلیل تصویر, تشخیص صدا, کیوان وب" />
    <meta name="author" content="کیوان وب | Keyvan Web" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- ==========================================================
    Open Graph
    ========================================================== -->
    <meta property="og:title" content="Keyvan AI | دستیار هوشمند کیوان وب" />
    <meta property="og:description" content="دستیار هوش مصنوعی قدرتمند با قابلیت چت، ساخت عکس، تحلیل تصویر و تشخیص صدا." />
    <meta property="og:image" content="https://keyvanwebsite.ir/images/wolf-logo.jpg" />
    <meta property="og:url" content="https://keyvanwebsite.ir/ai.php" />
    <meta property="og:type" content="website" />
    
    <!-- ==========================================================
    تگ‌های سئو
    ========================================================== -->
    <link rel="canonical" href="https://keyvanwebsite.ir/ai.php" />
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Keyvan AI - دستیار هوش مصنوعی",
        "description": "پلتفرم تخصصی هوش مصنوعی کیوان وب",
        "url": "https://keyvanwebsite.ir/ai.php"
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
        .ai-model-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: rgba(5,10,30,0.8);
            border-radius: 12px;
            margin: 10px 20px;
            direction: ltr;
            flex-wrap: wrap;
            backdrop-filter: blur(10px);
        }
        .ai-model-selector select {
            background: #1e2a4a;
            color: #fff;
            border: 1px solid #4f8cff;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            cursor: pointer;
        }
        @media (max-width: 600px) {
            .ai-model-selector { flex-direction: column; align-items: stretch; }
            .ai-model-selector span { display: none; }
        }
        .subscription-warning {
            background: rgba(255,80,100,0.15);
            border: 1px solid #ff5a78;
            border-radius: 12px;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
        }
        .disabled-btn {
            opacity: 0.5;
            cursor: not-allowed !important;
            pointer-events: none;
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
        
        <?php if(!$has_advanced_ai): ?>
        // ========== غیرفعال کردن قابلیت‌های پیشرفته برای کاربران بدون اشتراک ==========
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('ai-file-input');
            const imageInput = document.getElementById('ai-image-input');
            const voiceBtn = document.getElementById('ai-voice-btn');
            const modelSelect = document.getElementById('model-select');
            const sendBtn = document.querySelector('.ai-send-btn');
            
            if(fileInput) {
                fileInput.disabled = true;
                fileInput.parentElement.classList.add('disabled-btn');
            }
            if(imageInput) {
                imageInput.disabled = true;
                imageInput.parentElement.classList.add('disabled-btn');
            }
            if(voiceBtn) {
                voiceBtn.disabled = true;
                voiceBtn.classList.add('disabled-btn');
            }
            if(modelSelect) {
                for(let i = modelSelect.options.length - 1; i >= 0; i--) {
                    if(modelSelect.options[i].value === 'gpt-image-2' || 
                       modelSelect.options[i].value === 'gpt-4o-mini-transcribe') {
                        modelSelect.remove(i);
                    }
                }
            }
            
            const composerActions = document.querySelector('.ai-composer-actions');
            if(composerActions && !document.querySelector('.subscription-warning')) {
                const warning = document.createElement('div');
                warning.className = 'subscription-warning';
                warning.innerHTML = '🔒 قابلیت ارسال فایل، عکس، ساخت تصویر و تشخیص صدا نیاز به <a href="plans.php" style="color:#4f8cff;">اشتراک حرفه‌ای</a> دارد';
                composerActions.parentNode.appendChild(warning);
            }
        });
        <?php endif; ?>
    </script>
</head>

<body class="page-ai">
    <!-- ==========================================================
    هدر سایت
    ========================================================== -->
    <header>
        <div class="header-start">
            <div class="logo">
                <div class="logo-icon"><img src="images/wolf-logo.jpg" alt="لوگوی گرگ کیوان وب" /></div>
                <div class="logo-text"><span class="fa">کیوان وب</span><span class="en">Keyvan Web</span></div>
            </div>
            <a href="ai.php" class="header-ai-btn is-active" aria-label="دستیار هوش مصنوعی">
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
    برنامه اصلی هوش مصنوعی
    ========================================================== -->
    <div class="ai-app">
        <!-- ===== سایدبار چپ (تاریخچه) ===== -->
        <aside class="ai-sidebar" id="ai-sidebar">
            <button type="button" class="ai-new-chat" id="ai-new-chat">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                گفتگوی جدید
            </button>
            <div class="ai-history-label">گفتگوهای اخیر</div>
            <div class="ai-history" id="ai-history-list">
                <div class="ai-history-loading" style="text-align:center; padding:20px; opacity:0.6;">در حال بارگذاری...</div>
            </div>
            <div class="ai-sidebar-brand">
                <div class="ai-brand-orb"><img src="images/wolf-logo.jpg" alt="" /></div>
                <div>
                    <strong>Keyvan AI</strong>
                    <span>دستیار هوشمند کیوان وب</span>
                </div>
            </div>
        </aside>
        <div class="ai-sidebar-backdrop" id="ai-sidebar-backdrop"></div>

        <!-- ===== بخش اصلی چت ===== -->
        <main class="ai-chat">
            <h1 style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;">
                دستیار هوش مصنوعی Keyvan AI - چت آنلاین، ساخت عکس و تحلیل تصویر
            </h1>
            
            <!-- ===== پیام‌ها ===== -->
            <div class="ai-messages" id="ai-messages">
                <div class="ai-welcome" id="ai-welcome">
                    <div class="ai-welcome-glow"></div>
                    <div class="ai-welcome-avatar"><img src="images/wolf-logo.jpg" alt="Keyvan AI" /></div>
                    <h2>سلام! من Keyvan AI هستم</h2>
                    <p>هر سوالی درباره وب، هوش مصنوعی، برنامه‌نویسی یا ایده‌های خلاقانه داری بپرس. می‌تونی متن، عکس، فایل یا پیام صوتی هم بفرستی.</p>
                    <div class="ai-suggestions">
                        <button type="button" class="ai-suggestion" data-prompt="یک ایده جذاب برای سایت شخصی بهم بده">ایده برای سایت شخصی</button>
                        <button type="button" class="ai-suggestion" data-prompt="از کجا شروع کنم یادگیری JavaScript؟">شروع JavaScript</button>
                        <button type="button" class="ai-suggestion" data-prompt="چطور یک ربات تلگرام بسازم؟">ساخت ربات تلگرام</button>
                        <button type="button" class="ai-suggestion" data-prompt="بهترین ابزارهای هوش مصنوعی برای طراحی چیه؟">ابزارهای AI</button>
                    </div>
                </div>
            </div>

            <!-- ===== بخش پیوست‌ها ===== -->
            <div class="ai-attachments" id="ai-attachments" hidden></div>

            <!-- ===== بخش نوشتن پیام ===== -->
            <div class="ai-composer-wrap">
                <!-- ===== انتخاب مدل و زبان ===== -->
                <div class="ai-model-selector">
                    <span style="font-size: 13px; opacity: 0.8;">🤖 مدل:</span>
                    <select id="model-select">
                        <option value="gemini-3.1-flash-lite-preview">⚡ Gemini 3.1 Flash Lite (چت روزمره - ارزان)</option>
                        <option value="glm-4.7-flash">💻 GLM-4.7 Flash (کدنویسی - ارزان)</option>
                        <option value="gpt-4o-mini">🚀 GPT-4o Mini (همه‌کاره - سریع)</option>
                        <option value="deepseek-chat">🧠 DeepSeek Chat (تحلیل - قدرتمند)</option>
                        <option value="claude-haiku-4-5">⚖️ Claude 3.5 Haiku (متعادل)</option>
                        <?php if($has_advanced_ai): ?>
                        <option value="gpt-image-2">🖼 gpt-image-2 (ساخت عکس سریع)</option>
                        <option value="gpt-4o-mini-transcribe">🎤 GPT-4o Mini Transcribe (تبدیل ویس به متن)</option>
                        <?php endif; ?>
                    </select>
                    <span style="margin-right: 10px;">🌍 زبان:</span>
                    <select id="language-select">
                        <option value="فارسی">فارسی</option>
                        <option value="English">English</option>
                    </select>
                </div>

                <!-- ===== فرم ارسال پیام ===== -->
                <form class="ai-composer" id="ai-composer">
                    <div class="ai-composer-box">
                        <textarea id="ai-input" rows="1" placeholder="پیام خود را بنویسید..."></textarea>
                        <div class="ai-composer-actions">
                            <label class="ai-tool-btn" title="ارسال فایل">
                                <input type="file" id="ai-file-input" hidden />
                                <svg viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M14 2v6h6M12 18v-6M9 15h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </label>
                            <label class="ai-tool-btn" title="ارسال عکس">
                                <input type="file" id="ai-image-input" accept="image/*" hidden />
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="8.5" cy="10" r="1.5" fill="currentColor"/>
                                    <path d="M21 16l-5.5-5.5a1.5 1.5 0 0 0-2.12 0L3 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </label>
                            <button type="button" class="ai-tool-btn" id="ai-voice-btn" title="پیام صوتی">
                                <svg viewBox="0 0 24 24">
                                    <rect x="9" y="3" width="6" height="11" rx="3" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M5 11a7 7 0 0 0 14 0M12 18v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <button type="submit" class="ai-send-btn" id="ai-send-btn">
                                <svg viewBox="0 0 24 24">
                                    <path d="M5 12h12M13 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="ai-disclaimer">Keyvan AI ممکن است اشتباه کند. اطلاعات مهم را بررسی کنید.</p>
                </form>
            </div>
        </main>
    </div>

    <!-- ==========================================================
    افکت ستاره‌ها
    ========================================================== -->
    <div id="stars-container"></div>

    <!-- ==========================================================
    اسکریپت‌های اصلی
    ========================================================== -->
    <script>
    // ========== بارگذاری تاریخچه ==========
    async function loadHistory() {
        const historyDiv = document.getElementById('ai-history-list');
        if (!historyDiv) return;
        
        try {
            const response = await fetch('get-history.php');
            const data = await response.json();
            
            if (data.history.length === 0) {
                historyDiv.innerHTML = '<div style="text-align:center; padding:20px; opacity:0.6;">📭 شما هنوز تاریخچه‌ای ندارید</div>';
                return;
            }
            
            historyDiv.innerHTML = '';
            data.history.forEach(item => {
                const date = new Date(item.created_at).toLocaleDateString('fa-IR');
                const btn = document.createElement('button');
                btn.className = 'ai-history-item';
                btn.innerHTML = `${item.user_message.substring(0, 35)}${item.user_message.length > 35 ? '...' : ''}<br><small style="font-size:10px; opacity:0.5;">${date}</small>`;
                btn.onclick = () => {
                    document.getElementById('ai-input').value = item.user_message;
                    document.getElementById('ai-composer').requestSubmit();
                };
                historyDiv.appendChild(btn);
            });
        } catch(e) {
            console.error('خطا در بارگذاری تاریخچه:', e);
            historyDiv.innerHTML = '<div style="text-align:center; padding:20px; opacity:0.6;">⚠️ خطا در بارگذاری</div>';
        }
    }

    loadHistory();
    
    // ===== منوی موبایل =====
    const toggle = document.querySelector(".menu-toggle");
    const nav = document.querySelector("nav");
    if (toggle) toggle.addEventListener("click", () => { toggle.classList.toggle("active"); nav.classList.toggle("open"); });
    if (nav) nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => { if (toggle) toggle.classList.remove("active"); nav.classList.remove("open"); }));
    
    // ===== متغیرهای اصلی =====
    const messagesEl = document.getElementById("ai-messages");
    const welcomeEl = document.getElementById("ai-welcome");
    const composer = document.getElementById("ai-composer");
    const input = document.getElementById("ai-input");
    const attachmentsEl = document.getElementById("ai-attachments");
    const fileInput = document.getElementById("ai-file-input");
    const imageInput = document.getElementById("ai-image-input");
    const voiceBtn = document.getElementById("ai-voice-btn");
    const newChatBtn = document.getElementById("ai-new-chat");
    const sidebar = document.getElementById("ai-sidebar");
    const sidebarBackdrop = document.getElementById("ai-sidebar-backdrop");
    let pendingAttachments = [];
    let isRecording = false;
    
    // ===== کلید API =====
    const API_KEY = 'aa-bchPur7jt1ktjL7vRSZnfLayNkG9Wh5p60pgKaTzN8t0NXjg';
    const API_URL = 'https://api.avalai.ir/v1/chat/completions';
    const IMG_API_URL = 'https://api.avalai.ir/v1/images/generations';
    
    // ===== دریافت پاسخ از API =====
    async function getRealReply(text, imageBase64 = null) {
        const modelSelect = document.getElementById('model-select');
        const langSelect = document.getElementById('language-select');
        let selectedModel = modelSelect ? modelSelect.value : 'gpt-4o-mini';
        const selectedLang = langSelect ? langSelect.value : 'فارسی';
        
        <?php if(!$has_advanced_ai): ?>
        if (imageBase64) {
            return '⚠️ قابلیت ارسال عکس و فایل نیاز به <a href="plans.php">اشتراک حرفه‌ای</a> دارد.';
        }
        if (selectedModel === 'gpt-image-2' || selectedModel === 'gpt-4o-mini-transcribe') {
            return '⚠️ این قابلیت فقط برای کاربران دارای <a href="plans.php">اشتراک حرفه‌ای</a> فعال است.';
        }
        <?php endif; ?>
        
        const finalText = `به زبان ${selectedLang} پاسخ بده: ${text}`;
        
        try {
            let messages = [];
            if (imageBase64) {
                messages = [{ role: "user", content: [{ type: "image_url", image_url: { url: imageBase64 } }, { type: "text", text: finalText }] }];
            } else {
                messages = [{ role: "user", content: finalText }];
            }
            
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${API_KEY}`, 
                    'Content-Type': 'application/json' 
                },
                body: JSON.stringify({ 
                    model: selectedModel, 
                    messages: messages, 
                    temperature: 0.7, 
                    max_tokens: 1024 
                })
            });
            
            const data = await response.json();
            
            if (data.choices && data.choices[0] && data.choices[0].message) {
                const realAnswer = data.choices[0].message.content;
                
                // ذخیره در تاریخچه
                fetch('save-chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_message: text,
                        ai_response: realAnswer,
                        model_used: selectedModel
                    })
                })
                .then(() => loadHistory())
                .catch(e => console.log('خطا در ذخیره تاریخچه:', e));
                
                return realAnswer;
            } else if (data.error) {
                return `❌ خطا: ${data.error.message}`;
            } else {
                return 'پاسخی دریافت نشد. دوباره تلاش کن.';
            }
        } catch (error) { 
            console.error('API Error:', error); 
            return '⚠️ خطا در ارتباط با سرور. لطفاً چند دقیقه دیگر تلاش کن.'; 
        }
    }
    
    // ===== ساخت عکس با هوش مصنوعی =====
    async function generateImage(prompt) {
        <?php if(!$has_advanced_ai): ?>
        return '⚠️ قابلیت ساخت عکس نیاز به <a href="plans.php">اشتراک حرفه‌ای</a> دارد.';
        <?php else: ?>
        try {
            const response = await fetch(IMG_API_URL, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${API_KEY}`, 'Content-Type': 'application/json' },
                body: JSON.stringify({ model: "gpt-image-2", prompt: prompt, n: 1, size: "1024x1024", response_format: "b64_json" })
            });
            const data = await response.json();
            if (data.data && data.data[0] && data.data[0].b64_json) return `data:image/png;base64,${data.data[0].b64_json}`;
            else if (data.data && data.data[0] && data.data[0].url) return data.data[0].url;
            else { console.error("ساختار پاسخ غیرمنتظره:", data); return null; }
        } catch (error) { console.error('خطا در ساخت عکس:', error); return null; }
        <?php endif; ?>
    }
    
    // ===== توابع کمکی =====
    function escapeHtml(text) { return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"); }
    function resizeInput() { if (input) { input.style.height = "auto"; input.style.height = Math.min(input.scrollHeight, 160) + "px"; } }
    if (input) { input.addEventListener("input", resizeInput); input.addEventListener("keydown", (e) => { if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); if (composer) composer.requestSubmit(); } }); }
    function hideWelcome() { if (welcomeEl) welcomeEl.remove(); }
    function scrollToBottom() { if (messagesEl) messagesEl.scrollTop = messagesEl.scrollHeight; }
    
    function createMessage(role, content, extras = "") {
        hideWelcome();
        const msg = document.createElement("div");
        msg.className = `ai-message ai-message-${role}`;
        const avatar = role === "assistant" ? `<div class="ai-msg-avatar"><img src="images/wolf-logo.jpg" alt="" /></div>` : `<div class="ai-msg-avatar ai-msg-avatar-user"><span>شما</span></div>`;
        msg.innerHTML = `${avatar}<div class="ai-msg-body"><div class="ai-msg-content">${content}</div>${extras}</div>`;
        if (messagesEl) messagesEl.appendChild(msg);
        scrollToBottom();
        if (msg) msg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return msg;
    }
    
    function showTyping() {
        if (!messagesEl) return;
        const typing = document.createElement("div");
        typing.className = "ai-message ai-message-assistant ai-typing";
        typing.id = "ai-typing";
        typing.innerHTML = `<div class="ai-msg-avatar"><img src="images/wolf-logo.jpg" alt="" /></div><div class="ai-msg-body"><div class="ai-msg-content"><span class="ai-typing-dot"></span><span class="ai-typing-dot"></span><span class="ai-typing-dot"></span></div></div>`;
        messagesEl.appendChild(typing);
        scrollToBottom();
    }
    function removeTyping() { document.getElementById("ai-typing")?.remove(); }
    
    function renderAttachmentsHtml(items) {
        if (!items.length) return "";
        const html = items.map(item => item.type.startsWith("image/") ? `<img class="ai-msg-image" src="${item.url}" alt="${item.name}" />` : `<div class="ai-msg-file"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="none" stroke="currentColor"/><path d="M14 2v6h6" stroke="currentColor"/></svg><span>${item.name}</span></div>`).join("");
        return `<div class="ai-msg-attachments">${html}</div>`;
    }
    
    function updateAttachmentPreview() {
        if (!attachmentsEl) return;
        if (!pendingAttachments.length) { attachmentsEl.hidden = true; attachmentsEl.innerHTML = ""; return; }
        attachmentsEl.hidden = false;
        attachmentsEl.innerHTML = pendingAttachments.map((item, index) => `<div class="ai-attachment-chip">${item.type.startsWith("image/") ? `<img src="${item.url}" alt="" />` : `<span class="ai-attachment-icon">📄</span>`}<span>${item.name}</span><button type="button" data-index="${index}" aria-label="حذف">×</button></div>`).join("");
        attachmentsEl.querySelectorAll("button").forEach(btn => { btn.addEventListener("click", () => { const i = Number(btn.dataset.index); URL.revokeObjectURL(pendingAttachments[i].url); pendingAttachments.splice(i, 1); updateAttachmentPreview(); }); });
    }
    
    function addFiles(files) { Array.from(files).forEach(file => { pendingAttachments.push({ name: file.name, type: file.type, url: URL.createObjectURL(file) }); }); updateAttachmentPreview(); }
    if (fileInput) fileInput.addEventListener("change", () => { if (fileInput.files.length) addFiles(fileInput.files); fileInput.value = ""; });
    if (imageInput) imageInput.addEventListener("change", () => { if (imageInput.files.length) addFiles(imageInput.files); imageInput.value = ""; });
    
    // ===== ارسال پیام =====
    if (composer) {
        composer.addEventListener("submit", async (e) => {
            e.preventDefault();
            const text = input ? input.value.trim() : "";
            const isImageRequest = text.toLowerCase().includes("ساخت عکس") || text.toLowerCase().includes("تصویر بساز") || text.toLowerCase().startsWith("/image");
            
            <?php if(!$has_advanced_ai): ?>
            if (isImageRequest && text) {
                createMessage("assistant", "⚠️ قابلیت ساخت عکس نیاز به <a href='plans.php'>اشتراک حرفه‌ای</a> دارد.");
                return;
            }
            <?php endif; ?>
            
            if (isImageRequest && text) {
                const imagePrompt = text.replace(/ساخت عکس|تصویر بساز|\/image/gi, '').trim();
                createMessage("user", text);
                if (input) input.value = "";
                showTyping();
                const imageUrl = await generateImage(imagePrompt || "یک تصویر زیبا و مدرن");
                removeTyping();
                if (imageUrl) createMessage("assistant", `<img src="${imageUrl}" style="max-width:100%; border-radius:16px;" /><br/><em>تصویر ساخته شد</em>`);
                else createMessage("assistant", "❌ خطا در ساخت عکس. دوباره تلاش کن.");
                return;
            }
            
            let imageBase64 = null;
            if (pendingAttachments.length > 0) {
                const imageFile = pendingAttachments[0];
                if (imageFile.type.startsWith("image/")) imageBase64 = imageFile.url;
            }
            
            <?php if(!$has_advanced_ai): ?>
            if (imageBase64) {
                createMessage("assistant", "⚠️ قابلیت ارسال عکس و فایل نیاز به <a href='plans.php'>اشتراک حرفه‌ای</a> دارد.");
                return;
            }
            <?php endif; ?>
            
            if (!text && !imageBase64) return;
            const attachmentCopy = [...pendingAttachments];
            const attachmentHtml = renderAttachmentsHtml(attachmentCopy);
            if (imageBase64) createMessage("user", text || "🌄 این عکس رو ببین و توضیح بده", attachmentHtml);
            else createMessage("user", text ? `<p>${escapeHtml(text).replace(/\n/g, "<br>")}</p>` : "", attachmentHtml);
            if (input) { input.value = ""; resizeInput(); }
            pendingAttachments.forEach(item => URL.revokeObjectURL(item.url));
            pendingAttachments = [];
            updateAttachmentPreview();
            showTyping();
            try {
                const realAnswer = await getRealReply(text, imageBase64);
                removeTyping();
                createMessage("assistant", `<p>${realAnswer.replace(/\n/g, "<br>")}</p>`);
            } catch (err) { removeTyping(); createMessage("assistant", "<p>⚠️ خطا در ارتباط با سرور. لطفاً دوباره تلاش کن.</p>"); }
        });
    }
    
    // ===== دکمه‌های پیشنهادی =====
    document.querySelectorAll(".ai-suggestion").forEach(btn => btn.addEventListener("click", () => { if (input) { input.value = btn.dataset.prompt; resizeInput(); } if (composer) composer.requestSubmit(); }));
    
    // ===== دکمه گفتگوی جدید =====
    if (newChatBtn) newChatBtn.addEventListener("click", () => { 
        if (messagesEl) { 
            messagesEl.innerHTML = `<div class="ai-welcome" id="ai-welcome">
                <div class="ai-welcome-glow"></div>
                <div class="ai-welcome-avatar"><img src="images/wolf-logo.jpg" alt="Keyvan AI" /></div>
                <h2>سلام! من Keyvan AI هستم</h2>
                <p>هر سوالی درباره وب، هوش مصنوعی، برنامه‌نویسی یا ایده‌های خلاقانه داری بپرس. می‌تونی متن، عکس، فایل یا پیام صوتی هم بفرستی.</p>
                <div class="ai-suggestions">
                    <button type="button" class="ai-suggestion" data-prompt="یک ایده جذاب برای سایت شخصی بهم بده">ایده برای سایت شخصی</button>
                    <button type="button" class="ai-suggestion" data-prompt="از کجا شروع کنم یادگیری JavaScript؟">شروع JavaScript</button>
                    <button type="button" class="ai-suggestion" data-prompt="چطور یک ربات تلگرام بسازم؟">ساخت ربات تلگرام</button>
                    <button type="button" class="ai-suggestion" data-prompt="بهترین ابزارهای هوش مصنوعی برای طراحی چیه؟">ابزارهای AI</button>
                </div>
            </div>`;
            document.querySelectorAll(".ai-suggestion").forEach(btn => btn.addEventListener("click", () => { 
                if (input) { input.value = btn.dataset.prompt; resizeInput(); } 
                if (composer) composer.requestSubmit(); 
            })); 
        } 
        loadHistory();
        if (sidebar) sidebar.classList.remove("open"); 
    });
    
    // ===== سایدبار =====
    if (sidebarBackdrop && sidebar) sidebarBackdrop.addEventListener("click", () => sidebar.classList.remove("open"));
    
    // ===== افکت ستاره‌ها =====
    const container = document.getElementById("stars-container");
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (!isMobile) {
        function createStar() { const star = document.createElement("div"); star.classList.add("star"); const size = Math.random() * 2 + 1; star.style.width = size + "px"; star.style.height = size + "px"; star.style.left = Math.random() * 100 + "vw"; star.style.top = "100vh"; const duration = Math.random() * 6 + 4; star.style.animationDuration = duration + "s"; container.appendChild(star); setTimeout(() => star.remove(), duration * 1000); }
        function createSpark() { const spark = document.createElement("div"); spark.classList.add("spark"); spark.style.left = Math.random() * 100 + "vw"; spark.style.top = Math.random() * 100 + "vh"; container.appendChild(spark); setTimeout(() => spark.remove(), 1200); }
        setInterval(createStar, 120); setInterval(createSpark, 600);
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