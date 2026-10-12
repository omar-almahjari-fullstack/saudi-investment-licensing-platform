

        <!-- Backdrop for mobile -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>




<aside class="sidebar" id="app-sidebar" data-step="10" data-intro="هذه هي القائمة الجانبية. تحتوي على روابط تعمل مباشرة — اضغط أي رابط هنا للانتقال فوراً إلى الصفحة المقابلة والتصفح داخل المنصة.">


  <nav class="sidebar-nav">
    @php
      $defaultNav = [
        ["path" => "/", "label" => "الصفحة الرئيسية", "icon" => "🏠"],
        ["path" => "/investor/dashboard", "label" => "لوحة تحكم المستثمر", "icon" => "🧾"],
        ["path" => "/license", "label" => "طلب ترخيص تجاري", "icon" => "📄"],
        ["path" => "/register-investor", "label" => "تسجيل مستثمر جديد", "icon" => "👤"],
        ["path" => "/investment-levels", "label" => "مركز مستويات الاستثمار", "icon" => "📊"],
        ["path" => "/small-investment", "label" => "بوابة الاستثمار الصغيرة", "icon" => "💰"],
        ["path" => "/large-investment", "label" => "مركز استثماري كبير", "icon" => "🏦"],
        ["path" => "/dashboard", "label" => "لوحة معلومات الاستثمار", "icon" => "📈"],
        ["path" => "/documents", "label" => "تحميل المستندات", "icon" => "📄"],
        ["path" => "/market-insights", "label" => "مركز رؤى السوق", "icon" => "📊"],
        ["path" => "/success-stories", "label" => "قصص النجاح", "icon" => "🏆"],
        ["path" => "/trust-compliance", "label" => "مركز الثقة والامتثال", "icon" => "🛡️"]
      ];
      try {
        $records = \App\Models\NavigationItem::where('active', true)->orderBy('order')->get(['path','label','icon']);
        $navigationItems = $records->isNotEmpty() ? $records->map(function($r){ return ['path'=>$r->path, 'label'=>$r->label, 'icon'=>$r->icon ?? '']; })->toArray() : $defaultNav;
      } catch (\Throwable $e) {
        $navigationItems = $defaultNav;
      }
    @endphp

    @foreach ($navigationItems as $item)
      @php
        $origPath = $item['path'] ?? '/';
        $href = $origPath;
        $dataAttrs = '';
        if (isset($item['path']) && $item['path'] === '/register-investor') {
          $isAuth = \Illuminate\Support\Facades\Auth::check() || session('user_id');
          $dataAttrs = 'data-auth="' . ($isAuth ? '1' : '0') . '" data-intended="/register-investor" data-role="investor"';
          if (!$isAuth) {
            $href = route('auth.login.form', ['intended' => '/register-investor']);
          }
        }
      @endphp
  <a href="{{ $href }}" {!! $dataAttrs !!} @if($loop->first) data-step="10" data-intro="مثال: هذا رابط فعّال — اضغط عليه الآن لتجربة الانتقال داخل الموقع." @endif class="flex items-center w-full p-3 rounded-lg transition-colors text-right {{ request()->is(ltrim($origPath, '/')) ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-cyan-400' }}">
        <span class="text-xl ml-3">{{ $item['icon'] }}</span>
        <span class="flex-1">{{ $item['label'] }}</span>
        @if (request()->is(ltrim($origPath, '/')))
          <span class="bg-cyan-500 text-black text-xs px-2 py-1 rounded"></span>
        @endif
      </a>
    @endforeach
  </nav>

  <div class="sidebar-footer-space"></div>
</aside>

