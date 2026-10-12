<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar','fa','he']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    {{-- Title-specific and navbar-specific platform values may be provided via platform_title_setting and platform_navbar_setting --}}
    @php
        $__titlePlat = isset($platform_title_setting) && is_array($platform_title_setting) && count($platform_title_setting) ? $platform_title_setting : (isset($platform_setting) && is_array($platform_setting) ? $platform_setting : []);
        $__navPlat = isset($platform_navbar_setting) && is_array($platform_navbar_setting) && count($platform_navbar_setting) ? $platform_navbar_setting : (isset($platform_setting) && is_array($platform_setting) ? $platform_setting : []);
    @endphp
    <title>{{ isset($__titlePlat['name']) ? $__titlePlat['name'] : (config('app.name') ?? 'الاستثمار السعودي') }}</title>
    <meta name="description" content="{{ isset($__titlePlat['description']) ? $__titlePlat['description'] : (config('app.description') ?? '') }}">
   
      <!-- أيقونة التاب -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

  


<!-- وصف الموقع -->
<meta name="description" content="Istithmar Platform تقدم أفضل فرص الاستثمار للمستثمرين العرب بطريقة سهلة وآمنة.">

<!-- كلمات مفتاحية SEO -->
<meta name="keywords" content="استثمار, منصات استثمار, فرص استثمارية, Istithmar,استثمار, عوائد الاستثمار, الاستثمار السعودي, فرص استثمارية, منصات استثمارية, مشاريع مربحة, المال, المستثمر العربي, التمويل الشخصي, الاستثمار الآمن, الأسهم السعودية, سوق المال, استثمار عقاري, استثمار طويل الأجل, استثمار قصير الأجل, مشاريع صغيرة, مشاريع كبيرة, أرباح الاستثمار, رأس المال, إدارة الاستثمار, نصائح استثمارية, التمويل الجماعي, مشاريع ناجحة, عوائد مالية, شركات استثمار, حلول استثمارية, الاستثمار في السعودية, استثمار الكتروني, عوائد مالية عالية, فرص مالية, تنمية رأس المال, محافظ استثمار, الاستثمار الذكي, التقاعد والاستثمار, الاستثمار الدولي, العملات الرقمية, الذهب والاستثمار, الاستثمارات البديلة, صناديق الاستثمار, تمويل المشاريع, الاستثمار عبر الإنترنت, المشاريع المربحة, السوق السعودي, التطوير المالي, مستقبل الاستثمار, نمو الاستثمار, الاستثمار للمبتدئين, استثمار آمن للمستثمرين, استراتيجيات الاستثمار, إدارة المخاطر الاستثمارية
,المستثمر العربي">

<!-- تهيئة لمحركات البحث -->
<meta name="robots" content="index, follow">
<meta name="language" content="ar">
<meta name="author" content="Istithmar Platform">