<style>
    .nav-brand{
        margin-right: 35px;
    }
    .main-nav{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(15, 23, 36, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 12px 24px;
    width: 100%;
    box-sizing: border-box;
    min-height: 56px; /* Keep navbar height stable on small screens */
    z-index: 1201; /* sit above mobile backdrops */
  }
    .nav-container{
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1400px;
        margin: 0 auto;
        }

    .nav-menu{
            display: flex;
            align-items: center;
            gap: 24px;
            }

            .nav-link{
                color: #d1d5db;
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 8px;
                transition: all 0.3s ease;
                }

    /* Responsive Navigation */
    @media (max-width: 768px) {
        .nav-menu {
            display: none !important;
        }

        .nav-brand a {
            font-size: 18px !important;
        }

        .nav-actions .btn-primary {
            padding: 8px 16px !important;
            font-size: 12px !important;
        }

    /* Mobile: show title and subtitle stacked, fully visible */
    .main-nav { height: auto !important; padding: 8px 12px; align-items: flex-start; }
    .nav-container { align-items: flex-start; }
    .main-nav .nav-brand { max-width: 70vw; overflow: visible; }
    .main-nav .nav-brand .sidebar-title {
      font-size: 14px; /* fit better on small screens */
      white-space: nowrap; /* keep on one line */
      overflow: visible;
      text-overflow: clip;
      display: block;
      line-height: 1.2;
    }
    .main-nav .nav-brand .sidebar-subtitle {
      display: block;
      font-size: 12px;
      white-space: nowrap; /* keep on one line under title */
      overflow: visible;
      line-height: 1.2;
      margin-top: 2px;
      color: #9ca3af;
    }
  }
</style>

<style>
  /* Remove icon backgrounds on small screens: show simple icons (no filled bubble) */
  @media (max-width: 768px) {
    /* Make action icons flat and match the navbar look */
    .nav-actions .icon-btn {
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      color: var(--gold-primary) !important;
    }

    /* Specific overrides for account/logout/chat icons so they don't carry a gradient/border on mobile */
    #accountBtn, #logoutBtn, .nav-actions a.icon-btn {
      background: transparent !important;
      border: none !important;
      color: var(--gold-primary) !important;
    }

    /* Language toggle: show as plain text/icon on phones */
    #langToggle {
      background: transparent !important;
      border: none !important;
      padding: 6px 8px !important;
      color: var(--gold-primary) !important;
      box-shadow: none !important;
    }

    /* Reduce icon size slightly so it fits well with no background and shrink button size */
    .nav-actions .icon-btn {
      width: 34px !important;
      height: 34px !important;
      padding: 0 !important;
    }
    .nav-actions .icon-btn i, .nav-actions .icon-btn svg {
      font-size: 16px !important;
    }

    /* Make the chevron in the language toggle match the icon color */
    #langToggle i.fa-chevron-down {
      color: var(--gold-primary) !important;
    }

    /* Ensure chat icon specifically uses cyan on mobile */
    .nav-actions a.icon-btn i.fa-comments, .nav-actions a.icon-btn .fa-comments {
      color: var(--gold-primary) !important;
    }
  }
</style>

<style>
  /* Make logout icon red globally (overrides inline colors) */
  #logoutBtn i {
    color: #dc3545 !important;
  }
</style>

<style>
  /* Tight mobile constraints: keep the navbar fixed, prevent overflow, and preserve icons on very small screens */
  @media (max-width: 480px) {
    .main-nav {
      padding: 10px 10px !important;
      min-height: 56px !important; /* ensure stable tappable area */
      height: 66px !important; /* lock height so it doesn't collapse */
      align-items: center !important;
      /* allow dropdowns to overflow below the navbar instead of being clipped */
      overflow: visible !important;
    }

    .nav-container {
      max-width: 100% !important;
      width: 100% !important;
      padding: 0 8px !important;
      gap: 8px !important;
      align-items: center !important;
      flex-wrap: nowrap !important; /* prevent wrapping that increases height */
      overflow: visible !important;
    }

    .nav-brand {
      min-width: 0 !important;
      max-width: calc(100% - 140px) !important; /* leave room for action icons */
      overflow: hidden !important;
    }

    .main-nav .nav-brand .sidebar-title,
    .main-nav .nav-brand .sidebar-subtitle {
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }

    /* Keep action icons visible and tappable without shrinking the navbar */
    .nav-actions {
      display: flex !important;
      gap: 8px !important;
      align-items: center !important;
      flex-shrink: 0 !important;
    }

    .nav-actions .icon-btn {
      min-width: 44px !important;
      min-height: 44px !important;
      width: auto !important;
      height: auto !important;
      padding: 6px !important;
      border-radius: 8px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      flex-shrink: 0 !important;
    }

    /* Ensure menus appear above overlays when opened */
    #accountMenu, #langMenu {
      z-index: 1202 !important;
    }
  }
</style>