<!-- Open Graph للروابط عند المشاركة على وسائل التواصل -->
<meta property="og:title" content="Istithmar Platform - منصة الاستثمار العربي">
<meta property="og:description" content="Istithmar Platform تقدم أفضل فرص الاستثمار للمستثمرين العرب بطريقة سهلة وآمنة.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://astithmar.com/">
<meta property="og:image" content="https://astithmar.com/logo.png">

  
  
  
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">
    <script>
        window.APP_USER_ID = '{{ auth()->check() ? auth()->id() : '' }}';
    </script>
  
  
         <style>

        .mobile-breadcrumb {
            width: 99%;
            border-radius: 12px;
            padding: 2px 10px;
            backdrop-filter: blur(10px);
            border: 1px solid #4ccaf017;
        }
        
        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            align-items: center;
            padding: 0;
            margin: 0;
            font-size: 1rem;
        }
        
        .breadcrumb-item {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }
        
        .breadcrumb-item a {
            color: #22d3ee;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .breadcrumb-item a:hover {
            background: rgba(76, 201, 240, 0.15);
        }
        
        .breadcrumb-item.active {
            color: #22d3ee;
            text-shadow: #ffffff 0 0 2px;
            font-weight: 600;
            padding: 8px 12px;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #4cc9f0;
            margin-left: 10px;
            font-weight: bold;
        }
        
        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 480px) {
            .breadcrumb {
                font-size: 0.9rem;
            }
            
            .breadcrumb-item {
                margin-left: 8px;
            }
            
            .breadcrumb-item a {
                padding: 6px 10px;
            }
            
            .breadcrumb-item.active {
                padding: 6px 10px;
            }
            
            .breadcrumb-item + .breadcrumb-item::before {
                margin-left: 8px;
            }
        }
        
        @media (max-width: 360px) {
            .breadcrumb {
                font-size: 0.85rem;
            }
            
            .mobile-breadcrumb {
                padding: 14px 16px;
            }
        }
             @media (max-width: 468px) {
 
    .text_center_title{
    text-align: center;
    }
  
  }
    </style>
  
  
    <style>
        /* Small inline fallback styles */
        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Noto Sans Arabic', 'Inter', sans-serif;
            /* Base dark color */
            background-color: #0f1724;
            color: #fff;
            margin: 0;
        }

        /* Investor pages only: layered, fixed backgrounds with contrast overlay */
        .is-investor {
            /* Top gradient overlay + grid (map moved to ::before for per-layer animation) */
            background-image:
                linear-gradient(to bottom, rgba(15,23,36,0.75) 0%, rgba(15,23,36,0.55) 120px, rgba(15,23,36,0) 280px),
                url('{{ asset('images/grid-growth-bg.svg') }}') !important;
            background-repeat: no-repeat, no-repeat !important;
            background-attachment: scroll, fixed !important;
            background-position:
                top 0 center,
                center center !important;
            background-size:
                100% 280px,
                cover !important;
        }

        @media (max-width: 768px) {
            .is-investor {
                /* Mobile/iOS Safari fallback: avoid background-attachment: fixed jank */
                background-attachment: scroll, scroll !important;
                background-position:
                    top 0 center,
                    center center !important;
                background-size:
                    100% 200px,
                    cover !important;
            }
        }

        /* Animated map layer on top of grid, below content */
        .is-investor::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: url('{{ asset('images/saudi-growth-map.svg') }}');
            background-repeat: no-repeat;
            background-position: center calc(var(--nav-h) + 22px);
            background-size: min(1200px, 92vw) auto;
            opacity: 0.18;
            animation: mapPulse 8s ease-in-out infinite alternate, mapDrift 28s ease-in-out infinite alternate;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .is-investor::before {
                background-position: center calc(var(--nav-h) + 42px);
                background-size: 92vw auto;
                opacity: 0.16;
                animation: mapPulse 10s ease-in-out infinite alternate; /* disable drift on mobile for perf */
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .is-investor::before, .bg-anim-layer .anim-item { animation: none !important; }
        }

        @keyframes mapPulse {
            0% { opacity: 0.10; filter: saturate(0.95) brightness(0.95); }
            50% { opacity: 0.28; filter: saturate(1.05) brightness(1.0); }
            100% { opacity: 0.14; filter: saturate(0.9) brightness(0.92); }
        }

        @keyframes mapDrift {
            0%   { transform: translate3d(-8px, -6px, 0) scale(1.0); }
            50%  { transform: translate3d( 10px,  8px, 0) scale(1.02); }
            100% { transform: translate3d(-6px,  6px, 0) scale(1.0); }
        }

        a{
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00ffff 0%, #0099cc 100%);
            border: none;
            color: #000;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 25px;
            cursor: pointer
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #0044ff;
            color: #ffd700;
            padding: 10px 28px;
            border-radius: 25px;
            cursor: pointer
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px
        }

        /* Page frame: copy the visual width of the example glass-card
           and center pages that are not admin (body has class .is-investor)
           - max-width chosen to match a typical 'card' width; responsive down to mobile
           - does not change content or data, only constrains layout width */
        .is-investor .page-frame,
        .is-investor .app-content > .container,
        .is-investor .app-content > .container-fluid {
            max-width: 720px; /* copied width (approx) from the example box */
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            padding-left: 12px;
            padding-right: 12px;
        }

        /* Ensure glass-card elements inside the page frame don't stretch past it */
        .is-investor .page-frame .glass-card,
        .is-investor .app-content > .container .glass-card,
        .is-investor .app-content > .container-fluid .glass-card {
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100%;
        }

        /* Sidebar layout */
        .sidebar-toggle {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 10px 12px;
            border-radius: 10px;
            color: #fff;
            cursor: pointer
        }

        .language-toggle {
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 8px 12px;
            border-radius: 10px;
            color: #fff
        }

        @media (max-width:768px) {
            .language-toggle {
                display: none
            }
        }

        .sidebar {
            position: fixed;
            right: 0;
            top: 68px;
            bottom: 0;
            width: 260px;
            background: #0f1724;
            border-left: 1px solid rgba(255, 255, 255, 0.06);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform .25s ease;
            overflow-y: auto
        }

        .sidebar.open {
            transform: translateX(0)
        }

        .sidebar-header {
            position: sticky;
            top: 0;
            background: #0f1724;
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06)
        }

        .sidebar-title {
            color: #06b6d4;
            font-weight: 800;
            text-align: center
        }

        .sidebar-subtitle {
            color: #9ca3af;
            text-align: center;
            font-size: 12px;
            margin-top: 4px
        }

        .sidebar-close {
            position: absolute;
            left: 10px;
            top: 10px;
            background: transparent;
            border: 0;
            color: #cbd5e1;
            font-size: 22px;
            cursor: pointer
        }

        .sidebar-user {
            padding: 12px 16px
        }

        .sidebar-welcome {
            color: #c7f9fb;
            margin-bottom: 8px
        }

        .sidebar-auth {
            color: #cbd5e1;
            display: flex;
            justify-content: center;
            gap: 8px
        }

        .sidebar-auth a {
            color: #cbd5e1;
            text-decoration: underline
        }

        .sidebar-logout {
            margin-top: 8px;
            text-align: center
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            padding: 12px
        }

        .sidebar-nav a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 12px
        }

        .sidebar-nav a:hover {
            background: #111827
        }

        .sidebar-footer-space {
            height: 40px
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease
        }

        /* ensure back-home right movement is smooth */
        #back-home-container {
            transition: right .18s ease;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            pointer-events: auto
        }

        /* Content padding when sidebar docked (desktop) */
          .app-content {
                /* Remove outer left/right/bottom padding so content touches viewport edges.
                    Keep top padding to remain below the fixed navbar (set via body > .app-content). */
                padding: calc(var(--nav-h) + var(--nav-gap)) 0 0 0;
                transition: padding-right .25s ease;
                    position: relative; /* keep content above animated background layer */
                    z-index: 1;
          }

        @media (min-width:1024px) {
            body.sidebar-docked .app-content {
                padding-right: 260px
            }

            body.sidebar-docked .sidebar {
                transform: translateX(0)
            }

            body.sidebar-docked .sidebar-backdrop {
                display: none
            }

            /* keep sidebar toggle visible on desktop so user can collapse/expand the docked sidebar */
            .sidebar-toggle {
                display: block
            }

            /* collapsed state when user hides the docked sidebar */
            body.sidebar-docked.sidebar-collapsed .sidebar {
                transform: translateX(100%);
            }

            body.sidebar-docked.sidebar-collapsed .app-content {
                /* When collapsed we remove extra right padding so the main content aligns flush with the viewport */
                padding-right: 0 !important;
            }

            body.sidebar-docked.sidebar-collapsed .sidebar-backdrop {
                display: none;
            }
        }

        /* Normalize legacy left-margin when sidebar moved to right */
        .app-content [style*="margin-left:260px"] {
            margin-left: 0 !important;
        }

        /* accommodate pages that still have inline margin-left by neutralizing them
      and prefer padding-right on .app-content to create space for right sidebar */
        .app-content [style*="margin-right:260px"] {
            margin-right: 0 !important;
        }

        /* Responsive grids helpers for old inline-grid sections */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px
        }

        .grid-5 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px
        }

        @media (max-width:1024px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr)
            }

            .grid-5 {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media (max-width:768px) {

            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr
            }

            .grid-5 {
                grid-template-columns: repeat(2, 1fr)
            }

            .grid-2 {
                grid-template-columns: 1fr
            }
        }

        /* Force responsiveness for legacy inline grid/flex layouts */
        @media (max-width:1024px) {

            /* 4/5 columns -> 2/3 on tablets */
            .app-content [style*="grid-template-columns:repeat(4,1fr)"] {
                grid-template-columns: repeat(2, 1fr) !important
            }

            .app-content [style*="grid-template-columns:repeat(5,1fr)"] {
                grid-template-columns: repeat(3, 1fr) !important
            }
        }

        /* Mobile: ensure full-bleed layout and prevent horizontal overflow
           - Make common constrained containers span full width on phones
           - Keep small internal gutters for readability
           - Protect against accidental inline widths/margins left by legacy pages */
        @media (max-width: 768px) {
            html, body { width: 100%; max-width: 100%; overflow-x: hidden; }

            /* Make the main content area truly full-bleed on phones */
            .app-content { width: 100vw; max-width: 100vw; padding-left: 0; padding-right: 0; }

            /* Ensure common constrained wrappers don't limit width on mobile */
            .app-content .container,
            .app-content .container-fluid,
            .container,
            .container-fluid,
            .glass-card,
            .card {
                max-width: 100% !important;
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 12px !important; /* small inner gutter */
                padding-right: 12px !important;
            }

            /* Neutralize inline margin hacks that shift content off-screen */
            .app-content [style*="margin-left:260px"] { margin-left: 0 !important; }
            .app-content [style*="margin-right:260px"] { margin-right: 0 !important; }

            /* Images and embeds should not overflow */
            .app-content img, .app-content iframe, .app-content video {
                max-width: 100% !important;
                height: auto !important;
            }
        }

        @media (max-width:768px) {

            /* Any 2/3/4/5 column grids -> single column on phones */
            .app-content [style*="grid-template-columns:repeat(2,1fr)"],
            .app-content [style*="grid-template-columns:repeat(3,1fr)"],
            .app-content [style*="grid-template-columns:repeat(4,1fr)"],
            .app-content [style*="grid-template-columns:repeat(5,1fr)"] {
                grid-template-columns: 1fr !important
            }

            /* Make flex rows wrap on phones */
            .app-content [style*="display:flex"] {
                flex-wrap: wrap
            }

            .app-content [style*="display:flex"]>* {
                flex: 1 1 100%
            }

            .app-content img {
                max-width: 100%;
                height: auto
            }
        }
        /* Top nav layout */
        .main-nav {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: 52px;
            display: flex;
            align-items: center;
            background: #0f1724;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            z-index: 1000;
            padding: 0 12px;
            height: 68px;
        }

        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 12px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;

        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.06);
            color: #cbd5e1;
            cursor: pointer;
            transition: background .2s ease, transform .1s ease;
        }

        .icon-btn:hover { background: rgba(255,255,255,0.1); color:#fff; }
        .icon-btn:active { transform: scale(0.98); }

    .hidden { display: none !important; }

    /* Ensure content sits below the fixed top navbar across pages (dynamic) */
        :root { --nav-h: 68px; --nav-gap: 10px; }
        body > .app-content { padding-top: calc(var(--nav-h) + var(--nav-gap)); }
        /* Chat panel helpers: ensure the chat area is constrained and thread scrolls
           - .chat-panel: container for header, thread and composer; sets a viewport-aware height
           - .chat-thread: flex child that scrolls when content overflows; min-height:0 required for flex
        */
        .chat-panel {
            display: flex;
            flex-direction: column;
            /* Prefer a full-viewport aware height but keep a sensible max for desktops */
            height: calc(100vh - var(--nav-h) - 120px);
            max-height: 80vh;
        }

        .chat-thread {
            flex: 1 1 auto;
            min-height: 0; /* allow flex children to shrink and enable scrolling */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .chat-panel { height: calc(100vh - var(--nav-h) - 100px); max-height: none; }
        }

        /*تنسيقات اشكال الاسكرول*/
        /* تنسيق Scrollbar للمتصفحات التي تدعم WebKit (Chrome, Safari, Edge) */
::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

::-webkit-scrollbar-track {
    background: #1a1a1a;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #00ffff, #00ffcc);
    border-radius: 10px;
    border: 2px solid #1a1a1a;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #00ffcc, #00ffff);
    box-shadow: 0 0 8px rgba(0, 255, 255, 0.6);
}

::-webkit-scrollbar-corner {
    background: #1a1a1a;
}

/* تنسيق Scrollbar للمتصفحات التي تدعم Firefox */
* {
    scrollbar-width: thin;
    scrollbar-color: #00ffff #1a1a1a;
}

/* تنسيق إضافي للعناصر المختلفة */
html {
    scrollbar-color: #00ffff #1a1a1a;
}

body {
    scrollbar-width: thin;
    scrollbar-color: #00ffff #1a1a1a;
}

/* تأثيرات إضافية */
::-webkit-scrollbar-thumb:active {
    background: linear-gradient(45deg, #00ccff, #00ffaa);
    box-shadow: 0 0 12px rgba(0, 255, 255, 0.8);
}

/* تنسيق للشريط الجانبي إذا كان موجوداً */
.sidebar::-webkit-scrollbar,
aside::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb,
aside::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #00ffff, #00ffaa);
}
        /*تنسيقات اشكال الاسكرول*/
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">
    <script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    @include('partials.flash')
    <style>

                :root {
            /* نظام الألوان الأساسي */
            --primary-bg: #0a192f;
            --secondary-bg: #112240;
            --tertiary-bg: #233554;

            /* الألوان الذهبية المتدرجة */
            --gold-primary: #00ffff;
            --gold-secondary: #0099cc;
            --gold-tertiary: #04bfd8;
            --gold-light: #0796d8;
            --gold-dark: #1f8db8;

            /* ألوان النصوص */
            --text-light: #ccd6f6;
            --text-muted: #aab6d3;
            --text-dark: #1a1a1a;
            --text-white: #ffffff;

            /* ألوان Bootstrap المتوافقة مع الذهبي */
            --bs-primary: var(--gold-primary);
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #079cff;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;

            /* الظلال والتأثيرات */
            --shadow-gold: 0 4px 20px #00ffff;
            --shadow-dark: 0 10px 30px rgba(0, 0, 0, 0.3);
            --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.1);

            /* التحولات والحركات */
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --transition-fast: all 0.2s ease;
            --transition-slow: all 0.6s ease;

            /* الزوايا */
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;

            /* حدود */
            --border-color: rgba(212, 175, 55, 0.2);
        }

        [data-theme="light"] {
            --primary-bg: #f8f9fa;
            --secondary-bg: #ffffff;
            --tertiary-bg: #e9ecef;

            --text-light: #1a1a1a;
            --text-muted: #4a5568;
            --text-dark: #000000;
            --text-white: #1a1a1a;

            --shadow-gold: 0 4px 20px #00ffff;
            --shadow-dark: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.05);

            --border-color: rgba(212, 175, 55, 0.3);
        }
        /* تخصيص NProgress ليتناسب مع تصميمك */
        #nprogress .bar {
            background: linear-gradient(135deg, #00ffff 0%, #0099cc 100%);
        }

        #nprogress .peg {
            box-shadow: 0 0 10px var(--gold-primary), 0 0 5px var(--gold-primary) !important;
        }

        #nprogress .spinner-icon {
            border-top-color: var(--gold-primary) !important;
            border-left-color: var(--gold-primary) !important;
        }

             .page-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(135deg, #00ffff 0%, #0099cc 100%);
            z-index: 10000;
            transition: width 0.3s;
            box-shadow: var(--shadow-gold);
            display: none;
        }


                /* زر العودة للأعلى - إصلاح */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            z-index: 9999;
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
            color: #000;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.25);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s, transform 0.4s;
            font-size: 1.5rem;
        }

        .back-to-top.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(-10px);
        }

        .back-to-top:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
    </style>
        <!-- Early theme init to avoid FOUC -->
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('theme');
                var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                /* ignore */
            }
        })();
    </script>
    <style>
        /* Intro.js RTL + theme tweaks */
        .introjs-overlay { background: rgba(0,0,0,0.65) !important; }
        .introjs-tooltip, .introjs-helperLayer, .introjs-tooltiptext { direction: rtl !important; text-align: right !important; }
        .introjs-tooltip { background: #0f1724 !important; color: #e6eef8 !important; border: 1px solid rgba(255,255,255,0.06) !important; box-shadow: 0 8px 30px rgba(0,0,0,0.6); }
        .introjs-tooltip .introjs-header { display:none; }
        .introjs-nextbutton, .introjs-prevbutton, .introjs-skipbutton, .introjs-donebutton { background: linear-gradient(135deg,#00ffff 0%,#0099cc 100%) !important; color: #000 !important; border:0 !important; border-radius:6px; padding:6px 12px; }

                /* Subtle animated arrows/sparks overlay (investor pages) */
                .bg-anim-layer {
                    position: fixed;
                    inset: 0;
                    z-index: 0; /* under nav (1000) and content (1) */
                    pointer-events: none;
                    overflow: hidden;
                }

                .bg-anim-layer .anim-item {
                    position: absolute;
                    user-select: none;
                    -webkit-user-select: none;
                    will-change: transform, opacity;
                    filter: drop-shadow(0 0 2px rgba(0,0,0,0.05));
                }

                .bg-anim-layer .arrow {
                    color: rgba(125, 211, 252, 0.22); /* sky-300 */
                    text-shadow: 0 0 8px rgba(56, 189, 248, 0.14), 0 0 18px rgba(56,189,248,0.12);
                }

                .bg-anim-layer .spark {
                    color: rgba(34, 197, 94, 0.22); /* emerald-500 */
                    text-shadow: 0 0 6px rgba(16,185,129,0.12), 0 0 12px rgba(16,185,129,0.12);
                }

                @keyframes shimmer {
                    0% { opacity: 0.08; }
                    50% { opacity: 0.28; }
                    100% { opacity: 0.12; }
                }

                @keyframes blink {
                    0% { opacity: 0.06; }
                    50% { opacity: 0.30; }
                    100% { opacity: 0.08; }
                }

                @keyframes floatDrift {
                    0%   { transform: translate(-6px, -6px) rotate(var(--rot, 0deg)); }
                    50%  { transform: translate( 6px,  6px) rotate(var(--rot, 0deg)); }
                    100% { transform: translate(-6px,  6px) rotate(var(--rot, 0deg)); }
                }

                @media (max-width: 768px) {
                    .bg-anim-layer .anim-item { opacity: 0.12 !important; }
                }

                /* Kill legacy per-page digital background blocks so global investor bg shows everywhere */
                .is-investor .digital-background { display: none !important; }
        .introjs-prevbutton { background: transparent !important; color: #cbd5e1 !important; border:1px solid rgba(255,255,255,0.06) !important; }
        .introjs-tooltip .introjs-skipbutton { margin-left: 8px; }
    
                      .amr_almahjari{
                    display: none;
                }
 
  </style>
    <script>
        // Global onboarding tour helpers
        window.startOnboardingTour = function(){
            try {
                if (typeof introJs === 'undefined') {
                    console.warn('Intro.js is not loaded');
                    alert('جولة الإرشاد غير متاحة حالياً. يرجى المحاولة لاحقاً.');
                    return;
                }

                // Collect steps from DOM elements that have data-intro
                var els = Array.prototype.slice.call(document.querySelectorAll('[data-intro]'));
                if (!els.length) {
                    console.info('No onboarding steps found (no elements with data-intro)');
                    return;
                }

                // If elements have data-step, sort by it (numeric), otherwise preserve DOM order
                els.sort(function(a,b){
                    var sa = a.getAttribute('data-step');
                    var sb = b.getAttribute('data-step');
                    if (sa === null && sb === null) return 0;
                    if (sa === null) return 1;
                    if (sb === null) return -1;
                    return (parseInt(sa)||0) - (parseInt(sb)||0);
                });

                var steps = [];
                els.forEach(function(el){
                    var text = el.getAttribute('data-intro');
                    if (!text) return;
                    // ensure element is in the document and visible
                    try {
                        var rect = el.getBoundingClientRect();
                        if (rect.width === 0 && rect.height === 0) {
                            // skip invisible elements
                            return;
                        }
                    } catch(e) {}

                    // Build a step object referencing the element
                    steps.push({
                        element: el,
                        intro: text
                    });
                });

                if (!steps.length) {
                    console.info('No visible onboarding steps found');
                    return;
                }

                var intro = introJs();
                intro.setOptions({
                    steps: steps,
                    nextLabel: 'التالي',
                    prevLabel: 'السابق',
                    doneLabel: 'تم',
                    skipLabel: 'تخطي',
                    showStepNumbers: false,
                });

                intro.oncomplete(function(){
                    try { localStorage.setItem('site_onboarding_shown', '1'); } catch(e){}
                });
                intro.onexit(function(){
                    try { localStorage.setItem('site_onboarding_shown', '1'); } catch(e){}
                });

                console.info('Starting onboarding tour with', steps.length, 'steps');
                intro.start();
            } catch (e) { console.error('Intro error', e); alert('حدث خطأ أثناء بدء الجولة التعليمية'); }
        };

        window.resetAndStartTour = function(){
            try { localStorage.removeItem('site_onboarding_shown'); } catch(e){}
            // small delay to allow removal to persist
            setTimeout(function(){ window.startOnboardingTour(); }, 50);
        };
    </script>
</head>
<body class="rtl {{ (request()->is('admin') || request()->is('admin/*')) ? 'is-admin' : 'is-investor' }}">
    <div id="pageProgress" class="page-progress" aria-hidden="true"></div>

    <!-- Global flash toasts container (present on every page) -->
    <div id="flash-toasts" class="flash-toasts" aria-live="polite" aria-atomic="true"></div>


<nav class="nav main-nav">
  
   <div class="amr_almahjari" style="background-color: rgb(36, 35, 35); border-radius: 5px 0 0 5px; position: fixed; right: 10px; top: 20px; z-index: 9999;">
            <button id="mobile-sidebar-toggle" class="px-2 py-1 rounded-md border border-gray-700 text-gray-200 hover:bg-gray-800" style="" title="القائمة" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu w-5 h-5"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
            </button>
        </div>

    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="فتح القائمة" data-step="9" data-intro="افتح وأغلق القائمة الجانبية من هنا للوصول السريع إلى أقسام الموقع.">☰</button>

    <div class="nav-container">
        <!-- Logo -->
        <div class="nav-brand">
         <div class="sidebar-title">{{ isset($platform_navbar_setting['name']) ? $platform_navbar_setting['name'] : (isset($platform_setting['name']) ? $platform_setting['name'] : (config('app.name') ?? 'عوائد الخليج للاستثمار السعودي')) }}</div>
         <div class="sidebar-subtitle">{{ isset($platform_navbar_setting['description']) ? $platform_navbar_setting['description'] : (isset($platform_setting['description']) ? $platform_setting['description'] : (config('app.description') ?? 'منصة التراخيص الموحدة')) }}</div>
        </div>

        <div class="nav-actions">
            @if(session()->has('user_id'))
            @php $currentUser = \App\Models\User::find(session('user_id')) ?? null; @endphp
            <div class="account-dropdown-wrapper" style="position:relative;display:flex;align-items:center;gap:8px;">
                <!-- Logout button shown next to account -->
                <button id="logoutBtn" type="button" class="icon-btn" title="تسجيل الخروج" aria-label="تسجيل الخروج" style="background:transparent;color:#ff9b9b;border:1px solid rgba(255,155,155,0.08);">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>

                <!-- Account button styled to match chat accent -->
                <button id="accountBtn" type="button" class="icon-btn" title="حسابي" aria-label="حسابي" style="background:linear-gradient(135deg,#00ffff 0%,#0099cc 100%);color:#000;border:0;">
                    <i class="fa-solid fa-user"></i>
                </button>

                <div id="accountMenu" class="hidden" role="menu" aria-labelledby="accountBtn" style="position:absolute;right:0;top:110%;min-width:220px;background:#0f1724;border:1px solid rgba(255,255,255,0.08);box-shadow:0 10px 25px rgba(0,0,0,0.35);border-radius:12px;overflow:hidden;z-index:1003;text-align:right;">
                    <div style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.06);">
                        <div style="font-weight:700;color:#c7f9fb;">{{ $currentUser->full_name ?? $currentUser->name ?? 'مستخدم' }}</div>
                        <div style="font-size:13px;color:#9ca3af;margin-top:4px;">{{ $currentUser->email ?? '' }}</div>
                    </div>
                  <!--
                    <a href="{{ route('investor.profile') }}" style="display:block;padding:10px 12px;color:#e5e7eb;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.03);">ملفّي الشخصي</a>
                  -->
              </div>
            </div>
            <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
            <!-- Chat icon: only visible to logged-in users -->
            @php
                $adminMessagesUnread = 0;
                if (isset($currentUser) && $currentUser) {
                    try {
                        $adminMessagesUnread = $currentUser->unreadNotifications->where('type', App\Notifications\AdminSentMessageNotification::class)->count();
                    } catch (Exception $e) {
                        $adminMessagesUnread = 0;
                    }
                }
            @endphp
            <a href="{{ route('chat') }}" class="icon-btn" title="الدردشة مع المدير" aria-label="الدردشة" style="background:linear-gradient(135deg,#00ffff 0%,#0099cc 100%);color:#000;border:0;position:relative;">
                <i class="fa-solid fa-comments" style="color: black;"></i>
                @if($adminMessagesUnread > 0)
                    <span style="position:absolute;top:-6px;right:-6px;background:#e11d48;color:#fff;border-radius:999px;padding:3px 6px;font-size:11px;font-weight:700;">{{ $adminMessagesUnread }}</span>
                @endif
            </a>
            @else
            <a href="{{ route('auth.login.form') }}" class="icon-btn" title="تسجيل حساب / تسجيل الدخول" aria-label="تسجيل حساب / تسجيل الدخول" data-step="11" data-intro="اضغط هنا لتسجيل حساب جديد أو لتسجيل الدخول إلى حسابك الحالي.">
                <i class="fa-solid fa-user-plus"></i>
            </a>
            @endif
            {{-- <a href="{{ route('chat') }}" class="icon-btn" title="الدردشة مع المدير" aria-label="الدردشة">
                <i class="fa-solid fa-comments"></i>
            </a> --}}
            <div class="lang-dropdown" style="position:relative;">
<!--                <button id="langToggle" class="language-toggle_a" aria-haspopup="true" aria-expanded="false" style="
                    background: linear-gradient(135deg, #00ffff 0%, #0099cc 100%);
                    border: none;
                    color: #000;
                    font-weight: 600;
                    padding: 10px 24px;
                    border-radius: 25px;
                    cursor: pointer;
                    font-size: 14px;">
                    <span id="langToggleLabel">AR</span>
                    <i class="fa-solid fa-chevron-down" style="margin-right:8px;font-size:12px;color:#0a0a0a;"></i>
                </button>
-->
                <div id="langMenu" class="hidden" role="menu" aria-labelledby="langToggle" style="
                    position:absolute;left:0;top:110%;min-width:180px;
                    background:#0f1724;border:1px solid rgba(255,255,255,0.08);
                    box-shadow:0 10px 25px rgba(0,0,0,0.35);
                    border-radius:12px;overflow:hidden;z-index:1002;">
                    <button data-lang="ar" role="menuitem" style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:right;padding:10px 12px;color:#e5e7eb;background:transparent;border:0;cursor:pointer;">
                        <span>العربية</span>
                        <span style="opacity:.7;font-size:12px;">AR</span>
                    </button>
                    <div style="height:1px;background:rgba(255,255,255,0.06);"></div>
                    <button data-lang="en" role="menuitem" style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:right;padding:10px 12px;color:#e5e7eb;background:transparent;border:0;cursor:pointer;">
                        <span>English</span>
                        <span style="opacity:.7;font-size:12px;">EN</span>
                    </button>
                </div>
            </div>
        </div>


    </div>

</nav>

    @include('layouts.navigation')
    @if(!(request()->is('admin') || request()->is('admin/*')))
    <div id="bgAnimLayer" class="bg-anim-layer" aria-hidden="true"></div>
    @endif
    <script>
        // Dynamically set --nav-h to the current navbar height to keep a small, consistent gap
        (function(){
            function setNavH(){
                try {
                    var nav = document.querySelector('.main-nav');
                    if(!nav) return;
                    var h = nav.offsetHeight || 68;
                    document.documentElement.style.setProperty('--nav-h', h + 'px');
                } catch(e){}
            }
            document.addEventListener('DOMContentLoaded', setNavH);
            window.addEventListener('load', setNavH);
            window.addEventListener('resize', function(){ setTimeout(setNavH, 50); });
        })();
    </script>
    <script>
        // Lightweight background animation: ensure it exists on all investor pages
        (function initBgAnim(){
            function rand(min, max){ return Math.random() * (max - min) + min; }
            function pick(arr){ return arr[Math.floor(Math.random() * arr.length)]; }

            function ensureLayer(){
                if (!document.body.classList.contains('is-investor')) return null;
                var layer = document.getElementById('bgAnimLayer');
                if (!layer) {
                    layer = document.createElement('div');
                    layer.id = 'bgAnimLayer';
                    layer.className = 'bg-anim-layer';
                    var anchor = document.querySelector('.app-content') || document.body.firstChild;
                    document.body.insertBefore(layer, anchor);
                }
                return layer;
            }

            function populate(layer){
                if (!layer) return;
                if (layer.childElementCount > 0) return; // already populated

                var isDesktop = window.innerWidth >= 1024;
                var arrowsCount = isDesktop ? 18 : 10;
                var sparksCount = isDesktop ? 26 : 14;
                var arrowChars = ['➤','➔','➣','➙','➛','›'];
                var sparkChars = ['✦','✧','•','*','﹡','⋆','<'];

                for (var i = 0; i < arrowsCount; i++) {
                    var el = document.createElement('span');
                    el.className = 'anim-item arrow';
                    el.textContent = pick(arrowChars);
                    el.style.top = rand(6, 88) + 'vh';
                    el.style.left = rand(4, 96) + 'vw';
                    el.style.fontSize = rand(10, 22) + 'px';
                    el.style.setProperty('--rot', rand(-35, 35).toFixed(1) + 'deg');
                    el.style.opacity = (rand(0.08, 0.18)).toFixed(2);
                    var delay = rand(-12, 0).toFixed(1) + 's';
                    el.style.animation = 'shimmer ' + rand(6, 14).toFixed(1) + 's ease-in-out ' + delay + ' infinite alternate, '
                                       + 'floatDrift ' + rand(12, 20).toFixed(1) + 's ease-in-out ' + delay + ' infinite';
                    layer.appendChild(el);
                }

                for (var j = 0; j < sparksCount; j++) {
                    var sp = document.createElement('span');
                    sp.className = 'anim-item spark';
                    sp.textContent = pick(sparkChars);
                    sp.style.top = rand(2, 96) + 'vh';
                    sp.style.left = rand(2, 98) + 'vw';
                    sp.style.fontSize = rand(8, 16) + 'px';
                    sp.style.opacity = (rand(0.06, 0.22)).toFixed(2);
                    var sDelay = rand(-4, 0).toFixed(1) + 's';
                    sp.style.animation = 'blink ' + rand(2, 5).toFixed(1) + 's ease-in-out ' + sDelay + ' infinite alternate';
                    layer.appendChild(sp);
                }
            }

            function init(){ populate(ensureLayer()); }

            // Run on load and when restoring from bfcache
            document.addEventListener('DOMContentLoaded', init);
            window.addEventListener('pageshow', init);
        })();
    </script>
    <div class="app-content">
        @yield('content')

         @include('layouts.footer')
    </div>
    <button id="backToTop" class="back-to-top" style="color: black;" type="button" aria-label="العودة للأعلى"><i
            class="fas fa-arrow-up"></i></button>




    {{-- Page-level scripts injected via @push('scripts') from child views --}}
    @stack('scripts')

    <!-- Global flash-toasts renderer: reads common session keys and shows toasts -->
    <script>
        (function(){
            try{
                var container = document.getElementById('flash-toasts');
                if (!container) return;

                var flashes = {!! json_encode(session()->only(['success','error','warning','info','status','message'])) !!};
                Object.keys(flashes || {}).forEach(function(k){
                    var v = flashes[k]; if (!v) return;
                    var toast = document.createElement('div');
                    toast.className = 'ft-toast ft-' + (k || 'info');
                    var body = document.createElement('div'); body.className = 'ft-body'; body.textContent = String(v);
                    var close = document.createElement('button'); close.className = 'ft-close'; close.setAttribute('aria-label','اغلاق'); close.innerHTML='×';
                    close.addEventListener('click', function(){ try{ toast.remove(); }catch(e){} });
                    toast.appendChild(body); toast.appendChild(close); container.appendChild(toast);
                    setTimeout(function(){ toast.classList.add('visible'); }, 20);
                    setTimeout(function(){ try{ toast.classList.remove('visible'); setTimeout(function(){ try{ toast.remove(); }catch(e){} },220); }catch(e){} }, 5000);
                });
            }catch(e){ console.warn('flash-toasts error', e); }
        })();
    </script>

    <script>

           document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100
                });
            }

            // Enhanced Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            const themeColorMeta = document.getElementById('themeColorMeta');

            function updateTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);

                // Update theme color meta tag
                if (themeColorMeta) {
                    themeColorMeta.content = theme === 'dark' ? '#0a192f' : '#f8f9fa';
                }

                // Update toggle icon
                if (themeToggle) {
                    themeToggle.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' :
                        '<i class="fas fa-moon"></i>';
                }
            }

            if (themeToggle) {
                // Set initial theme
                let currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                updateTheme(currentTheme);

                themeToggle.addEventListener('click', function() {
                    const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ?
                        'light' : 'dark';
                    updateTheme(newTheme);
                });
            }

            // إصلاح زر العودة للأعلى
            const backToTop = document.getElementById('backToTop');
            if (backToTop) {
                window.addEventListener('scroll', function() {
                    backToTop.classList.toggle('show', window.scrollY > 300);
                });

                backToTop.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }


            // Register Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function() {});
                });
            }

            // Enhanced Install Prompt
            let deferredPrompt;
            const installBtn = document.getElementById('installApp');

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                if (installBtn) {
                    installBtn.classList.remove('d-none');
                }
            });

            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;

                    deferredPrompt.prompt();
                    const {
                        outcome
                    } = await deferredPrompt.userChoice;

                    if (outcome === 'accepted') {
                        installBtn.classList.add('d-none');
                    }
                    deferredPrompt = null;
                });
            }

            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Chat moved to dedicated /chat page
        });


        (function() {
            var body = document.body;
            var sidebar = document.getElementById('app-sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');
            var toggleBtn = document.getElementById('sidebar-toggle');
            var closeBtn = document.getElementById('sidebar-close');

            function openSidebar() {
                sidebar.classList.add('open');
                backdrop.classList.add('show');
                dispatchSidebarToggle();
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                backdrop.classList.remove('show');
                dispatchSidebarToggle();
            }

            function dockSidebar() {
                body.classList.add('sidebar-docked');
                dispatchSidebarToggle();
            }

            function undockSidebar() {
                body.classList.remove('sidebar-docked');
                dispatchSidebarToggle();
            }

            function dispatchSidebarToggle() {
                try {
                    window.dispatchEvent(new CustomEvent('sidebar:toggle'));
                } catch (e) {}
            }

            // Initialize docked mode on desktop
            function syncDock() {
                if (window.innerWidth >= 1024) {
                    dockSidebar();
                } else {
                    undockSidebar();
                    closeSidebar();
                }
            }
            syncDock();
            window.addEventListener('resize', syncDock);

            // Toggle actions
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    // if docked on desktop, toggle a collapsed state instead of relying on open class
                    if (body.classList.contains('sidebar-docked')) {
                        if (body.classList.contains('sidebar-collapsed')) {
                            body.classList.remove('sidebar-collapsed');
                        } else {
                            body.classList.add('sidebar-collapsed');
                        }
                        dispatchSidebarToggle();
                    } else {
                        // mobile behavior: open/close overlay sidebar
                        if (sidebar.classList.contains('open')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }
                });
            }
            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }
            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            // Close on navigation click (mobile)
            document.addEventListener('click', function(e) {
                var a = e.target.closest('.sidebar-nav a');
                if (a && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            // Language dropdown logic
            try {
                var langToggle = document.getElementById('langToggle');
                var langMenu = document.getElementById('langMenu');
                var langLabel = document.getElementById('langToggleLabel');

                function applyLang(lang) {
                    // Persist
                    localStorage.setItem('site-lang', lang);
                    // Update html attributes
                    var html = document.documentElement;
                    if (lang === 'ar') {
                        html.setAttribute('lang', 'ar');
                        html.setAttribute('dir', 'rtl');
                        body.classList.add('rtl');
                        langLabel.textContent = 'AR';
                    } else {
                        html.setAttribute('lang', 'en');
                        html.setAttribute('dir', 'ltr');
                        body.classList.remove('rtl');
                        langLabel.textContent = 'EN';
                    }
                }

                function getSavedLang() {
                    return localStorage.getItem('site-lang');
                }

                // Init saved language
                var saved = getSavedLang();
                if (saved) {
                    applyLang(saved);
                }

                if (langToggle && langMenu) {
                    langToggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var isHidden = langMenu.classList.contains('hidden');
                        langMenu.classList.toggle('hidden', !isHidden);
                        langToggle.setAttribute('aria-expanded', String(isHidden));
                    });

                    langMenu.addEventListener('click', function(e) {
                        var btn = e.target.closest('button[data-lang]');
                        if (!btn) return;
                        var lang = btn.getAttribute('data-lang');
                        applyLang(lang);
                        langMenu.classList.add('hidden');
                        langToggle.setAttribute('aria-expanded', 'false');
                    });

                    // Close on outside click / Esc
                    document.addEventListener('click', function(e) {
                        if (!e.target.closest('.lang-dropdown')) {
                            langMenu.classList.add('hidden');
                            langToggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            langMenu.classList.add('hidden');
                            langToggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            } catch (e) { /* ignore */ }
        })();
    </script>
        <script>
        // Mutual menu behavior: clicking one toggle hides the other's menu; clicking anywhere hides both.
        (function(){
            try {
                var accountBtn = document.getElementById('accountBtn');
                var accountMenu = document.getElementById('accountMenu');
                var langToggle = document.getElementById('langToggle');
                var langMenu = document.getElementById('langMenu');

                // When opening language toggle, ensure account menu is closed
                if (langToggle && accountMenu) {
                    langToggle.addEventListener('click', function(){
                        try {
                            if (accountMenu && !accountMenu.classList.contains('hidden')) {
                                accountMenu.classList.add('hidden');
                                if (accountBtn) accountBtn.setAttribute('aria-expanded', 'false');
                            }
                        } catch(e){}
                    });
                }

                // When opening account toggle, ensure language menu is closed
                if (accountBtn && langMenu) {
                    accountBtn.addEventListener('click', function(){
                        try {
                            if (langMenu && !langMenu.classList.contains('hidden')) {
                                langMenu.classList.add('hidden');
                                if (langToggle) langToggle.setAttribute('aria-expanded', 'false');
                            }
                        } catch(e){}
                    });
                }

                // Global click: if click is outside account/lang wrappers, close both menus
                document.addEventListener('click', function(e){
                    try {
                        var inAccount = !!e.target.closest('.account-dropdown-wrapper');
                        var inLang = !!e.target.closest('.lang-dropdown');

                        if (!inAccount) {
                            if (accountMenu) accountMenu.classList.add('hidden');
                            if (accountBtn) accountBtn.setAttribute('aria-expanded', 'false');
                        }
                        if (!inLang) {
                            if (langMenu) langMenu.classList.add('hidden');
                            if (langToggle) langToggle.setAttribute('aria-expanded', 'false');
                        }
                    } catch(e){}
                });
            } catch(e) { /* ignore */ }
        })();
    </script>
    <script>
        // استخدام NProgress
        document.addEventListener('DOMContentLoaded', function() {
            // بدء التقدم
            NProgress.start();

            // إكمال التقدم عند تحميل الصفحة
            window.addEventListener('load', function() {
                NProgress.done();
            });

            // إعادة التشغيل عند النقر على الروابط
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href &&
                    !link.href.startsWith('#') &&
                    !link.href.startsWith('javascript:') &&
                    !link.target === '_blank') {

                    NProgress.start();
                }
            });
        });
    </script>
    <script>
        // Account menu + logout confirmation handler
        (function(){
            try {
                var logoutForm = document.getElementById('logoutForm');

                // Logout button inside account menu
                var logoutBtn = document.getElementById('logoutBtn');
                if (logoutBtn && logoutForm) {
                    logoutBtn.addEventListener('click', function(e){
                        e.preventDefault();
                        var ok = confirm('هل أنت متأكد من تسجيل الخروج؟');
                        if (ok) {
                            logoutForm.submit();
                        }
                    });
                }

                // Account menu toggle
                var accountBtn = document.getElementById('accountBtn');
                var accountMenu = document.getElementById('accountMenu');
                if (accountBtn && accountMenu) {
                    accountBtn.addEventListener('click', function(e){
                        e.stopPropagation();
                        var isHidden = accountMenu.classList.contains('hidden');
                        accountMenu.classList.toggle('hidden', !isHidden);
                        accountBtn.setAttribute('aria-expanded', String(isHidden));
                    });

                    // Close on outside click
                    document.addEventListener('click', function(e){
                        if (!e.target.closest('.account-dropdown-wrapper')) {
                            accountMenu.classList.add('hidden');
                            if (accountBtn) accountBtn.setAttribute('aria-expanded', 'false');
                        }
                    });

                    document.addEventListener('keydown', function(e){
                        if (e.key === 'Escape') {
                            accountMenu.classList.add('hidden');
                            if (accountBtn) accountBtn.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            } catch (e) { /* ignore */ }
        })();
    </script>
    {{-- Allow pages to inject scripts --}}
    <script>
        // Global password show/hide toggle for all password inputs across the site.
        (function(){
            function addToggle(inp){
                try {
                    if (!inp || inp.dataset.pwdToggle) return;
                    inp.dataset.pwdToggle = '1';

                    // Ensure we have a positioned wrapper that exactly matches the input box
                    var wrapper = inp.parentNode;
                    var needWrap = false;
                    try {
                        var wStyle = window.getComputedStyle(wrapper);
                        // If wrapper is a label or form-group and not positioned or has strange display, wrap
                        if (wStyle.position === 'static' || wStyle.overflow === 'hidden' || wrapper.tagName.toLowerCase() === 'label') {
                            needWrap = true;
                        }
                    } catch(e){ needWrap = true; }

                    if (needWrap) {
                        var wrap = document.createElement('span');
                        wrap.className = 'pwd-toggle-wrapper';
                        wrap.style.position = 'relative';
                        // preserve block flow — make wrapper an inline-block sized to input width
                        wrap.style.display = 'inline-block';
                        wrap.style.width = inp.offsetWidth ? inp.offsetWidth + 'px' : '100%';
                        // replace input with wrapper->input
                        wrapper.replaceChild(wrap, inp);
                        wrap.appendChild(inp);
                        wrapper = wrap;
                        // ensure input fills wrapper
                        inp.style.width = '100%';
                        inp.style.boxSizing = 'border-box';
                    } else {
                        // ensure wrapper is positioned so absolute button sits correctly
                        if (window.getComputedStyle(wrapper).position === 'static') wrapper.style.position = 'relative';
                    }

                    // create button
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('aria-label', 'إظهار أو إخفاء كلمة المرور');
                    btn.className = 'pwd-toggle-btn';

                    // inline SVG icons (eye / eye-slash) to avoid emoji or monkey icons
                    var eyeSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle></svg>';
                    var eyeSlashSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a20.2 20.2 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 1l22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

                    // default to inline SVG (consistent, no monkey emoji)
                    btn.innerHTML = eyeSvg;

                    // style: place near the input edge and centered vertically
                    btn.style.position = 'absolute';
                    btn.style.top = '50%';
                    btn.style.transform = 'translateY(-50%)';
                    // horizontal placement depends on document direction (RTL -> left edge, LTR -> right edge)
                    if (document.documentElement.getAttribute('dir') === 'rtl' || document.body.classList.contains('rtl')) {
                        btn.style.left = '10px';
                        btn.style.right = 'auto';
                    } else {
                        btn.style.right = '10px';
                        btn.style.left = 'auto';
                    }
                    btn.style.zIndex = '6000';
                    btn.style.background = 'transparent';
                    btn.style.border = 'none';
                    btn.style.cursor = 'pointer';
                    btn.style.color = 'rgba(203,213,225,0.95)';
                    btn.style.fontSize = '18px';
                    btn.style.padding = '6px';
                    btn.style.lineHeight = '1';

                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        if (inp.type === 'password') {
                            inp.type = 'text';
                            btn.innerHTML = eyeSlashSvg;
                        } else {
                            inp.type = 'password';
                            btn.innerHTML = eyeSvg;
                        }
                        inp.focus();
                    });

                    // increase input padding on the side where the icon will appear to avoid overlap
                    var style = window.getComputedStyle(inp);
                    var padLeft = parseInt(style.paddingLeft) || 12;
                    var padRight = parseInt(style.paddingRight) || 12;
                    if (document.documentElement.getAttribute('dir') === 'rtl' || document.body.classList.contains('rtl')) {
                        inp.style.paddingLeft = (padLeft + 44) + 'px';
                    } else {
                        inp.style.paddingRight = (padRight + 44) + 'px';
                    }

                    wrapper.appendChild(btn);
                } catch (err) { console.error('pwd-toggle error', err); }
            }

            function scanAndAttach(root){
                root = root || document;
                var inputs = root.querySelectorAll('input[type="password"]');
                inputs.forEach(addToggle);
            }

            document.addEventListener('DOMContentLoaded', function(){ scanAndAttach(document); });

            // Also observe DOM changes to attach toggles for dynamically added inputs
            var obs = new MutationObserver(function(muts){
                muts.forEach(function(m){
                    if (!m.addedNodes) return;
                    m.addedNodes.forEach(function(node){
                        try {
                            if (node.nodeType !== 1) return;
                            if (node.matches && node.matches('input[type="password"]')) addToggle(node);
                            else if (node.querySelectorAll) scanAndAttach(node);
                        } catch (e){}
                    });
                });
            });
            obs.observe(document.documentElement || document.body, { childList: true, subtree: true });
        })();
    </script>
    @yield('scripts')
</body>

</html>
