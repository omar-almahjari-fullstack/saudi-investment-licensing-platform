@extends('layouts.app')

@section('title', 'لوحة تحكم المدير')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Ensure jQuery is available for existing dashboard scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        // Fallback log if jQuery failed (very rare)
        if (typeof window.jQuery === 'undefined') {
            console.warn('jQuery لم تُحمّل، سيتم الإعتماد على Vanilla للعرض.');
        }
    </script>
    <style>
        /* glass-card effect to match React "glass" look */
        .glass-card {
            backdrop-filter: blur(6px);
            background: rgba(17, 24, 39, 0.45);
            border-radius: 0.5rem;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        /* Hide global app layout sidebar elements inside admin dashboard */
        .sidebar,
        .sidebar-toggle,
        .sidebar-backdrop {
            display: none !important;
        }

        /* neutralize layout's extra right padding when sidebar-docked is set */
        body.sidebar-docked .app-content {
            padding-right: 24px !important;
        }

        /* Platform-colored checkbox (exclusive brand style) */
        :root {
            --brand-100: #dbeafe; /* blue-100 */
            --brand-300: #93c5fd; /* blue-300 */
            --brand-400: #60a5fa; /* blue-400 */
            --brand-500: #3b82f6; /* blue-500 */
            --brand-600: #2563eb; /* blue-600 */
        }

        input.brand-chk {
            -webkit-appearance: none;
            appearance: none;
            display: inline-grid;
            place-content: center;
            width: 1.1rem;
            height: 1.1rem;
            margin-inline-start: 0.25rem; /* aligns with existing mr-2 in RTL */
            background:
                linear-gradient(180deg, rgba(30, 41, 59, 0.85), rgba(15, 23, 42, 0.75));
            border: 1.5px solid rgba(148, 163, 184, 0.45);
            border-radius: 0.375rem; /* md */
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.35);
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease, transform 60ms ease;
            cursor: pointer;
        }
        input.brand-chk:hover { border-color: var(--brand-400); }
        input.brand-chk:active { transform: scale(0.96); }
        input.brand-chk:focus-visible { outline: 2px solid rgba(59, 130, 246, 0.6); outline-offset: 2px; }
        input.brand-chk:disabled { opacity: 0.6; cursor: not-allowed; }

        input.brand-chk::before {
            content: "";
            width: 0.62rem;
            height: 0.62rem;
            transform: scale(0);
            transition: transform 120ms ease-out;
            /* checkmark */
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 20%, 80% 0, 43% 62%);
            background: white;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.15);
        }
        input.brand-chk:checked {
            background:
                linear-gradient(180deg, var(--brand-500), var(--brand-600));
            border-color: var(--brand-400);
        }
        input.brand-chk:checked::before { transform: scale(1); }

         @media (max-width: 768px) {
  .admin-sidebar {
    display: none;
  }


        }
        /* Admin full-bleed main content (match header width and be responsive) */
        .admin-fullwidth .container {
            max-width: 100% !important;
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        /* Ensure any constrained wrappers inside admin become fluid on small screens */
        @media (max-width: 768px) {
            .admin-fullwidth .container,
            .admin-fullwidth .glass-card,
            .admin-fullwidth .card {
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
        }
                /* Make the admin "إدارة النظام الشاملة" content take the full dashboard width
                     Hide the local admin sidebar and expand the main content to span all grid columns
                     This affects large screens (desktop) where the grid uses 12 columns. */
                @media (min-width: 1024px) {
                        #admin-sidebar { display: none !important; }
                        /* the content section will expand to full width of the 12-column grid */
                        #admin-main-content { grid-column: 1 / -1 !important; }
                }

        .amr_almahjari{
          display:block;
        }
      
        
    </style>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 admin-fullwidth">

   

        <script>
            (function(){
                try {
                    var toggle = document.getElementById('mobile-sidebar-toggle');
                    var sidebar = document.getElementById('admin-sidebar');
                    if(!toggle) return;

                    // Toggle sidebar visibility on each click. Works on mobile where sidebar has 'hidden'.
                    toggle.addEventListener('click', function(e){
                        try {
                            if(!sidebar) return;
                            // toggle hidden class
                            if(sidebar.classList.contains('hidden')){
                                sidebar.classList.remove('hidden');
                                toggle.setAttribute('aria-expanded','true');
                            } else {
                                sidebar.classList.add('hidden');
                                toggle.setAttribute('aria-expanded','false');
                            }
                        } catch(err){ console.error('sidebar toggle error', err); }
                    });
                } catch(err){ console.error('mobile sidebar init error', err); }
            })();
        </script>
             
        <!-- Header -->
    <header class="mt-0 bg-black/20 backdrop-blur-sm border-b border-white/10">
            <div class="container mx-auto px-4 md:px-6 py-4">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield" class="w-6 h-6 text-white"></i>
                        </div>

                        <div class="min-w-0">
                            <h1 class="text-xl font-bold text-white truncate">لوحة تحكم المدير</h1>
                            <h1 class="text-base md:text-[20px] text-white leading-tight break-words overflow-hidden mb-2 glow-text">{{ isset($platform_setting['name']) ? $platform_setting['name'] : (config('app.name') ?? 'عوائد الخليج للاستثمار السعودي') }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 flex-shrink-0 mt-3 md:mt-0">
                        <div class="text-right">
                            <p id="admin-fullname" class="text-white font-semibold">
                                {{ auth()->check() ? auth()->user()->full_name : 'مدير النظام' }}</p>
                            <p class="text-gray-400 text-sm">مدير النظام</p>
                        </div>
                        <a href="{{ route('admin.logout') }}"
                            class="border border-red-500/50 text-red-400 hover:bg-red-500/10 px-3 py-2 rounded-md text-sm flex items-center">
                            <i data-lucide="log-out" class="w-4 h-4 ml-2"></i>
                            تسجيل خروج
                        </a>
                    </div>
                </div>
            </div>


                         <!-- Modal: Investment Reject (admin) -->
                        <div id="investment-reject-modal" class="hidden fixed inset-0 z-60 items-center justify-center bg-black/60" style="z-index:99999;">
                            <div class="bg-[#0b1220] border border-gray-700 rounded-lg shadow-xl w-[520px] max-w-[95%] p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-white font-semibold">رفض الاستثمار</h4>
                                    <button id="invreject-close" class="text-gray-300">✕</button>
                                </div>
                                <div class="text-sm text-gray-300 space-y-3">
                                    <div>
                                        <div class="text-gray-400">المعرف:</div>
                                        <div id="invreject-id" class="text-white">—</div>
                                    </div>
                                    <div>
                                        <label for="invreject-reason" class="block text-gray-300 mb-1">سبب الرفض (اختياري)</label>
                                        <textarea id="invreject-reason" rows="4" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="اكتب سبب الرفض هنا"></textarea>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        سيتم إرسال هذا السبب كملاحظة داخل النظام فقط. لن يتم تنبيه المستثمر تلقائيًا إلا إذا قمت بإرسال تذكير أو رسالة منفصلة.
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-end gap-2">
                                    <button id="invreject-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إلغاء</button>
                                    <button id="invreject-confirm" class="px-3 py-2 rounded-md bg-red-600 text-white hover:bg-red-500">تأكيد الرفض</button>
                                </div>
                            </div>
                        </div>
        </header>

        <div class="container mx-auto px-4 md:px-6 py-6 md:py-8">
            <!-- Loading Overlay -->
            <div id="loading-overlay" class="fixed inset-0 hidden items-center justify-center bg-slate-900/60 z-50">
                <div class="text-white text-xl">جاري تحميل لوحة التحكم...</div>
            </div>

            <!-- Stats Overview (enhanced to mirror comprehensive dashboard) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="glass-card border-blue-500/20">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">إجمالي المستخدمين</p>
                                <p id="stat-users" class="text-2xl font-bold text-white">0</p>
                                <p class="text-green-400 text-xs">+<span id="stat-users-today">0</span> اليوم</p>
                            </div>

                            <!-- License payment reminder removed per request -->
                            <i data-lucide="users" class="w-8 h-8 text-blue-400"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card border-green-500/20">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">الاستثمارات النشطة</p>
                                <p id="stat-investments-active" class="text-2xl font-bold text-white">0</p>
                                <p class="text-yellow-400 text-xs"><span id="stat-investments-pending">0</span> معلق</p>
                            </div>
                            <i data-lucide="trending-up" class="w-8 h-8 text-green-400"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card border-purple-500/20">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">التراخيص المعتمدة</p>
                                <p id="stat-licenses-approved" class="text-2xl font-bold text-white">0</p>
                                <p class="text-yellow-400 text-xs"><span id="stat-licenses-pending">0</span> معلق</p>
                            </div>
                            <i data-lucide="file-text" class="w-8 h-8 text-purple-400"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card border-orange-500/20">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">حجم الاستثمار</p>
                                <p id="stat-amount" class="text-2xl font-bold text-white">0 ريال</p>
                                <p class="text-gray-400 text-xs">ريال سعودي</p>
                            </div>
                            <i data-lucide="dollar-sign" class="w-8 h-8 text-orange-400"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card border-cyan-500/20">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">معدل النجاح</p>
                                <p id="stat-success-rate" class="text-2xl font-bold text-white">98%</p>
                                <p class="text-green-400 text-xs">ممتاز</p>
                            </div>
                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="glass-card">
                <div class="p-6 border-b border-gray-700/50 flex items-center justify-between">
                    <h2 class="text-white text-xl">إدارة النظام الشاملة</h2>
                    <div class="flex items-center gap-2">
                      
                        <i data-lucide="monitor" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Tabs Layout: Sidebar + Content -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Mobile Tabs Selector -->
                        <div class="lg:col-span-12 block lg:hidden">
                            <label for="mobile-tabs" class="sr-only">التبويبات</label>
                            <select id="mobile-tabs"
                                class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2">
                                <option value="overview">نظرة عامة</option>
                                <option value="users">المستخدمين</option>
                                <option value="investors">المستثمرين</option>
                                <option value="investments">الاستثمارات</option>
                                <option value="packages">حزم الاستثمار</option>
                                <option value="navigation">القائمة</option>
                                <option value="settings">إعدادات النظام</option>
                                <!-- Logs option removed -->
                                <option value="reports">التقارير</option>
                                <option value="backups">النسخ الاحتياطية</option>
                                <option value="system-stats">إحصائيات النظام</option>
                                <option value="licenses">التراخيص</option>
                                {{-- <option value="license-requests">إدارة طلبات التراخيص</option> --}}
                                <option value="branding">مظهر المنصة</option>
                                <option value="documents">المستندات</option>
                                <option value="page-market">مركز رؤى السوق</option>
                                <option value="page-success">قصص النجاح</option>
                                <option value="page-trust">مركز الثقة والامتثال</option>
                                <option value="chat">الدردشة</option>
                                <option value="user-permissions">إدارة صلاحيات المستخدمين</option>
                                <option value="reports">التقارير</option>
                                <option value="license-reports">تقارير التراخيص</option>
                            </select>
                        </div>
                        <!-- Local Dashboard Sidebar (tabs) -->
                        <aside id="admin-sidebar" class="hidden md:block lg:col-span-3">
                            <div
                                class="bg-gray-800/50 rounded-md p-2 space-y-2 border border-gray-700/50 max-h-[75vh] overflow-y-auto pr-1">
                                            <button data-tab="navigation" data-active-bg="bg-blue-600"
                                                class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="menu" class="w-4 h-4 ml-2"></i>
                                        القائمة</span>
                                    <span class="text-xs text-white/60">Alt+5</span>
                                </button>
                                <button data-tab="overview" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base bg-blue-600 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="activity"
                                            class="w-4 h-4 ml-2"></i>نظرة عامة لنظام</span>
                                    <span class="text-xs text-white/80">Alt+1</span>
                                </button>
                                <button data-tab="users" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="users"
                                            class="w-4 h-4 ml-2"></i> إدارة المستخدمين</span>
                                    <span class="text-xs text-white/60">Alt+2</span>
                                </button>
                                <button data-tab="investors" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 ml-2"></i> إدارة المستثمرين</span>
                                    <span class="text-xs text-white/60">Alt+I</span>
                                </button>
                                {{-- <button data-tab="license-requests" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-4 py-3 hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="file-signature"
                                            class="w-4 h-4 ml-2"></i> إدارة طلبات التراخيص</span>
                                    <span class="text-xs text-white/60">Alt+R</span>
                                </button> --}}
                                <button data-tab="packages" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="package"
                                            class="w-4 h-4 ml-2"></i> إدارة حزم الاستثمار</span>
                                    <span class="text-xs text-white/60">Alt+4</span>
                                </button>
                                <button data-tab="investments" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="trending-up"
                                            class="w-4 h-4 ml-2"></i> طلبات الاستثمارات</span>
                                    <span class="text-xs text-white/60">Alt+3</span>
                                </button>
                                            <button data-tab="licenses" data-active-bg="bg-blue-600"
                                                class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="file-text"
                                            class="w-4 h-4 ml-2"></i> طلبات التراخيص</span>
                                    <span class="text-xs text-white/60">Alt+9</span>
                                </button>
                                
                                <!-- Logs tab removed as requested -->
                                <button data-tab="reports" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="pie-chart"
                                            class="w-4 h-4 ml-2"></i> التقارير</span>
                                    <span class="text-xs text-white/60"></span>
                                </button>
                                <button data-tab="license-reports" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="file-bar-chart"
                                            class="w-4 h-4 ml-2"></i> تقارير التراخيص</span>
                                    <span class="text-xs text-white/60"></span>
                                </button>
                               
                                
                                <button data-tab="documents" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="file" class="w-4 h-4 ml-2"></i>
                                        المستندات</span>
                                    <span class="text-xs text-white/60">Alt+D</span>
                                </button>
                               
                                <!-- Pages (Investor) Tabs -->
                                <button data-tab="page-market" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="bar-chart-3"
                                            class="w-4 h-4 ml-2"></i> مركز رؤى السوق</span>
                                    <span class="text-xs text-white/60">Alt+M</span>
                                </button>
                                <button data-tab="page-success" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="trophy"
                                            class="w-4 h-4 ml-2"></i> قصص النجاح</span>
                                    <span class="text-xs text-white/60">Alt+S</span>
                                </button>
                                <button data-tab="page-trust" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="shield-check"
                                            class="w-4 h-4 ml-2"></i> مركز الثقة والامتثال</span>
                                    <span class="text-xs text-white/60">Alt+C</span>
                                </button>
                                <button data-tab="user-permissions" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="shield"
                                            class="w-4 h-4 ml-2"></i> إدارة صلاحيات المستخدمين</span>
                                    <span class="text-xs text-white/60">Alt+U</span>
                                </button>
                                <button data-tab="backups" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="hard-drive"
                                            class="w-4 h-4 ml-2"></i> النسخ الاحتياطية</span>
                                    <span class="text-xs text-white/60">Alt+8</span>
                                </button>
                                            <button data-tab="chat" data-active-bg="bg-blue-600"
                                                class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="message-square"
                                            class="w-4 h-4 ml-2"></i> الدردشة</span>
                                    <span class="text-xs text-white/60">Alt+H</span>
                                </button>
                                <button data-tab="branding" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="paintbrush"
                                            class="w-4 h-4 ml-2"></i> مظهر المنصة</span>
                                    <span class="text-xs text-white/60">Alt+B</span>
                                </button>
                                <!-- Sub-tab under Branding: Bank Transfer -->
                                <button data-tab="bank" data-active-bg="bg-blue-600"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/20 rounded-md flex items-center justify-between ml-4">
                                    <span class="flex items-center gap-2"><i data-lucide="credit-card"
                                            class="w-4 h-4 ml-2"></i> إدارة التحويل البنكي</span>
                                    <span class="text-xs text-white/60"> </span>
                                </button>
                                <!-- New tab: System Stats -->
                                <button data-tab="system-stats" data-active-bg="bg-gradient-to-r from-indigo-600 to-cyan-500"
                                    class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-indigo-600/20 rounded-md flex items-center justify-between ml-4">
                                    <span class="flex items-center gap-2"><i data-lucide="bar-chart-3" class="w-4 h-4 ml-2"></i> إحصائيات النظام</span>
                                    <span class="text-xs text-white/60">Alt+0</span>
                                </button>
                                            <button data-tab="settings" data-active-bg="bg-blue-600"
                                                class="tab-trigger w-full text-white px-3 py-2 text-sm md:px-4 md:py-3 md:text-base hover:bg-blue-600/30 rounded-md flex items-center justify-between">
                                    <span class="flex items-center gap-2"><i data-lucide="settings"
                                            class="w-4 h-4 ml-2"></i> إعدادات النظام</span>
                                    <span class="text-xs text-white/60">Alt+6</span>
                                </button>
                            </div>
                        </aside>
                        </aside>

                        <!-- Permission-based tab hiding: remove sidebar buttons, mobile options and tab panels
                             for tabs the current user is not allowed to see. Mapping below maps data-tab -> permission key. -->
                        <script>
                            (function(){
                                try {
                                    // Short-circuit: show all tabs for admin users only
                                    var IS_ADMIN = false;
                                    try {
                                        IS_ADMIN = !!(@json(auth()->check() && ((auth()->user()->role ?? '') === 'admin')));
                                    } catch(_e) {}
                                    if (IS_ADMIN) {
                                        // Do not hide any tab for admins
                                        return;
                                    }

                                    // Map tab names (data-tab / option value / tab-panel id suffix) to required permission keys.
                                    var tabPermissionMap = {
                                        'reports': 'view_reports',
                                        'license-reports': 'view_license_reports',
                                        'backups': 'view_backups',
                                        'users': 'manage_users',
                                        'investors': 'manage_investors',
                                        'investments': 'manage_investments',
                                        'packages': 'manage_packages',
                                        'licenses': 'manage_licenses',
                                        // 'logs' removed
                                        'documents': 'view_documents',
                                        'bank': 'manage_bank',
                                        'system-stats': 'view_system_stats',
                                        'branding': 'manage_branding',
                                        'settings': 'manage_settings',
                                        'user-permissions': 'manage_user_permissions',
                                        'chat': 'view_chat',
                                        'page-market': 'view_market_page',
                                        'page-success': 'view_success_page',
                                        'page-trust': 'view_trust_page',
                                        'navigation': 'view_navigation',
                                    };

                                    function normalizePerms(resp){
                                        if(!resp) return [];
                                        if(Array.isArray(resp)) return resp;
                                        if(resp.permissions && Array.isArray(resp.permissions)) return resp.permissions;
                                        if(resp.data && Array.isArray(resp.data)) return resp.data;
                                        return [];
                                    }

                                    function hideTab(tab){
                                        try{
                                            // remove sidebar button
                                            var btn = document.querySelector('[data-tab="'+tab+'"]');
                                            if(btn && btn.parentNode) btn.parentNode.removeChild(btn);
                                            // remove mobile select option
                                            var opt = document.querySelector('#mobile-tabs option[value="'+tab+'"]');
                                            if(opt && opt.parentNode) opt.parentNode.removeChild(opt);
                                            // remove tab panel (id: tab-<name>)
                                            var panel = document.getElementById('tab-'+tab);
                                            if(panel && panel.parentNode) panel.parentNode.removeChild(panel);
                                        }catch(e){ console.error('hideTab error', tab, e); }
                                    }

                                    // If a global cache of permissions was set by other scripts, reuse it
                                    var existing = window.__myPermissionsCache;

                                    var doHide = function(perms){
                                        var list = normalizePerms(perms);
                                        // make it a set for fast lookup
                                        var set = {};
                                        list.forEach(function(p){ set[p] = true; });

                                        for(var tab in tabPermissionMap){
                                            var need = tabPermissionMap[tab];
                                            if(!set[need]){
                                                hideTab(tab);
                                            }
                                        }
                                    };

                                    if(existing){
                                        doHide(existing);
                                    } else {
                                        // fetch current user's permissions from API endpoint; endpoint returns array or object
                                        fetch('/api/admin/me/permissions', {credentials: 'same-origin'})
                                            .then(function(r){ if(!r.ok) throw new Error('no-perm'); return r.json(); })
                                            .then(function(json){
                                                var perms = normalizePerms(json);
                                                // cache for other scripts
                                                window.__myPermissionsCache = perms;
                                                doHide(perms);
                                            })
                                            .catch(function(){ /* silently ignore - leave all tabs visible on failure */ });
                                    }
                                } catch(err){ console.error('tab-permission script error', err); }
                            })();
                        </script>
                        <!-- Content area for tabs -->
                        <section id="admin-main-content" class="lg:col-span-9">

                            <!-- Overview Tab -->
                            <div id="tab-overview" class="tab-panel space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                    <!-- Favorite Stats -->
                                    <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center gap-2 mb-4">
                                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-white"></i>
                                            <span class="text-white font-semibold">الإحصائيات المُفضلة</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                                                <p class="text-gray-300 text-sm mb-2">المستخدمين النشطين</p>
                                                <p id="fav-active-users" class="text-3xl font-bold text-white">0</p>
                                            </div>
                                            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                                                <p class="text-gray-300 text-sm mb-2">الاستثمارات المكتملة</p>
                                                <p id="fav-completed-investments" class="text-3xl font-bold text-white">0
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Activity -->
                                    <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="activity" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">النشاط الأخير</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div id="recent-bell" class="relative">
                                                    <i data-lucide="bell" class="w-5 h-5 text-white"></i>
                                                    <div id="recent-unread-badge" class="absolute -top-2 -right-2 bg-red-600 text-white text-[11px] leading-none rounded-full px-2 py-0.5 hidden" style="min-width:20px;text-align:center;">0</div>
                                                </div>
                                                <button id="mark-all-read" class="text-xs bg-gray-800/60 hover:bg-gray-800 text-white rounded px-2 py-1">وضع الكل كمقروء</button>
                                            </div>
                                        </div>
                                        <div id="recent-activity" class="space-y-3 max-h-72 overflow-y-auto"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reports Tab -->
                            <div id="tab-reports" class="tab-panel space-y-4 hidden">
                                <!-- Include chart and export libs only when needed -->
                                <div class="hidden" id="rep-lib-loader">
                                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
                                    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
                                    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
                                </div>

                                <div id="reports-content" class="space-y-4">
                                    <!-- Report Header (shown on screen and in PDF) -->
                                    <div id="rep-header-card" class="bg-gray-900/50 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 id="rep-header-title" class="text-white text-lg font-semibold">تقرير الاستثمارات</h3>
                                                <div id="rep-header-range" class="text-gray-300 text-sm mt-1">النطاق الزمني: كل الفترات</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Filters & Actions -->
                                    <div id="rep-filters-card" class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex flex-col gap-4">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                                                    <label class="text-gray-300 text-sm shrink-0">من</label>
                                                    <input type="date" id="rep-from" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" />
                                                </div>
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                                                    <label class="text-gray-300 text-sm shrink-0">إلى</label>
                                                    <input type="date" id="rep-to" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" />
                                                </div>
                                                <div class="min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">نوع الاستثمار</label>
                                                    <select id="rep-type" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2">
                                                        <option value="any">الكل</option>
                                                        <option value="small">صغير (حزم)</option>
                                                        <option value="large">كبير</option>
                                                    </select>
                                                </div>
                                                <div class="min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">الحالة</label>
                                                    <select id="rep-status" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" multiple>
                                                        <option value="pending">قيد المراجعة</option>
                                                        <option value="approved">موافق عليه</option>
                                                        <option value="completed">مكتمل</option>
                                                        <option value="rejected">مرفوض</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                <div class="md:col-span-2 min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">المستثمرون</label>
                                                    <div id="rep-investors" class="flex flex-wrap gap-2 bg-gray-800/50 border border-gray-700 rounded-md p-2 min-h-[40px]">
                                                        <span class="text-gray-400 text-xs">سيتم تحميل قائمة المستثمرين بعد تطبيق الفلاتر</span>
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap items-stretch gap-2 w-full md:w-auto">
                                                    <button id="rep-apply" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500 w-full sm:w-auto">تطبيق</button>
                                                    <button id="rep-reset" class="px-4 py-2 rounded-md bg-gray-700 text-white hover:bg-gray-600 w-full sm:w-auto">إعادة تعيين</button>
                                                </div>
                                                <div class="flex flex-col sm:flex-row flex-wrap items-stretch gap-2 justify-stretch sm:justify-end w-full md:w-auto">
                                                    <input id="rep-preset-name" placeholder="اسم الحفظ" class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-2 w-full sm:w-40" />
                                                    <button id="rep-save-preset" class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-500 w-full sm:w-auto">حفظ الفلاتر</button>
                                                    <select id="rep-presets" class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-2 w-full sm:w-auto">
                                                        <option value="">استدعاء إعداد محفوظ</option>
                                                    </select>
                                                    <button id="rep-export" class="px-4 py-2 rounded-md bg-cyan-600 text-white hover:bg-cyan-500 w-full sm:w-auto">تصدير PDF</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">إجمالي الاستثمارات</p>
                                            <p id="rep-total-count" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">إجمالي المبالغ</p>
                                            <p id="rep-total-amount" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">إجمالي الأرباح (ضمن الفترة)</p>
                                            <p id="rep-total-profit" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                    </div>

                                    <!-- Charts -->
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="line-chart" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">أرباح كل استثمار حسب الأشهر</span>
                                            </div>
                                            <canvas id="rep-chart-investments" height="180"></canvas>
                                        </div>
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">مجموع الأرباح لكل مستثمر</span>
                                            </div>
                                            <canvas id="rep-chart-investors" height="180"></canvas>
                                        </div>
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="columns" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">حالة الاستثمارات مقابل المبالغ</span>
                                            </div>
                                            <canvas id="rep-chart-status" height="180"></canvas>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="table" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">تفاصيل الاستثمارات</span>
                                            </div>
                                            <input id="rep-search" placeholder="بحث..." class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-1.5 w-48" />
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm text-right text-gray-300" id="rep-table">
                                                <thead class="text-xs bg-gray-800/70 text-gray-200">
                                                    <tr>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="created_at">تاريخ الإضافة</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="investor_name">المستثمر</th>
                                                        <th class="px-3 py-2">النوع</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="amount">المبلغ</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="profit_rate">النسبة</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="expected_return">الربح المتوقع</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="duration_months">عدد الأشهر</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="months_earned">الشهر الحالي</th>
                                                        <th class="px-3 py-2">الحالة</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rep-table-body" class="divide-y divide-gray-700/60">
                                                    <tr><td colspan="9" class="text-center text-gray-400 py-6">اختر فلاتر ثم اضغط تطبيق لعرض التقارير</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- License Reports Tab -->
                            <div id="tab-license-reports" class="tab-panel space-y-4 hidden">
                                <!-- Use existing dynamic loaders for charts and export -->
                                <div id="license-reports-content" class="space-y-4">
                                    <!-- Header -->
                                    <div id="lrep-header-card" class="bg-gray-900/50 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 id="lrep-header-title" class="text-white text-lg font-semibold">تقارير التراخيص</h3>
                                                <div id="lrep-header-range" class="text-gray-300 text-sm mt-1">النطاق الزمني: كل الفترات</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Filters & Actions -->
                                    <div id="lrep-filters-card" class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex flex-col gap-4">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                                                    <label class="text-gray-300 text-sm shrink-0">من</label>
                                                    <input type="date" id="lrep-from" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" />
                                                </div>
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                                                    <label class="text-gray-300 text-sm shrink-0">إلى</label>
                                                    <input type="date" id="lrep-to" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" />
                                                </div>
                                                <div class="min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">نوع الترخيص</label>
                                                    <select id="lrep-type" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2">
                                                        <option value="any">الكل</option>
                                                    </select>
                                                </div>
                                                <div class="min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">الحالة</label>
                                                    <select id="lrep-status" class="w-full bg-gray-800/70 border border-gray-700 text-white rounded-md p-2" multiple>
                                                        <option value="pending">قيد المراجعة</option>
                                                        <option value="approved">موافق عليه</option>
                                                        <option value="rejected">مرفوض</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                <div class="md:col-span-2 min-w-0">
                                                    <label class="text-gray-300 text-sm mb-1 block">المالكون</label>
                                                    <div id="lrep-owners" class="flex flex-wrap gap-2 bg-gray-800/50 border border-gray-700 rounded-md p-2 min-h-[40px]">
                                                        <span class="text-gray-400 text-xs">سيتم تحميل قائمة المالكين بعد تطبيق الفلاتر</span>
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap items-stretch gap-2 w-full md:w-auto">
                                                    <button id="lrep-apply" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500 w-full sm:w-auto">تطبيق</button>
                                                    <button id="lrep-reset" class="px-4 py-2 rounded-md bg-gray-700 text-white hover:bg-gray-600 w-full sm:w-auto">إعادة تعيين</button>
                                                </div>
                                                <div class="flex flex-col sm:flex-row flex-wrap items-stretch gap-2 justify-stretch sm:justify-end w-full md:w-auto">
                                                    <input id="lrep-preset-name" placeholder="اسم الحفظ" class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-2 w-full sm:w-40" />
                                                    <button id="lrep-save-preset" class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-500 w-full sm:w-auto">حفظ الفلاتر</button>
                                                    <select id="lrep-presets" class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-2 w-full sm:w-auto">
                                                        <option value="">استدعاء إعداد محفوظ</option>
                                                    </select>
                                                    <button id="lrep-export" class="px-4 py-2 rounded-md bg-cyan-600 text-white hover:bg-cyan-500 w-full sm:w-auto">تصدير PDF</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">إجمالي التراخيص</p>
                                            <p id="lrep-total-count" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">الموافق عليها</p>
                                            <p id="lrep-total-approved" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                                            <p class="text-gray-300 text-sm mb-1">المرفوضة</p>
                                            <p id="lrep-total-rejected" class="text-2xl font-bold text-white">—</p>
                                        </div>
                                    </div>

                                    <!-- Charts -->
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="pie-chart" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">حسب النوع</span>
                                            </div>
                                            <canvas id="lrep-chart-bytype" height="180"></canvas>
                                        </div>
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">حسب الحالة</span>
                                            </div>
                                            <canvas id="lrep-chart-bystatus" height="180"></canvas>
                                        </div>
                                        <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">شهري (مكدس)</span>
                                            </div>
                                            <canvas id="lrep-chart-monthly" height="180"></canvas>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div class="bg-gray-900/40 rounded-xl border border-white/10 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="table" class="w-5 h-5 text-white"></i>
                                                <span class="text-white font-semibold">تفاصيل التراخيص</span>
                                            </div>
                                            <input id="lrep-search" placeholder="بحث..." class="bg-gray-800/70 border border-gray-700 text-white rounded-md p-1.5 w-48" />
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm text-right text-gray-300" id="lrep-table">
                                                <thead class="text-xs bg-gray-800/70 text-gray-200">
                                                    <tr>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="issued_at">تاريخ الإصدار</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="id">رقم الترخيص</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="name">الاسم التجاري</th>
                                                        <th class="px-3 py-2">النوع</th>
                                                        <th class="px-3 py-2">الحالة</th>
                                                        <th class="px-3 py-2">الملاحظات</th>
                                                        <th class="px-3 py-2 cursor-pointer" data-sort="owner_name">المالك</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lrep-table-body" class="divide-y divide-gray-700/60">
                                                    <tr><td colspan="7" class="text-center text-gray-400 py-6">اختر فلاتر ثم اضغط تطبيق لعرض التقارير</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal: Investment Pay (admin) -->
                            <div id="investment-pay-modal" class="hidden fixed inset-0 z-60 items-center justify-center bg-black/60" style="z-index:99999;">
                                <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 mx-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-white font-semibold">تأكيد/تنفيذ الدفع</h4>
                                        <button id="investment-pay-modal-close" class="text-gray-300">✕</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="text-sm text-gray-300">المعرف: <span data-__adm_pay_modal_id_display class="text-white">—</span></div>
                                        <div class="text-sm text-gray-300">المستثمر: <span data-__adm_pay_modal_investor_display class="text-white">—</span></div>
                                        <div class="text-sm text-gray-300">المبلغ المتوقع: <span data-__adm_pay_modal_amount_display class="text-white">—</span></div>
                                        <div>
                                            <label class="text-gray-300 text-sm">قيمة الدفع (اختياري)</label>
                                            <input name="adm_pay_amount" type="text" class="w-full mt-1 p-2 rounded bg-gray-800 border border-gray-700 text-white" placeholder="أدخل قيمة الدفع أو اتركها" />
                                        </div>
                                        <div>
                                            <label class="text-gray-300 text-sm">ملاحظة للمستثمر (اختياري)</label>
                                            <textarea name="adm_pay_note" class="w-full mt-1 p-2 rounded bg-gray-800 border border-gray-700 text-white" rows="3" placeholder="مثال: يرجى تسديد المبلغ خلال 3 أيام..."></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-end gap-2">
                                        <input data-__adm_pay_modal_id type="hidden" value="" />
                                        <button id="investment-pay-modal-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إلغاء</button>
                                        <button id="investment-pay-modal-confirm" class="px-3 py-2 rounded-md bg-emerald-600 text-black">إرسال تذكير</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Inline admin-pay handler removed: centralized handler in public/js/admin-dashboard.js is used to avoid duplicates -->
                            <!-- Minimal fallback: open admin pay modal on click if centralized handler didn't (idempotent, defensive) -->
                            <script>
                                (function(){
                                    try {
                                        document.addEventListener('click', function(e){
                                            var el = e.target;
                                            while(el && el !== document){
                                                if(el.getAttribute && (el.getAttribute('data-__adm_pay_btn') !== null)){
                                                    e.preventDefault();
                                                    var id = el.getAttribute('data-__adm_pay_id') || (el.dataset && el.dataset.__admPayId) || '';
                                                    var m = document.getElementById('investment-pay-modal');
                                                    if(!m) return;
                                                    // set hidden id
                                                    try { var hid = m.querySelector('[data-__adm_pay_modal_id]'); if(hid) hid.value = id || ''; } catch(_){}
                                                    // set displays
                                                    try { var idDisplay = m.querySelector('[data-__adm_pay_modal_id_display]'); if(idDisplay) idDisplay.textContent = id || '—'; } catch(_){}
                                                    // investor display: prefer current inv modal name, else fetch via API
                                                    try {
                                                        var invDisplay = m.querySelector('[data-__adm_pay_modal_investor_display]');
                                                        if(invDisplay){
                                                            var nameEl = document.getElementById('inv-name');
                                                            var nameTxt = nameEl && nameEl.textContent ? nameEl.textContent.trim() : '';
                                                            if(nameTxt && nameTxt !== '—'){
                                                                invDisplay.textContent = nameTxt;
                                                            } else {
                                                                invDisplay.textContent = '—';
                                                                if(id){
                                                                    try {
                                                                        var xhrName = new XMLHttpRequest();
                                                                        xhrName.open('GET', '/api/admin/investments/' + encodeURIComponent(id), true);
                                                                        xhrName.onload = function(){
                                                                            if(xhrName.status >= 200 && xhrName.status < 300){
                                                                                try {
                                                                                    var data = JSON.parse(xhrName.responseText || '{}');
                                                                                    var nm = (data && data.investor && (data.investor.full_name || data.investor.name)) || '';
                                                                                    if(nm) {
                                                                                        invDisplay.textContent = nm;
                                                                                        // If note was auto-filled, refresh it with the final investor name (investment context, no amount)
                                                                                        try {
                                                                                            var noteInput = m.querySelector('[name="adm_pay_note"]');
                                                                                            if(noteInput && noteInput.dataset.__auto === '1'){
                                                                                                var tpl = 'مرحبًا ' + nm + '،\n' +
                                                                                                          'نذكّركم بوجود دفعة مستحقة لاستثماركم.\n' +
                                                                                                          'نرجو تسديد الرسوم وإرفاق إيصال التحويل لاستكمال الإجراءات.\n' +
                                                                                                          'شكرًا لتعاونكم.';
                                                                                                noteInput.value = tpl;
                                                                                            }
                                                                                        } catch(_){ }
                                                                                    }
                                                                                } catch(_){ }
                                                                            }
                                                                        };
                                                                        xhrName.send();
                                                                    } catch(_){ }
                                                                }
                                                            }
                                                        }
                                                    } catch(_){}
                                                    // amount display: reuse inv detail amount if present
                                                    try {
                                                        var amtDisplay = m.querySelector('[data-__adm_pay_modal_amount_display]');
                                                        var invDetAmt = document.getElementById('invdet-amount');
                                                        if(amtDisplay) amtDisplay.textContent = invDetAmt ? invDetAmt.textContent : '—';
                                                        var amountInput = m.querySelector('input[name="adm_pay_amount"]');
                                                        if(amountInput){
                                                            var raw = invDetAmt && invDetAmt.textContent ? invDetAmt.textContent : '';
                                                            var map = {'٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
                                                            var norm = raw.replace(/[^0-9٠-٩,\.,،\-]/g,'');
                                                            norm = norm.replace(/[٠-٩]/g, function(c){ return map[c] || c; });
                                                            norm = norm.replace(/[،,]/g, '.');
                                                            var parts = norm.split('.'); if(parts.length > 2){ norm = parts.shift() + '.' + parts.join(''); }
                                                            norm = norm.replace(/[^0-9\.\-]/g, '');
                                                            amountInput.value = norm;
                                                        }
                                                        // Prefill editable note message (auto) - investment reminder without amount
                                                        try {
                                                            var noteInput = m.querySelector('[name="adm_pay_note"]');
                                                            if(noteInput){
                                                                var currentNameEl = m.querySelector('[data-__adm_pay_modal_investor_display]');
                                                                var currentName = currentNameEl && currentNameEl.textContent ? currentNameEl.textContent.trim() : '';
                                                                var nm2 = (currentName && currentName !== '—') ? currentName : 'العميل الكريم';
                                                                var tpl = 'مرحبًا ' + nm2 + '،\n' +
                                                                          'نذكّركم بوجود دفعة مستحقة لاستثماركم.\n' +
                                                                          'نرجو تسديد الرسوم وإرفاق إيصال التحويل لاستكمال الإجراءات.\n' +
                                                                          'شكرًا لتعاونكم.';
                                                                noteInput.value = tpl;
                                                                noteInput.dataset.__auto = '1';
                                                                try { noteInput.addEventListener('input', function(){ this.dataset.__auto = '0'; }, { once: true }); } catch(_){ }
                                                            }
                                                        } catch(_){ }
                                                    } catch(_){}
                                                    // show modal
                                                    try { m.classList.remove('hidden'); m.classList.add('flex'); } catch(_){}
                                                    return;
                                                }
                                                el = el.parentNode;
                                            }
                                        }, true);
                                    } catch(err){ console.error('admin-pay fallback error', err); }
                                })();
                            </script>

                           

                            <script>
                                (function(){
                                    try {
                                        var confirmBtn = document.getElementById('investment-pay-modal-confirm');
                                        if(!confirmBtn) return;
                                        if(confirmBtn.dataset.__admPayFallbackAttached) return;
                                        confirmBtn.dataset.__admPayFallbackAttached = '1';
                                        confirmBtn.addEventListener('click', function(e){
                                            e.preventDefault();
                                            var m = document.getElementById('investment-pay-modal');
                                            if(!m) return;
                                            // ensure investor display exists
                                            try { var invNameDisplay = m.querySelector('[data-__adm_pay_modal_investor_display]'); if(invNameDisplay && (!invNameDisplay.textContent || invNameDisplay.textContent.trim() === '')) invNameDisplay.textContent = '—'; } catch(_){}
                                            var id = (m.querySelector('[data-__adm_pay_modal_id]')||{value:''}).value;
                                            if(!id || id.toString().trim() === '') return alert('معرّف الاستثمار مفقود');
                                            var amount = (m.querySelector('[name="adm_pay_amount"]')||{value:''}).value || '';
                                            var note = (m.querySelector('[name="adm_pay_note"]')||{value:''}).value || '';
                                            var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));
                                            var params = 'amount=' + encodeURIComponent(amount) + '&note=' + encodeURIComponent(note) + '&_token=' + encodeURIComponent(token);
                                            var xhr = new XMLHttpRequest();
                                            xhr.open('POST', '/api/admin/investments/' + encodeURIComponent(id) + '/notify-payment', true);
                                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                                            try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(e){}
                                            confirmBtn.disabled = true;
                                            xhr.onload = function(){
                                                confirmBtn.disabled = false;
                                                if(xhr.status >= 200 && xhr.status < 300){
                                                    try {
                                                        if (typeof showToast === 'function') {
                                                            showToast('success', 'تم ارسال طلب الدفع بنجاح،');
                                                        } else {
                                                            alert('تم ارسال طلب الدفع بنجاح،');
                                                        }
                                                    } catch(e){
                                                        try { alert('تم ارسال طلب الدفع بنجاح،'); } catch(_){}
                                                    }
                                                    try {
                                                        m.classList.add('hidden'); m.classList.remove('flex');
                                                        // clear fields for next use
                                                        try { var hid = m.querySelector('[data-__adm_pay_modal_id]'); if(hid) hid.value = ''; } catch(_){ }
                                                        try { var idDisplay = m.querySelector('[data-__adm_pay_modal_id_display]'); if(idDisplay) idDisplay.textContent = '—'; } catch(_){ }
                                                        try { var invDisplay = m.querySelector('[data-__adm_pay_modal_investor_display]'); if(invDisplay) invDisplay.textContent = '—'; } catch(_){ }
                                                        try { var amtDisplay = m.querySelector('[data-__adm_pay_modal_amount_display]'); if(amtDisplay) amtDisplay.textContent = '—'; } catch(_){ }
                                                        try { var amountInput = m.querySelector('[name="adm_pay_amount"]'); if(amountInput) amountInput.value = ''; } catch(_){ }
                                                        try { var noteInput = m.querySelector('[name="adm_pay_note"]'); if(noteInput) noteInput.value = ''; } catch(_){ }
                                                    } catch(e){}
                                                    try { if(typeof fetchAll === 'function') fetchAll(); } catch(e){}
                                                } else {
                                                    alert('فشل إرسال التذكير');
                                                }
                                            };
                                            xhr.onerror = function(){ confirmBtn.disabled = false; alert('فشل في الاتصال'); };
                                            xhr.send(params);
                                        });
                                    } catch(err){ console.error('admin-pay confirm fallback error', err); }
                                })();
                            </script>
                            <script>
                                (function(){
                                    try {
                                        function closePayModal(){
                                            var m = document.getElementById('investment-pay-modal');
                                            if(!m) return;
                                            try { m.classList.add('hidden'); m.classList.remove('flex'); } catch(_){ }
                                            // also clear fields so previous data doesn't persist
                                            try { var hid = m.querySelector('[data-__adm_pay_modal_id]'); if(hid) hid.value = ''; } catch(_){ }
                                            try { var idDisplay = m.querySelector('[data-__adm_pay_modal_id_display]'); if(idDisplay) idDisplay.textContent = '—'; } catch(_){ }
                                            try { var invDisplay = m.querySelector('[data-__adm_pay_modal_investor_display]'); if(invDisplay) invDisplay.textContent = '—'; } catch(_){ }
                                            try { var amtDisplay = m.querySelector('[data-__adm_pay_modal_amount_display]'); if(amtDisplay) amtDisplay.textContent = '—'; } catch(_){ }
                                            try { var amountInput = m.querySelector('[name="adm_pay_amount"]'); if(amountInput) amountInput.value = ''; } catch(_){ }
                                            try { var noteInput = m.querySelector('[name="adm_pay_note"]'); if(noteInput) noteInput.value = ''; } catch(_){ }
                                        }

                                        // attach to close X and cancel button (idempotent)
                                        ['investment-pay-modal-close','investment-pay-modal-cancel','investment-pay-modal-cancel2'].forEach(function(id){
                                            try {
                                                var el = document.getElementById(id);
                                                if(el && !el.dataset.__admPayCloseAttached){
                                                    el.addEventListener('click', function(e){ e.preventDefault(); closePayModal(); });
                                                    el.dataset.__admPayCloseAttached = '1';
                                                }
                                            } catch(_){}
                                        });

                                        // clicking the backdrop closes the modal
                                        try {
                                            var modal = document.getElementById('investment-pay-modal');
                                            if(modal && !modal.dataset.__admPayBackdropAttached){
                                                modal.addEventListener('click', function(e){ if(e.target === modal){ closePayModal(); } });
                                                modal.dataset.__admPayBackdropAttached = '1';
                                            }
                                        } catch(_){}

                                        // ESC to close (one-time attachment)
                                        try {
                                            if(!document.body.dataset.__admPayEscAttached){
                                                document.addEventListener('keydown', function(e){ if(e.key === 'Escape' || e.key === 'Esc'){ closePayModal(); } });
                                                document.body.dataset.__admPayEscAttached = '1';
                                            }
                                        } catch(_){}
                                    } catch(err){ console.error('admin-pay close attach error', err); }
                                })();
                            </script>

                            <!-- Chat Tab -->
                            <div id="tab-chat" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-white flex items-center gap-2"><i
                                            data-lucide="message-square" class="w-5 h-5"></i> الدردشة</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <!-- Conversations/users list -->
                                    <aside class="md:col-span-4 space-y-2 max-h-[65vh] overflow-y-auto">
                                        <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-white font-semibold">المحادثات</span>
                                                        <span id="chat-total-unread"
                                                            class="text-xs bg-red-600/80 text-white rounded-full px-2 py-0.5 hidden"></span>
                                                    </div>
                                                    <div id="chat-users-list" class="space-y-2 overflow-y-auto max-h-[65vh]"></div>
                                                </div>
                                    </aside>
                                    <!-- Chat thread -->
                                    <div class="md:col-span-8">
                                        <div
                                            class="bg-gray-800/50 border border-gray-700 rounded-md h-full min-h-[50vh] flex flex-col">
                                            <div class="p-3 border-b border-gray-700/60">
                                                <div class="relative inline-block">
                                                    <h4 id="chat-active-name" class="text-white font-semibold">—</h4>
                                                    <span id="chat-active-unread"
                                                        class="absolute -top-2 right-0 text-[10px] bg-red-600 text-white rounded-full px-1.5 py-0.5 hidden"></span>
                                                </div>
                                            </div>
                                            <div id="chat-thread" class="flex-1 overflow-y-auto p-3 space-y-2" style="max-height: 55vh;"></div>
                                            
                                            <form id="chat-send-form"
                                                class="p-3 border-t border-gray-700/60 flex items-center gap-2">
                                                <input id="chat-input"
                                                    class="flex-1 bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="اكتب رسالتك..." />
                                                <button
                                                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-md px-3 py-2"
                                                    type="submit">إرسال</button>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Admin pay modal and scripts removed per user request -->

                            

                            <!-- License payment modal wiring removed per user request -->

                            </div>

                            <!-- Users Tab -->
                            <div id="tab-users" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة المستخدمين</h3>
                                    <div class="flex items-center gap-2">
                                        <button id="user-open-modal"
                                            class="bg-green-600 hover:bg-green-700 text-white text-sm rounded-md px-3 py-2 flex items-center gap-2">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            إضافة مستخدم
                                        </button>
                                        <span id="users-count"
                                            class="inline-flex items-center rounded-md bg-blue-600 text-white text-xs font-semibold px-2.5 py-1.5">0
                                            مستخدم</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex gap-2 items-center">
                                        <input type="text" id="user-search" placeholder="ابحث بالاسم أو البريد أو المعرف"
                                               class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                        <select id="user-role-filter" class="bg-gray-800/60 border border-gray-700 text-white rounded p-2 text-sm" title="تصفية حسب الدور">
                                            <option value="all">الكل</option>
                                            <option value="users">المستخدمين</option>
                                            <option value="investors">المستثمرين</option>
                                        </select>
                                    </div>
                                    <div id="user-results">@includeIf('admin.partials.users_table', ['users' => []])</div>
                                </div>

                                <div id="users-list" class="space-y-3 max-h-[60vh] overflow-y-auto"></div>

                                <!-- Modal: User Create/Edit -->
                                <div id="user-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="user-modal-title" class="text-white font-semibold">مستخدم جديد</h4>
                                            <button id="user-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">اسم المستخدم (username)</label>
                                                <input type="text" id="user-username" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الاسم الكامل</label>
                                                <input type="text" id="user-full-name" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">البريد الإلكتروني</label>
                                                <input type="email" id="user-email" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الهاتف</label>
                                                <input type="text" id="user-phone" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm text-gray-300 mb-1">الدور</label>
                                                    <select id="user-role" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white">
                                                        <option value="admin">مدير</option>
                                                        <option value="staff">مستخدم</option>
                                                        <option value="investor">مستثمر</option>
                                                    </select>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        المدير والمستخدم يدخلان لوحة تحكم المدير.<br>
                                                        المستثمر يدخل المنصة فقط.
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 mt-6">
                                                    <input type="checkbox" id="user-active" class="w-4 h-4">
                                                    <label for="user-active" class="text-sm text-gray-300">نشط</label>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">كلمة المرور (اتركها فارغة بدون تغيير)</label>
                                                <input type="password" id="user-password" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div class="mt-2">
                                                <div class="flex items-center justify-between mb-2">
                                                    <p class="text-white font-semibold">صلاحيات المستخدم (اختياري)</p>
                                                    <div class="flex items-center gap-2">
                                                        <label class="inline-flex items-center text-xs text-white"><input type="checkbox" id="user-perms-select-all" class="brand-chk"> <span class="ml-2">تحديد الكل</span></label>
                                                    </div>
                                                </div>
                                                <div id="user-perms" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
                                                <p class="text-xs text-gray-400 mt-1">إن لم تحدد صلاحيات، سيتم تطبيق صلاحيات الدور المختار.</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <button id="user-modal-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إلغاء</button>
                                            <button id="user-modal-save" class="px-3 py-2 rounded-md bg-blue-600 text-white">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Investors Tab -->
                            <div id="tab-investors" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة المستثمرين</h3>
                                    <span id="investors-count" class="inline-flex items-center rounded-md bg-teal-600 text-white text-xs font-semibold px-2.5 py-1.5">0 مستثمر</span>
                                </div>

                                <!-- Server-backed search input (updates partial via AJAX) -->
                                <div id="investors-tab" class="tab-content">
                                    <input type="text" id="investor-search" placeholder="ابحث بالاسم أو الرقم التعريفي أو الهاتف"
                                           class="border p-2 rounded w-full mb-4 text-sm bg-gray-900/60 text-white" />

                                    <div id="investor-results">
                                        {{-- initial empty partial: we'll let JS load real data via API/search --}}
                                        @includeIf('admin.partials.investors_table', ['investors' => []])
                                    </div>
                                </div>

                                <!-- Modal: Investor Details -->
                                <div id="inv-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-4xl p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="inv-modal-title" class="text-white font-semibold">تفاصيل المستثمر</h4>
                                            <button id="inv-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3 text-center">
                                                <div class="flex justify-center mb-2">
                                                        <div id="inv-avatar" class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-xl" style="background: #334155">IN</div>
                                                </div>
                                                <div class="text-white font-semibold" id="inv-name">—</div>
                                                <div class="text-gray-300 text-sm" id="inv-email">—</div>
                                                <div class="text-gray-300 text-sm" id="inv-phone">—</div>
                                            </div>

                                            <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                <p class="text-white font-semibold mb-2">ملخص الاستثمارات</p>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                    <div class="bg-gray-900/50 border border-gray-700 rounded p-3">
                                                        <p class="text-gray-300 text-xs">الكل</p>
                                                        <p class="text-white text-lg font-bold" id="inv-stats-total">0</p>
                                                    </div>
                                                    <div class="bg-gray-900/50 border border-gray-700 rounded p-3">
                                                        <p class="text-gray-300 text-xs">نشطة</p>
                                                        <p class="text-white text-lg font-bold" id="inv-stats-active">0</p>
                                                    </div>
                                                    <div class="bg-gray-900/50 border border-gray-700 rounded p-3">
                                                        <p class="text-gray-300 text-xs">مكتملة</p>
                                                        <p class="text-white text-lg font-bold" id="inv-stats-completed">0</p>
                                                    </div>
                                                    <div class="bg-gray-900/50 border border-gray-700 rounded p-3">
                                                        <p class="text-gray-300 text-xs">المجموع (ريال)</p>
                                                        <p class="text-white text-lg font-bold" id="inv-stats-amount">0</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                <p class="text-white font-semibold mb-2">استثمارات المستثمر</p>
                                                <div id="inv-investments" class="space-y-2 max-h-[50vh] overflow-y-auto"></div>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <button id="inv-modal-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Investments Tab -->
                            <div id="tab-investments" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة الاستثمارات</h3>
                                    <span id="investments-count"
                                        class="inline-flex items-center rounded-md bg-green-600 text-white text-xs font-semibold px-2.5 py-1.5">0
                                        استثمار</span>
                                </div>
                                <div class="mb-4">
                                    <input type="text" id="investment-search" placeholder="ابحث بالمعرف أو الحالة أو المبلغ"
                                           class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                    <div id="investment-results">@includeIf('admin.partials.investments_table', ['investments' => []])</div>
                                </div>
                                <div id="investments-list" class="space-y-3 max-h-[60vh] overflow-y-auto"></div>
                                <!-- Modal: Investment Details -->
                                <div id="investment-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-4xl p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="investment-modal-title" class="text-white font-semibold">تفاصيل الاستثمار</h4>
                                            <button id="investment-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                            <div class="lg:col-span-2 space-y-3">
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">بيانات الاستثمار</p>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-300">
                                                        <div><span class="text-gray-400">المعرف:</span> <span id="invdet-id" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">المبلغ:</span> <span id="invdet-amount" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">معدل الربح:</span> <span id="invdet-rate" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">العائد المتوقع:</span> <span id="invdet-expected" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">المدة (أشهر):</span> <span id="invdet-duration" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">المخاطر:</span> <span id="invdet-risk" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الحالة:</span> <span id="invdet-status" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">تاريخ الإنشاء:</span> <span id="invdet-created" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">أول دفعة ستُدرج بعد:</span> <span id="invdet-next-payout" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">حالة المزامنة:</span> <span id="invdet-earnings-status" class="text-white">—</span></div>
                                                        <div class="md:col-span-2"><span class="text-gray-400">ملاحظات:</span> <span id="invdet-notes" class="text-white">—</span></div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">المدفوعات الأخيرة</p>
                                                    <div id="invdet-payments" class="space-y-2"></div>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">حركات الاستثمار</p>
                                                    <div id="invdet-trans" class="space-y-2"></div>
                                                </div>
                                            </div>
                                            <div class="space-y-3">
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">المستثمر</p>
                                                    <div class="space-y-1 text-sm text-gray-300">
                                                        <div><span class="text-gray-400">الاسم:</span> <span id="invdet-investor-name" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">البلد:</span> <span id="invdet-investor-nationality" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">البريد:</span> <span id="invdet-investor-email" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الهاتف:</span> <span id="invdet-investor-phone" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الهوية:</span> <span id="invdet-investor-nid" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الحساب البنكي:</span> <span id="invdet-investor-bank" class="text-white">—</span></div>
                                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                                            <div class="flex flex-col items-center">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="invdet-id-front" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                </div>
                                                                <div id="invdet-id-front-actions" class="mt-1 flex flex-col items-center gap-1"></div>
                                                            </div>
                                                            <div class="flex flex-col items-center">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="invdet-id-back" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                </div>
                                                                <div id="invdet-id-back-actions" class="mt-1 flex flex-col items-center gap-1"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="invdet-package-card" class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">الحزمة</p>
                                                    <div class="text-sm text-gray-300 space-y-1">
                                                        <div><span class="text-gray-400">المعرف:</span> <span id="invdet-package-id" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">المبلغ:</span> <span id="invdet-package-amount" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">معدل الربح:</span> <span id="invdet-package-rate" class="text-white">—</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <!-- Admin actions: Approve, Active, Completed, Reject, Pay, Close -->
                                            <button id="investment-modal-approve" data-id="" class="px-3 py-2 rounded-md bg-green-600 text-black hover:bg-green-500">اعتماد</button>
                                            <!-- زر 'نشط' أُزيل من واجهة لوحة التحكم حسب طلب المستخدم -->
                                            {{-- <button id="investment-modal-active" data-id="" class="px-3 py-2 rounded-md border border-orange-600 text-orange-300 hover:bg-orange-600/20">نشط</button> --}}
                                            <button id="investment-modal-complete" data-id="" class="px-3 py-2 rounded-md border border-blue-600 text-blue-300 hover:bg-blue-600/20">مكتمل</button>
                                            <button id="investment-modal-reject" data-id="" class="px-3 py-2 rounded-md border border-red-700 text-red-300 hover:bg-red-700/20">رفض</button>
                                            <button id="investment-modal-pay" data-__adm_pay_btn="1" data-__adm_pay_id="" class="px-3 py-2 rounded-md bg-emerald-600 text-black hover:bg-emerald-500">دفع</button>
                                            <button id="investment-modal-close2" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Licenses Tab -->
                            <div id="tab-packages" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">حزم الاستثمار</h3>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <button id="new-package-toggle"
                                        class="bg-green-600 hover:bg-green-700 text-white text-sm rounded-md px-3 py-2 flex items-center gap-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        إضافة حزمة جديدة
                                    </button>
                                    <span id="packages-count"
                                        class="inline-flex items-center rounded-md bg-orange-600 text-white text-xs font-semibold px-2.5 py-1.5"></span>
                                </div>
                                <div class="mb-4">
                                    <input type="text" id="packages-search" placeholder="ابحث باسم الحزمة أو المبلغ أو المعرف"
                                           class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                    <div id="packages-results">@includeIf('admin.partials.packages_table', ['packages' => []])</div>
                                </div>
                                <div id="packages-list" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                </div>

                                <!-- Modal: Package Create/Edit -->
                                <div id="pkg-modal"
                                    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div
                                        class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="pkg-modal-title" class="text-white font-semibold">حزمة جديدة</h4>
                                            <button id="pkg-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">مبلغ الاستثمار
                                                    (ريال)</label>
                                                <input type="number" id="pkg-amount"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">معدل الربح (%)</label>
                                                <input type="number" step="0.01" id="pkg-profit"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">اسم الحزمة</label>
                                                <input type="text" id="pkg-name"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">المدة (شهور)</label>
                                                <input type="number" id="pkg-duration" min="1" step="1" value="12"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <label class="inline-flex items-center text-sm text-gray-300"><input type="checkbox" id="pkg-active" checked class="mr-2"/> مفعل</label>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm text-gray-300 mb-1">وصف (اختياري)</label>
                                            <textarea id="pkg-desc" rows="2" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                <p class="text-gray-300 text-sm mb-1">الربح المتوقع</p>
                                                <p id="pkg-prev-profit" class="text-green-400 font-bold">+0 ريال</p>
                                            </div>
                                            <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                <p class="text-gray-300 text-sm mb-1">إجمالي العائد</p>
                                                <p id="pkg-prev-total" class="text-white font-bold">0 ريال</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p class="text-white font-semibold mb-2">معاينة البطاقة</p>
                                            <div class="glass-card"
                                                style="cursor:default;border:2px solid transparent;transition:all .2s;">
                                                <div style="text-align:center;padding:14px 14px 0 14px;">
                                                    <div style="font-size:22px;color:#22d3ee;font-weight:700;margin-bottom:8px;"
                                                        id="pkg-prev-amount">0 ريال</div>
                                                </div>
                                                <div style="text-align:center;padding:0 14px 14px 14px;">
                                                    <div
                                                        style="background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;border-radius:999px;padding:10px 14px;margin-bottom:10px;">
                                                        <div style="font-size:24px;font-weight:800;" id="pkg-prev-rate">0%
                                                        </div>
                                                        <div style="font-size:12px;font-weight:600;">عائد شهري</div>
                                                    </div>
                                                    <div
                                                        style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                                                        <span style="color:#9ca3af;">الأرباح:</span>
                                                        <span style="color:#22c55e;font-weight:700;"
                                                            id="pkg-prev-profit-line">+0 ريال</span>
                                                    </div>
                                                    <div
                                                        style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:10px;">
                                                        <span style="color:#9ca3af;">إجمالي العائد:</span>
                                                        <span style="color:#fff;font-weight:700;"
                                                            id="pkg-prev-total-line">0 ريال</span>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button id="pkg-modal-cancel"
                                                class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                                            <button id="pkg-modal-save"
                                                class="bg-green-600 hover:bg-green-700 text-white rounded-md px-3 py-2">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Logs tab panel removed -->

                            <div id="tab-navigation" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة عناصر القائمة</h3>
                                    <span id="nav-count"
                                        class="inline-flex items-center rounded-md bg-cyan-600 text-white text-xs font-semibold px-2.5 py-1.5"></span>
                                </div>
                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                        <input id="nav-new-label"
                                            class="bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                            placeholder="العنوان (label)" />
                                        <input id="nav-new-path"
                                            class="bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                            placeholder="المسار (path) مثل /documents" />
                                        <input id="nav-new-icon"
                                            class="bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                            placeholder="الأيقونة (رمز) مثل 🏆" />
                                        <input id="nav-new-order" type="number"
                                            class="bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                            placeholder="الترتيب" />
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center text-white text-sm">
                                                <input id="nav-new-active" type="checkbox" class="mr-2" checked /> مفعل
                                            </label>
                                            <button id="nav-new-save"
                                                class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm rounded-md px-3 py-2">إضافة</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="nav-list" class="space-y-3 max-h-96 overflow-y-auto"></div>
                            </div>

                            <div id="tab-backups" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">النسخ الاحتياطية</h3>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <button id="run-backup"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md px-3 py-2">تشغيل
                                        نسخة احتياطية الآن</button>
                                    <label
                                        class="cursor-pointer border border-gray-600 text-gray-200 text-sm rounded-md px-3 py-2">
                                        تحميل ملف استعادة
                                        <input id="restore-file" type="file" accept=".zip" class="hidden" />
                                    </label>
                                    <button id="restore-backup"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2"
                                        disabled>استعادة النسخة</button>
                                    <span id="backup-status" class="text-gray-300 text-sm"></span>
                                </div>
                                <div id="backups-list" class="space-y-2"></div>
                            </div>

                            <div id="tab-licenses" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة التراخيص التجارية</h3>
                                    <span id="licenses-count"
                                        class="inline-flex items-center rounded-md bg-purple-600 text-white text-xs font-semibold px-2.5 py-1.5">0
                                        ترخيص</span>
                                </div>
                                <div class="mb-4">
                                    <input type="text" id="license-search" placeholder="ابحث باسم المنشأة أو النشاط أو المعرف"
                                           class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                    <div id="license-results">@includeIf('admin.partials.licenses_table', ['licenses' => []])</div>
                                </div>
                                <div id="licenses-list" class="space-y-3 max-h-96 overflow-y-auto"></div>
                                <!-- Modal: License Details (investment-style) -->
                                <div id="lic-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-4xl p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="lic-modal-title" class="text-white font-semibold">تفاصيل الترخيص</h4>
                                            <button id="lic-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                            <div class="lg:col-span-2 space-y-3">
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">بيانات الترخيص</p>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-300">
                                                        <div><span class="text-gray-400">المعرف:</span> <span id="licdet-id" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">تاريخ الإنشاء:</span> <span id="licdet-created" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">المنشأة:</span> <span id="licdet-business-name" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">نوع النشاط:</span> <span id="licdet-business-type" class="text-white">—</span></div>
                                                        <div class="md:col-span-2"><span class="text-gray-400">الوصف:</span> <span id="licdet-activity" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الموقع:</span> <span id="licdet-location" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الحالة:</span> <span id="licdet-status" class="text-white">—</span></div>
                                                        <div class="md:col-span-2"><span class="text-gray-400">ملاحظات:</span> <span id="licdet-notes" class="text-white">—</span></div>
                                                    </div>
                                                </div>

                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">الوثائق</p>
                                                    <div id="licdet-docs" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3"></div>
                                                </div>

                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">المدفوعات / الطلبات</p>
                                                    <div id="licdet-payments" class="space-y-2"></div>
                                                </div>

                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">حركات الترخيص</p>
                                                    <div id="licdet-trans" class="space-y-2"></div>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">معلومات المالك</p>
                                                    <div class="space-y-1 text-sm text-gray-300">
                                                        <div><span class="text-gray-400">الاسم:</span> <span id="licdet-owner-name" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">البريد:</span> <span id="licdet-owner-email" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الهاتف:</span> <span id="licdet-owner-phone" class="text-white">—</span></div>
                                                        <div><span class="text-gray-400">الهوية:</span> <span id="licdet-owner-nid" class="text-white">—</span></div>
                                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                                            <div id="licdet-id-front-wrap" class="flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-id-front" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-id-front-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">صورة الهوية الأمامي</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-id-front-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-id-front-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                            <div id="licdet-id-back-wrap" class="flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-id-back" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-id-back-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">صورة الهوية الخلفي</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-id-back-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-id-back-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Extra small previews: bank statement & salary certificate -->
                                                        <div id="licdet-extra-box" class="mt-3 grid grid-cols-2 gap-2">
                                                            <div id="licdet-bank" class="hidden flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-bank-img" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-bank-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">كشف الحساب البنكي</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-bank-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-bank-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                            <div id="licdet-salary" class="hidden flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-salary-img" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-salary-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">شهادة الراتب</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-salary-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-salary-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Extra small previews: bank statement & salary certificate (mirrors investor layout) -->
                                                        <div id="licdet-extra-box" class="mt-3 grid grid-cols-2 gap-2">
                                                            <div id="licdet-bank" class="hidden flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-bank-img" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-bank-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">كشف الحساب البنكي</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-bank-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-bank-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                            <div id="licdet-salary" class="hidden flex flex-col items-center text-sm text-white">
                                                                <div class="w-16 h-16 rounded overflow-hidden bg-gray-900/40 flex items-center justify-center border border-gray-700">
                                                                    <img id="licdet-salary-img" class="w-16 h-16 object-cover rounded-sm hidden" />
                                                                    <div id="licdet-salary-file" class="w-16 h-16 flex items-center justify-center text-cyan-300 font-bold hidden">ملف</div>
                                                                </div>
                                                                <div class="text-white mt-2 text-xs text-center">شهادة الراتب</div>
                                                                <div class="mt-2 flex gap-2">
                                                                    <a id="licdet-salary-open" class="text-cyan-300 text-xs hidden" target="_blank">فتح</a>
                                                                    <a id="licdet-salary-download" class="text-cyan-300 text-xs hidden" download>تنزيل</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-white font-semibold mb-2">تفاصيل أخرى</p>
                                                    <div class="text-sm text-gray-300 space-y-1">
                                                        <div><span class="text-gray-400">حالة النظام:</span> <span id="licdet-system-status" class="text-white">—</span></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <!-- Approve/payment/reject controls -->
                                            <button type="button" id="licdet-approve" class="px-3 py-2 rounded-md border border-emerald-600 text-emerald-400 bg-gray-900 hover:text-emerald-300 hover:border-emerald-500">اعتماد</button>
                                            <button type="button" id="licdet-pay" class="px-3 py-2 rounded-md border border-emerald-600 text-emerald-600 bg-gray-900 hover:text-emerald-500 hover:border-emerald-500">دفع</button>
                                            <button type="button" id="licdet-reject" class="px-3 py-2 rounded-md border border-red-700 text-red-300 hover:bg-red-700/20">رفض</button>
                                            <button id="licdet-close" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                             <!-- Modal: License Request Pay (separate from any other modal) -->
                            <div id="license-request-modal" class="hidden fixed inset-0 z-60 items-center justify-center bg-black/60" style="z-index:99999;">
                                <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 mx-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-white font-semibold">طلب دفع ترخيص</h4>
                                        <button id="license-request-modal-close" class="text-gray-300">✕</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="text-sm text-gray-300">معرف الترخيص: <span data-licreq-id-display class="text-white">—</span></div>
                                        <div class="text-sm text-gray-300">المالك: <span data-licreq-owner-display class="text-white">—</span></div>
                                        <!-- Highlighted greeting line (bold, larger, golden) -->
                                        <div class="text-center">
                                            <span data-licreq-greeting class="inline-block text-xl font-extrabold text-yellow-400">مرحبًا،</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">المبلغ</label>
                                            <input type="number" step="0.01" min="0" data-licreq-amount class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="0.00" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">ملاحظة (اختياري)</label>
                                            <textarea data-licreq-note rows="4" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="سيتم تعبئة رسالة منسّقة تلقائيًا يمكن تعديلها قبل الإرسال"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-end gap-2">
                                        <input data-licreq-id type="hidden" value="" />
                                        <button id="license-request-modal-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إلغاء</button>
                                        <button id="license-request-modal-confirm" class="px-3 py-2 rounded-md bg-emerald-600 text-black">إرسال الطلب</button>
                                    </div>
                                </div>
                            </div>
                            <script>
                                (function(){
                                    try {
                                        // Centered square success toast with circular progress and checkmark inside
                                        if(!window.__showLicenseSuccessToast){
                                            window.__showLicenseSuccessToast = function(opts){
                                                try {
                                                    var message = (opts && opts.message) || 'تم إرسال طلب دفع الترخيص بنجاح';
                                                    var ownerName = opts && opts.ownerName ? String(opts.ownerName) : '';
                                                    var duration = Number(opts && opts.duration) || 3500;

                                                    // overlay root to center the card
                                                    var root = document.createElement('div');
                                                    root.setAttribute('dir','rtl');
                                                    root.style.position = 'fixed';
                                                    root.style.inset = '0';
                                                    root.style.display = 'flex';
                                                    root.style.alignItems = 'center';
                                                    root.style.justifyContent = 'center';
                                                    root.style.zIndex = '100000';
                                                    root.style.pointerEvents = 'none';

                                                    // card (square)
                                                    var card = document.createElement('div');
                                                    card.className = 'shadow-xl rounded-xl border border-gray-200 bg-white text-black flex flex-col items-center justify-center';
                                                    card.style.width = '240px';
                                                    card.style.height = '240px';
                                                    card.style.padding = '16px';
                                                    card.style.pointerEvents = 'auto';

                                                    // SVG circular progress with checkmark
                                                    var svgNS = 'http://www.w3.org/2000/svg';
                                                    var size = 180, cx = size/2, cy = size/2, r = 75;
                                                    var C = 2 * Math.PI * r;

                                                    var svg = document.createElementNS(svgNS, 'svg');
                                                    svg.setAttribute('width', String(size));
                                                    svg.setAttribute('height', String(size));
                                                    svg.setAttribute('viewBox', '0 0 ' + size + ' ' + size);

                                                    // track circle
                                                    var track = document.createElementNS(svgNS, 'circle');
                                                    track.setAttribute('cx', String(cx));
                                                    track.setAttribute('cy', String(cy));
                                                    track.setAttribute('r', String(r));
                                                    track.setAttribute('fill', 'none');
                                                    track.setAttribute('stroke', '#e5e7eb');
                                                    track.setAttribute('stroke-width', '10');

                                                    // progress circle (animated)
                                                    var prog = document.createElementNS(svgNS, 'circle');
                                                    prog.setAttribute('cx', String(cx));
                                                    prog.setAttribute('cy', String(cy));
                                                    prog.setAttribute('r', String(r));
                                                    prog.setAttribute('fill', 'none');
                                                    prog.setAttribute('stroke', '#10B981');
                                                    prog.setAttribute('stroke-width', '10');
                                                    prog.setAttribute('stroke-linecap', 'round');
                                                    prog.setAttribute('stroke-dasharray', String(C));
                                                    prog.setAttribute('stroke-dashoffset', String(C));
                                                    prog.style.transform = 'rotate(-90deg)';
                                                    prog.style.transformOrigin = cx + 'px ' + cy + 'px';
                                                    prog.style.transition = 'stroke-dashoffset ' + duration + 'ms linear';

                                                    // check mark (centered)
                                                    var check = document.createElementNS(svgNS, 'path');
                                                    check.setAttribute('d', 'M '+(cx-28)+' '+cy+' l 18 18 l 36 -36');
                                                    check.setAttribute('fill', 'none');
                                                    check.setAttribute('stroke', '#10B981');
                                                    check.setAttribute('stroke-width', '10');
                                                    check.setAttribute('stroke-linecap', 'round');
                                                    check.setAttribute('stroke-linejoin', 'round');

                                                    svg.appendChild(track);
                                                    svg.appendChild(prog);
                                                    svg.appendChild(check);

                                                    // Title and description
                                                    var title = document.createElement('div');
                                                    title.textContent = 'تم الإرسال بنجاح';
                                                    title.style.fontWeight = '700';
                                                    title.style.marginTop = '8px';
                                                    var desc = document.createElement('div');
                                                    desc.style.color = '#374151';
                                                    desc.style.fontSize = '14px';
                                                    desc.style.textAlign = 'center';
                                                    desc.style.marginTop = '2px';
                                                    desc.textContent = message;

                                                    // Owner name on its own line in golden color
                                                    var ownerLine = null;
                                                    if(ownerName){
                                                        ownerLine = document.createElement('div');
                                                        ownerLine.style.color = '#FFD700'; // gold
                                                        ownerLine.style.fontWeight = '700';
                                                        ownerLine.style.textAlign = 'center';
                                                        ownerLine.style.marginTop = '4px';
                                                        ownerLine.textContent = 'المالك: ' + ownerName;
                                                    }

                                                    card.appendChild(svg);
                                                    card.appendChild(title);
                                                    card.appendChild(desc);
                                                    if(ownerLine){ card.appendChild(ownerLine); }

                                                    root.appendChild(card);
                                                    document.body.appendChild(root);

                                                    // animate circular progress
                                                    requestAnimationFrame(function(){ prog.setAttribute('stroke-dashoffset', '0'); });

                                                    // auto close after duration
                                                    setTimeout(function(){ try { document.body.removeChild(root); } catch(_){} }, duration + 150);
                                                } catch(err){ try { console.warn('toast error', err); } catch(_){} }
                                            };
                                        }

                                        // Dedicated APPROVAL toast (same design, different default title/message)
                                        if(!window.__showLicenseApprovedToast){
                                            window.__showLicenseApprovedToast = function(opts){
                                                try {
                                                    var message = (opts && opts.message) || 'تم اعتماد الترخيص بنجاح';
                                                    var ownerName = opts && opts.ownerName ? String(opts.ownerName) : '';
                                                    var duration = Number(opts && opts.duration) || 3000;
                                                    var titleText = (opts && opts.title) || 'تم الاعتماد بنجاح';

                                                    // overlay root to center the card
                                                    var root = document.createElement('div');
                                                    root.setAttribute('dir','rtl');
                                                    root.style.position = 'fixed';
                                                    root.style.inset = '0';
                                                    root.style.display = 'flex';
                                                    root.style.alignItems = 'center';
                                                    root.style.justifyContent = 'center';
                                                    root.style.zIndex = '100000';
                                                    root.style.pointerEvents = 'none';

                                                    // card (square)
                                                    var card = document.createElement('div');
                                                    card.className = 'shadow-xl rounded-xl border border-gray-200 bg-white text-black flex flex-col items-center justify-center';
                                                    card.style.width = '240px';
                                                    card.style.height = '240px';
                                                    card.style.padding = '16px';
                                                    card.style.pointerEvents = 'auto';

                                                    // SVG circular progress with checkmark
                                                    var svgNS = 'http://www.w3.org/2000/svg';
                                                    var size = 180, cx = size/2, cy = size/2, r = 75;
                                                    var C = 2 * Math.PI * r;

                                                    var svg = document.createElementNS(svgNS, 'svg');
                                                    svg.setAttribute('width', String(size));
                                                    svg.setAttribute('height', String(size));
                                                    svg.setAttribute('viewBox', '0 0 ' + size + ' ' + size);

                                                    // track circle
                                                    var track = document.createElementNS(svgNS, 'circle');
                                                    track.setAttribute('cx', String(cx));
                                                    track.setAttribute('cy', String(cy));
                                                    track.setAttribute('r', String(r));
                                                    track.setAttribute('fill', 'none');
                                                    track.setAttribute('stroke', '#e5e7eb');
                                                    track.setAttribute('stroke-width', '10');

                                                    // progress circle (animated)
                                                    var prog = document.createElementNS(svgNS, 'circle');
                                                    prog.setAttribute('cx', String(cx));
                                                    prog.setAttribute('cy', String(cy));
                                                    prog.setAttribute('r', String(r));
                                                    prog.setAttribute('fill', 'none');
                                                    prog.setAttribute('stroke', '#10B981');
                                                    prog.setAttribute('stroke-width', '10');
                                                    prog.setAttribute('stroke-linecap', 'round');
                                                    prog.setAttribute('stroke-dasharray', String(C));
                                                    prog.setAttribute('stroke-dashoffset', String(C));
                                                    prog.style.transform = 'rotate(-90deg)';
                                                    prog.style.transformOrigin = cx + 'px ' + cy + 'px';
                                                    prog.style.transition = 'stroke-dashoffset ' + duration + 'ms linear';

                                                    // check mark (centered)
                                                    var check = document.createElementNS(svgNS, 'path');
                                                    check.setAttribute('d', 'M '+(cx-28)+' '+cy+' l 18 18 l 36 -36');
                                                    check.setAttribute('fill', 'none');
                                                    check.setAttribute('stroke', '#10B981');
                                                    check.setAttribute('stroke-width', '10');
                                                    check.setAttribute('stroke-linecap', 'round');
                                                    check.setAttribute('stroke-linejoin', 'round');

                                                    svg.appendChild(track);
                                                    svg.appendChild(prog);
                                                    svg.appendChild(check);

                                                    // Title and description
                                                    var title = document.createElement('div');
                                                    title.textContent = titleText;
                                                    title.style.fontWeight = '700';
                                                    title.style.marginTop = '8px';
                                                    var desc = document.createElement('div');
                                                    desc.style.color = '#374151';
                                                    desc.style.fontSize = '14px';
                                                    desc.style.textAlign = 'center';
                                                    desc.style.marginTop = '2px';
                                                    desc.textContent = message;

                                                    // Owner name on its own line in golden color
                                                    var ownerLine = null;
                                                    if(ownerName){
                                                        ownerLine = document.createElement('div');
                                                        ownerLine.style.color = '#FFD700'; // gold
                                                        ownerLine.style.fontWeight = '700';
                                                        ownerLine.style.textAlign = 'center';
                                                        ownerLine.style.marginTop = '4px';
                                                        ownerLine.textContent = 'المالك: ' + ownerName;
                                                    }

                                                    card.appendChild(svg);
                                                    card.appendChild(title);
                                                    card.appendChild(desc);
                                                    if(ownerLine){ card.appendChild(ownerLine); }

                                                    root.appendChild(card);
                                                    document.body.appendChild(root);

                                                    // animate circular progress
                                                    requestAnimationFrame(function(){ prog.setAttribute('stroke-dashoffset', '0'); });

                                                    // auto close after duration
                                                    setTimeout(function(){ try { document.body.removeChild(root); } catch(_){} }, duration + 150);
                                                } catch(err){ try { console.warn('toast error', err); } catch(_){} }
                                            };
                                        }

                                        // Delegated handler so it works even if the button renders later
                                        if(!window.__licReqDelegatedAttached){
                                            window.__licReqDelegatedAttached = 1;
                                            document.addEventListener('click', function(e){
                                                try {
                                                    var t = e.target;
                                                    var isBtn = t && (t.id === 'licdet-pay');
                                                    if(!isBtn && t && t.closest){
                                                        var maybe = t.closest('#licdet-pay');
                                                        if(maybe) isBtn = true;
                                                    }
                                                    if(!isBtn) return;
                                                    // prevent any default form actions just in case
                                                    try { e.preventDefault(); } catch(_){ }

                                                    var licId = (document.getElementById('licdet-id')?.textContent || '').trim();
                                                    var ownerName = (document.getElementById('licdet-owner-name')?.textContent || '').trim();
                                                    var modal = document.getElementById('license-request-modal');
                                                    if(!modal) return;

                                                    var idDisp = modal.querySelector('[data-licreq-id-display]');
                                                    var ownerDisp = modal.querySelector('[data-licreq-owner-display]');
                                                    var greetEl = modal.querySelector('[data-licreq-greeting]');
                                                    var idInput = modal.querySelector('[data-licreq-id]');
                                                    var amtInput = modal.querySelector('[data-licreq-amount]');
                                                    var noteInput = modal.querySelector('[data-licreq-note]');

                                                    if(idDisp) idDisp.textContent = licId || '—';
                                                    if(ownerDisp) ownerDisp.textContent = ownerName || '—';
                                                    // Set highlighted greeting text (bold, larger, golden)
                                                    try {
                                                        var greetName = ownerName && ownerName !== '—' ? ownerName : '';
                                                        var greetText = greetName ? ('مرحبًا ' + greetName + '،') : 'مرحبًا،';
                                                        if(greetEl) greetEl.textContent = greetText;
                                                    } catch(_){ }
                                                    if(idInput) idInput.value = licId || '';
                                                    if(amtInput) amtInput.value = '';
                                                    // Prefill a formatted Arabic message specific to LICENSE (not investment)
                                                    if(noteInput){
                                                        var greetName = ownerName && ownerName !== '—' ? ownerName : '';
                                                        var greet = greetName ? ('مرحبًا ' + greetName + '،') : 'مرحبًا،';
                                                        var defaultNote = [
                                                            greet,
                                                            'نذكّركم بوجود دفعة مستحقة لترخيصكم.',
                                                            'نرجو تسديد الرسوم وإرفاق إيصال التحويل لاستكمال الإجراءات.',
                                                            'شكرًا لتعاونكم.'
                                                        ].join('\n');
                                                        noteInput.value = defaultNote;
                                                    }

                                                    modal.classList.remove('hidden');
                                                    modal.classList.add('flex');
                                                    // focus amount input for convenience
                                                    try { if(amtInput){ setTimeout(function(){ amtInput.focus(); }, 0); } } catch(_){ }
                                                } catch(err){ console.error('open license-request modal error', err); }
                                            }, true);
                                        }

                                        function closeLicReq(){
                                            var modal = document.getElementById('license-request-modal');
                                            if(!modal) return;
                                            modal.classList.remove('flex');
                                            modal.classList.add('hidden');
                                        }

                                        // Close buttons
                                        ['license-request-modal-close','license-request-modal-cancel'].forEach(function(id){
                                            try {
                                                var el = document.getElementById(id);
                                                if(!el) return;
                                                if(el.dataset.licReqCloseAttached) return;
                                                el.dataset.licReqCloseAttached = '1';
                                                el.addEventListener('click', function(e){ e.preventDefault(); closeLicReq(); });
                                            } catch(_){ }
                                        });

                                        // Backdrop click
                                        try {
                                            var backdrop = document.getElementById('license-request-modal');
                                            if(backdrop && !backdrop.dataset.licReqBackdrop){
                                                backdrop.dataset.licReqBackdrop = '1';
                                                backdrop.addEventListener('click', function(e){ if(e.target === backdrop) closeLicReq(); });
                                            }
                                        } catch(_){ }

                                        // ESC to close
                                        try {
                                            if(!window.__licReqEsc){
                                                window.__licReqEsc = 1;
                                                document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeLicReq(); });
                                            }
                                        } catch(_){ }

                                        // Confirm: send reminder to backend then close
                                        try {
                                            var confirmBtn = document.getElementById('license-request-modal-confirm');
                                            if(confirmBtn && !confirmBtn.dataset.licReqConfirm){
                                                confirmBtn.dataset.licReqConfirm = '1';
                                                confirmBtn.addEventListener('click', function(e){
                                                    try { e.preventDefault(); } catch(_){ }
                                                    var modal = document.getElementById('license-request-modal');
                                                    if(!modal){ return; }
                                                    var idInput = modal.querySelector('[data-licreq-id]');
                                                    var amtInput = modal.querySelector('[data-licreq-amount]');
                                                    var noteInput = modal.querySelector('[data-licreq-note]');
                                                    var id = (idInput && idInput.value) ? idInput.value.trim() : '';
                                                    if(!id){ return alert('معرّف الترخيص مفقود'); }
                                                    var amount = amtInput ? (amtInput.value || '') : '';
                                                    var note = noteInput ? (noteInput.value || '') : '';
                                                    var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));

                                                    // Build params
                                                    var params = 'amount=' + encodeURIComponent(amount) + '&note=' + encodeURIComponent(note) + '&_token=' + encodeURIComponent(token);

                                                    // Send POST to license notify-payment endpoint
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.open('POST', '/api/admin/licenses/' + encodeURIComponent(id) + '/notify-payment', true);
                                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                                                    try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(_){ }

                                                    confirmBtn.disabled = true;
                                                    xhr.onload = function(){
                                                        confirmBtn.disabled = false;
                                                        var ok = false, msg = '';
                                                        try { var res = JSON.parse(xhr.responseText || '{}'); ok = !!res.ok; msg = res.message || ''; } catch(_){ }
                                                        if(xhr.status >= 200 && xhr.status < 300 && ok){
                                                            try {
                                                                var ownerNm = (document.getElementById('licdet-owner-name')?.textContent || '').trim();
                                                                if(window.__showLicenseSuccessToast){
                                                                    window.__showLicenseSuccessToast({ ownerName: ownerNm, message: msg || 'تم إرسال طلب دفع الترخيص بنجاح', duration: 3500 });
                                                                } else {
                                                                    alert((msg || 'تم إرسال طلب دفع الترخيص بنجاح') + (ownerNm ? (' للمالك: ' + ownerNm) : ''));
                                                                }
                                                            } catch(_){ }
                                                            closeLicReq();
                                                        } else {
                                                            try { alert(msg || 'فشل إرسال التذكير'); } catch(_){ }
                                                        }
                                                    };
                                                    xhr.onerror = function(){
                                                        confirmBtn.disabled = false;
                                                        try { alert('فشل في الاتصال'); } catch(_){ }
                                                    };
                                                    xhr.send(params);
                                                });
                                            }
                                        } catch(_){ }
                                    } catch(err){ console.error('license-request modal wiring error', err); }
                                })();
                            </script>

                            <!-- Modal: License Reject -->
                            <div id="license-reject-modal" class="hidden fixed inset-0 z-60 items-center justify-center bg-black/60" style="z-index:99999;">
                                <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 mx-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-white font-semibold">رفض طلب الترخيص</h4>
                                        <button id="license-reject-modal-close" class="text-gray-300">✕</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="text-sm text-gray-300">معرف الترخيص: <span data-licrej-id-display class="text-white">—</span></div>
                                        <div class="text-sm text-gray-300">المالك: <span data-licrej-owner-display class="text-white">—</span></div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">سبب الرفض (اختياري)</label>
                                            <textarea data-licrej-reason rows="4" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="أدخل سبب الرفض (اختياري)"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-end gap-2">
                                        <input data-licrej-id type="hidden" value="" />
                                        <button id="license-reject-modal-cancel" class="px-3 py-2 rounded-md border border-gray-600 text-gray-200">إلغاء</button>
                                        <button id="license-reject-modal-confirm" class="px-3 py-2 rounded-md border border-red-700 text-red-300 hover:bg-red-700/20">تأكيد الرفض</button>
                                    </div>
                                </div>
                            </div>

                            <script>
                                (function(){
                                    try {
                                        function licRejClose(){
                                            var modal = document.getElementById('license-reject-modal');
                                            if(!modal) return;
                                            modal.classList.remove('flex');
                                            modal.classList.add('hidden');
                                        }

                                        // Open modal on clicking Reject button
                                        if(!window.__licRejOpenDelegated){
                                            window.__licRejOpenDelegated = 1;
                                            document.addEventListener('click', function(e){
                                                var t = e.target;
                                                if(!t) return;
                                                var isBtn = (t.id === 'licdet-reject') || (t.closest && t.closest('#licdet-reject'));
                                                if(!isBtn) return;
                                                try { e.preventDefault(); } catch(_){ }

                                                var licId = (document.getElementById('licdet-id')?.textContent || '').trim();
                                                var ownerName = (document.getElementById('licdet-owner-name')?.textContent || '').trim();
                                                var modal = document.getElementById('license-reject-modal');
                                                if(!modal) return;

                                                var idDisp = modal.querySelector('[data-licrej-id-display]');
                                                var ownerDisp = modal.querySelector('[data-licrej-owner-display]');
                                                var idInput = modal.querySelector('[data-licrej-id]');
                                                var reasonInput = modal.querySelector('[data-licrej-reason]');
                                                if(idDisp) idDisp.textContent = licId || '—';
                                                if(ownerDisp) ownerDisp.textContent = ownerName || '—';
                                                if(idInput) idInput.value = licId || '';
                                                if(reasonInput) reasonInput.value = '';

                                                modal.classList.remove('hidden');
                                                modal.classList.add('flex');
                                                try { if(reasonInput){ setTimeout(function(){ reasonInput.focus(); }, 0); } } catch(_){ }
                                            }, true);
                                        }

                                        // Close buttons
                                        ['license-reject-modal-close','license-reject-modal-cancel'].forEach(function(id){
                                            try {
                                                var el = document.getElementById(id);
                                                if(!el || el.dataset.licRejCloseAttached) return;
                                                el.dataset.licRejCloseAttached = '1';
                                                el.addEventListener('click', function(e){ try { e.preventDefault(); } catch(_){ } licRejClose(); });
                                            } catch(_){ }
                                        });

                                        // Backdrop click
                                        try {
                                            var backdrop = document.getElementById('license-reject-modal');
                                            if(backdrop && !backdrop.dataset.licRejBackdrop){
                                                backdrop.dataset.licRejBackdrop = '1';
                                                backdrop.addEventListener('click', function(e){ if(e.target === backdrop) licRejClose(); });
                                            }
                                        } catch(_){ }

                                        // ESC to close
                                        try {
                                            if(!window.__licRejEsc){
                                                window.__licRejEsc = 1;
                                                document.addEventListener('keydown', function(e){ if(e.key === 'Escape') licRejClose(); });
                                            }
                                        } catch(_){ }

                                        // Confirm reject -> POST
                                        try {
                                            var confirmBtn = document.getElementById('license-reject-modal-confirm');
                                            if(confirmBtn && !confirmBtn.dataset.licRejConfirm){
                                                confirmBtn.dataset.licRejConfirm = '1';
                                                confirmBtn.addEventListener('click', function(e){
                                                    try { e.preventDefault(); } catch(_){ }
                                                    var modal = document.getElementById('license-reject-modal');
                                                    if(!modal) return;
                                                    var idInput = modal.querySelector('[data-licrej-id]');
                                                    var reasonInput = modal.querySelector('[data-licrej-reason]');
                                                    var id = (idInput && idInput.value) ? idInput.value.trim() : '';
                                                    if(!id){ return alert('معرّف الترخيص مفقود'); }
                                                    var reason = reasonInput ? (reasonInput.value || '') : '';
                                                    var note = reason ? ('تم رفض الطلب - السبب: ' + reason) : 'تم رفض الطلب';
                                                    var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));

                                                    var params = 'note=' + encodeURIComponent(note) + '&highlight=1&_token=' + encodeURIComponent(token);
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.open('POST', '/api/admin/licenses/' + encodeURIComponent(id) + '/notify-rejection', true);
                                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                                                    try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(_){ }

                                                    confirmBtn.disabled = true;
                                                    xhr.onload = function(){
                                                        confirmBtn.disabled = false;
                                                        var ok = (xhr.status >= 200 && xhr.status < 300);
                                                        var res = {};
                                                        try { res = JSON.parse(xhr.responseText || '{}'); } catch(_){ res = {}; }
                                                        if(ok){
                                                            try {
                                                                var ownerNm = (document.getElementById('licdet-owner-name')?.textContent || '').trim();
                                                                if(window.__showLicenseSuccessToast){
                                                                    window.__showLicenseSuccessToast({ title:'تم رفض الترخيص', message: ownerNm ? ('تم إرسال إشعار الرفض إلى ' + ownerNm) : 'تم إرسال إشعار الرفض لمالك الترخيص', duration: 3500 });
                                                                } else {
                                                                    alert('تم رفض الترخيص وإشعار المالك');
                                                                }
                                                            } catch(_){ }

                                                            // Update status label in the license detail modal
                                                            try {
                                                                var newStatus = (res && res.status) ? res.status : 'rejected';
                                                                var lbl = (typeof statusLabel === 'function') ? statusLabel(newStatus) : newStatus;
                                                                var cls = (typeof statusTextClass === 'function') ? statusTextClass(newStatus) : '';
                                                                var el = document.getElementById('licdet-status');
                                                                if (el) {
                                                                    el.textContent = lbl || '—';
                                                                    try { el.classList.remove('text-emerald-400','text-red-400','text-yellow-300','text-gray-300'); if(cls) el.classList.add(cls); } catch(_){ }
                                                                }
                                                            } catch(_){ }

                                                            // Update badge in list card
                                                            try {
                                                                var id = (document.querySelector('#license-reject-modal [data-licrej-id]')?.value || '').trim();
                                                                if (id) {
                                                                    var card = document.querySelector('[data-inv-id="' + id + '"]');
                                                                    if (card) {
                                                                        var badge = card.querySelector('span.inline-flex.items-center');
                                                                        var cfg = (typeof toBadge === 'function') ? toBadge('rejected') : {cls:'bg-red-500 text-white', text:'مرفوض', icon:'alert-circle'};
                                                                        if (badge) {
                                                                            badge.className = 'inline-flex items-center gap-1 rounded-md ' + cfg.cls + ' text-xs font-semibold px-2.5 py-1.5';
                                                                            badge.innerHTML = '<i data-lucide="' + cfg.icon + '" class="w-3 h-3"></i>' + cfg.text;
                                                                            if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
                                                                        }
                                                                    }
                                                                }
                                                            } catch(_){ }
                                                            licRejClose();
                                                        } else {
                                                            try { alert('فشل إرسال الرفض'); } catch(_){ }
                                                        }
                                                    };
                                                    xhr.onerror = function(){ confirmBtn.disabled = false; try { alert('فشل في الاتصال'); } catch(_){ } };
                                                    xhr.send(params);
                                                });
                                            }
                                        } catch(_){ }
                                    } catch(err){ console.error('license-reject modal wiring error', err); }
                                })();
                            </script>

                            <!-- Branding (Platform Appearance) Tab -->
                            <div id="tab-branding" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">مظهر المنصة</h3>
                                        <div class="flex items-center gap-2">
                                        <button id="save-branding"
                                         class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-md px-3 py-2">حفظ
                                         المظهر</button>
                                        
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <p class="text-white font-semibold mb-3">هوية المنصة</p>
                                        <label class="block text-sm text-gray-300 mb-1">رابط الشعار (Logo URL)</label>
                                        <input id="brand-logo-url"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3"
                                            placeholder="https://.../logo.png" />
                                        <label class="block text-sm text-gray-300 mb-1">نص تذييل الموقع</label>
                                        <textarea id="brand-footer-text" rows="3"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="حقوق النشر ..."></textarea>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <p class="text-white font-semibold mb-3">روابط التواصل الاجتماعي</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Twitter/X</label>
                                                <input id="social-twitter"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="https://x.com/..." />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Instagram</label>
                                                <input id="social-instagram"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="https://instagram.com/..." />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">LinkedIn</label>
                                                <input id="social-linkedin"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="https://linkedin.com/company/..." />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Facebook</label>
                                                <input id="social-facebook"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="https://facebook.com/..." />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">YouTube</label>
                                                <input id="social-youtube"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="https://youtube.com/@..." />
                                            </div>
                                        </div>
                                        <p id="branding-status" class="text-sm text-gray-300 mt-3"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Page: Bank Transfer Settings (moved) -->
                            <div id="tab-bank" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة التحويل البنكي</h3>
                                    <div>
                                        <button id="bank-save" class="bg-green-600 hover:bg-green-700 text-white text-sm rounded-md px-3 py-2">حفظ إعدادات التحويل</button>
                                    </div>
                                </div>

                                <!-- Bank Transfer Settings (moved from Settings) -->
                                <div class="mb-4">
                                    <input type="text" id="bank-search" placeholder="ابحث باسم الحساب أو رقم الحساب"
                                           class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                    <div id="bank-results">
                                        <div class="text-gray-400 text-sm">جارٍ التحميل...</div>
                                    </div>
                                    <script>
                                        (function(){
                                            var container = document.getElementById('bank-results');
                                            if(!container) return;
                                            function renderTable(rows){
                                                var cols = ['id','account_name','bank_name','account_number','iban','swift','reference_code','instructions','ask_sender','ask_datetime','require_receipt','is_active','created_by','created_at','updated_at'];
                                                var tbl = document.createElement('table'); tbl.className = 'w-full text-sm text-left text-gray-200';
                                                var thead = document.createElement('thead'); thead.className = 'text-xs uppercase text-gray-400 bg-gray-900/30';
                                                var thr = document.createElement('tr');
                                                cols.forEach(function(c){ var th = document.createElement('th'); th.className = 'px-3 py-2'; th.textContent = c; thr.appendChild(th); });
                                                thead.appendChild(thr); tbl.appendChild(thead);
                                                var tbody = document.createElement('tbody'); tbody.className = 'divide-y divide-gray-700';
                                                if(!rows || rows.length===0){ var tr = document.createElement('tr'); var td = document.createElement('td'); td.className='px-3 py-6 text-center text-gray-400'; td.colSpan=cols.length; td.textContent='لا توجد سجلات'; tr.appendChild(td); tbody.appendChild(tr); }
                                                else {
                                                    rows.forEach(function(r){ var tr = document.createElement('tr'); cols.forEach(function(c){ var td = document.createElement('td'); td.className='px-3 py-2'; var v = (r[c] === null || r[c] === undefined) ? '' : r[c]; td.textContent = v; tr.appendChild(td); }); tbody.appendChild(tr); });
                                                }
                                                tbl.appendChild(tbody);
                                                container.innerHTML = ''; container.appendChild(tbl);
                                            }

                                            fetch('/api/admin/bank-accounts', { credentials: 'same-origin' })
                                            .then(function(res){ return res.json(); })
                                            .then(function(json){ if(json && json.data) renderTable(json.data); else container.innerHTML = '<div class="text-gray-400">لا توجد بيانات</div>'; })
                                            .catch(function(err){ container.innerHTML = '<div class="text-red-400">خطأ في جلب البيانات</div>'; console.error(err); });
                                        })();
                                    </script>
                                </div>
                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                    <p class="text-white font-semibold mb-3">معلومات التحويل البنكي</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">اسم الحساب</label>
                                            <input id="set-bank-account-name"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">البنك</label>
                                            <input id="set-bank-name"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">رقم الحساب</label>
                                            <input id="set-bank-account-number"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">IBAN</label>
                                            <input id="set-bank-iban"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">SWIFT / BIC</label>
                                            <input id="set-bank-swift"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1">رمز مرجع الإيداع (اختياري)</label>
                                            <input id="set-bank-reference-code" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="مثال: REF-12345" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm text-gray-300 mb-1">تعليمات التحويل (ستظهر
                                                للمستخدم)</label>
                                            <textarea id="set-bank-instructions" rows="3"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                        <label class="inline-flex items-center text-white text-sm">
                                            <input type="checkbox" id="bank-ask-sender" class="mr-2"> طلب اسم
                                            المُحوِّل
                                        </label>
                                        <label class="inline-flex items-center text-white text-sm">
                                            <input type="checkbox" id="bank-ask-datetime" class="mr-2"> طلب
                                            تاريخ/وقت التحويل
                                        </label>
                                        <label class="inline-flex items-center text-white text-sm">
                                            <input type="checkbox" id="bank-require-receipt" class="mr-2"> إلزام
                                            إرفاق إيصال التحويل
                                        </label>
                                    </div>
                                </div>
                            </div>

                           

                            <!-- Documents Tab -->
                            <div id="tab-documents" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إدارة مستندات المستخدمين</h3>
                                    <span id="documents-count"
                                        class="inline-flex items-center rounded-md bg-sky-600 text-white text-xs font-semibold px-2.5 py-1.5">0
                                        مستند</span>
                                </div>
                                <div class="mb-4">
                                    <input type="text" id="documents-search" placeholder="ابحث باسم الملف أو النوع أو المعرّف"
                                           class="border p-2 rounded w-full mb-2 text-sm bg-gray-900/60 text-white" />
                                    <div id="documents-results">@includeIf('admin.partials.documents_table', ['documents' => []])</div>
                                </div>
                                <div id="documents-list" class="space-y-3 max-h-[60vh] overflow-y-auto"></div>

                                <!-- Modal: Document Preview -->
                                <div id="doc-modal"
                                    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div
                                        class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-4xl p-4 max-h-[92vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="doc-modal-title" class="text-white font-semibold">عرض المستند</h4>
                                            <button id="doc-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                            <div class="lg:col-span-1 space-y-2">
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-gray-400 text-xs">اسم الملف</p>
                                                    <p id="doc-filename" class="text-white font-semibold break-all">-</p>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-gray-400 text-xs">النوع</p>
                                                    <p id="doc-type" class="text-white">-</p>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-gray-400 text-xs">المستخدم</p>
                                                    <p id="doc-user" class="text-white text-sm">-</p>
                                                </div>
                                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                                                    <p class="text-gray-400 text-xs">التاريخ</p>
                                                    <p id="doc-date" class="text-white text-sm">-</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a id="doc-download" href="#" target="_blank" rel="noopener"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md px-3 py-2 disabled:opacity-50">فتح
                                                        في تبويب</a>
                                                    <button id="doc-modal-cancel"
                                                        class="border border-gray-600 text-gray-200 rounded-md px-3 py-2 text-sm">إغلاق</button>
                                                </div>
                                            </div>
                                            <div class="lg:col-span-2">
                                                <div id="doc-preview"
                                                    class="bg-gray-800/50 border border-gray-700 rounded-md p-3 flex items-center justify-center min-h-[300px]">
                                                    <p class="text-gray-400">لا توجد معاينة متاحة</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page: Market Insights -->
                            <div id="tab-page-market" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">مركز رؤى السوق (صفحة المستثمر)</h3>
                                    <!-- Desktop buttons -->
                                    <div class="hidden md:flex items-center gap-2 flex-wrap">
                                        <button id="mi-add-indicator"
                                            class="border border-cyan-500/50 text-cyan-300 hover:bg-cyan-500/10 text-sm rounded-md px-3 py-2">إضافة
                                            مؤشر</button>
                                        <button id="mi-add-sector"
                                            class="border border-green-500/50 text-green-300 hover:bg-green-500/10 text-sm rounded-md px-3 py-2">إضافة
                                            قطاع</button>
                                        <button id="mi-add-news"
                                            class="border border-blue-500/50 text-blue-300 hover:bg-blue-500/10 text-sm rounded-md px-3 py-2">إضافة
                                            خبر</button>
                                        <button id="mi-add-trend"
                                            class="border border-yellow-500/50 text-yellow-300 hover:bg-yellow-500/10 text-sm rounded-md px-3 py-2">إضافة
                                            اتجاه</button>
                                    </div>
                                    <!-- Mobile condensed menu -->
                                    <div class="md:hidden relative">
                                        <button id="mi-menu-toggle"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md px-3 py-2 flex items-center gap-2">
                                            <i data-lucide="plus" class="w-4 h-4"></i> إضافة
                                        </button>
                                        <div id="mi-menu"
                                            class="hidden absolute left-0 mt-2 w-48 bg-gray-900 border border-gray-700 rounded-md shadow-lg z-50">
                                            <button
                                                class="mi-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="indicator">إضافة مؤشر</button>
                                            <button
                                                class="mi-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="sector">إضافة قطاع</button>
                                            <button
                                                class="mi-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="news">إضافة خبر</button>
                                            <button
                                                class="mi-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="trend">إضافة اتجاه</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">المؤشرات</p>
                                            <button data-type="indicator" class="mi-add border border-cyan-500/50 text-cyan-300 hover:bg-cyan-500/10 text-xs rounded-md px-2 py-1">إضافة</button>
                                        </div>
                                        <div id="mi-indicators-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">أفضل القطاعات</p>
                                            <button data-type="sector" class="mi-add border border-green-500/50 text-green-300 hover:bg-green-500/10 text-xs rounded-md px-2 py-1">إضافة</button>
                                        </div>
                                        <div id="mi-sectors-list" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">الأخبار الحديثة</p>
                                            <button data-type="news" class="mi-add border border-blue-500/50 text-blue-300 hover:bg-blue-500/10 text-xs rounded-md px-2 py-1">إضافة</button>
                                        </div>
                                        <div id="mi-news-list" class="grid grid-cols-1 gap-3"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">اتجاهات الاستثمار</p>
                                            <button data-type="trend" class="mi-add border border-yellow-500/50 text-yellow-300 hover:bg-yellow-500/10 text-xs rounded-md px-2 py-1">إضافة</button>
                                        </div>
                                        <div id="mi-trends-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
                                    </div>
                                </div>

                                <!-- Modal: Market Insights (dynamic fields) -->
                                <div id="mi-modal"
                                    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div
                                        class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-2xl p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 id="mi-modal-title" class="text-white font-semibold">إضافة</h4>
                                            <button id="mi-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div id="mi-form" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button id="mi-modal-cancel"
                                                class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                                            <button id="mi-modal-save"
                                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-md px-3 py-2">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    /* Admin MI cards: make them responsive and allow long text to wrap vertically */
                                    .mi-card { width:100%; box-sizing:border-box; overflow-wrap:anywhere; word-break:break-word; }
                                    .mi-card .mi-summary { color:#cbd5e1; margin-bottom:8px; white-space:normal; overflow-wrap:anywhere; word-break:break-word; }
                                    /* Ensure grid gap and card sizing behave on small screens */
                                    @media (max-width:640px) {
                                        #mi-indicators-list, #mi-sectors-list, #mi-news-list, #mi-trends-list { grid-template-columns: 1fr !important; }
                                    }
                                </style>
                            </div>

                            <!-- Page: Success Stories -->
                            <div id="tab-page-success" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">قصص النجاح (صفحة المستثمر)</h3>
                                    <div class="flex items-center gap-2">
                                        <button id="ss-open-modal"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            قصة نجاح</button>
                                        <button id="ss-save-all"
                                            class="border border-emerald-500/50 text-emerald-300 hover:bg-emerald-500/10 text-sm rounded-md px-3 py-2">حفظ
                                            الكل</button>
                                    </div>
                                </div>

                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                    <p class="text-white font-semibold mb-2">أبرز القصص</p>
                                    <div id="ss-top-list" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                                </div>
                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-white font-semibold">جميع القصص</p>
                                        <span id="ss-count"
                                            class="inline-flex items-center rounded-md bg-sky-700/40 text-white text-xs font-semibold px-2.5 py-1.5"></span>
                                    </div>
                                    <div id="ss-stories-list"
                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
                                </div>

                                <!-- Modal: Success Story -->
                                <div id="ss-modal"
                                    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div
                                        class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-3xl p-4 max-h-[92vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-white font-semibold">إضافة/تعديل قصة نجاح</h4>
                                            <button id="ss-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الاسم</label>
                                                <input id="ss-name"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الرمز (avatar)</label>
                                                <input id="ss-avatar"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">المدينة</label>
                                                <input id="ss-location"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">مبلغ الاستثمار</label>
                                                <input id="ss-amount"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="مثال: 15,000 ريال" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">العوائد</label>
                                                <input id="ss-returns"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="مثال: 5,250 ريال" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">المدة</label>
                                                <input id="ss-duration"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="مثال: شهر واحد" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">نسبة الربح</label>
                                                <input id="ss-profit"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"
                                                    placeholder="مثال: 35%" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الفئة</label>
                                                <select id="ss-category"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white">
                                                    <option value="small">small</option>
                                                    <option value="large">large</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end gap-3">
                                                <label class="inline-flex items-center text-sm text-white"><input
                                                        type="checkbox" id="ss-featured" class="mr-2"> مميز</label>
                                                <label class="inline-flex items-center text-sm text-white"><input
                                                        type="checkbox" id="ss-istop" class="mr-2"> ضمن الأبرز</label>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm text-gray-300 mb-1">القصة</label>
                                                <textarea id="ss-story" rows="3"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm text-gray-300 mb-1">الشهادة
                                                    (Testimonial)</label>
                                                <textarea id="ss-testimonial" rows="2"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button id="ss-modal-cancel"
                                                class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                                            <button id="ss-modal-save"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-md px-3 py-2">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page: Trust & Compliance -->
                            <div id="tab-page-trust" class="tab-panel space-y-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">مركز الثقة والامتثال (صفحة المستثمر)</h3>
                                    <!-- Desktop buttons -->
                                    <div class="hidden md:flex items-center gap-2 flex-wrap">
                                        <!-- Use filled green style (like Success Stories) to make Add buttons consistent -->
                                        <button id="tc-add-reg"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            ترخيص/لائحة</button>
                                        <button id="tc-add-sec"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            ميزة أمان</button>
                                        <button id="tc-add-risk"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            مستوى مخاطر</button>
                                        <button id="tc-add-cert"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            شهادة</button>
                                        <button id="tc-add-audit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-2">إضافة
                                            تقرير مراجعة</button>
                                    </div>
                                    <!-- Mobile condensed menu -->
                                    <div class="md:hidden relative">
                                        <button id="tc-menu-toggle"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md px-3 py-2 flex items-center gap-2">
                                            <i data-lucide="plus" class="w-4 h-4"></i> إضافة
                                        </button>
                                        <div id="tc-menu"
                                            class="hidden absolute left-0 mt-2 w-56 bg-gray-900 border border-gray-700 rounded-md shadow-lg z-50">
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="reg">إضافة ترخيص/لائحة</button>
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="sec">إضافة ميزة أمان</button>
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="risk">إضافة مستوى مخاطر</button>
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="cert">إضافة شهادة</button>
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="audit">إضافة تقرير مراجعة</button>
                                            <button
                                                class="tc-menu-item w-full text-right px-3 py-2 text-sm text-white hover:bg-gray-800"
                                                data-type="metric">إضافة مؤشر</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    {{-- partial removed: using legacy layout only --}}
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">اللوائح والتراخيص</p>
                                            <button class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="reg">إضافة</button>
                                        </div>
                                        <div id="tc-reg-list" class="space-y-2"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">ميزات الأمان</p>
                                            <button class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="sec">إضافة</button>
                                        </div>
                                        <div id="tc-sec-list" class="space-y-2"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">مستويات المخاطر</p>
                                            <button class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="risk">إضافة</button>
                                        </div>
                                        <div id="tc-risk-list" class="space-y-2"></div>
                                    </div>
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">الشهادات</p>
                                            <button class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="cert">إضافة</button>
                                        </div>
                                        <div id="tc-cert-list" class="space-y-2"></div>
                                    </div>
                                    <!-- Metrics panel (client-side only UI) -->
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">المؤشرات</p>
                                            <button id="tc-metrics-add" class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="metric">إضافة</button>
                                        </div>
                                        <div id="tc-metrics-list" class="space-y-2"></div>
                                    </div>

                                    <!-- Modal: Metrics (client-side only) -->
                                    <div id="metrics-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                        <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-md p-4 max-h-[90vh] overflow-y-auto mx-3">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-white font-semibold">إضافة/تعديل مؤشر</h4>
                                                <button id="metrics-modal-close" class="text-gray-300">✕</button>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-sm text-gray-300">المفتاح (مقروء برمجياً)</label>
                                                    <input id="metrics-key" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                                </div>
                                                <div>
                                                    <label class="text-sm text-gray-300">التسمية (ظاهر)</label>
                                                    <input id="metrics-label" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                                </div>

                                                <div>
                                                    <label class="text-sm text-gray-300">القيمة</label>
                                                    <input id="metrics-value" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                                </div>
                                                <div>
                                                    <label class="text-sm text-gray-300">الأيقونة ()</label>
                                                    <input id="metrics-icon" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="text-sm text-gray-300">بيانات إضافية (JSON)</label>
                                                    <textarea id="metrics-meta" rows="3" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-end gap-2 mt-3">
                                                <button id="metrics-modal-cancel" class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                                                <button id="metrics-modal-save" class="bg-blue-600 hover:bg-blue-700 text-white rounded-md px-3 py-2">حفظ</button>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        (function(){
                                            // Simple client-side metrics UI: stores in-memory only (no server calls)
                                            window.__PAGES = window.__PAGES || {};
                                            window.__PAGES.trust_compliance = window.__PAGES.trust_compliance || {};
                                            window.__PAGES.trust_compliance.metrics = window.__PAGES.trust_compliance.metrics || [];

                                            function renderMetrics(){
                                                var container = document.getElementById('tc-metrics-list');
                                                if(!container) return;
                                                var items = window.__PAGES.trust_compliance.metrics || [];
                                                if(items.length === 0){ container.innerHTML = '<div class="text-gray-400">لا توجد سجلات.</div>'; return; }
                                                var html = '';
                                                items.forEach(function(m, idx){
                                                    var label = m.label || m.key || ('مؤشر #' + (idx+1));
                                                    html += '<div class="p-3 bg-gray-900/40 border border-gray-700 rounded flex items-center justify-between">'
                                                        + '<div>'
                                                            + '<div class="text-sm text-gray-400">'+escapeHtml(label)+'</div>'
                                                            + (m.value ? ('<div class="text-sm text-gray-200 font-bold">'+escapeHtml(m.value)+'</div>') : '')
                                                        + '</div>'
                                                        + '<div class="flex items-center gap-2">'
                                                        + '<button data-idx="'+idx+'" class="metrics-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs">تعديل</button>'
                                                        + '<button data-idx="'+idx+'" class="metrics-delete border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs">حذف</button>'
                                                        + '</div></div>';
                                                });
                                                container.innerHTML = html;
                                            }

                                            function escapeHtml(s){ if(!s && s!==0) return ''; return String(s).replace(/[&<>"'`]/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;","`":"&#96;"})[c]; }); }

                                            function openModal(mode, idx){
                                                var modal = document.getElementById('metrics-modal');
                                                var keyEl = document.getElementById('metrics-key');
                                                    var labelEl = document.getElementById('metrics-label');
                                                    var valueEl = document.getElementById('metrics-value');
                                                    var iconEl = document.getElementById('metrics-icon');
                                                    var metaEl = document.getElementById('metrics-meta');
                                                modal.classList.remove('hidden'); modal.classList.add('flex');
                                                modal.dataset.mode = mode;
                                                modal.dataset.idx = (typeof idx === 'number') ? idx : '';
                                                if(mode === 'edit' && typeof idx === 'number'){
                                                    var item = window.__PAGES.trust_compliance.metrics[idx] || {};
                                                    keyEl.value = item.key || '';
                                                    labelEl.value = item.label || '';
                                                    valueEl.value = item.value || '';
                                                    iconEl.value = item.icon || '';
                                                    try{ metaEl.value = item.meta ? (typeof item.meta === 'object' ? JSON.stringify(item.meta) : item.meta) : ''; }catch(e){ metaEl.value = '' }
                                                } else { keyEl.value = ''; labelEl.value = ''; valueEl.value = ''; iconEl.value = ''; metaEl.value = ''; }
                                                setTimeout(function(){ keyEl.focus(); },50);
                                            }

                                            function closeModal(){ var modal = document.getElementById('metrics-modal'); if(!modal) return; modal.classList.add('hidden'); modal.classList.remove('flex'); modal.dataset.mode = ''; modal.dataset.idx = ''; }

                                            document.addEventListener('click', function(e){
                                                var t = e.target;
                                                if(t && t.id === 'tc-metrics-add'){ e.preventDefault(); try{ e.stopPropagation(); }catch(e){} openModal('create'); }
                                                if(t && t.id === 'metrics-modal-close') closeModal();
                                                if(t && t.id === 'metrics-modal-cancel') closeModal();
                                                if(t && t.classList && t.classList.contains('metrics-edit')){
                                                    var idx = parseInt(t.getAttribute('data-idx'),10); openModal('edit', idx);
                                                }
                                                if(t && t.classList && t.classList.contains('metrics-delete')){
                                                    var idx = parseInt(t.getAttribute('data-idx'),10);
                                                    if(!confirm('هل تريد حذف هذا المؤشر؟')) return;
                                                    window.__PAGES.trust_compliance.metrics.splice(idx,1);
                                                    renderMetrics();
                                                }
                                            }, true);

                                            document.getElementById('metrics-modal-save').addEventListener('click', function(){
                                                var modal = document.getElementById('metrics-modal');
                                                var mode = modal.dataset.mode || 'create';
                                                var idx = modal.dataset.idx !== '' ? parseInt(modal.dataset.idx,10) : -1;
                                                var key = document.getElementById('metrics-key').value.trim();
                                                var label = document.getElementById('metrics-label').value.trim();
                                                var value = document.getElementById('metrics-value').value.trim();
                                                var icon = document.getElementById('metrics-icon').value.trim();
                                                var meta = document.getElementById('metrics-meta').value.trim();
                                                if(!label){ alert('التسمية مطلوبة'); return; }
                                                // ensure items array exists
                                                var items = window.__PAGES.trust_compliance.metrics = window.__PAGES.trust_compliance.metrics || [];

                                                // Normalize value: keep '%' if provided, otherwise keep numeric string
                                                var valueToSend = value || '';
                                                // If value contains Arabic-Indic digits, convert to latin for storage
                                                try { valueToSend = valueToSend.replace(/[٠-٩]/g, function(d){ return String('٠١٢٣٤٥٦٧٨٩').indexOf(d); }); } catch(e) {}

                                                var payload = { key: key || ('metric_'+Date.now()), label: label, value: valueToSend || null, icon: icon || null, meta: meta ? (function(){ try{ return JSON.parse(meta); }catch(e){ return meta; } })() : null };

                                                // If server API is present, try to persist; otherwise fallback to in-memory push
                                                var csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                                                function finalizeWith(obj){
                                                    // Defensive merge: preserve existing entries and only add/replace the target item.
                                                    try {
                                                        if (!obj) obj = {};
                                                        if (!obj.id) {
                                                            obj._local_id = obj._local_id || ('local-' + Date.now() + '-' + Math.floor(Math.random()*1000));
                                                            obj._is_local = true;
                                                        }
                                                    } catch(e) {}

                                                    // Ensure the global array exists and operate on it directly
                                                    var arr = window.__PAGES.trust_compliance.metrics = window.__PAGES.trust_compliance.metrics || [];

                                                    var replaced = false;
                                                    // If we have a real server id, prefer to update by id
                                                    if (obj.id) {
                                                        for (var i = 0; i < arr.length; i++) {
                                                            if (arr[i] && arr[i].id === obj.id) { arr[i] = obj; replaced = true; break; }
                                                        }
                                                    }
                                                    // Otherwise, try to match by local id
                                                    if (!replaced && obj._local_id) {
                                                        for (var j = 0; j < arr.length; j++) {
                                                            if (arr[j] && arr[j]._local_id && arr[j]._local_id === obj._local_id) { arr[j] = obj; replaced = true; break; }
                                                        }
                                                    }
                                                    // Fallback: if editing by index, honor that index
                                                    if (!replaced && mode === 'edit' && idx >= 0 && idx < arr.length) {
                                                        arr[idx] = obj; replaced = true;
                                                    }
                                                    // If still not replaced, append
                                                    if (!replaced) {
                                                        arr.push(obj);
                                                    }

                                                    renderMetrics(); closeModal();
                                                }

                                                // detect API availability by attempting to POST; if fails fallback
                                                if (mode === 'edit' && idx >=0 && items[idx] && items[idx].id) {
                                                    // update existing DB row
                                                    $.ajax({ url: '/api/admin/tc/metrics/' + encodeURIComponent(items[idx].id), method: 'PUT', contentType: 'application/json', data: JSON.stringify(payload), headers: { 'X-CSRF-TOKEN': csrf } })
                                                        .done(function(resp){
                                                            // Refresh the canonical list from server so we show all rows
                                                            try{ loadMetricsFromServer(); }catch(e){ try{ finalizeWith(resp); }catch(ex){ finalizeWith(payload); } }
                                                            try{ showAdminToast && showAdminToast('success','تم الحفظ'); }catch(e){}
                                                        })
                                                        .fail(function(){ // fallback local
                                                            try{ finalizeWith(payload); }catch(e){ console.warn('metrics save fallback failed', e); }
                                                        });
                                                } else {
                                                    // create
                                                    $.ajax({ url: '/api/admin/tc/metrics', method: 'POST', contentType: 'application/json', data: JSON.stringify(payload), headers: { 'X-CSRF-TOKEN': csrf } })
                                                        .done(function(resp){
                                                            try{ loadMetricsFromServer(); }catch(e){ try{ finalizeWith(resp); }catch(ex){ finalizeWith(payload); } }
                                                            try{ showAdminToast && showAdminToast('success','تم الإنشاء'); }catch(e){}
                                                        })
                                                        .fail(function(){ // fallback local
                                                            try{ finalizeWith(payload); }catch(e){ console.warn('metrics create fallback failed', e); }
                                                        });
                                                }
                                            });

                                            // try to load metrics from server (admin) then render; fall back to in-memory
                                            function loadMetricsFromServer(){
                                                fetch('/api/admin/tc/metrics', { credentials: 'same-origin' })
                                                    .then(function(resp){ if(!resp.ok) throw resp; return resp.json(); })
                                                    .then(function(data){
                                                        try{
                                                            var server = (data || []).map(function(r){ return { id: r.id || null, key: r.key || '', label: r.label || '', value: r.value || '', icon: r.icon || '', meta: r.meta || null }; });
                                                            // Merge: preserve local-only items (those without id, e.g., temporary) and update or add server items
                                                            var existing = window.__PAGES.trust_compliance.metrics || [];
                                                            var byId = {};
                                                            existing.forEach(function(e){ if (e && e.id) byId[e.id] = e; });
                                                            // Start with local-only items
                                                            var merged = (existing || []).filter(function(x){ return !x || !x.id; }).slice();
                                                            server.forEach(function(s){
                                                                if (s && s.id && byId[s.id]) {
                                                                    // replace existing server-backed entry with the fresh server version
                                                                    var idx = merged.findIndex(function(x){ return x && x.id === s.id; });
                                                                    if (idx >= 0) merged[idx] = s; else merged.push(s);
                                                                } else {
                                                                    // just add server entry
                                                                    merged.push(s);
                                                                }
                                                            });
                                                            window.__PAGES.trust_compliance.metrics = merged;
                                                        }catch(e){ console.warn('metrics parse error', e); }
                                                        renderMetrics();
                                                    }).catch(function(err){ console.warn('metrics load failed', err); renderMetrics(); });
                                            }
                                            try{ loadMetricsFromServer(); }catch(e){ console.warn('metrics render error', e); renderMetrics(); }
                                        })();
                                    </script>
                                    
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4 lg:col-span-2">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">تقارير المراجعة</p>
                                            <button class="tc-panel-add bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md px-3 py-1" data-type="audit">إضافة</button>
                                        </div>
                                        <div id="tc-audit-list" class="space-y-2"></div>
                                    </div>
                                </div>

                                <!-- Modal: Trust & Compliance (dynamic) -->
                                <div id="tc-modal"
                                    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
                                    <div
                                        class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-2xl p-4 max-h-[90vh] overflow-y-auto mx-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <h4 id="tc-modal-title" class="text-white font-semibold">إضافة</h4>
                                                <div id="tc-modal-file-actions" class="flex items-center gap-2"></div>
                                            </div>
                                            <button id="tc-modal-close" class="text-gray-300">✕</button>
                                        </div>
                                        <div id="tc-form" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button id="tc-modal-cancel"
                                                class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                                            <button id="tc-modal-save"
                                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-md px-3 py-2">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                                <script src="{{ asset('js/admin-tc-legacy.js') }}?v=1"></script>
                                <script>
                                    // Ensure TC modal is a child of document.body so fixed centering works
                                    document.addEventListener('DOMContentLoaded', function(){
                                        try {
                                            var modal = document.getElementById('tc-modal');
                                            if (modal && modal.parentNode && modal.parentNode !== document.body) {
                                                document.body.appendChild(modal);
                                            }
                                        } catch(e) { console.warn('tc-modal move failed', e); }
                                    });
                                </script>
                            </div>

                            <!-- Settings Tab (Contact + Platform + Advanced) -->
                            <div id="tab-settings" class="tab-panel space-y-6 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">إعدادات النظام</h3>
                                    <button id="save-settings"
                                        class="bg-green-600 hover:bg-green-700 text-white text-sm rounded-md px-3 py-2">حفظ
                                        الإعدادات</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">معلومات المنصة</p>
                                            <div class="flex items-center gap-2">
                                                <button id="edit-platform-btn" type="button" class="px-3 py-1 text-xs rounded-md bg-blue-600 hover:bg-blue-500">تعديل</button>
                                                <button id="cancel-edit-platform" type="button" class="px-3 py-1 text-xs rounded-md bg-gray-700 hover:bg-gray-600 hidden">إلغاء</button>
                                            </div>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                            <!-- Inputs now span the full width so the previous checkbox column area is reclaimed -->
                                            <div class="md:col-span-4">
                                                <label class="block text-sm text-gray-300 mb-1">اسم المنصة</label>
                                                <input id="set-platform-name"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3" disabled />
                                                <label class="block text-sm text-gray-300 mb-1">الرابط</label>
                                                <input id="set-platform-url"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3" disabled />
                                                <label class="block text-sm text-gray-300 mb-1">اللغة</label>
                                                <input id="set-platform-locale"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" disabled />
                                                <label class="block text-sm text-gray-300 mb-1 mt-3">وصف المنصة</label>
                                                <textarea id="set-platform-description" rows="3"
                                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" disabled></textarea>

                                                <!-- Move checkboxes under the description: two per row -->
                                                <div class="mt-4">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" id="apply-global" class="brand-chk" checked />
                                                            <span class="text-sm text-gray-300">تطبيق التغيير على مستوى المنصة (عام)</span>
                                                        </label>
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" id="apply-home" class="brand-chk" />
                                                            <span class="text-sm text-gray-300">تطبيق على الصفحة الرئيسية فقط</span>
                                                        </label>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" id="apply-admin" class="brand-chk" />
                                                            <span class="text-sm text-gray-300">تطبيق على لوحة تحكم المدير فقط</span>
                                                        </label>
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" id="apply-navbar" class="brand-chk" />
                                                            <span class="text-sm text-gray-300">تطبيق على شريط التنقل (Navbar) فقط</span>
                                                        </label>
                                                    </div>

                                                    <div class="mt-3 flex justify-center">
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" id="apply-title" class="brand-chk" />
                                                            <span class="text-sm text-gray-300">تطبيق على عنوان الموقع فقط</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-white font-semibold">معلومات الاتصال</p>
                                            <div class="flex items-center gap-2">
                                                <button id="edit-contact-btn" type="button" class="px-3 py-1 text-xs rounded-md bg-blue-600 hover:bg-blue-500">تعديل</button>
                                                <button id="cancel-edit-contact" type="button" class="px-3 py-1 text-xs rounded-md bg-gray-700 hover:bg-gray-600 hidden">إلغاء</button>
                                            </div>
                                        </div>
                                        <label class="block text-sm text-gray-300 mb-1">البريد</label>
                                        <input id="set-contact-email"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3" />
                                        <label class="block text-sm text-gray-300 mb-1">الهاتف</label>
                                        <input id="set-contact-phone"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3" />
                                        <label class="block text-sm text-gray-300 mb-1">واتساب</label>
                                        <input id="set-contact-whatsapp"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white mb-3" />
                                        <label class="block text-sm text-gray-300 mb-1">العنوان</label>
                                        <input id="set-contact-address"
                                            class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                    </div>

                                    <!-- New: Admin Information Settings -->
                                    <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                        <p class="text-white font-semibold mb-3">معلومات المدير</p>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">اسم المدير</label>
                                                <input id="set-admin-name" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="مثال: مدير النظام" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">البريد الإلكتروني</label>
                                                <input id="set-admin-email" type="email" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="admin@example.com" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">الهاتف</label>
                                                <input id="set-admin-phone" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="05xxxxxxxx" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">واتساب</label>
                                                <input id="set-admin-whatsapp" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="رابط أو رقم واتساب" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">المنصب/الصفة</label>
                                                <input id="set-admin-title" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="مثال: مدير العمليات" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">رابط الصورة (اختياري)</label>
                                                <input id="set-admin-avatar" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="https://.../avatar.png" />
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm text-gray-300 mb-1">ملاحظة للتواصل (اختياري)</label>
                                                <textarea id="set-admin-note" rows="2" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="ساعات الرد، تفضيلات التواصل ..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                  
                                </div>
                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4">
                                    <p class="text-white font-semibold mb-4">إعدادات النظام المتقدمة</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-md">
                                            <div>
                                                <p class="text-white font-semibold">وضع الصيانة</p>
                                                <p class="text-gray-400 text-xs">إيقاف النظام مؤقتاً</p>
                                            </div>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="adv-maintenance" class="sr-only" />
                                                <span class="w-11 h-6 bg-gray-600 rounded-full shadow-inner relative">
                                                    <span
                                                        class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-md">
                                            <div>
                                                <p class="text-white font-semibold">التسجيل الجديد</p>
                                                <p class="text-gray-400 text-xs">السماح بالتسجيل</p>
                                            </div>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="adv-registration" class="sr-only" />
                                                <span class="w-11 h-6 bg-gray-600 rounded-full shadow-inner relative">
                                                    <span
                                                        class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-md">
                                            <div>
                                                <p class="text-white font-semibold">الاستثمار</p>
                                                <p class="text-gray-400 text-xs">تمكين الاستثمارات</p>
                                            </div>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="adv-investment" class="sr-only" />
                                                <span class="w-11 h-6 bg-gray-600 rounded-full shadow-inner relative">
                                                    <span
                                                        class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="p-3 bg-gray-700/40 rounded-md">
                                            <label class="block text-sm text-gray-300 mb-1">نسبة النجاح الافتراضية
                                                (%)</label>
                                            <input type="number" id="adv-success-rate" min="0" max="100"
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                                        </div>
                                    </div>
                                </div>
                                <div id="settings-status" class="text-sm text-gray-300"></div>
                            </div>

                            <!-- User Permissions Management (UI only) -->
                         <div id="tab-user-permissions" class="tab-panel space-y-4 hidden">
                                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-4 mt-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-white font-semibold">صلاحيات الأدوار</h4>
                                        <div class="flex items-center gap-2">
                                            <select id="perm-role" class="bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white">
                                                <option value="admin">مدير</option>
                                                <option value="staff">مستخدم</option>
                                                <option value="investor">مستثمر</option>
                                            </select>
                                            <button id="perm-save" class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md px-3 py-2">حفظ</button>
                                        </div>
                                    </div>
                                    <div id="perm-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2"></div>
                                    <p id="perm-status" class="text-sm text-gray-300 mt-2"></p>
                                </div>
                            
                        </section>
                    </div>

                </div>
            </div>

            <!-- Mobile Sidebar Drawer (only used on this admin dashboard page) -->
            <div id="mobile-sidebar-overlay" class="fixed inset-0 z-50 hidden">
                <div id="mobile-sidebar-backdrop" class="absolute inset-0 bg-black/50"></div>
                <div id="mobile-sidebar-drawer" class="absolute right-0 top-0 h-full w-72 max-w-[90%] bg-gray-900 border-l border-gray-700 transform translate-x-full transition-transform duration-200 ease-out">
                    <div class="p-3 flex items-center justify-between border-b border-gray-800">
                        <span class="text-white font-semibold">القائمة</span>
                        <button id="mobile-sidebar-close" class="text-gray-300">✕</button>
                    </div>
                    <div id="mobile-sidebar-content" class="p-2 space-y-2 overflow-y-auto" style="max-height: calc(100% - 52px);"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const api = '/api';
                // ===== Permissions UI wiring =====
                const $permList = $('#perm-list');
                const $permRole = $('#perm-role');
                const $permStatus = $('#perm-status');

                function loadPermissions() {
                    if (!$permList.length) return;
                    $permList.empty();
                    $.get(api + '/admin/permissions').done(function(perms){
                        // group by category
                        const byCat = {};
                        perms.forEach(p=>{ (byCat[p.category||'other'] ||= []).push(p); });
                        const role = $permRole.val();
                        $.get(api + '/admin/roles/' + role + '/permissions').done(function(keys){
                            Object.keys(byCat).forEach(cat=>{
                                const group = $('<div class="bg-gray-900/40 border border-gray-700 rounded-md p-3">');
                                group.append('<div class="text-white font-semibold mb-2">' + cat + '</div>');
                                byCat[cat].forEach(p=>{
                                    const id = 'perm_'+p.key;
                                    const checked = keys.includes(p.key) ? 'checked' : '';
                                    group.append('<label class="inline-flex items-center text-sm text-gray-200 gap-2 mb-1"><input type="checkbox" class="brand-chk perm-item" data-key="'+p.key+'" '+checked+'> <span>'+p.label+'</span></label>');
                                });
                                $permList.append(group);
                            });
                        });
                    });

                    // Branding header previously offered a quick link to the Bank tab.
                    // That behavior was removed so bank settings appear only in the dedicated Bank tab.

                    
                }
                $permRole.on('change', loadPermissions);
                $('#perm-save').on('click', function(){
                    const role = $permRole.val();
                    const selected = [];
                    $permList.find('.perm-item:checked').each(function(){ selected.push($(this).data('key')); });
                    $permStatus.text('... جاري الحفظ');
                    $.ajax({
                        url: api + '/admin/roles/' + role + '/permissions',
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify({ permissions: selected }),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function(){
                        $permStatus.text('تم الحفظ');
                    }).fail(function(){
                        $permStatus.text('تعذر الحفظ');
                    });
                });

                // initialize when settings tab becomes active
                setTimeout(loadPermissions, 300);
                // Demo fallback data to visualize the dashboard when APIs are unauthorized (e.g., no admin session)
                const DEMO_DATA = {
                    stats: {
                        total_users: 4,
                        total_investments: 3,
                        total_licenses: 2,
                        total_investment_amount_formatted: '125,000'
                    },
                    analytics: {
                        users: {
                            total: 4,
                            new_today: 1,
                            active: 3
                        },
                        investments: {
                            active: 1,
                            pending: 1,
                            completed: 1
                        },
                        licenses: {
                            approved: 1,
                            pending: 1
                        },
                        success_rate: 98
                    },
                    users: [{
                            id: 1,
                            full_name: 'محمد الأحمد',
                            email: 'm.ahmad@example.com',
                            phone: '0551234567',
                            is_active: 1
                        },
                        {
                            id: 2,
                            full_name: 'سارة العتيبي',
                            email: 's.otaibi@example.com',
                            phone: '0569876543',
                            is_active: 1
                        },
                        {
                            id: 3,
                            full_name: 'خالد المطيري',
                            email: 'k.mutairi@example.com',
                            phone: '0541112233',
                            is_active: 0
                        },
                        {
                            id: 4,
                            full_name: 'نورة القحطاني',
                            email: 'n.qahtani@example.com',
                            phone: '0573332211',
                            is_active: 1
                        },
                    ],
                    investments: [{
                            id: 11,
                            amount: 50000,
                            investment_type: 'small',
                            profit_rate: 12,
                            expected_return: 56000,
                            status: 'pending'
                        },
                        {
                            id: 12,
                            amount: 30000,
                            investment_type: 'small',
                            profit_rate: 10,
                            expected_return: 33000,
                            status: 'active'
                        },
                        {
                            id: 13,
                            amount: 45000,
                            investment_type: 'large',
                            profit_rate: 15,
                            expected_return: 51750,
                            status: 'completed'
                        },
                    ],
                    licenses: [{
                            id: 21,
                            business_name: 'مؤسسة الأفق',
                            business_type: 'تجارة إلكترونية',
                            location: 'الرياض',
                            status: 'pending'
                        },
                        {
                            id: 22,
                            business_name: 'شركة النور',
                            business_type: 'مطاعم',
                            location: 'جدة',
                            status: 'approved'
                        },
                    ],
                    activity: [{
                            type: 'investment_created',
                            text: 'طلب استثمار جديد بمبلغ 100000 ريال',
                            time: '١٢:٣٥ م · ٢٠٢٥/٠٦/٠١'
                        },
                        {
                            type: 'user_registration',
                            text: 'تسجيل مستخدم جديد: ',
                            time: '١٠:٤٠ ص · ٢٠٢٥/٠٦/٠١'
                        },
                        {
                            type: 'user_registration',
                            text: 'تسجيل مستخدم جديد: hehhhkj@gmail.com',
                            time: '٠٢:٥٩ ص · ٢٠٢٥/٠٦/٠١'
                        },
                    ]
                };

                function showLoading(v) {
                    $('#loading-overlay').toggleClass('hidden', !v).toggleClass('flex', !!v);
                }

                function toBadge(status) {
                    const map = {
                        pending: { cls: 'bg-yellow-500 text-white', text: 'معلق', icon: 'clock' },
                        // Active = orange (برتقالي)
                        active:  { cls: 'bg-orange-600 text-white', text: 'نشط', icon: 'play-circle' },
                        approved:{ cls: 'bg-green-500 text-white', text: 'موافق عليه', icon: 'check-circle' },
                        rejected:{ cls: 'bg-red-500 text-white', text: 'مرفوض', icon: 'alert-circle' },
                        // Completed = blue (أزرق)
                        completed:{ cls: 'bg-blue-600 text-white', text: 'مكتمل', icon: 'check-circle' }
                    };
                    return map[status] || { cls: 'bg-gray-500 text-white', text: status || '-', icon: 'activity' };
                }

                function statusLabel(status){
                    if(!status) return '—';
                    try{
                        var key = String(status).toLowerCase();
                        return (toBadge(key) && toBadge(key).text) ? toBadge(key).text : status;
                    }catch(e){ return status; }
                }

                // map status to a text color class for consistent coloring in lists and modals
                function statusTextClass(status){
                    if(!status) return 'text-gray-300';
                    const key = String(status).toLowerCase();
                    // approved -> green
                    if(key === 'approved' || key === 'approved_by_admin') return 'text-emerald-400';
                    // rejected -> red
                    if(key === 'rejected' || key === 'مrefused' || key === 'مرفوض' || key === 'رفض') return 'text-red-400';
                    // pending -> yellow
                    if(key === 'pending' || key === 'قيد المراجعة' || key === 'pending_payment' || key === 'awaiting') return 'text-yellow-300';
                    // active -> orange (برتقالي)
                    if(key === 'active' || key === 'نشط') return 'text-orange-400';
                    // completed -> blue (أزرق)
                    if(key === 'completed' || key === 'مكتمل') return 'text-blue-400';
                    // default
                    return 'text-gray-300';
                }

                function applyStats(d) {
                    $('#stat-users').text(d.total_users || d.users || 0);
                    $('#stat-amount').text(((d.total_investment_amount_formatted) || '0') + ' ريال');
                }

                function applyAnalytics(a) {
                    const an = a || DEMO_DATA.analytics;
                    $('#stat-users').text(an.users.total || 0);
                    $('#stat-users-today').text(an.users.new_today || 0);
                    $('#stat-investments-active').text(an.investments.active || 0);
                    $('#stat-investments-pending').text(an.investments.pending || 0);
                    $('#stat-licenses-approved').text(an.licenses.approved || 0);
                    $('#stat-licenses-pending').text(an.licenses.pending || 0);
                    $('#stat-success-rate').text(((an.success_rate ?? 98)) + '%');
                }

                function fetchStats() {
                    showLoading(true);
                    $.get(api + '/stats').done(function(d) {
                        applyStats(d);
                    }).fail(function() {
                        // Fallback to demo stats
                        applyStats(DEMO_DATA.stats);
                    }).always(function() {
                        showLoading(false);
                    });
                }

                // Users modal helpers (outer scope so other functions can call)
                function clearUserModal() {
                    $('#user-modal').removeData('mode').removeData('id');
                    $('#user-full-name').val('');
                    $('#user-username').val('');
                    $('#user-email').val('');
                    $('#user-phone').val('');
                    $('#user-role').val('investor');
                    $('#user-active').prop('checked', true);
                    $('#user-password').val('');
                    $('#user-perms').empty();
                }

                function openUserModal(user) {
                    if (user) {
                        $('#user-modal-title').text('تعديل مستخدم');
                        $('#user-modal').data('mode', 'edit').data('id', user.id);
                        $('#user-full-name').val(user.full_name || '');
                        $('#user-username').val(user.username || '');
                        $('#user-email').val(user.email || '');
                        $('#user-phone').val(user.phone || '');
                        $('#user-role').val(user.role || 'investor');
                        $('#user-active').prop('checked', !!user.is_active);
                        $('#user-password').val('');
                        // load user-specific permissions
                        loadAllPerms(function(){ loadUserPerms(user.id); });
                    } else {
                        $('#user-modal-title').text('مستخدم جديد');
                        clearUserModal();
                        $('#user-modal').data('mode', 'create');
                        loadAllPerms();
                    }
                    $('#user-modal').removeClass('hidden').addClass('flex');
                }

                function renderPerms(perms, checkedKeys){
                    const $wrap = $('#user-perms').empty();
                    const byCat = {};
                    perms.forEach(p=>{ (byCat[p.category||'other'] ||= []).push(p); });
                    Object.keys(byCat).forEach(cat=>{
                        const group = $('<div class="bg-gray-800/40 border border-gray-700 rounded-md p-2">');
                        const catId = 'cat_'+cat.replace(/[^a-zA-Z0-9_-]/g,'_');
                        group.append('<div class="flex items-center justify-between mb-1"><div class="text-white font-semibold">'+cat+'</div><label class="inline-flex items-center text-xs text-white"><input type="checkbox" class="brand-chk user-perm-cat" data-cat="'+catId+'" /> <span class="ml-2">تحديد الكل</span></label></div>');
                        const list = $('<div class="space-y-1" data-cat="'+catId+'"></div>');
                        byCat[cat].forEach(p=>{
                            const id='up_'+p.key;
                            const chk = checkedKeys && checkedKeys.includes(p.key) ? 'checked' : '';
                            list.append('<label class="block"><span class="inline-flex items-center text-sm text-gray-200 gap-2"><input type="checkbox" class="brand-chk user-perm" data-key="'+p.key+'" '+chk+'> <span class="perm-label">'+p.label+'</span></span></label>');
                        });
                        group.append(list);
                        $wrap.append(group);
                    });
                }

                function loadAllPerms(cb){
                    $.get(api + '/admin/permissions').done(function(perms){ renderPerms(perms, []); if(cb) cb(); });
                }
                function loadUserPerms(userId){
                    $.get(api + '/admin/users/' + userId + '/permissions').done(function(keys){
                        // reload all perms and mark checked
                        $.get(api + '/admin/permissions').done(function(perms){ renderPerms(perms, keys); });
                    });
                }

                function renderUsers(users) {
                    const $wrap = $('#users-list').empty();
                    $('#users-count').text(users.length + ' مستخدم');
                    users.forEach(u => {
                        const badgeCls = u.is_active ? 'bg-green-600' : 'bg-red-600';
                        const $card = $('<div class="bg-gray-800/50 border border-gray-700 rounded-md"></div>');
                        const $inner = $('<div class="p-4 flex items-center justify-between"></div>');
                        const $left = $('<div class="flex items-center gap-4"></div>');
                        $left.append(makeAvatarHtmlGlobal(u.full_name || u.name || ('مستخدم #' + u.id), 'w-10 h-10', 'text-white'));
                        $left.append('<div><p class="text-white font-semibold">' + (u.full_name || '-') +
                            '</p><p class="text-gray-400 text-sm">' + (u.email || '') +
                            '</p><p class="text-gray-400 text-sm">' + (u.phone || '') + '</p></div>');
                        const $right = $('<div class="flex items-center gap-2"></div>');
                        $right.append(
                            '<label class="inline-flex items-center cursor-pointer select-none"><input type="checkbox" class="sr-only user-toggle" ' +
                            (u.is_active ? 'checked' : '') + ' data-user-id="' + u.id +
                            '" /><span class="w-11 h-6 bg-gray-600 rounded-full shadow-inner relative"><span class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition"></span></span></label>'
                            );
                        $right.append('<span class="inline-flex items-center rounded-md ' + badgeCls +
                            ' text-white text-xs font-semibold px-2.5 py-1.5">' + (u.is_active ? 'نشط' :
                                'غير نشط') + '</span>');
                        $right.append('<button class="btn-user-edit px-2 py-1.5 text-xs rounded-md bg-blue-600 text-white" data-user-id="' + u.id + '">تعديل</button>');
                        $right.append('<button class="btn-user-del px-2 py-1.5 text-xs rounded-md bg-red-600 text-white" data-user-id="' + u.id + '">حذف</button>');
                        $inner.append($left).append($right);
                        $card.append($inner);
                        $wrap.append($card);
                    });
                    if (window.lucide && lucide.createIcons) {
                        lucide.createIcons();
                    }

                    // Developer test helper: if URL contains ?openPkgTest=1 auto-click first edit button
                    try {
                        if (location && String(location.search || '').indexOf('openPkgTest=1') !== -1) {
                            setTimeout(function() {
                                var b = document.querySelector('#packages-list .btn-edit');
                                if (b) b.click();
                            }, 250);
                        }
                    } catch (e) { /* ignore in older browsers */ }

                    // bind toggles
                    $('#users-list').off('change.userToggle').on('change.userToggle', '.user-toggle', function() {
                        const id = $(this).data('user-id');
                        const status = this.checked ? 'active' : 'inactive';
                        $.ajax({
                                url: api + '/admin/users/' + id + '/status',
                                method: 'PUT',
                                data: {
                                    status,
                                    _token: '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                fetchUsers();
                            })
                            .fail(function() {
                                alert('تعذر تحديث حالة المستخدم');
                            });
                    });

                    // bind edit/delete
                    $('#users-list').off('click.userEdit').on('click.userEdit', '.btn-user-edit', function() {
                        const id = $(this).data('user-id');
                        const u = (users || []).find(x => x.id == id);
                        openUserModal(u);
                    });
                                // تحميل jQuery احتياطي إن لم تكن موجودة (لتفادي كسر بقية الشيفرة التي تعتمد عليها)
                                if (typeof window.jQuery === 'undefined') {
                                    console.warn('jQuery غير موجودة، سيتم إضافة معالج نقر Vanilla لأزرار عرض طلب الترخيص.');
                                    // معالج Vanilla عام للنقر على أزرار العرض
                                    document.addEventListener('click', function(e) {
                                        const btn = e.target.closest('.btn-lreq-view');
                                        if (!btn) return;
                                        const rawId = btn.getAttribute('data-id');
                                        const id = Number(rawId);
                                        // البحث في البيانات المبدئية أو المحدثة
                                        const dataArr = (window.__INIT_LICENSES__ && Array.isArray(window.__INIT_LICENSES__)) ? window.__INIT_LICENSES__ : (window.__LICENSE_REQUESTS__ || []);
                                        let item = dataArr.find(x => Number(x.id) === id);
                                        if (!item) {
                                            // بناء كائن من data-* كحل احتياطي
                                            item = {
                                                id: id,
                                                business_name: btn.getAttribute('data-business-name'),
                                                business_type: btn.getAttribute('data-business-type'),
                                                activity_description: btn.getAttribute('data-activity-description'),
                                                location: btn.getAttribute('data-location'),
                                                status: btn.getAttribute('data-status'),
                                                user_name: btn.getAttribute('data-user-name'),
                                                user_national_id: btn.getAttribute('data-user-nid'),
                                                user_nationality: btn.getAttribute('data-user-nationality'),
                                                user_phone: btn.getAttribute('data-user-phone'),
                                                user_bank_account: btn.getAttribute('data-user-bank'),
                                                user_identity_image: btn.getAttribute('data-user-image')
                                            };
                                        }
                                        openLicenseReqModal(item);
                                    });
                                } else {
                                    // أيضاً إضافة ربط عام يعتمد على jQuery في حال أعيد بناء القائمة لاحقاً بدون استدعاء renderLicenseRequests
                                    $(document).off('click.globalLreq').on('click.globalLreq', '.btn-lreq-view', function() {
                                        const id = parseInt($(this).data('id'), 10);
                                        let source = [];
                                        if (window.__INIT_LICENSES__ && Array.isArray(window.__INIT_LICENSES__)) source = window.__INIT_LICENSES__;
                                        if (window.__LICENSE_REQUESTS__ && Array.isArray(window.__LICENSE_REQUESTS__)) source = window.__LICENSE_REQUESTS__;
                                        let item = source.find(x => parseInt(x.id,10) === id);
                                        if (!item) {
                                            const $btn = $(this);
                                            item = {
                                                id: id,
                                                business_name: $btn.data('business-name'),
                                                business_type: $btn.data('business-type'),
                                                activity_description: $btn.data('activity-description'),
                                                location: $btn.data('location'),
                                                status: $btn.data('status'),
                                                user_name: $btn.data('user-name'),
                                                user_national_id: $btn.data('user-nid'),
                                                user_nationality: $btn.data('user-nationality'),
                                                user_phone: $btn.data('user-phone'),
                                                user_bank_account: $btn.data('user-bank'),
                                                user_identity_image: $btn.data('user-image')
                                            };
                                        }
                                        openLicenseReqModal(item);
                                    });
                                }
                    $('#users-list').off('click.userDel').on('click.userDel', '.btn-user-del', function() {
                        const id = $(this).data('user-id');
                        if (!confirm('تأكيد حذف هذا المستخدم؟')) return;
                        $.ajax({
                            url: api + '/admin/users/' + id,
                            method: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' }
                        }).done(function() {
                            fetchUsers();
                        }).fail(function() {
                            alert('تعذر حذف المستخدم (عرض تجريبي)');
                            fetchUsers();
                        });
                    });
                }

                function renderInvestments(items) {
                    const $wrap = $('#investments-list').empty();
                    $('#investments-count').text(items.length + ' استثمار');
                    items.forEach(it => {
                        const cfg = toBadge(it.status);
                        const amountTxt = (it.amount ? Number(it.amount).toLocaleString() + ' ريال سعودي' : '-');
                        const $card = $('<div class="bg-gray-800/50 border border-gray-700 rounded-md"></div>');
                        const $inner = $('<div class="p-4 flex items-center justify-between"></div>');
                        const $left = $('<div></div>');
                        // apply status color to amount/title to give quick visual cue
                        const statusColorClass = statusTextClass(it.status || it.state || '');
                        $left.append('<p class="text-white font-semibold ' + statusColorClass + '">' + amountTxt + '</p>');
                        $left.append('<p class="text-gray-400 text-sm">نوع الاستثمار: ' + (it.investment_type ===
                            'small' ? 'صغير' : it.investment_type === 'large' ? 'كبير' : (it
                                .investment_type || '-')) + '</p>');
                        $left.append('<p class="text-gray-400 text-sm">معدل الربح: ' + (it.profit_rate || '-') +
                            '% | العائد المتوقع: ' + (it.expected_return ? Number(it.expected_return)
                                .toLocaleString() + ' ريال' : '-') + '</p>');
                        const $right = $('<div class="flex items-center gap-2"></div>');
                        $right.append('<span class="inline-flex items-center gap-1 rounded-md ' + cfg.cls +
                            ' text-xs font-semibold px-2.5 py-1.5"><i data-lucide="' + cfg.icon +
                            '" class="w-3 h-3"></i>' + cfg.text + '</span>');
                        $right.append('<button class="border border-blue-500/50 text-blue-400 hover:bg-blue-500/10 text-xs rounded-md px-3 py-1.5" data-action="view" data-id="'+it.id+'">عرض</button>');
                        $inner.append($left).append($right);
                        // attach an identifier so we can locate and animate this card later
                        $card.attr('data-inv-id', it.id || '');
                        $card.append($inner);
                        $wrap.append($card);

                        // approve/reject are handled inside the investment modal (to keep list clean)
                        // Animate and scroll the card when the admin clicks "عرض"
                        $card.on('click', '[data-action="view"]', function(){
                            const id = $(this).data('id');
                            try{
                                const selector = '[data-inv-id="' + id + '"]';
                                const $target = $(selector).first();
                                if($target && $target.length){
                                    // smooth scroll into view
                                    try{ $target[0].scrollIntoView({behavior:'smooth', block:'center'}); }catch(e){}
                                    // temporary highlight + lift animation
                                    $target.css('transition', 'box-shadow 0.35s ease, border-color 0.35s ease, transform 0.2s ease');
                                    $target.css('box-shadow', '0 12px 40px rgba(34,211,238,0.14)');
                                    $target.css('border-color', '#22d3ee');
                                    $target.css('transform', 'translateY(-6px)');
                                    // small bounce back
                                    setTimeout(function(){ $target.css('transform','translateY(0)'); }, 220);
                                    // remove highlight after a short duration
                                    setTimeout(function(){
                                        $target.css('box-shadow','');
                                        $target.css('border-color','rgba(255,255,255,0.08)');
                                    }, 1800);
                                }
                            }catch(e){}
                            openInvestmentDetail(id);
                        });
                    });
                    if (window.lucide && lucide.createIcons) {
                        lucide.createIcons();
                    }
                }

                function renderLicenses(items) {
                    const $wrap = $('#licenses-list').empty();
                    $('#licenses-count').text(items.length + ' ترخيص');
                    items.forEach(it => {
                        const cfg = toBadge(it.status);
                        const $card = $('<div class="bg-gray-800/50 border border-gray-700 rounded-md"></div>');
                        const $inner = $('<div class="p-4 flex items-center justify-between"></div>');
                        const $left = $('<div></div>');
                        $left.append('<p class="text-white font-semibold">' + (it.business_name || '-') + '</p>');
                        $left.append('<p class="text-gray-400 text-sm">نوع النشاط: ' + (it.business_type || '-') +
                            '</p>');
                        $left.append('<p class="text-gray-400 text-sm">الموقع: ' + (it.location || '-') + '</p>');
                        const $right = $('<div class="flex items-center gap-2"></div>');
                        $right.append('<span class="inline-flex items-center gap-1 rounded-md ' + cfg.cls +
                            ' text-xs font-semibold px-2.5 py-1.5"><i data-lucide="' + cfg.icon +
                            '" class="w-3 h-3"></i>' + cfg.text + '</span>');
                        $right.append('<button class="border border-blue-500/50 text-blue-400 hover:bg-blue-500/10 text-xs rounded-md px-3 py-1.5" data-action="view" data-id="'+(it.id||'')+'">عرض</button>');
                        $inner.append($left).append($right);
                        $card.append($inner);
                        // attach an identifier so we can locate and animate this card later (reuse same selector as investments)
                        $card.attr('data-inv-id', it.id || '');
                        $wrap.append($card);

                        // approve/reject handled from license modal
                        // animate and open license detail similar to investments list
                        $card.on('click', '[data-action="view"]', function(){
                            const id = $(this).data('id');
                            try{
                                const selector = '[data-inv-id="' + id + '"]';
                                const $target = $(selector).first();
                                if($target && $target.length){
                                    try{ $target[0].scrollIntoView({behavior:'smooth', block:'center'}); }catch(e){}
                                    $target.css('transition', 'box-shadow 0.35s ease, border-color 0.35s ease, transform 0.2s ease');
                                    $target.css('box-shadow', '0 12px 40px rgba(34,211,238,0.14)');
                                    $target.css('border-color', '#22d3ee');
                                    $target.css('transform', 'translateY(-6px)');
                                    setTimeout(function(){ $target.css('transform','translateY(0)'); }, 220);
                                    setTimeout(function(){ $target.css('box-shadow',''); $target.css('border-color','rgba(255,255,255,0.08)'); }, 1800);
                                }
                            }catch(e){}
                            try{ openLicenseDetail(id || it.id || it); }catch(e){
                                // fallback minimal display
                                $('#licdet-id').text(it.id || '—');
                                $('#licdet-business-name').text(it.business_name || '-');
                                $('#licdet-business-type').text(it.business_type || '-');
                                $('#licdet-activity').text(it.activity_description || '-');
                                $('#licdet-location').text(it.location || '-');
                                $('#licdet-status').text(it.status || '-');
                                $('#lic-modal').removeClass('hidden').addClass('flex');
                            }
                        });
                    });
                    if (window.lucide && lucide.createIcons) {
                        lucide.createIcons();
                    }
                }

                // Open license detail modal and populate with API response (mirrors openInvestmentDetail)
                function openLicenseDetail(id){
                    const $m = $('#lic-modal');
                    // reset
                    $m.find('#licdet-id,#licdet-created,#licdet-business-name,#licdet-business-type,#licdet-activity,#licdet-location,#licdet-status,#licdet-notes,#licdet-owner-name,#licdet-owner-email,#licdet-owner-phone,#licdet-owner-nid').text('—');
                    $('#licdet-payments').empty();
                    $('#licdet-trans').empty();
                    $('#licdet-docs').empty();
                    $('#licdet-id-front,#licdet-id-back').attr('src','').addClass('hidden');

                    $.get(api + '/admin/licenses/' + encodeURIComponent(id))
                        .done(function(d){
                            const lic = d.license || d || {};
                            const owner = d.owner || lic.owner || {};
                            $('#lic-modal-title').text('الترخيص #' + (lic.id || id));
                            $('#licdet-id').text(lic.id || '—');
                            $('#licdet-created').text(lic.created_at || lic.created || '—');
                            $('#licdet-business-name').text(lic.business_name || '—');
                            $('#licdet-business-type').text(lic.business_type || '—');
                            $('#licdet-activity').text(lic.activity_description || '—');
                            $('#licdet-location').text(lic.location || '—');
                            $('#licdet-status').text(statusLabel(lic.status));
                            try{ var stCls = statusTextClass(lic.status || ''); $('#licdet-status').removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300').addClass(stCls); }catch(e){}
                            $('#licdet-notes').text(lic.notes || '—');

                            // owner
                            $('#licdet-owner-name').text(owner.full_name || owner.name || lic.user_name || '—');
                            $('#licdet-owner-email').text(owner.email || lic.user_email || '—');
                            $('#licdet-owner-phone').text(owner.phone || lic.user_phone || '—');
                            $('#licdet-owner-nid').text(owner.national_id || owner.nid || lic.user_national_id || '—');

                            // documents
                            const docs = d.documents || lic.documents || [];
                            if(Array.isArray(docs) && docs.length){
                                docs.forEach(doc => {
                                    const url = (doc.url || doc.path || doc.link || '').toString();
                                    const normalized = (function(u){ if(!u) return null; if(/^https?:\/\//i.test(u)) return u; if(u.startsWith('/')) return u; if(u.startsWith('storage/')) return '/' + u.replace(/^\/+/, ''); return '/storage/' + u.replace(/^\/+/, ''); })(url);
                                    const cell = $('<div class="bg-gray-900/50 border border-gray-700 rounded p-2 text-sm text-white">');
                                    if(normalized){
                                        cell.append('<div class="font-semibold">'+ (doc.name || doc.filename || 'ملف') +'</div>');
                                        cell.append('<div class="text-xs text-gray-300">'+ (doc.type || '') +'</div>');
                                        cell.append('<div class="mt-2"><a href="'+ normalized +'" target="_blank" class="text-cyan-300 text-xs underline">فتح/تحميل</a></div>');
                                    } else {
                                        cell.text(doc.name || doc.filename || 'ملف');
                                    }
                                    $('#licdet-docs').append(cell);
                                });
                            } else {
                                $('#licdet-docs').append('<div class="text-gray-400">لا توجد مستندات مرفوعة</div>');
                            }

                            // identity images resolution (front/back)
                            try{
                                function chooseUrl(candidate){ if(!candidate) return null; if(typeof candidate === 'string') return candidate; if(typeof candidate === 'object') return candidate.url || candidate.path || null; return null; }
                                function normalizeStorageUrl(u){ if(!u) return u; if(/^https?:\/\//i.test(u)) return u; if(u.startsWith('/')) return u; if(u.startsWith('storage/')) return '/' + u.replace(/^\/+/, ''); return '/storage/' + u.replace(/^\/+/, ''); }
                                let front = chooseUrl(owner.identity_image_front) || chooseUrl(lic.identity_image_front) || chooseUrl((docs && docs.find(x=>/(front|id_front)/i.test(x.name||x.filename||'') )?.url));
                                let back = chooseUrl(owner.identity_image_back) || chooseUrl(lic.identity_image_back) || chooseUrl((docs && docs.find(x=>/(back|id_back)/i.test(x.name||x.filename||''))?.url));
                                front = normalizeStorageUrl(front);
                                back = normalizeStorageUrl(back);
                                if(front) $('#licdet-id-front').attr('src', front).removeClass('hidden');
                                if(back) $('#licdet-id-back').attr('src', back).removeClass('hidden');
                                // set open/download links under each identity preview (use separate anchors)
                                try{
                                    try{ $('#licdet-id-front-open').attr('href','').addClass('hidden'); $('#licdet-id-front-download').attr('href','').addClass('hidden'); }catch(e){}
                                    if(front){ try{ $('#licdet-id-front-open').attr('href', front).removeClass('hidden'); $('#licdet-id-front-download').attr('href', front).removeClass('hidden'); }catch(e){ console.warn('set front links failed', e); } }
                                }catch(e){ console.warn('front control error', e); }
                                try{
                                    try{ $('#licdet-id-back-open').attr('href','').addClass('hidden'); $('#licdet-id-back-download').attr('href','').addClass('hidden'); }catch(e){}
                                    if(back){ try{ $('#licdet-id-back-open').attr('href', back).removeClass('hidden'); $('#licdet-id-back-download').attr('href', back).removeClass('hidden'); }catch(e){ console.warn('set back links failed', e); } }
                                }catch(e){ console.warn('back control error', e); }
                            }catch(e){ console.warn('id images', e); }

                            // populate bank & salary previews similarly to investor view
                            try{
                                function isImageUrl(u){ return typeof u === 'string' && /\.(png|jpe?g|gif|webp|bmp)(\?|$)/i.test(u); }
                                function chooseUrl(candidate){ if(!candidate) return null; if(typeof candidate === 'string') return candidate; if(typeof candidate === 'object') return candidate.url || candidate.path || null; return null; }
                                // attempt to pick from license first, otherwise from docs array
                                const pickFromDocs = (matcher) => { if(!Array.isArray(docs)) return null; const found = docs.find(x => matcher((x.type||'') + ' ' + (x.name||x.filename||''))); return found ? (found.url || found.path || null) : null; };
                                let bankCandidate = chooseUrl(lic.bank_statement) || pickFromDocs(s=>/bank_statement|bank|bankstatement/i.test(s));
                                let salaryCandidate = chooseUrl(lic.salary_certificate) || pickFromDocs(s=>/salary_certificate|salary|certificate|payroll/i.test(s));
                                bankCandidate = normalizeStorageUrl(bankCandidate);
                                salaryCandidate = normalizeStorageUrl(salaryCandidate);

                                // reset bank UI
                                $('#licdet-bank').addClass('hidden'); $('#licdet-bank-img').addClass('hidden'); $('#licdet-bank-file').addClass('hidden'); $('#licdet-bank-open').addClass('hidden'); $('#licdet-bank-download').addClass('hidden');
                                if(bankCandidate){
                                    $('#licdet-bank').removeClass('hidden');
                                    if(isImageUrl(bankCandidate)){
                                        $('#licdet-bank-img').attr('src', bankCandidate).removeClass('hidden');
                                    } else {
                                        $('#licdet-bank-file').removeClass('hidden');
                                    }
                                    $('#licdet-bank-open').attr('href', bankCandidate).removeClass('hidden');
                                    $('#licdet-bank-download').attr('href', bankCandidate).removeClass('hidden');
                                }

                                // reset salary UI
                                $('#licdet-salary').addClass('hidden'); $('#licdet-salary-img').addClass('hidden'); $('#licdet-salary-file').addClass('hidden'); $('#licdet-salary-open').addClass('hidden'); $('#licdet-salary-download').addClass('hidden');
                                if(salaryCandidate){
                                    $('#licdet-salary').removeClass('hidden');
                                    if(isImageUrl(salaryCandidate)){
                                        $('#licdet-salary-img').attr('src', salaryCandidate).removeClass('hidden');
                                    } else {
                                        $('#licdet-salary-file').removeClass('hidden');
                                    }
                                    $('#licdet-salary-open').attr('href', salaryCandidate).removeClass('hidden');
                                    $('#licdet-salary-download').attr('href', salaryCandidate).removeClass('hidden');
                                }
                            }catch(e){ console.warn('extra docs render failed', e); }

                            // payments (if any)
                            const payments = Array.isArray(d.payments) ? d.payments : (Array.isArray(lic.payments) ? lic.payments : []);
                            const $p = $('#licdet-payments');
                            if(!payments.length) { $p.append('<div class="text-gray-400 text-sm">لا توجد دفعات</div>'); }
                            payments.forEach(p => {
                                const badge = (p.status === 'completed') ? 'bg-green-600' : ((p.status === 'failed') ? 'bg-red-600' : 'bg-yellow-600');
                                const method = p.method || p.payment_method || '-';
                                const amt = fmtMoney(p.amount);
                                const ref = p.reference || p.payment_reference || '-';
                                const rec = p.receipt || p.receipt_url || p.receipt_path || null;
                                let receipt = rec;
                                if(receipt && typeof receipt === 'string' && !/^https?:\/\//i.test(receipt)){
                                    if(receipt.startsWith('/')) receipt = receipt; else if(receipt.startsWith('storage/')) receipt = '/' + receipt; else receipt = '/storage/' + receipt;
                                }
                                const receiptHtml = receipt ? ('<a href="'+receipt+'" target="_blank" class="text-xs underline text-cyan-300">عرض</a> <span class="text-gray-500">•</span> <a href="'+receipt+'" download class="text-xs underline text-cyan-300">تحميل</a>') : '';
                                const thumb = receipt ? ('<a href="'+receipt+'" target="_blank"><img src="'+receipt+'" class="w-16 h-10 object-cover rounded border border-gray-700"/></a>') : '';
                                $p.append('<div class="bg-gray-900/50 border border-gray-700 rounded p-2 flex items-center justify-between gap-2">\n'
                                    + '<div class="text-white text-xs md:text-sm">'+ method +' • مرجع: '+ ref +' '+ receiptHtml +'</div>\n'
                                    + '<div class="flex items-center gap-2">'+ (thumb || '') +'\n'
                                        + '<span class="text-xs text-white rounded-md px-2 py-1 '+badge+'">'+ (p.status || '-') +'</span>\n'
                                        + '<span class="text-xs text-white rounded-md px-2 py-1 bg-gray-700">'+ amt +'</span>\n'
                                    + '</div>\n'
                                + '</div>');
                            });

                            // transactions
                            const txs = Array.isArray(d.transactions) ? d.transactions : (Array.isArray(lic.transactions) ? lic.transactions : []);
                            const $t = $('#licdet-trans');
                            if(!txs.length) { $t.append('<div class="text-gray-400 text-sm">لا توجد حركات حالياً</div>'); }
                            txs.forEach(tr => {
                                const typeBadge = (tr.type || '').toLowerCase() === 'credit' ? 'bg-emerald-600' : ((tr.type || '').toLowerCase() === 'debit' ? 'bg-red-600' : 'bg-gray-700');
                                const amt = fmtMoney(tr.amount);
                                const tType = (tr.type || '').toLowerCase();
                                const tTypeLabel = tType === 'credit' ? 'دائن' : (tType === 'debit' ? 'مدين' : (tr.type || '-'));
                                const desc = tr.description || tr.note || tr.summary || '-';
                                const at = tr.created_at ? (' • ' + tr.created_at) : '';
                                $t.append('<div class="bg-gray-900/50 border border-gray-700 rounded p-2 flex items-center justify-between">\n'
                                    + '<div class="text-white text-sm">'+ desc + at +'</div>'
                                    + '<div class="flex items-center gap-2">'
                                        + '<span class="text-xs text-white rounded-md px-2 py-1 '+typeBadge+'">'+ tTypeLabel +'</span>'
                                        + '<span class="text-xs text-white rounded-md px-2 py-1 bg-gray-700">'+ amt +'</span>'
                                    + '</div>'
                                + '</div>');
                            });

                            // All license admin controls (approve/pay/reject) removed per request
                            try{}
                            catch(e){}

                            $m.removeClass('hidden').addClass('flex');

                            // Approve/payment/reject handlers removed per request — UI no longer exposes these actions here.

                        })
                        .fail(function(xhr){
                            // fallback to minimal UI
                            alert('تعذر جلب تفاصيل الترخيص' + (xhr && xhr.status ? ' ('+xhr.status+')' : ''));
                        });
                    
                    // close handler
                    $('#licdet-close, #lic-modal-close').off('click').on('click', function(){ $('#lic-modal').addClass('hidden').removeClass('flex'); });
                }

                function renderOverview(users, investments, activity) {
                    // Favorite stats
                    const activeUsers = users.filter(u => !!u.is_active).length;
                    const completedInvestments = investments.filter(i => i.status === 'completed').length;
                    $('#fav-active-users').text(activeUsers);
                    $('#fav-completed-investments').text(completedInvestments);

                    // Recent activity list
                    const $list = $('#recent-activity').empty();
                    const items = activity && activity.length ? activity : DEMO_DATA.activity;
                    // map certain types to Arabic-friendly labels/messages
                    const typeMap = {
                        'user_registration': { badge: 'تسجيل مستخدم', message: 'تسجيل مستخدم جديد' },
                        'investment_created': { badge: 'استثمار', message: 'طلب استثمار جديد' },
                        'license_request': { badge: 'ترخيص', message: 'طلب ترخيص جديد' },
                        'payment_reminder': { badge: 'تذكير دفع', message: 'تذكير دفع جديد' }
                    };

                    // localStorage helpers for seen activity ids
                    function getSeenActivityIds() {
                        try { return JSON.parse(localStorage.getItem('admin.activity.seen') || '[]') || []; } catch(e){ return []; }
                    }
                    function saveSeenActivityIds(arr) { try { localStorage.setItem('admin.activity.seen', JSON.stringify(arr || [])); } catch(e){} }
                    function markActivitySeen(id) { try { if(!id) return; var s = getSeenActivityIds(); if(!s.includes(id)){ s.push(id); saveSeenActivityIds(s); } } catch(e){} }
                    function markAllActivitySeen(ids) { try { if(Array.isArray(ids)) saveSeenActivityIds(ids); else saveSeenActivityIds([]); } catch(e){} }

                    // normalize items to ensure id/time/message exist
                    const normalized = (items || []).map(it => {
                        const id = it.id || (it.type ? (it.type + '_' + (it.time || it.created_at || Date.now())) : ('act_' + (it.created_at || Date.now())));
                        const time = it.time || it.created_at || it.createdAt || '';
                        const text = it.text || it.message || it.msg || '';
                        const type = it.type || 'activity';
                        return { id: String(id), time: time, text: text, type: type };
                    });

                    // sort newest first
                    normalized.sort(function(a,b){ return new Date(b.time || 0) - new Date(a.time || 0); });

                    // compute seen set and unread count
                    const seen = getSeenActivityIds();
                    const seenSet = {};
                    seen.forEach(sid => seenSet[sid] = true);
                    const unreadIds = normalized.filter(n => !seenSet[n.id]).map(n => n.id);
                    // update unread badge UI (bell)
                    if (unreadIds.length && unreadIds.length > 0) {
                        $('#recent-unread-badge').text(unreadIds.length).removeClass('hidden');
                    } else {
                        $('#recent-unread-badge').text('0').addClass('hidden');
                    }

                    // attach mark all read handler
                    $('#mark-all-read').off('click').on('click', function(){
                        markAllActivitySeen(normalized.map(n => n.id));
                        renderOverview(users, investments, normalized);
                        return false;
                    });

                    // clicking the bell brings the Overview into view and scrolls activity to top
                    $('#recent-bell').off('click').on('click', function(){
                        try { activateTab('overview', true); } catch(e){}
                        setTimeout(function(){ try { var el = document.getElementById('recent-activity'); if(el) el.scrollTop = 0; } catch(e){} }, 120);
                    });

                    normalized.forEach(ev => {
                        const t = ev.type || 'activity';
                        const mapped = typeMap[t] || null;
                        const badgeLabel = mapped ? mapped.badge : t;
                        const mainText = ev.text || (mapped ? mapped.message : '-');

                        const $row = $(
                            '<div class="bg-gray-800/60 border border-gray-700 rounded-md p-3 flex items-center justify-between cursor-pointer"></div>'
                        );
                        const $left = $('<div class="flex items-start gap-2 min-w-0"></div>');

                        const unseen = !seenSet[ev.id];
                        const $dot = $(unseen ? '<span class="w-2 h-2 rounded-full bg-red-600 mt-1 flex-shrink-0" title="جديد"></span>' : '<span class="w-2 h-2 rounded-full bg-transparent mt-1 flex-shrink-0"></span>');
                        const $textWrap = $('<div class="min-w-0"></div>');
                        $textWrap.append('<p class="text-white truncate">' + (mainText || '-') + '</p>');
                        if (ev.time) {
                            $textWrap.append('<p class="text-gray-400 text-xs mt-1">' + ev.time + '</p>');
                        }
                        const $badge = $('<span class="text-xs bg-blue-700/60 text-white rounded-md px-2 py-1">' + badgeLabel + '</span>');

                        $left.append($dot).append($textWrap);
                        $row.append($left).append($badge);

                        // click marks as seen and optionally expand or navigate
                        $row.on('click', function(){
                            markActivitySeen(ev.id);
                            // visual update: remove dot
                            $dot.removeClass('bg-red-600').addClass('bg-transparent');
                            // update unread badge
                            const newSeen = getSeenActivityIds();
                            const newUnread = normalized.filter(n => !newSeen.includes(n.id)).length;
                            if (newUnread && newUnread > 0) { $('#recent-unread-badge').text(newUnread).removeClass('hidden'); } else { $('#recent-unread-badge').text('0').addClass('hidden'); }
                        });

                        $list.append($row);
                    });
                }

                function renderPackages(items) {
                    const $wrap = $('#packages-list').empty();
                    $('#packages-count').text((items?.length || 0) + ' حزمة');
                    (items || []).forEach(p => {
                        const id = p.id;
                        const amount = Number(p.amount ?? 0);
                        const profit = Number(p.profit_rate ?? 0);
                        const $card = $('<div class="bg-gray-800/50 border border-gray-700 rounded-md p-4"></div>');
                        const $content = $('<div class="space-y-3"></div>');
                        const $display = $('<div class="text-center"></div>');
                        const profitValue = Math.round(amount * (profit / 100));
                        const total = amount + profitValue;
                        $display.append('<p class="text-2xl font-bold text-white">' + amount.toLocaleString() +
                            '<span class="text-sm text-gray-400"> ريال</span></p>');
                        $display.append(
                            '<div class="mt-2 inline-flex items-center rounded-md bg-amber-400 text-black text-sm font-extrabold px-2.5 py-1.5">' +
                            profit + '% عائد شهري</div>');
                        $display.append(
                            '<div class="flex items-center justify-between text-xs text-gray-300 mt-2"><span>الأرباح:</span><span class="text-green-400 font-bold">+' +
                            profitValue.toLocaleString() + ' ريال</span></div>');
                        $display.append(
                            '<div class="flex items-center justify-between text-xs text-gray-300"><span>إجمالي العائد:</span><span class="text-white font-bold">' +
                            total.toLocaleString() + ' ريال</span></div>');
                        const $actions = $('<div class="flex justify-center gap-2"></div>');
                        // include data attributes to make the edit action self-contained (robust if items array isn't available later)
                        const safeName = (p.name || '').toString().replace(/"/g, '\\"');
                        const safeDesc = (p.description || '').toString().replace(/"/g, '\\"');
                        const safeDuration = (p.duration_months !== undefined && p.duration_months !== null) ? p.duration_months : 12;
                        const safeActive = p.is_active ? 1 : 0;
                        $actions.append(
                            '<button onclick="return window.__openPkgEditFromBtn(this)" class="btn-edit border border-blue-500/50 text-blue-400 rounded-md px-3 py-1.5 text-sm" data-id="' + id + '" data-name="' + safeName + '" data-duration="' + safeDuration + '" data-desc="' + safeDesc + '" data-active="' + safeActive + '" data-amount="' + (amount || '') + '" data-profit-rate="' + (profit || '') + '"><i data-lucide="edit" class="w-3 h-3"></i></button>');
                        $actions.append(
                            '<button class="btn-delete border border-red-500/50 text-red-400 rounded-md px-3 py-1.5 text-sm" data-id="' +
                            id + '"><i data-lucide="trash-2" class="w-3 h-3"></i></button>');
                        $content.append($display).append($actions);
                        $card.append($content);
                        $wrap.append($card);
                    });

                    if (window.lucide && lucide.createIcons) {
                        lucide.createIcons();
                    }

                    // handlers
                        $('#packages-list').off('click.pkg').on('click.pkg', '.btn-edit', function() {
                        const $btn = $(this);
                        const id = $btn.data('id');
                        console.debug('btn-edit clicked', { id: id });
                        let item = (items || []).find(x => String(x.id) === String(id)) || {};
                        // If item not found in items array, build from data-attributes embedded on the button
                        if (!item || !item.id) {
                            try {
                                item = item || {};
                                item.id = id;
                                // prefer explicit data attributes for amount and profit-rate (more reliable than DOM parsing)
                                const dAmount = $btn.attr('data-amount');
                                const dProfit = $btn.attr('data-profit-rate');
                                if (typeof dAmount !== 'undefined' && dAmount !== '') {
                                    item.amount = parseFloat(dAmount) || (item.amount || 0);
                                } else {
                                    item.amount = parseFloat($btn.closest('div').find('.text-2xl').first().text().replace(/[^0-9.]/g, '')) || (item.amount || 0);
                                }
                                if (typeof dProfit !== 'undefined' && dProfit !== '') {
                                    item.profit_rate = parseFloat(dProfit) || item.profit_rate || null;
                                } else {
                                    item.profit_rate = parseFloat($btn.data('profit-rate')) || item.profit_rate || null;
                                }
                                // read explicit data attributes we embedded
                                const dName = $btn.attr('data-name');
                                const dDuration = $btn.attr('data-duration');
                                const dDesc = $btn.attr('data-desc');
                                const dActive = $btn.attr('data-active');
                                if (typeof dName !== 'undefined') item.name = dName;
                                if (typeof dDuration !== 'undefined') item.duration_months = parseInt(dDuration, 10) || 12;
                                if (typeof dDesc !== 'undefined') item.description = dDesc;
                                if (typeof dActive !== 'undefined') item.is_active = (dActive == '1' || dActive === 'true');
                            } catch (e) { console.error('failed to build item from btn attributes', e); }
                        }
                        try {
                            console.debug('opening pkg modal with item', item);
                            openPkgModal('edit', item);
                        } catch (err) {
                            console.error('openPkgModal threw', err && err.stack ? err.stack : err, item);
                            // Fallback: populate modal directly to ensure admin can edit
                            try {
                                $('#pkg-modal-title').text('تعديل حزمة');
                                $('#pkg-modal').data('mode', 'edit').data('id', item.id);
                                $('#pkg-amount').val(item.amount || '');
                                $('#pkg-profit').val(item.profit_rate || '');
                                $('#pkg-name').val(item.name || '');
                                $('#pkg-duration').val(item.duration_months ?? 12);
                                $('#pkg-desc').val(item.description || '');
                                $('#pkg-active').prop('checked', (typeof item.is_active !== 'undefined') ? !!item.is_active : true);
                                updatePkgPreview();
                                $('#pkg-modal').removeClass('hidden').addClass('flex');
                                console.debug('Fallback: modal populated directly');
                            } catch (err2) {
                                console.error('Fallback populate also failed', err2, item);
                                // Developer hard fallback: inject a minimal modal into body so admin can still edit
                                                                        try {
                                                                        var modalId = 'pkg-modal-dev-fallback';
                                                                        // remove any previous fallback
                                                                        $('#' + modalId).remove();
                                                                        var html = `
<div id="${modalId}" class="fixed inset-0 z-60 flex items-center justify-center bg-black/60">
    <!-- BEGIN: Full copied pkg-modal (verbatim) -->
    <div id="pkg-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="bg-gray-900 border border-gray-700 rounded-lg w-full max-w-lg p-4 max-h-[90vh] overflow-y-auto mx-3">
            <div class="flex items-center justify-between mb-3">
                <h4 id="pkg-modal-title" class="text-white font-semibold">حزمة جديدة</h4>
                <button id="pkg-modal-close" class="text-gray-300">✕</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">مبلغ الاستثمار (ريال)</label>
                    <input type="number" id="pkg-amount" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">معدل الربح (%)</label>
                    <input type="number" step="0.01" id="pkg-profit" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">اسم الحزمة</label>
                    <input type="text" id="pkg-name" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">المدة (شهور)</label>
                    <input type="number" id="pkg-duration" min="1" step="1" value="12" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />
                </div>
                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center text-sm text-gray-300"><input type="checkbox" id="pkg-active" checked class="mr-2"/> مفعل</label>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-sm text-gray-300 mb-1">وصف (اختياري)</label>
                <textarea id="pkg-desc" rows="2" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white"></textarea>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                    <p class="text-gray-300 text-sm mb-1">الربح المتوقع</p>
                    <p id="pkg-prev-profit" class="text-green-400 font-bold">+0 ريال</p>
                </div>
                <div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">
                    <p class="text-gray-300 text-sm mb-1">إجمالي العائد</p>
                    <p id="pkg-prev-total" class="text-white font-bold">0 ريال</p>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-white font-semibold mb-2">معاينة البطاقة</p>
                <div class="glass-card" style="cursor:default;border:2px solid transparent;transition:all .2s;">
                    <div style="text-align:center;padding:14px 14px 0 14px;">
                        <div style="font-size:22px;color:#22d3ee;font-weight:700;margin-bottom:8px;" id="pkg-prev-amount">0 ريال</div>
                    </div>
                    <div style="text-align:center;padding:0 14px 14px 14px;">
                        <div style="background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;border-radius:999px;padding:10px 14px;margin-bottom:10px;">
                            <div style="font-size:24px;font-weight:800;" id="pkg-prev-rate">0%</div>
                            <div style="font-size:12px;font-weight:600;">عائد شهري</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                            <span style="color:#9ca3af;">الأرباح:</span>
                            <span style="color:#22c55e;font-weight:700;" id="pkg-prev-profit-line">+0 ريال</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:10px;">
                            <span style="color:#9ca3af;">إجمالي العائد:</span>
                            <span style="color:#fff;font-weight:700;" id="pkg-prev-total-line">0 ريال</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-3">
                <button id="pkg-modal-cancel" class="border border-gray-600 text-gray-200 rounded-md px-3 py-2">إلغاء</button>
                <button id="pkg-modal-save" class="bg-green-600 hover:bg-green-700 text-white rounded-md px-3 py-2">حفظ</button>
            </div>
        </div>
    </div>
    <!-- END: Full copied pkg-modal -->
</div>`;
                                                                        $('body').append(html);
                                                                        var $root = $('#' + modalId);
                                                                        // show the copied inner modal (original markup has 'hidden')
                                                                        $root.find('#pkg-modal').removeClass('hidden').addClass('flex');
                                                                        // prefill inputs inside the copied modal from item
                                                                        try{
                                                                            $root.find('#pkg-amount').val(item.amount || '');
                                                                            $root.find('#pkg-profit').val(item.profit_rate || '');
                                                                            $root.find('#pkg-name').val(item.name || '');
                                                                            $root.find('#pkg-duration').val(item.duration_months ?? 12);
                                                                            $root.find('#pkg-desc').val(item.description || '');
                                                                            $root.find('#pkg-active').prop('checked', (typeof item.is_active !== 'undefined') ? !!item.is_active : true);
                                                                            // update preview fields inside wrapper
                                                                            $root.find('#pkg-prev-amount').text((item.amount ? Number(item.amount).toLocaleString() + ' ريال' : '0 ريال'));
                                                                            $root.find('#pkg-prev-rate').text((item.profit_rate || 0) + '%');
                                                                            var pv = Math.round((item.amount || 0) * ((item.profit_rate || 0) / 100));
                                                                            $root.find('#pkg-prev-profit').text((pv ? ('+' + pv.toLocaleString() + ' ريال') : '+0 ريال'));
                                                                            $root.find('#pkg-prev-profit-line').text((pv ? ('+' + pv.toLocaleString() + ' ريال') : '+0 ريال'));
                                                                            var tot = (item.amount || 0) + pv;
                                                                            $root.find('#pkg-prev-total').text((tot ? tot.toLocaleString() + ' ريال' : '0 ريال'));
                                                                            $root.find('#pkg-prev-total-line').text((tot ? tot.toLocaleString() + ' ريال' : '0 ريال'));
                                                                        }catch(e){ console.warn('prefill fallback modal failed', e); }
                                                                        // handlers scoped to the wrapper so the injected copy works correctly
                                                                        $root.find('#pkg-modal-close, #pkg-modal-cancel').on('click', function(){ $root.remove(); });
                                                                        $root.find('#pkg-modal-save').on('click', function(){
                                                                            const $r = $root;
                                                                            const amount = parseFloat($r.find('#pkg-amount').val() || 0);
                                                                            const profit_rate = parseFloat($r.find('#pkg-profit').val() || 0);
                                                                            const name = ($r.find('#pkg-name').val() || '').trim();
                                                                            const duration_months = parseInt($r.find('#pkg-duration').val() || 12, 10) || 12;
                                                                            const description = ($r.find('#pkg-desc').val() || '').trim();
                                                                            const is_active = !!$r.find('#pkg-active').is(':checked');
                                                                            const id = item.id;
                                                                            if (!id) return alert('معرّف الحزمة غير متوفر');
                                                                            $.ajax({
                                                                                url: api + '/admin/investment-packages/' + id,
                                                                                method: 'PUT',
                                                                                contentType: 'application/json',
                                                                                data: JSON.stringify({ amount, profit_rate, name, duration_months, description, is_active }),
                                                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                                                            }).done(function(){ $root.remove(); fetchInvestmentPackages(); }).fail(function(){ alert('تعذر الحفظ'); });
                                                                        });
                                                                } catch (err3) {
                                                                        console.error('Creating fallback modal failed', err3, item);
                                                                        alert('خطأ داخلي شديد: تعذّر فتح نافذة التعديل أو إنشاء نافذة احتياطية. راجع Console للمزيد من التفاصيل.');
                                                                }
                            }
                        }
                        // Global fallback: also bind at document level to catch edits rendered in different contexts
                        $(document).off('click.pkgGlobal').on('click.pkgGlobal', '.btn-edit', function(e){
                            try {
                                e.preventDefault();
                                const $btn = $(this);
                                const id = $btn.data('id') || $btn.attr('data-id');
                                const item = {
                                    id: id,
                                    amount: (function(){ const v = $btn.attr('data-amount') || $btn.data('amount'); return (typeof v !== 'undefined' && v !== '') ? parseFloat(v) : 0; })(),
                                    profit_rate: (function(){ const v = $btn.attr('data-profit-rate') || $btn.data('profit-rate'); return (typeof v !== 'undefined' && v !== '') ? parseFloat(v) : null; })(),
                                    name: $btn.attr('data-name') || $btn.data('name') || '',
                                    duration_months: parseInt($btn.attr('data-duration') || $btn.data('duration') || 12, 10) || 12,
                                    description: $btn.attr('data-desc') || $btn.data('desc') || '',
                                    is_active: (($btn.attr('data-active') || $btn.data('active')) == '1' || $btn.attr('data-active') === 'true' || $btn.data('active') === 1)
                                };
                                console.debug('pkg edit (global) clicked', { id: id, item: item });
                                openPkgModal('edit', item);
                            } catch(e) { console.error('pkg edit global handler failed', e); }
                        });
                    }).on('click.pkg', '.btn-delete', function() {
                        const id = $(this).data('id');
                        if (!confirm('حذف الحزمة؟')) return;
                        $.ajax({
                                url: api + '/admin/investment-packages/' + id,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                fetchInvestmentPackages();
                            })
                            .fail(function() {
                                alert('تعذر الحذف');
                            });
                    });
                }

                <!-- renderLogs removed -->

                function renderNavigation(items) {
                    const $wrap = $('#nav-list').empty();
                    $('#nav-count').text((items?.length || 0) + ' عنصر');
                    (items || []).forEach(function(n) {
                        const $card = $(
                            '<div class="nav-card bg-gray-800/50 border border-gray-700 rounded-md p-4" data-id="' +
                            n.id + '"></div>');
                        // view row
                        const $view = $('<div class="view flex items-center justify-between"></div>');
                        const $left = $('<div class="flex items-center gap-3"></div>');
                        $left.append('<div class="text-xl">' + (n.icon || '') + '</div>');
                        $left.append('<div><p class="text-white font-semibold">' + (n.label || '-') +
                            '</p><p class="text-gray-400 text-sm">' + (n.path || '') + '</p></div>');
                        const $right = $('<div class="flex items-center gap-2"></div>');
                        $right.append(
                            '<label class="inline-flex items-center text-sm text-white"><input type="checkbox" class="nav-toggle-active mr-2" ' +
                            (n.active ? 'checked' : '') + ' />مفعل</label>');
                        $right.append('<span class="text-xs text-gray-300">ترتيب: ' + (n.order ?? 0) + '</span>');
                        $right.append(
                            '<button class="nav-edit border border-blue-500/50 text-blue-400 rounded-md px-3 py-1.5 text-xs">تعديل</button>'
                            );
                        $right.append(
                            '<button class="nav-delete border border-red-500/50 text-red-400 rounded-md px-3 py-1.5 text-xs">حذف</button>'
                            );
                        $view.append($left).append($right);

                        // edit row
                        const $edit = $(
                            '<div class="edit hidden grid grid-cols-1 md:grid-cols-5 gap-3 mt-3"></div>');
                        $edit.append(
                            '<input class="f-label bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' +
                            (n.label || '') + '" />');
                        $edit.append(
                            '<input class="f-path bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' +
                            (n.path || '') + '" />');
                        $edit.append(
                            '<input class="f-icon bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' +
                            (n.icon || '') + '" />');
                        $edit.append(
                            '<input class="f-order bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" type="number" value="' +
                            (n.order ?? 0) + '" />');
                        $edit.append(
                            '<div class="flex items-end gap-2"><label class="inline-flex items-center text-white text-sm"><input type="checkbox" class="f-active mr-2" ' +
                            (n.active ? 'checked' : '') +
                            ' />مفعل</label><button class="nav-save bg-green-600 hover:bg-green-700 text-white rounded-md px-3 py-2 text-sm">حفظ</button><button class="nav-cancel border border-gray-600 text-gray-200 rounded-md px-3 py-2 text-sm">إلغاء</button></div>'
                            );

                        $card.append($view).append($edit);
                        $wrap.append($card);
                    });
                }

                function renderBackups(items) {
                    const $wrap = $('#backups-list').empty();
                    (items || []).forEach(b => {
                        const $row = $(
                            '<div class="bg-gray-800/60 border border-gray-700 rounded-md p-3 flex items-center justify-between"></div>'
                            );
                        const $left = $('<div class="flex items-center gap-3"></div>');
                        $left.append('<div class="text-white">' + (b.file || '-') + '</div>');
                        $left.append('<div class="text-gray-300 text-xs">' + (b.size_mb || 0) + 'MB · ' + (b
                            .created_at || '') + '</div>');
                        const $actions = $('<div class="flex items-center gap-2"></div>');
                        // show download link only if user can view or manage backups
                        const canManageBackups = (window.MY_PERMS || []).includes('manage_backups');
                        const canViewBackups = (window.MY_PERMS || []).includes('view_backups');
                        if (canManageBackups || canViewBackups) {
                            $actions.append(
                                '<a class="border border-blue-500/50 text-blue-400 hover:bg-blue-500/10 text-xs rounded-md px-3 py-1.5" href="' +
                                api + '/admin/backups/download/' + encodeURIComponent(b.file) + '">تنزيل</a>');
                        }
                        if (canManageBackups) {
                            $actions.append(
                                '<button class="border border-emerald-500/50 text-emerald-400 hover:bg-emerald-500/10 text-xs rounded-md px-3 py-1.5 btn-restore" data-file="' +
                                b.file + '">استعادة</button>');
                        }
                        $row.append($left).append($actions);
                        $wrap.append($row);
                    });
                }

                // Fetch current user's permissions and apply UI gating
                function fetchMyPermsAndApply() {
                    window.MY_PERMS = window.MY_PERMS || [];
                    $.get(api + '/admin/me/permissions').done(function(perms){
                        window.MY_PERMS = Array.isArray(perms) ? perms : [];
                        applyPermissionUI();
                    }).fail(function(){
                        window.MY_PERMS = [];
                        applyPermissionUI();
                    });
                }

                function applyPermissionUI(){
                    const perms = window.MY_PERMS || [];
                    const canViewReports = perms.includes('view_reports') || perms.includes('manage_reports');
                    const canManageBackups = perms.includes('manage_backups');
                    const canViewBackups = perms.includes('view_backups') || canManageBackups;

                    // Reports: if no view, disable apply and show a message
                    if (!canViewReports) {
                        $('#rep-apply').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                        $('#rep-status').text('محجوب — لا تمتلك صلاحية عرض التقارير');
                    } else {
                        $('#rep-apply').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                        $('#rep-status').text('');
                    }

                    // Backups: show/hide run+restore buttons
                    if (!canManageBackups) {
                        $('#run-backup').hide();
                        $('#restore-backup').hide();
                        $('#restore-file').hide();
                    } else {
                        $('#run-backup').show();
                        $('#restore-backup').show();
                        $('#restore-file').show();
                    }

                    // When rendering backup list, renderBackups reads window.MY_PERMS
                    // Trigger a refresh so action buttons update
                    try { fetchBackups(); } catch(e){}
                }

                // Initialize perms early
                $(function(){ fetchMyPermsAndApply(); });

                    function fillSettingsForm(data){
                        const p = (data && data.platform) || {};
                        const c = (data && data.contact) || {};
                        const adv = (data && data.advanced) || {};
                        const adminInfo = (data && data.admin) || {};
                        const bank = (data && data.bank) || {};
                    // Keep a copy of platform data so cancel can revert edits
                    window._lastPlatformData = p || {};

                    $('#set-platform-name').val(p.name || '');
                    $('#set-platform-url').val(p.url || '');
                    $('#set-platform-locale').val(p.locale || '');
                    $('#set-platform-description').val(p.description || '');
                    // set apply-target checkboxes based on overrides
                    try {
                        // global: checked if platform DB row exists
                        $('#apply-global').prop('checked', !!(window._platformOverride && Object.keys(window._platformOverride).length));
                        var pages = (data && data.pages) || {};
                        $('#apply-home').prop('checked', !!(pages.home && pages.home.platform && Object.keys(pages.home.platform).length));
                        $('#apply-admin').prop('checked', !!(pages.admin && pages.admin.platform && Object.keys(pages.admin.platform).length));
                        $('#apply-navbar').prop('checked', !!(pages.navbar && pages.navbar.platform && Object.keys(pages.navbar.platform).length));
                        $('#apply-title').prop('checked', !!(pages.title && pages.title.platform && Object.keys(pages.title.platform).length));
                    } catch(e) { console.warn('apply-checkbox init err', e); }

                    // Ensure platform inputs are not editable until admin clicks "تعديل"
                    $('#set-platform-name,#set-platform-url,#set-platform-locale,#set-platform-description').prop('disabled', true);
                    // Keep a copy of contact data so cancel can revert edits
                    window._lastContactData = c || {};
                    // Fill contact inputs and keep them disabled until edit is requested
                    $('#set-contact-email').val(c.email || '');
                    $('#set-contact-phone').val(c.phone || '');
                    $('#set-contact-whatsapp').val(c.whatsapp || '');
                    $('#set-contact-address').val(c.address || '');
                    $('#set-contact-email,#set-contact-phone,#set-contact-whatsapp,#set-contact-address').prop('disabled', true);
                    // branding
                    $('#brand-logo-url').val(p.logo_url || '');
                    $('#brand-footer-text').val(p.footer_text || '');
                    const s = c.social || {};
                    $('#social-twitter').val(s.twitter || '');
                    $('#social-instagram').val(s.instagram || '');
                    $('#social-linkedin').val(s.linkedin || '');
                    $('#social-facebook').val(s.facebook || '');
                    $('#social-youtube').val(s.youtube || '');
                    // advanced toggles
                    $('#adv-maintenance').prop('checked', !!adv.maintenance_mode);
                    $('#adv-registration').prop('checked', adv.registration_enabled !== false);
                    $('#adv-investment').prop('checked', adv.investment_enabled !== false);
                    $('#adv-success-rate').val(typeof adv.default_success_rate !== 'undefined' ? adv.default_success_rate :
                        98);
                    // header sub-title platform name
                    if (p.name) {
                        $("header .text-gray-400.text-sm").first().text(p.name);
                    }

                    // admin information
                    $('#set-admin-name').val(adminInfo.name||'');
                    $('#set-admin-email').val(adminInfo.email||'');
                    $('#set-admin-phone').val(adminInfo.phone||'');
                    $('#set-admin-whatsapp').val(adminInfo.whatsapp||'');
                    $('#set-admin-title').val(adminInfo.title||'');
                    $('#set-admin-avatar').val(adminInfo.avatar||'');
                    $('#set-admin-note').val(adminInfo.note||'');
                    if(adminInfo.name){ $('#admin-fullname').text(adminInfo.name); }

                    // bank transfer info
                    const DEFAULT_BANK_INSTRUCTIONS = [
                        'يرجى التحويل بنفس المبلغ المستحق الموضح أعلاه.',
                        'قم بكتابة رمز المرجع في خانة ملاحظة التحويل.',
                        'بعد التحويل، قم بإرفاق إيصال التحويل للمراجعة السريعة.'
                    ].join('\n');
                    $('#set-bank-account-name').val(bank.account_name || '');
                    $('#set-bank-name').val(bank.bank_name || '');
                    $('#set-bank-account-number').val(bank.account_number || '');
                    // helpers to format/strip IBAN
                    if (!window._formatIBAN) { window._formatIBAN = function(iban){ try{ return String(iban||'').replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' ');}catch(e){ return iban||''; } }; }
                    if (!window._stripIBAN) { window._stripIBAN = function(iban){ return String(iban||'').replace(/\s+/g,'').toUpperCase(); }; }
                    $('#set-bank-iban').val(window._formatIBAN(bank.iban || ''));
                    $('#set-bank-swift').val(bank.swift || '');
                    $('#set-bank-reference-code').val(bank.reference_code || '');
                    // If the bank object explicitly has the 'instructions' property (even empty), show it as-is so admin can clear it.
                    if (Object.prototype.hasOwnProperty.call(bank, 'instructions')) {
                        $('#set-bank-instructions').val(bank.instructions || '');
                    } else {
                        $('#set-bank-instructions').val(DEFAULT_BANK_INSTRUCTIONS);
                    }
                    $('#bank-ask-sender').prop('checked', !!bank.ask_sender);
                    $('#bank-ask-datetime').prop('checked', !!bank.ask_datetime);
                    $('#bank-require-receipt').prop('checked', !!bank.require_receipt);
                }

                // Load current active bank account via admin API (for the Bank tab)
                function loadBankAccounts() {
                    $.get(api + '/admin/bank-accounts').done(function(list){
                        // find active
                        var active = null;
                        if (Array.isArray(list)) {
                            for (var i=0;i<list.length;i++){
                                if (list[i].is_active) { active = list[i]; break; }
                            }
                        }
                        if (!active && Array.isArray(list) && list.length>0) active = list[0];
                        if (active) {
                            $('#set-bank-account-name').val(active.account_name || '');
                            $('#set-bank-name').val(active.bank_name || '');
                            $('#set-bank-account-number').val(active.account_number || '');
                            $('#set-bank-iban').val(window._formatIBAN(active.iban || ''));
                            $('#set-bank-swift').val(active.swift || '');
                            $('#set-bank-reference-code').val(active.reference_code || '');
                            if (Object.prototype.hasOwnProperty.call(active, 'instructions')) {
                                $('#set-bank-instructions').val(active.instructions || '');
                            } else {
                                $('#set-bank-instructions').val(DEFAULT_BANK_INSTRUCTIONS);
                            }
                            $('#bank-ask-sender').prop('checked', !!active.ask_sender);
                            $('#bank-ask-datetime').prop('checked', !!active.ask_datetime);
                            $('#bank-require-receipt').prop('checked', !!active.require_receipt);
                            // store active id on save button for updates
                            $('#bank-save').data('active-id', active.id);
                        } else {
                            $('#bank-save').data('active-id', '');
                        }
                    }).fail(function(){
                        // ignore silently
                    });
                }

                // Handle save button: create or update
                $('#bank-save').off('click').on('click', function(){
                    var btn = $(this);
                        var payload = {
                        account_name: $('#set-bank-account-name').val() || '',
                        bank_name: $('#set-bank-name').val() || '',
                        account_number: $('#set-bank-account-number').val() || '',
                        iban: window._stripIBAN($('#set-bank-iban').val() || ''),
                        swift: $('#set-bank-swift').val() || '',
                            reference_code: $('#set-bank-reference-code').val() || '',
                        instructions: $('#set-bank-instructions').val() || '',
                        ask_sender: $('#bank-ask-sender').is(':checked') ? 1 : 0,
                        ask_datetime: $('#bank-ask-datetime').is(':checked') ? 1 : 0,
                        require_receipt: $('#bank-require-receipt').is(':checked') ? 1 : 0,
                        is_active: 1,
                        _token: '{{ csrf_token() }}'
                    };

                    var activeId = btn.data('active-id');
                    btn.text('... جاري الحفظ').prop('disabled', true);
                    if (activeId && activeId !== ''){
                        $.ajax({url: api + '/admin/bank-accounts/' + activeId, method: 'PUT', data: payload}).done(function(){
                            btn.text('تم الحفظ').prop('disabled', false);
                            setTimeout(function(){ btn.text('حفظ إعدادات التحويل'); }, 1500);
                            // reload investor-facing public endpoint isn't necessary here; frontend will fetch on demand
                        }).fail(function(xhr){ btn.text('حفظ إعدادات التحويل').prop('disabled', false); alert('تعذر حفظ إعدادات البنك'); });
                    } else {
                        $.post(api + '/admin/bank-accounts', payload).done(function(){
                            btn.text('تم الإنشاء').prop('disabled', false);
                            setTimeout(function(){ btn.text('حفظ إعدادات التحويل'); }, 1500);
                        }).fail(function(){ btn.text('حفظ إعدادات التحويل').prop('disabled', false); alert('تعذر إنشاء حساب بنكي'); });
                    }
                });

                // initialize bank tab load when script runs
                $(function(){
                    loadBankAccounts();
                    // format IBAN visually as admin types/pastes
                    try{
                        $('#set-bank-iban').on('input blur', function(){
                            var v = $(this).val();
                            if(window._formatIBAN){ $(this).val(window._formatIBAN(v)); }
                            else { try{ v = String(v||'').replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' '); $(this).val(v);}catch(e){} }
                        });
                    }catch(e){}
                });

                function fetchUsers(q) {
                    // request a larger set by default; controller supports ?limit=
                    const url = api + '/users?limit=100' + (q ? '&q=' + encodeURIComponent(q) : '');
                    return $.get(url).done(function(d) {
                        // expected: an array of users from the DB
                        const users = Array.isArray(d) ? d : [];
                        // cache full list for client-side filtering
                        window.__USERS__ = users;

                        // If activity wasn't available from fetchActivity (e.g., 401), synthesize recent activity
                        // from investments and users so the Overview shows DB-driven items.
                        try {
                            if (!Array.isArray(window.__ACT) || window.__ACT.length === 0) {
                                var synth = [];
                                (window.__INV || []).slice(0,5).forEach(function(inv){
                                    var amt = (inv && inv.amount) ? (Number(inv.amount).toLocaleString() + ' ر.س') : (inv && inv.amount) || '';
                                    synth.push({ type: 'investment_created', text: 'طلب استثمار جديد #' + (inv.id || '') + (amt ? ' بمبلغ ' + amt : ''), time: inv.created_at || '' });
                                });
                                (users || []).slice(0,5).forEach(function(u){
                                    var label = (u.full_name || u.email || ('#' + (u.id || '')));
                                    synth.push({ type: 'user_registration', text: 'تسجيل مستخدم جديد: ' + label, time: u.created_at || '' });
                                });
                                synth.sort(function(a,b){ return new Date(b.time || 0) - new Date(a.time || 0); });
                                window.__ACT = synth.slice(0,10);
                            }
                        } catch(e) { /* ignore synthesis failures */ }

                        // apply current role filter if present
                        var roleFilterEl = document.getElementById('user-role-filter');
                        var roleFilter = roleFilterEl ? roleFilterEl.value : 'all';
                        var filtered = users;
                        if (roleFilter === 'users') {
                            filtered = users.filter(u => (u.role === 'admin' || u.role === 'staff'));
                        } else if (roleFilter === 'investors') {
                            filtered = users.filter(u => (u.role === 'investor'));
                        }

                        renderUsers(filtered);
                        renderOverview(users, window.__INV || [], window.__ACT || []);
                    }).fail(function(xhr) {
                        // clear list and show a helpful message instead of demo data
                        if (xhr && xhr.status === 401) {
                            $('#users-list').empty().append('<div class="text-gray-400">غير مصرح. سجّل الدخول كمسؤول لعرض المستخدمين.</div>');
                            $('#users-count').text('0 مستخدم');
                            renderOverview([], window.__INV || [], window.__ACT || []);
                            return;
                        }
                        window.__USERS__ = [];
                        $('#users-list').empty().append('<div class="text-gray-400">تعذر تحميل المستخدمين حالياً</div>');
                        $('#users-count').text('0 مستخدم');
                        try {
                            // synthesize activity from investments if users fetch failed and activity empty
                            if (!Array.isArray(window.__ACT) || window.__ACT.length === 0) {
                                var synth2 = [];
                                (window.__INV || []).slice(0,6).forEach(function(inv){
                                    var amt2 = (inv && inv.amount) ? (Number(inv.amount).toLocaleString() + ' ر.س') : (inv && inv.amount) || '';
                                    synth2.push({ type: 'investment_created', text: 'طلب استثمار جديد #' + (inv.id || '') + (amt2 ? ' بمبلغ ' + amt2 : ''), time: inv.created_at || '' });
                                });
                                synth2.sort(function(a,b){ return new Date(b.time || 0) - new Date(a.time || 0); });
                                window.__ACT = synth2.slice(0,8);
                            }
                        } catch(e) {}
                        renderOverview([], window.__INV || [], window.__ACT || []);
                    });
                }

                function fetchInvestments(q) {
                    const url = api + '/investments' + (q ? '?q=' + encodeURIComponent(q) : '');
                    return $.get(url).done(function(d) {
                        window.__INV = d;
                        renderInvestments(d);
                    }).fail(function() {
                        renderInvestments(DEMO_DATA.investments);
                        window.__INV = DEMO_DATA.investments;
                    });
                }

                function fetchLicenses(q) {
                    const url = api + '/business-licenses' + (q ? '?q=' + encodeURIComponent(q) : '');
                    return $.get(url)
                        .done(renderLicenses)
                        .fail(function(xhr) {
                            // لا نعرض بيانات وهمية هنا. إن كان غير مصرح (401) نظهر رسالة بسيطة.
                            if (xhr && xhr.status === 401) {
                                $('#licenses-list').empty().append('<div class="text-gray-400">غير مصرح. سجّل الدخول كمسؤول لعرض البيانات.</div>');
                                $('#licenses-count').text('0 ترخيص');
                                return;
                            }
                            // في حالات الخطأ الأخرى نعرض حالة فارغة
                            $('#licenses-list').empty().append('<div class="text-gray-400">تعذر تحميل البيانات حالياً</div>');
                            $('#licenses-count').text('0 ترخيص');
                        });
                }

                function fetchPackages(q) {
                    // Prefer comprehensive CRUD endpoint
                    const url = api + '/admin/investment-packages' + (q ? '?q=' + encodeURIComponent(q) : '');
                    return $.get(url).done(renderPackages).fail(function() {
                        // fallback to simple packages list
                        const url2 = api + '/admin/packages' + (q ? '?q=' + encodeURIComponent(q) : '');
                        $.get(url2).done(renderPackages).fail(function() {
                            renderPackages([
                                {
                                    id: 1,
                                    amount: 5000,
                                    profit_rate: 8
                                }
                            ]);
                        });
                    });
                }

                // Fetch recent activity (lightweight): populate window.__ACT but do NOT render a separate Logs UI
                function fetchActivity(q) {
                    const url = api + '/admin/logs' + (q ? '?q=' + encodeURIComponent(q) : '');
                    return $.get(url).done(function(d) {
                        // normalize to minimal shape used by renderOverview
                        try {
                            window.__ACT = Array.isArray(d) ? d.map(x => ({ id: x.id || (x.type + '_' + (x.created_at || x.time || Date.now())), type: x.type || 'activity', text: x.message || x.text || '', time: x.created_at || x.time })) : [];
                        } catch (e) { window.__ACT = []; }
                    }).fail(function() {
                        // If logs endpoint isn't available (403/401) leave window.__ACT empty
                        // so client-side synthesis from investments/users can run later.
                        window.__ACT = [];
                    });
                }

                <!-- fetchLogs removed -->

                function fetchBackups() {
                    return $.get(api + '/admin/backups').done(renderBackups).fail(function() {
                        renderBackups([{
                            file: 'backup_demo.zip',
                            size_mb: 120,
                            created_at: '2025-10-01 00:00:00'
                        }]);
                    });
                }

                function fetchSettings() {
                    // First try to fetch platform-specific DB-backed settings, then fetch the full admin settings
                    // and merge platform values so the UI prefers the DB row for platform info.
                    return $.get(api + '/admin/platform-settings').done(function(pdata) {
                        // store platform override for merging
                        window._platformOverride = pdata || {};
                    }).fail(function() {
                        window._platformOverride = null;
                    }).always(function() {
                        return $.get(api + '/admin/settings').done(function(data) {
                            data = data || {};
                            // merge platform override if present
                            if (window._platformOverride && Object.keys(window._platformOverride).length) {
                                data.platform = data.platform || {};
                                // copy only known platform keys to avoid clobbering other nested data
                                ['name', 'url', 'locale', 'description', 'logo_url', 'footer_text'].forEach(function(k){
                                    if (typeof window._platformOverride[k] !== 'undefined') {
                                        data.platform[k] = window._platformOverride[k];
                                    }
                                });
                            }
                            fillSettingsForm(data);
                        }).fail(function() {
                            // fallback demo (merge platform override if available)
                            var demo = {
                                platform: {
                                    name: 'منصة الاستثمار',
                                    url: '/',
                                    locale: 'ar'
                                },
                                contact: {
                                    email: 'support@example.com',
                                    phone: '+966...',
                                    address: 'الرياض'
                                },
                                advanced: {
                                    maintenance_mode: false,
                                    registration_enabled: true,
                                    investment_enabled: true,
                                    default_success_rate: 98
                                }
                            };
                            if (window._platformOverride && Object.keys(window._platformOverride).length) {
                                demo.platform = demo.platform || {};
                                Object.assign(demo.platform, window._platformOverride);
                            }
                            fillSettingsForm(demo);
                        });
                    });
                }

                function fetchAnalytics() {
                    return $.get(api + '/admin/analytics').done(applyAnalytics).fail(function() {
                        applyAnalytics(DEMO_DATA.analytics);
                    });
                }

                function fetchNavigation() {
                    return $.get(api + '/admin/navigation').done(renderNavigation).fail(function() {
                        renderNavigation([]);
                    });
                }
                // alias to reuse after CRUD ops
                function fetchInvestmentPackages() {
                    return fetchPackages();
                }

                function updateInvestmentStatus(id, status) {
                    $.ajax({
                            url: api + '/admin/investments/' + id + '/status',
                            method: 'PUT',
                            data: {
                                status: status,
                                _token: '{{ csrf_token() }}'
                            }
                        })
                        .done(function() {
                            fetchInvestments();
                            fetchStats();
                        })
                        .fail(function() {
                            alert('فشل تحديث الحالة');
                        });
                }

                function updateLicenseStatus(id, status) {
                    $.ajax({
                            url: api + '/admin/licenses/' + id + '/status',
                            method: 'PUT',
                            data: {
                                status: status,
                                _token: '{{ csrf_token() }}'
                            }
                        })
                        .done(function() {
                            fetchLicenses();
                            fetchStats();
                        })
                        .fail(function() {
                            alert('فشل تحديث الحالة');
                        });
                }

                function initTabs() {
                    const tabs = ['overview', 'users', 'investors', 'investments', 'packages', 'navigation', 'reports', 'license-reports', 'backups',
                        'licenses', 'license-requests', 'settings', 'branding', 'bank', 'documents', 'chat', 'page-market', 'page-success',
                        'page-trust', 'user-permissions'
                    ];
                    const activeClasses = ['bg-blue-600'];

                    function activateTab(tab, pushState = true) {
                        // safety: default to overview when unknown
                        if (!tabs.includes(tab)) tab = 'overview';
                        // update UI buttons
                        $('.tab-trigger').removeClass(activeClasses.join(' '));
                        const $btn = $('.tab-trigger[data-tab="' + tab + '"]');
                        if ($btn && $btn.length) {
                            const activeBg = $btn.data('activeBg');
                            if (activeBg) $btn.addClass(activeBg);
                        }
                        // show/hide panels
                        tabs.forEach(t => $('#tab-' + t).toggleClass('hidden', t !== tab));
                        // sync mobile selector
                        $('#mobile-tabs').val(tab);

                        // update URL hash so refresh preserves tab; use replaceState to avoid history spam when called programmatically
                        try {
                            if (pushState) {
                                history.replaceState(null, '', '#' + tab);
                            } else {
                                // when explicitly user-clicked, replace as well
                                history.replaceState(null, '', '#' + tab);
                            }
                        } catch (e) {}

                        // persist last active tab in sessionStorage as fallback
                        try { sessionStorage.setItem('admin.activeTab', tab); } catch (e) {}
                        // restore scroll position for this tab if previously saved
                        try {
                            var scrollKey = 'admin.scroll.' + tab;
                            var pos = sessionStorage.getItem(scrollKey);
                            if (pos !== null) {
                                window.scrollTo(0, parseInt(pos) || 0);
                                // remove after restoring
                                sessionStorage.removeItem(scrollKey);
                            }
                        } catch (e) {}
                    }

                    // click handler: activate clicked tab and store
                    $(document).on('click', '.tab-trigger', function() {
                        const tab = $(this).data('tab');
                        activateTab(tab, true);
                        try {
                            // ensure relevant data is fetched when a tab is opened so it displays DB data
                            if (tab === 'overview') {
                                // fetch recent activity, then investments, then users so renderOverview receives DB data
                                try {
                                    fetchActivity().always(function(){
                                        fetchInvestments().always(function(){ try { fetchUsers(); } catch(e){} });
                                    });
                                } catch(e){
                                    try { fetchInvestments().always(function(){ try { fetchUsers(); } catch(e){} }); } catch(e){}
                                }
                                try { fetchStats(); } catch(e){}
                                try { fetchAnalytics(); } catch(e){}
                            } else if (tab === 'users') {
                                try { fetchUsers(); } catch(e){}
                            } else if (tab === 'investments') {
                                try { fetchInvestments(); } catch(e){}
                            } else if (tab === 'packages') {
                                try { fetchPackages(); } catch(e){}
                            } else if (tab === 'licenses' || tab === 'license-reports') {
                                try { fetchLicenses(); } catch(e){}
                            } else if (tab === 'backups') {
                                try { fetchBackups(); } catch(e){}
                            } else if (tab === 'documents') {
                                try { fetchDocuments(); } catch(e){}
                            } else if (tab === 'chat') {
                                try { /* chat init handled elsewhere */ } catch(e){}
                            }
                        } catch(err) { console.error('tab-load error', err); }
                    });

                    // mobile selector change -> trigger tab activation and same fetch logic
                    $(document).on('change', '#mobile-tabs', function() {
                        const tab = $(this).val();
                        activateTab(tab, true);
                        try {
                            if (tab === 'overview') {
                                try { fetchStats(); } catch(e){}
                                try { fetchAnalytics(); } catch(e){}
                                try { fetchInvestments(); } catch(e){}
                                try { fetchUsers(); } catch(e){}
                            }
                        } catch(e){}
                    });

                    // determine initial tab: priority -> URL hash, sessionStorage, default overview
                    let initial = 'overview';
                    try {
                        if (location.hash && location.hash.length > 1) {
                            initial = location.hash.replace('#', '');
                        } else if (sessionStorage.getItem('admin.activeTab')) {
                            initial = sessionStorage.getItem('admin.activeTab');
                        }
                    } catch (e) { initial = 'overview'; }
                    // activate initial tab without pushing extra history
                    activateTab(initial, false);

                    // Periodically refresh activity when Overview is visible (best-effort)
                    try {
                        setInterval(function(){
                            try {
                                // only poll when overview tab is active to reduce noise
                                var activeTab = (function(){ try { return $('#mobile-tabs').val() || document.location.hash.replace('#','') || 'overview'; } catch(e){ return 'overview'; } })();
                                if (activeTab !== 'overview') return;
                                fetchActivity().always(function(){
                                    // refresh investments and users so synthesis fallback works when needed
                                    fetchInvestments().always(function(){ try { fetchUsers(); } catch(e){} });
                                });
                            } catch(e){}
                        }, 30000); // every 30s
                    } catch(e){}

                    // Alt+1..Alt+9 hotkeys to switch tabs in the order shown in the sidebar
                    // Order matches the labels (logs removed): 1:overview, 2:users, 3:investments, 4:packages, 5:navigation, 6:settings, 7:backups, 8:licenses
                    const hotkeyOrder = ['overview', 'users', 'investments', 'packages', 'navigation', 'settings',
                        'backups', 'licenses'
                    ];
                    document.addEventListener('keydown', function(e) {
                        // Ignore if focused inside form fields or contenteditable
                        const tag = (e.target && e.target.tagName) ? e.target.tagName.toUpperCase() : '';
                        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (e.target && e.target
                                .isContentEditable)) {
                            return;
                        }
                        if (!e.altKey || e.ctrlKey || e.metaKey || e.shiftKey) return;
                        let idx = null;
                        if (e.code && e.code.startsWith('Digit')) {
                            idx = parseInt(e.code.slice(5), 10);
                        } else if (e.code && e.code.startsWith('Numpad')) {
                            idx = parseInt(e.code.slice(6), 10);
                        } else if (/[1-9]/.test(e.key)) {
                            idx = parseInt(e.key, 10);
                        }
                        // Support letter shortcuts for new investor tabs
                        let letterTarget = null;
                        if (!idx && e.key) {
                            const k = e.key.toLowerCase();
                            if (k === 'm') letterTarget = 'page-market';
                            if (k === 's') letterTarget = 'page-success';
                            if (k === 'c') letterTarget = 'page-trust';
                            if (k === 'b') letterTarget = 'branding';
                            if (k === 'd') letterTarget = 'documents';
                            if (k === 'h') letterTarget = 'chat';
                            if (k === 'r') letterTarget = 'license-requests';
                            if (k === 'i') letterTarget = 'investors';
                        }
                        if (letterTarget) {
                            e.preventDefault();
                            const $btn = document.querySelector('.tab-trigger[data-tab="' + letterTarget + '"]');
                            if ($btn) {
                                $($btn).focus().trigger('click');
                            }
                            return;
                        }
                        if (!idx || idx < 1 || idx > hotkeyOrder.length) return;
                        e.preventDefault();
                        const targetTab = hotkeyOrder[idx - 1];
                        const $btn = document.querySelector('.tab-trigger[data-tab="' + targetTab + '"]');
                        if ($btn) {
                            $($btn).focus().trigger('click');
                        }
                    }, true);
                }

                // Mobile sidebar drawer (admin dashboard only): open/close and clone sidebar content
                (function(){
                    try {
                        function openMobileSidebar(){
                            try{
                                var src = document.querySelector('#admin-sidebar > div');
                                var dst = document.getElementById('mobile-sidebar-content');
                                if (src && dst) { dst.innerHTML = src.innerHTML; }
                            }catch(e){}
                            // rehydrate icons if lucide is available
                            try{ if (window.lucide && typeof window.lucide.createIcons === 'function') { window.lucide.createIcons(); } }catch(_){ }
                            try{ document.getElementById('mobile-sidebar-overlay').classList.remove('hidden'); }catch(e){}
                            try{ document.getElementById('mobile-sidebar-drawer').classList.remove('translate-x-full'); }catch(e){}
                        }
                        function closeMobileSidebar(){
                            try{ document.getElementById('mobile-sidebar-drawer').classList.add('translate-x-full'); }catch(e){}
                            try{ setTimeout(function(){ try{ document.getElementById('mobile-sidebar-overlay').classList.add('hidden'); }catch(_){} }, 180); }catch(e){}
                        }
                        // open/close buttons (toggle behavior: open if closed, close if open)
                        $(document).off('click.mobSideOpen').on('click.mobSideOpen', '#mobile-sidebar-toggle', function(e){
                            e.preventDefault();
                            try{
                                var overlay = document.getElementById('mobile-sidebar-overlay');
                                var drawer = document.getElementById('mobile-sidebar-drawer');
                                var isOpen = false;
                                if(overlay && !overlay.classList.contains('hidden')) isOpen = true;
                                else if(drawer && !drawer.classList.contains('translate-x-full')) isOpen = true;
                                if(isOpen){ closeMobileSidebar(); } else { openMobileSidebar(); }
                            }catch(_){ try{ openMobileSidebar(); }catch(__){} }
                        });
                        $(document).off('click.mobSideClose1').on('click.mobSideClose1', '#mobile-sidebar-close', function(e){ e.preventDefault(); closeMobileSidebar(); });
                        $(document).off('click.mobSideClose2').on('click.mobSideClose2', '#mobile-sidebar-backdrop', function(){ closeMobileSidebar(); });
                        // close on any sidebar item selection
                        $(document).off('click.mobSideItem').on('click.mobSideItem', '#mobile-sidebar-content .tab-trigger', function(){ closeMobileSidebar(); });
                        // ESC to close
                        try{ document.addEventListener('keydown', function(ev){ try{ if(ev.key === 'Escape'){ closeMobileSidebar(); } }catch(_){} }, true); }catch(_){ }
                    } catch(_){ }
                })();

                function fmtMoney(n){
                    if(n===null||n===undefined||n==='') return '—';
                    const v = Number(n); if (Number.isNaN(v)) return '—';
                    return v.toLocaleString('ar-EG') + ' ريال';
                }

                // Reports helpers (lightweight, client-side only)
                function _toDate(val){ try{ return val ? new Date(val) : null; }catch(_){ return null; } }
                function _inRange(dt, from, to){ if(!dt) return true; var t = dt.getTime(); if(from && t < from.getTime()) return false; if(to && t > to.getTime()) return false; return true; }
                function _asNumber(v, def){ var n = Number(v); return Number.isFinite(n) ? n : (def||0); }

                // State for Reports tab
                var REP = { rows: [], charts: {}, investors: [], filters: {} };

                function _fmtDateUi(val){
                    try{ if(!val) return ''; var d = new Date(val); return d.toLocaleDateString('ar-EG'); }catch(_){ return val||''; }
                }
                function updateRepHeader(){
                    var from = $('#rep-from').val();
                    var to = $('#rep-to').val();
                    var $range = $('#rep-header-range');
                    var text = 'النطاق الزمني: ';
                    if(from && to){ text += 'من ' + _fmtDateUi(from) + ' إلى ' + _fmtDateUi(to); }
                    else if(from){ text += 'ابتداءً من ' + _fmtDateUi(from); }
                    else if(to){ text += 'حتى ' + _fmtDateUi(to); }
                    else { text += 'كل الفترات'; }
                    $range.text(text);
                    $('#rep-header-title').text('تقرير الاستثمارات');
                }

                function _fmtDate(s){ if(!s) return '—'; try{ return new Date(s).toLocaleDateString('ar-EG'); }catch(_){ return s; } }
                function _fmtPct(n){ if(n===null||n===undefined||n==='') return '—'; var v = Number(n); return (Number.isNaN(v)?'—': (v.toFixed(0)+'%')); }
                function _typeLabel(row){ return row.package_id ? 'صغير' : 'كبير'; }
                function _statusLabelAr(s){
                    try{
                        var v = (s||'').toString().toLowerCase();
                        switch(v){
                            case 'pending': return 'قيد الانتظار';
                            case 'approved': return 'موافق عليه';
                            case 'active': return 'نشط';
                            case 'completed': return 'مكتمل';
                            case 'canceled':
                            case 'cancelled': return 'ملغى';
                            case 'rejected': return 'مرفوض';
                            case 'failed': return 'فشل';
                            case 'awaiting_payment': return 'بانتظار الدفع';
                            case 'paid': return 'مدفوع';
                            case 'under_review': return 'قيد المراجعة';
                            case 'locked': return 'مقفل';
                            case 'overdue': return 'متأخر';
                            case 'draft': return 'مسودة';
                            case 'refunded': return 'مسترجع';
                            case 'processing': return 'جارٍ المعالجة';
                            case 'on_hold': return 'معلق';
                            default: return s || '—';
                        }
                    }catch(_){ return s || '—'; }
                }

                function renderReportsFromInvestments(list){
                    try{
                        var from = _toDate($('#rep-from').val());
                        var to   = _toDate($('#rep-to').val());
                        if(to){
                            // include whole day for 'to'
                            to.setHours(23,59,59,999);
                        }
                        var items = Array.isArray(list) ? list : [];
                        var filtered = items.filter(function(x){
                            var dt = _toDate(x.created_at || x.date || x.approved_at || x.updated_at);
                            return _inRange(dt, from, to);
                        });

                        var totalCount = filtered.length;
                        var totalAmount = filtered.reduce(function(s,x){ return s + _asNumber(x.amount,0); }, 0);
                        var totalProfit = filtered.reduce(function(s,x){
                            var p = _asNumber(x.expected_return, NaN);
                            if(!Number.isFinite(p)){
                                var amt = _asNumber(x.amount,0); var r = _asNumber(x.profit_rate,0);
                                p = Math.round(amt * (r/100));
                            }
                            return s + (Number.isFinite(p)? p : 0);
                        }, 0);

                        $('#rep-total-count').text(totalCount.toLocaleString('ar-EG'));
                        $('#rep-total-amount').text(fmtMoney(totalAmount));
                        $('#rep-total-profit').text(fmtMoney(totalProfit));

                        var $tb = $('#rep-table-body').empty();
                        if(!filtered.length){
                            $tb.append('<tr><td colspan="6" class="text-center text-gray-400 py-6">لا توجد بيانات ضمن الفترة المحددة</td></tr>');
                            return;
                        }
                        filtered.slice(0,500).forEach(function(x){
                            var dt = (x.created_at || x.date || '—');
                            var inv = (x.investor_name || x.investor || x.investor_full_name || '—');
                            var type = (x.investment_type || x.type || '—');
                            var amt = fmtMoney(x.amount);
                            var rate = (x.profit_rate !== undefined && x.profit_rate !== null) ? (x.profit_rate + '%') : '—';
                            var status = (x.status || '—');
                            $tb.append('<tr>'+
                              '<td class="px-3 py-2">'+ dt +'</td>'+
                              '<td class="px-3 py-2">'+ inv +'</td>'+
                              '<td class="px-3 py-2">'+ type +'</td>'+
                              '<td class="px-3 py-2">'+ amt +'</td>'+
                              '<td class="px-3 py-2">'+ rate +'</td>'+
                              '<td class="px-3 py-2">'+ status +'</td>'+
                            '</tr>');
                        });
                    }catch(err){
                        console.warn('renderReportsFromInvestments failed', err);
                    }
                }

                // Bind apply button (delegated)
                $(document).off('click.reportsApply').on('click.reportsApply', '#rep-apply', function(e){
                    e.preventDefault();
                    // Ensure libs loaded
                    try{ $('#rep-lib-loader').removeClass('hidden'); }catch(_){ }
                    // Update header for current filters
                    try{ updateRepHeader(); }catch(_){ }
                    // collect filters
                    var params = {};
                    var f = $('#rep-from').val(); var t = $('#rep-to').val();
                    var type = $('#rep-type').val() || 'any';
                    var st = ($('#rep-status').val()||[]);
                    var invs = [];
                    $('#rep-investors input[type="checkbox"]:checked').each(function(){ invs.push($(this).val()); });
                    if(f) params.from = f; if(t) params.to = t; if(type && type!=='any') params.type = type;
                    if(st.length) params.status = st.join(',');
                    if(invs.length) params.investors = invs.join(',');
                    REP.filters = params;
                    // fetch server-side aggregated report
                    $.get('/api/admin/reports', params)
                      .done(function(d){
                        // fill investors selector
                        try{
                            var $iv = $('#rep-investors').empty();
                            (d.investors||[]).forEach(function(u){
                                var id = u.id; var name = u.full_name || u.email || id;
                                var $lbl = $('<label class="inline-flex items-center gap-2 text-gray-200 bg-gray-800/40 border border-gray-700 rounded px-2 py-1 text-xs"></label>');
                                var $cb = $('<input type="checkbox" class="accent-blue-500"/>').val(id);
                                $lbl.append($cb).append($('<span/>').text(name));
                                $iv.append($lbl);
                            });
                        }catch(_){ }
                        // Totals
                        try{
                            $('#rep-total-count').text((d.totals?.investments_count||0).toLocaleString('ar-EG'));
                            $('#rep-total-amount').text(fmtMoney(d.totals?.total_amount||0));
                            $('#rep-total-profit').text(fmtMoney(d.totals?.total_profit_paid||0));
                        }catch(_){ }
                        // Table
                        try{
                            REP.rows = Array.isArray(d.table)? d.table : [];
                            drawRepTable(REP.rows);
                        }catch(_){ }
                        // Charts
                        try{
                            drawRepCharts(d);
                        }catch(err){ console.warn('charts failed', err); }
                      })
                      .fail(function(xhr){
                        alert('تعذر تحميل التقارير');
                      });
                });

                // Reset filters
                $(document).off('click.reportsReset').on('click.reportsReset', '#rep-reset', function(){
                    $('#rep-from').val(''); $('#rep-to').val(''); $('#rep-type').val('any'); $('#rep-status').val([]);
                    $('#rep-investors').empty().append('<span class="text-gray-400 text-xs">سيتم تحميل قائمة المستثمرين بعد تطبيق الفلاتر</span>');
                    try{ updateRepHeader(); }catch(_){ }
                });

                // Table search
                $(document).off('input.repSearch').on('input.repSearch', '#rep-search', function(){
                    var q = ($(this).val()||'').toLowerCase();
                    var filtered = REP.rows.filter(function(r){
                        return (String(r.investor_name||'').toLowerCase().includes(q)) ||
                               (String(r.status||'').toLowerCase().includes(q)) ||
                               (String(r.id||'').toLowerCase().includes(q));
                    });
                    drawRepTable(filtered);
                });

                // Table sorting
                $(document).off('click.repSort').on('click.repSort', '#rep-table thead th[data-sort]', function(){
                    var key = $(this).data('sort');
                    var asc = $(this).data('asc') === true ? false : true; // toggle
                    $(this).data('asc', asc);
                    var rows = (REP.rows||[]).slice();
                    rows.sort(function(a,b){
                        var va = a[key]; var vb = b[key];
                        if(va==null) return 1; if(vb==null) return -1;
                        if(typeof va === 'string' && /\d{4}-\d{2}-\d{2}/.test(va)) { va = new Date(va).getTime(); }
                        if(typeof vb === 'string' && /\d{4}-\d{2}-\d{2}/.test(vb)) { vb = new Date(vb).getTime(); }
                        if(va < vb) return asc ? -1 : 1; if(va > vb) return asc ? 1 : -1; return 0;
                    });
                    drawRepTable(rows);
                });

                function drawRepTable(rows){
                    var $tb = $('#rep-table-body').empty();
                    if(!rows.length){
                        $tb.append('<tr><td colspan="9" class="text-center text-gray-400 py-6">لا توجد بيانات</td></tr>');
                        return;
                    }
                    rows.forEach(function(r){
                        var tr = '<tr>'+
                          '<td class="px-3 py-2">'+ _fmtDate(r.created_at) +'</td>'+
                          '<td class="px-3 py-2">'+ (r.investor_name||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ _typeLabel(r) +'</td>'+
                          '<td class="px-3 py-2">'+ fmtMoney(r.amount) +'</td>'+
                          '<td class="px-3 py-2">'+ _fmtPct(r.profit_rate) +'</td>'+
                          '<td class="px-3 py-2">'+ fmtMoney(r.expected_return) +'</td>'+
                          '<td class="px-3 py-2">'+ (r.duration_months ?? '—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.months_earned ?? 0) +'</td>'+
                          '<td class="px-3 py-2">'+ _statusLabelAr(r.status) +'</td>'+
                        '</tr>';
                        $tb.append(tr);
                    });
                    // ensure table is horizontally scrollable on small screens
                    try{
                        var $t = $('#rep-table');
                        if($t.length && !$t.parent().hasClass('rep-table-scroll')){
                            $t.wrap('<div class="rep-table-scroll" style="overflow-x:auto; -webkit-overflow-scrolling:touch;"></div>');
                        }
                    }catch(_){ }
                }

                // Lightweight dynamic loaders to ensure libs exist when needed
                function loadScript(src){
                    return new Promise(function(resolve, reject){
                        var el = document.createElement('script');
                        el.src = src; el.async = true; el.onload = resolve; el.onerror = reject;
                        document.head.appendChild(el);
                    });
                }
                async function ensureChartLib(){
                    if (window.Chart) return true;
                    try {
                        await loadScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js');
                        return !!window.Chart;
                    } catch(_) { return false; }
                }
                async function ensureExportLibs(){
                    if (!window.html2canvas){
                        try{ await loadScript('https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js'); }catch(_){}
                    }
                    if (!(window.jspdf && window.jspdf.jsPDF)){
                        try{ await loadScript('https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'); }catch(_){}
                    }
                    return !!(window.html2canvas && window.jspdf && window.jspdf.jsPDF);
                }

                // Fix chart canvas sizes once to prevent continuous re-growth on re-render
                function ensureRepChartFixedSizes(){
                    try{
                        if (document.getElementById('rep-chart-fixed-sizes')) return;
                        var style = document.createElement('style');
                        style.id = 'rep-chart-fixed-sizes';
                        style.textContent = `
                            #rep-chart-investments, #rep-chart-investors, #rep-chart-status {
                                width: 100% !important;
                                max-width: 100% !important;
                                display: block;
                            }
                            #rep-chart-investments { height: 380px !important; max-height: 380px !important; }
                            #rep-chart-investors  { height: 340px !important; max-height: 340px !important; }
                            #rep-chart-status     { height: 300px !important; max-height: 300px !important; }
                        `;
                        document.head.appendChild(style);
                    }catch(_){ }
                }

                async function drawRepCharts(d){
                    // ensure Chart.js is available
                    var ok = await ensureChartLib();
                    if(!ok) return;
                    // enforce fixed sizes (one-time CSS) to avoid growing continuously
                    try{ ensureRepChartFixedSizes(); }catch(_){ }
                    var ctx1 = document.getElementById('rep-chart-investments');
                    var ctx2 = document.getElementById('rep-chart-investors');
                    var ctx3 = document.getElementById('rep-chart-status');
                    if(!ctx1 || !ctx2 || !ctx3 || !window.Chart) return;
                    // destroy previous
                    ['c1','c2','c3'].forEach(function(k){ try{ if(REP.charts[k]){ REP.charts[k].destroy(); } }catch(_){ } });
                    // Set fixed canvas heights via style to prevent continuous growth
                    try{ ctx1.style.height = '380px'; ctx2.style.height = '340px'; ctx3.style.height = '300px'; }catch(_){ }
                    // Build per-investment monthly chart (limit to 8 series by highest amount)
                    var rows = REP.rows.slice().sort(function(a,b){ return (b.amount||0) - (a.amount||0); }).slice(0,8);
                    var datasets = rows.map(function(r, idx){
                        var series = (d.chart?.perInvestmentMonthly?.[r.id]) || [];
                        var data = series.map(function(p){ return p.profit_amount; });
                        var labels = series.map(function(p){ return 'شهر ' + p.month_number; });
                        // color palette
                        var hues = [200,160,120,80,40,0,260,300];
                        var h = hues[idx % hues.length];
                        return {
                            // Hide the raw ID from the legend label per request
                            label: (r.investor_name||'استثمار'),
                            data: data,
                            borderColor: 'hsl('+h+',80%,60%)',
                            backgroundColor: 'hsla('+h+',80%,60%,0.2)',
                            tension: 0.25,
                        };
                    });
                    var labels1 = (rows[0] && (d.chart?.perInvestmentMonthly?.[rows[0].id]||[]).map(function(p){ return 'شهر ' + p.month_number; })) || [];
                    REP.charts.c1 = new Chart(ctx1, { type:'line', data:{ labels:labels1, datasets:datasets }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color:'#e5e7eb' } } }, scales:{ x:{ ticks:{ color:'#9ca3af' } }, y:{ ticks:{ color:'#9ca3af' } } } } });

                    // Per-investor total profit (bar)
                    var arr = Array.isArray(d.chart?.perInvestorTotalProfit)? d.chart.perInvestorTotalProfit : [];
                    var labels2 = arr.map(function(x){ return x.investor_name || x.investor_id; });
                    var data2 = arr.map(function(x){ return x.total_profit; });
                    REP.charts.c2 = new Chart(ctx2, { type:'bar', data:{ labels:labels2, datasets:[{ label:'إجمالي الأرباح', data:data2, backgroundColor:'rgba(34,197,94,0.5)', borderColor:'#22c55e' }]}, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color:'#e5e7eb' } } }, scales:{ x:{ ticks:{ color:'#9ca3af' } }, y:{ ticks:{ color:'#9ca3af' } } } } });

                    // Status vs amounts (bar)
                    var arr3 = Array.isArray(d.chart?.statusByAmount)? d.chart.statusByAmount : [];
                    var labels3 = arr3.map(function(x){ return _statusLabelAr(x.status) || '—'; });
                    var data3 = arr3.map(function(x){ return x.total_amount || 0; });
                    REP.charts.c3 = new Chart(ctx3, { type:'bar', data:{ labels:labels3, datasets:[{ label:'إجمالي المبالغ', data:data3, backgroundColor:'rgba(59,130,246,0.5)', borderColor:'#3b82f6' }]}, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color:'#e5e7eb' } } }, scales:{ x:{ ticks:{ color:'#9ca3af' } }, y:{ ticks:{ color:'#9ca3af' } } } } });
                }

                // Export PDF (capture reports-content)
                $(document).off('click.repExport').on('click.repExport', '#rep-export', async function(){
                    try{
                        var ok = await ensureExportLibs();
                        if(!ok) { alert('تعذر تحميل مكتبات التصدير، تحقق من الاتصال بالإنترنت'); return; }
                        const el = document.getElementById('reports-content');
                        // Snapshot current filter values for header range only
                        var snap = (function(){
                            function fmtDate(v){ try{ if(!v) return ''; var d = new Date(v); return d.toLocaleDateString('ar-EG'); }catch(_){ return v||''; } }
                            var from = document.getElementById('rep-from')?.value || '';
                            var to = document.getElementById('rep-to')?.value || '';
                            return { from: fmtDate(from), to: fmtDate(to) };
                        })();
                        const canvas = await html2canvas(el, {
                            scale: 2,
                            backgroundColor: '#0b1220',
                            onclone: function(clonedDoc){
                                try{
                                    // Hide the entire filters card from the exported PDF
                                    var filterCard = clonedDoc.getElementById('rep-filters-card');
                                    if(filterCard){ filterCard.style.display = 'none'; }
                                    // Ensure header shows current range
                                    var titleEl = clonedDoc.getElementById('rep-header-title');
                                    var rangeEl = clonedDoc.getElementById('rep-header-range');
                                    if(titleEl){ titleEl.textContent = 'تقرير الاستثمارات'; }
                                    if(rangeEl){
                                        var txt = 'النطاق الزمني: ';
                                        if(snap.from && snap.to){ txt += 'من ' + snap.from + ' إلى ' + snap.to; }
                                        else if(snap.from){ txt += 'ابتداءً من ' + snap.from; }
                                        else if(snap.to){ txt += 'حتى ' + snap.to; }
                                        else { txt += 'كل الفترات'; }
                                        rangeEl.textContent = txt;
                                    }
                                }catch(e){ /* safe no-op */ }
                            }
                        });
                        const imgData = canvas.toDataURL('image/png');
                        const jsPDFCtor = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
                        if(!jsPDFCtor){ alert('JS PDF غير متاح'); return; }
                        const pdf = new jsPDFCtor('p', 'mm', 'a4');
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        // Preserve original design scale: do NOT upscale to full width. Scale down only if needed.
                        const imgProps = pdf.getImageProperties ? pdf.getImageProperties(imgData) : { width: canvas.width, height: canvas.height };
                        // Convert image pixels to mm using 96 DPI assumption (CSS px)
                        const mmPerPx = 25.4 / 96;
                        const naturalWmm = imgProps.width * mmPerPx;
                        const naturalHmm = imgProps.height * mmPerPx;
                        const maxWmm = pageWidth - 10; // horizontal margins
                        let pdfWidth = Math.min(naturalWmm, maxWmm);
                        let pdfHeight = (imgProps.height / imgProps.width) * pdfWidth; // keep aspect ratio
                        let y = 5;
                        let x = (pageWidth - pdfWidth) / 2; // center horizontally
                        const maxHmm = pageHeight - 10; // vertical margins
                        if(pdfHeight <= maxHmm){
                            pdf.addImage(imgData, 'PNG', x, y, pdfWidth, pdfHeight);
                        } else {
                            // paginate
                            let sY = 0; const pagePxHeight = imgProps.width ? (imgProps.width * (pageHeight-10) / pdfWidth) : (canvas.height * (pageHeight-10) / pdfWidth); // proportional
                            while (sY < imgProps.height) {
                                const section = document.createElement('canvas');
                                section.width = imgProps.width; section.height = Math.min(pagePxHeight, imgProps.height - sY);
                                const sctx = section.getContext('2d');
                                sctx.drawImage(canvas, 0, sY, imgProps.width, section.height, 0, 0, imgProps.width, section.height);
                                const sData = section.toDataURL('image/png');
                                pdf.addImage(sData, 'PNG', x, 5, pdfWidth, (pageHeight-10));
                                sY += pagePxHeight;
                                if(sY < imgProps.height) pdf.addPage();
                            }
                        }
                        pdf.save('investment-report.pdf');
                    }catch(err){
                        alert('تعذر تصدير PDF');
                        console.warn(err);
                    }
                });

                // Presets (localStorage)
                function loadRepPresets(){
                    try{
                        var raw = localStorage.getItem('adm.reports.presets');
                        var obj = raw ? JSON.parse(raw) : {};
                        var $sel = $('#rep-presets').empty().append('<option value="">استدعاء إعداد محفوظ</option>');
                        Object.keys(obj).forEach(function(name){ $sel.append('<option value="'+encodeURIComponent(name)+'">'+name+'</option>'); });
                    }catch(_){ }
                }
                $(document).off('click.repSavePreset').on('click.repSavePreset', '#rep-save-preset', function(){
                    var name = ($('#rep-preset-name').val()||'').trim(); if(!name) { alert('اكتب اسم الإعداد'); return; }
                    try{
                        var raw = localStorage.getItem('adm.reports.presets'); var obj = raw ? JSON.parse(raw) : {};
                        obj[name] = REP.filters || {};
                        localStorage.setItem('adm.reports.presets', JSON.stringify(obj));
                        loadRepPresets();
                    }catch(_){ }
                });
                $(document).off('change.repPreset').on('change.repPreset', '#rep-presets', function(){
                    var val = $(this).val(); if(!val) return;
                    try{
                        var name = decodeURIComponent(val);
                        var raw = localStorage.getItem('adm.reports.presets'); var obj = raw ? JSON.parse(raw) : {};
                        var p = obj[name] || {};
                        $('#rep-from').val(p.from||''); $('#rep-to').val(p.to||'');
                        $('#rep-type').val(p.type||'any');
                        // statuses
                        var sts = (p.status||'').split(',').filter(Boolean);
                        $('#rep-status').val(sts);
                        // investors will be checked after fetch
                        $('#rep-apply').trigger('click');
                    }catch(_){ }
                });
                // Init presets on page load
                try { loadRepPresets(); } catch(_){ }

                // ======================== License Reports JS ========================
                // State
                var LREP = { rows: [], charts: {}, filters: {}, inited: false };

                function _lrepFmtDateUi(val){ try{ if(!val) return ''; var d = new Date(val); return d.toLocaleDateString('ar-EG'); }catch(_){ return val||''; } }
                function _lrepUpdateHeader(){
                    var from = $('#lrep-from').val();
                    var to = $('#lrep-to').val();
                    var text = 'النطاق الزمني: ';
                    if(from && to){ text += 'من ' + _lrepFmtDateUi(from) + ' إلى ' + _lrepFmtDateUi(to); }
                    else if(from){ text += 'ابتداءً من ' + _lrepFmtDateUi(from); }
                    else if(to){ text += 'حتى ' + _lrepFmtDateUi(to); }
                    else { text += 'كل الفترات'; }
                    $('#lrep-header-range').text(text);
                }

                function ensureLrepChartFixedSizes(){
                    try{
                        if (document.getElementById('lrep-chart-fixed-sizes')) return;
                        var style = document.createElement('style');
                        style.id = 'lrep-chart-fixed-sizes';
                        style.textContent = '\n#lrep-chart-bytype, #lrep-chart-bystatus, #lrep-chart-monthly { height: 320px !important; max-height: 320px !important; }\n';
                        document.head.appendChild(style);
                    }catch(_){ }
                }

                function drawLrepTable(rows){
                    var $tb = $('#lrep-table-body').empty();
                    if(!rows || !rows.length){
                        $tb.append('<tr><td colspan="7" class="text-center text-gray-400 py-6">لا توجد بيانات</td></tr>');
                        return;
                    }
                    rows.forEach(function(r){
                        var tr = '<tr>'+
                          '<td class="px-3 py-2">'+ (_lrepFmtDateUi(r.issued_at) || '—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.id||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.name||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.type||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ (_statusLabelAr(r.status)||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.notes||'—') +'</td>'+
                          '<td class="px-3 py-2">'+ (r.owner_name||'—') +'</td>'+
                        '</tr>';
                        $tb.append(tr);
                    });
                    try{
                        var $t = $('#lrep-table');
                        if($t.length && !$t.parent().hasClass('lrep-table-scroll')){
                            $t.wrap('<div class="lrep-table-scroll" style="overflow-x:auto; -webkit-overflow-scrolling:touch;"></div>');
                        }
                    }catch(_){ }
                }

                async function drawLrepCharts(d){
                    var ok = await ensureChartLib();
                    if(!ok) return;
                    ensureLrepChartFixedSizes();
                    var ctx1 = document.getElementById('lrep-chart-bytype');
                    var ctx2 = document.getElementById('lrep-chart-bystatus');
                    var ctx3 = document.getElementById('lrep-chart-monthly');
                    if(!ctx1 || !ctx2 || !ctx3 || !window.Chart) return;
                    try{ if(LREP.charts.c1){ LREP.charts.c1.destroy(); } }catch(_){ }
                    try{ if(LREP.charts.c2){ LREP.charts.c2.destroy(); } }catch(_){ }
                    try{ if(LREP.charts.c3){ LREP.charts.c3.destroy(); } }catch(_){ }

                    // by type (doughnut)
                    var byType = (d && d.chart && d.chart.byType) ? d.chart.byType : {};
                    var labels1 = Object.keys(byType);
                    var data1 = labels1.map(function(k){ return byType[k]; });
                    LREP.charts.c1 = new Chart(ctx1, { type:'doughnut', data:{ labels:labels1, datasets:[{ data:data1, backgroundColor:['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa','#f472b6','#22d3ee'] }] }, options:{ plugins:{ legend:{ labels:{ color:'#e5e7eb' } } } } });

                    // by status (bar)
                    var byStatus = (d && d.chart && d.chart.byStatus) ? d.chart.byStatus : {};
                    var labels2 = Object.keys(byStatus).map(function(s){ return _statusLabelAr(s); });
                    var data2 = Object.keys(byStatus).map(function(s){ return byStatus[s]; });
                    LREP.charts.c2 = new Chart(ctx2, { type:'bar', data:{ labels:labels2, datasets:[{ label:'عدد التراخيص', data:data2, backgroundColor:'rgba(59,130,246,0.5)', borderColor:'#3b82f6' }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color:'#e5e7eb' } } }, scales:{ x:{ ticks:{ color:'#9ca3af' } }, y:{ ticks:{ color:'#9ca3af' } } } } });

                    // monthly stacked
                    var monthly = (d && d.chart && d.chart.monthly) ? d.chart.monthly : { labels:[], series:{approved:[],pending:[],rejected:[]} };
                    var labels3 = Array.isArray(monthly.labels) ? monthly.labels : [];
                    var sApproved = (monthly.series && monthly.series.approved) ? monthly.series.approved : [];
                    var sPending = (monthly.series && monthly.series.pending) ? monthly.series.pending : [];
                    var sRejected = (monthly.series && monthly.series.rejected) ? monthly.series.rejected : [];
                    LREP.charts.c3 = new Chart(ctx3, {
                        type:'bar',
                        data:{ labels:labels3, datasets:[
                            { label:'موافق', data:sApproved, backgroundColor:'rgba(34,197,94,0.6)', borderColor:'#22c55e', stack:'s' },
                            { label:'قيد المراجعة', data:sPending, backgroundColor:'rgba(234,179,8,0.6)', borderColor:'#eab308', stack:'s' },
                            { label:'مرفوض', data:sRejected, backgroundColor:'rgba(239,68,68,0.6)', borderColor:'#ef4444', stack:'s' }
                        ]},
                        options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color:'#e5e7eb' } } }, scales:{ x:{ stacked:true, ticks:{ color:'#9ca3af' } }, y:{ stacked:true, ticks:{ color:'#9ca3af' } } } }
                    });
                }

                function loadLrepPresets(){
                    try{
                        var raw = localStorage.getItem('adm.licenseReports.presets');
                        var obj = raw ? JSON.parse(raw) : {};
                        var $sel = $('#lrep-presets').empty().append('<option value="">استدعاء إعداد محفوظ</option>');
                        Object.keys(obj).forEach(function(name){ $sel.append('<option value="'+encodeURIComponent(name)+'">'+name+'</option>'); });
                    }catch(_){ }
                }

                // Apply
                $(document).off('click.lrepApply').on('click.lrepApply', '#lrep-apply', function(){
                    _lrepUpdateHeader();
                    var params = {};
                    var f = $('#lrep-from').val(); var t = $('#lrep-to').val();
                    var type = $('#lrep-type').val() || 'any';
                    var st = ($('#lrep-status').val()||[]);
                    var owners = [];
                    $('#lrep-owners input[type="checkbox"]:checked').each(function(){ owners.push($(this).val()); });
                    if(f) params.from = f; if(t) params.to = t; if(type && type!=='any') params.type = type;
                    if(st.length) params.status = st.join(',');
                    if(owners.length) params.owners = owners.join(',');
                    LREP.filters = params;
                    $.get('/api/admin/license-reports', params)
                      .done(function(d){
                        // owners list
                        try{
                            var $own = $('#lrep-owners').empty();
                            var arr = (d && d.owners) ? d.owners : [];
                            if(!arr.length){ $own.append('<span class="text-gray-400 text-xs">لا توجد نتائج</span>'); }
                            arr.forEach(function(o){ $own.append('<label class="text-xs text-gray-200 flex items-center gap-1"><input type="checkbox" value="'+(o.id||'')+'"/> '+(o.name||o.id||'')+'</label>'); });
                        }catch(_){ }
                        // types list
                        try{
                            var $sel = $('#lrep-type');
                            var types = (d && d.types) ? d.types : [];
                            var existing = {};
                            $sel.find('option').each(function(){ existing[$(this).val()] = true; });
                            types.forEach(function(tp){ if(!existing[tp]) { $sel.append('<option value="'+tp+'">'+tp+'</option>'); } });
                        }catch(_){ }
                        // totals
                        try{
                            var tot = (d && d.totals) ? d.totals : {};
                            $('#lrep-total-count').text((tot.total_count||0).toLocaleString('ar-EG'));
                            $('#lrep-total-approved').text((tot.total_approved||0).toLocaleString('ar-EG'));
                            $('#lrep-total-rejected').text((tot.total_rejected||0).toLocaleString('ar-EG'));
                        }catch(_){ }
                        // rows & charts
                        LREP.rows = (d && d.rows) ? d.rows : [];
                        try{ drawLrepTable(LREP.rows); }catch(_){ }
                        try{ drawLrepCharts(d); }catch(_){ }
                        LREP.inited = true;
                      })
                      .fail(function(){ alert('تعذر تحميل تقارير التراخيص'); });
                });

                // Reset
                $(document).off('click.lrepReset').on('click.lrepReset', '#lrep-reset', function(){
                    $('#lrep-from').val(''); $('#lrep-to').val(''); $('#lrep-type').val('any'); $('#lrep-status').val([]);
                    $('#lrep-owners').empty().append('<span class="text-gray-400 text-xs">سيتم تحميل قائمة المالكين بعد تطبيق الفلاتر</span>');
                    _lrepUpdateHeader();
                });

                // Search
                $(document).off('input.lrepSearch').on('input.lrepSearch', '#lrep-search', function(){
                    var q = ($(this).val()||'').toLowerCase();
                    var filtered = (LREP.rows||[]).filter(function(r){
                        return String(r.id||'').toLowerCase().indexOf(q) >= 0 ||
                               String(r.name||'').toLowerCase().indexOf(q) >= 0 ||
                               String(r.owner_name||'').toLowerCase().indexOf(q) >= 0 ||
                               String(r.status||'').toLowerCase().indexOf(q) >= 0;
                    });
                    drawLrepTable(filtered);
                });

                // Sort
                $(document).off('click.lrepSort').on('click.lrepSort', '#lrep-table thead th[data-sort]', function(){
                    var key = $(this).data('sort');
                    var asc = $(this).data('asc') === true ? false : true; // toggle
                    $(this).data('asc', asc);
                    var rows = (LREP.rows||[]).slice();
                    rows.sort(function(a,b){
                        var va = a[key]; var vb = b[key];
                        if(va==null) return 1; if(vb==null) return -1;
                        if(typeof va === 'string' && /\d{4}-\d{2}-\d{2}/.test(va)) { try{ va = new Date(va).getTime(); }catch(_){ } }
                        if(typeof vb === 'string' && /\d{4}-\d{2}-\d{2}/.test(vb)) { try{ vb = new Date(vb).getTime(); }catch(_){ } }
                        if(va < vb) return asc ? -1 : 1; if(va > vb) return asc ? 1 : -1; return 0;
                    });
                    drawLrepTable(rows);
                });

                // Export
                $(document).off('click.lrepExport').on('click.lrepExport', '#lrep-export', async function(){
                    try{
                        var ok = await ensureExportLibs();
                        if(!ok){ alert('تعذر تحميل مكتبات التصدير'); return; }
                        var el = document.getElementById('license-reports-content');
                        var canvas = await html2canvas(el, {
                            scale: 2,
                            backgroundColor: '#0b1220',
                            onclone: function(doc){
                                try{ var f = doc.getElementById('lrep-filters-card'); if(f) f.style.display = 'none'; }catch(_){ }
                            }
                        });
                        var img = canvas.toDataURL('image/png');
                        var jsPDFCtor = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
                        if(!jsPDFCtor){ alert('JS PDF غير متاح'); return; }
                        var pdf = new jsPDFCtor('p','mm','a4');
                        var pageW = pdf.internal.pageSize.getWidth();
                        var pageH = pdf.internal.pageSize.getHeight();
                        var mmPerPx = 25.4 / 96;
                        var natW = canvas.width * mmPerPx; var natH = canvas.height * mmPerPx;
                        var maxW = pageW - 10; var w = (natW < maxW) ? natW : maxW; var h = (canvas.height / canvas.width) * w;
                        var x = (pageW - w)/2; var y = 5; var maxH = pageH - 10;
                        if(h <= maxH){ pdf.addImage(img, 'PNG', x, y, w, h); }
                        else {
                            var sY = 0; var pagePx = canvas.width ? (canvas.width * (pageH-10) / w) : (canvas.height * (pageH-10) / w);
                            while(sY < canvas.height){
                                var pageCanvas = document.createElement('canvas');
                                var pw = canvas.width; var ph = Math.min(pagePx, canvas.height - sY);
                                pageCanvas.width = pw; pageCanvas.height = ph;
                                var ctx = pageCanvas.getContext('2d');
                                ctx.drawImage(canvas, 0, sY, pw, ph, 0, 0, pw, ph);
                                var seg = pageCanvas.toDataURL('image/png');
                                if(sY>0) pdf.addPage();
                                pdf.addImage(seg, 'PNG', x, y, w, (ph / pw) * w);
                                sY += ph;
                            }
                        }
                        pdf.save('license-report.pdf');
                    }catch(err){ alert('تعذر تصدير PDF'); }
                });

                // Presets
                $(document).off('click.lrepSavePreset').on('click.lrepSavePreset', '#lrep-save-preset', function(){
                    var name = ($('#lrep-preset-name').val()||'').trim(); if(!name){ alert('اكتب اسم الإعداد'); return; }
                    try{ var raw = localStorage.getItem('adm.licenseReports.presets'); var obj = raw ? JSON.parse(raw) : {}; obj[name] = LREP.filters || {}; localStorage.setItem('adm.licenseReports.presets', JSON.stringify(obj)); loadLrepPresets(); }catch(_){ }
                });
                $(document).off('change.lrepPreset').on('change.lrepPreset', '#lrep-presets', function(){
                    var val = $(this).val(); if(!val) return;
                    try{
                        var name = decodeURIComponent(val);
                        var raw = localStorage.getItem('adm.licenseReports.presets'); var obj = raw ? JSON.parse(raw) : {};
                        var p = obj[name] || {};
                        $('#lrep-from').val(p.from||''); $('#lrep-to').val(p.to||'');
                        $('#lrep-type').val(p.type||'any');
                        var sts = (p.status||'').split(',').filter(function(x){return !!x;});
                        $('#lrep-status').val(sts);
                        $('#lrep-apply').trigger('click');
                    }catch(_){ }
                });
                try{ loadLrepPresets(); }catch(_){ }

                // Auto-init: when switching to tab-license-reports, run once
                $(document).on('click', '.tab-trigger[data-tab="license-reports"]', function(){
                    setTimeout(function(){ if(!LREP.inited){ $('#lrep-apply').trigger('click'); } }, 60);
                });

                function openInvestmentDetail(id){
                    const $m = $('#investment-modal');
                    // reset
                    $m.find('#invdet-id,#invdet-amount,#invdet-rate,#invdet-expected,#invdet-duration,#invdet-risk,#invdet-status,#invdet-created,#invdet-next-payout,#invdet-earnings-status,#invdet-notes,#invdet-investor-name,#invdet-investor-nationality,#invdet-investor-email,#invdet-investor-phone,#invdet-investor-nid,#invdet-investor-bank,#invdet-package-id,#invdet-package-amount,#invdet-package-rate').text('—');
                    // hide package card by default until we confirm investment type
                    try { $('#invdet-package-card').addClass('hidden'); } catch(e){}
                    $('#invdet-payments').empty();
                    $('#invdet-trans').empty();
                    $('#invdet-id-front,#invdet-id-back').attr('src','').addClass('hidden');

                    $.get(api + '/admin/investments/' + id)
                        .done(function(d){
                            const inv = d.investment || {};
                            const invr = d.investor || {};
                            const pkg = d.package || {};
                            $('#investment-modal-title').text('الاستثمار #' + (inv.id || id));
                            $('#invdet-id').text(inv.id || '—');
                            $('#invdet-amount').text(fmtMoney(inv.amount));
                            $('#invdet-rate').text((inv.profit_rate ?? '—') + (inv.profit_rate!==undefined?'%':''));
                            $('#invdet-expected').text(fmtMoney(inv.expected_return));
                            $('#invdet-duration').text(inv.duration_months ?? '—');
                            $('#invdet-risk').text(inv.risk_level || '—');
                            $('#invdet-status').text(statusLabel(inv.status));
                            // color the modal status label as well (use exact hex for active/completed when available)
                            try{
                                var curStatusKey = String(inv.status||'').toLowerCase();
                                var stCls = statusTextClass(inv.status || '');
                                var $st = $('#invdet-status');
                                $st.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300 text-indigo-400 text-blue-400').addClass(stCls);
                                var SC = window.STATUS_COLORS || null;
                                if(SC && SC[curStatusKey]){ $st.css('color', SC[curStatusKey].hex); } else { $st.css('color',''); }
                            }catch(e){}
                            $('#invdet-created').text(inv.created_at || '—');
                            $('#invdet-notes').text(inv.notes || '—');

                            // Next payout & sync diagnostics (provided by server)
                            try{
                                var $es = $('#invdet-earnings-status');
                                $es.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300');
                                if(!inv.approved_at){
                                    // Investment not approved yet — show informative messages instead of blank
                                    $('#invdet-next-payout').text('ستُدرج بعد اعتماد الاستثمار');
                                    $es.text('غير معتمد');
                                    $es.addClass('text-gray-300');
                                } else {
                                    var nextPayout = inv.next_payout_at || '—';
                                    $('#invdet-next-payout').text(nextPayout);

                                    var miss = parseInt(inv.missing_earnings_count || 0, 10);
                                    var overdue = !!inv.overdue;
                                    if(miss > 0){
                                        $es.text(miss + ' دفعات مفقودة' + (overdue ? ' (متأخرة)' : ''));
                                        $es.addClass(overdue ? 'text-red-400' : 'text-yellow-300');
                                    } else {
                                        $es.text('منتظمة');
                                        $es.addClass('text-emerald-400');
                                    }
                                }
                            }catch(e){}

                            // Attach investment id to admin modal controls so actions target the current investment
                            try {
                                var $payBtn = $('#investment-modal-pay');
                                if ($payBtn && $payBtn.length) {
                                    $payBtn.attr('data-__adm_pay_id', inv.id || '');
                                    // Always show pay button for all statuses (pending, approved, rejected)
                                    $payBtn.removeClass('hidden');
                                }
                                // wire reject button id
                                var $rejBtn = $('#investment-modal-reject');
                                if($rejBtn && $rejBtn.length){
                                    $rejBtn.attr('data-id', inv.id || '');
                                    $rejBtn.removeClass('hidden');
                                }
                                // wire ACTIVE/COMPLETED buttons ids and disabled state based on current status
                                var $actBtn = $('#investment-modal-active');
                                var $cmpBtn = $('#investment-modal-complete');
                                var cur = String(inv.status||'').toLowerCase();
                                if($actBtn && $actBtn.length){
                                    $actBtn.attr('data-id', inv.id || '');
                                }
                                if($cmpBtn && $cmpBtn.length){
                                    $cmpBtn.attr('data-id', inv.id || '');
                                }
                                // Reset visual classes
                                try {
                                    if($actBtn && $actBtn.length){ $actBtn.removeClass('bg-orange-600 text-black hover:bg-orange-500 border border-orange-600 text-orange-300 hover:bg-orange-600/20 opacity-50 cursor-not-allowed'); }
                                    if($cmpBtn && $cmpBtn.length){ $cmpBtn.removeClass('bg-blue-600 text-black hover:bg-blue-500 border border-blue-600 text-blue-300 hover:bg-blue-600/20 opacity-50 cursor-not-allowed'); }
                                } catch(_){ }
                                // Reset inline styles
                                try { if($actBtn && $actBtn.length){ $actBtn.css({ backgroundColor: '', color: '', borderColor: '' }); } } catch(_){ }
                                try { if($cmpBtn && $cmpBtn.length){ $cmpBtn.css({ backgroundColor: '', color: '', borderColor: '' }); } } catch(_){ }
                                // Apply disabled + visual state by current status
                                var isAct = (cur==='active');
                                var isCmp = (cur==='completed');
                                try {
                                    var SCm = window.STATUS_COLORS || { active:{hex:'#f97316'}, completed:{hex:'#2563eb'} };
                                    if($actBtn && $actBtn.length){
                                        $actBtn.prop('disabled', isAct).toggleClass('opacity-50 cursor-not-allowed', isAct);
                                        if(isAct){
                                            $actBtn.addClass('bg-orange-600 text-black hover:bg-orange-500');
                                            try { $actBtn.css({ backgroundColor: SCm.active.hex, color: '#000' }); } catch(_){ }
                                        } else {
                                            $actBtn.addClass('border border-orange-600 text-orange-300 hover:bg-orange-600/20');
                                            try { $actBtn.css({ borderColor: SCm.active.hex, color: SCm.active.hex }); } catch(_){ }
                                        }
                                    }
                                    if($cmpBtn && $cmpBtn.length){
                                        $cmpBtn.prop('disabled', isCmp).toggleClass('opacity-50 cursor-not-allowed', isCmp);
                                        if(isCmp){
                                            $cmpBtn.addClass('bg-blue-600 text-black hover:bg-blue-500');
                                            try { $cmpBtn.css({ backgroundColor: SCm.completed.hex, color: '#000' }); } catch(_){ }
                                        } else {
                                            $cmpBtn.addClass('border border-blue-600 text-blue-300 hover:bg-blue-600/20');
                                            try { $cmpBtn.css({ borderColor: SCm.completed.hex, color: SCm.completed.hex }); } catch(_){ }
                                        }
                                    }
                                } catch(_){ }
                            } catch(e){}

                            // investor
                            $('#invdet-investor-name').text(invr.full_name || '—');
                            // Show investor nationality / country and append city/province in parentheses when available
                            try {
                                var _nat = invr.nationality || invr.country || invr.country_name || '';
                                var _city = invr.city || invr.province || invr.state || '';
                                var natText = _nat || '—';
                                if (_city && _city.toString().trim() !== '') {
                                    // avoid showing placeholder when nat is empty
                                    if (!_nat) {
                                        natText = _city;
                                    } else {
                                        natText = _nat + ' (' + _city + ')';
                                    }
                                }
                                $('#invdet-investor-nationality').text(natText);
                            } catch (e) {
                                $('#invdet-investor-nationality').text(invr.nationality || invr.city || '—');
                            }
                            $('#invdet-investor-email').text(invr.email || '—');
                            $('#invdet-investor-phone').text(invr.phone || '—');
                            $('#invdet-investor-nid').text(invr.national_id || '—');
                            $('#invdet-investor-bank').text(invr.bank_account || '—');
                            // Resolve identity image URLs using several fallbacks (investor fields, documents map, investment-level fields)
                            try{
                                const docs = (d.documents && typeof d.documents === 'object') ? d.documents : (d.documents || {});
                                // Helper: prefer structured preview object then simple url string
                                function chooseUrl(candidate){
                                    if(!candidate) return null;
                                    if(typeof candidate === 'string') return candidate;
                                    if(typeof candidate === 'object') return candidate.url || candidate.path || null;
                                    return null;
                                }

                                // Priority: investor.identity_image_front/back or investor.id_front_url/id_back_url
                                let idFrontUrl = chooseUrl(invr.identity_image_front) || chooseUrl(invr.id_front_url) || null;
                                let idBackUrl = chooseUrl(invr.identity_image_back) || chooseUrl(invr.id_back_url) || null;

                                // Normalize identity URLs: if a relative storage path is provided (e.g. 'investors/...'),
                                // convert it to '/storage/...' so the browser requests the correct public URL instead of
                                // resolving relative to the current admin path (/admin/...)
                                function normalizeStorageUrl(u){
                                    try{
                                        if(!u || typeof u !== 'string') return u;
                                        if(/^https?:\/\//i.test(u)) return u;
                                        if(u.startsWith('/')) return u;
                                        if(u.startsWith('storage/')) return '/' + u.replace(/^\/+/, '');
                                        // relative path like 'investors/...' -> '/storage/investors/...'
                                        return '/storage/' + u.replace(/^\/+/, '');
                                    }catch(e){ return u; }
                                }
                                idFrontUrl = normalizeStorageUrl(idFrontUrl);
                                idBackUrl = normalizeStorageUrl(idBackUrl);

                                // Next priority: documents passed in response (d.documents.identity_front/back or d.documents.id_front/id_back)
                                if(!idFrontUrl){ idFrontUrl = chooseUrl(docs.identity_front) || chooseUrl(docs.id_front) || chooseUrl(docs['identity_image_front']); }
                                if(!idBackUrl){ idBackUrl = chooseUrl(docs.identity_back) || chooseUrl(docs.id_back) || chooseUrl(docs['identity_image_back']); }

                                // Next priority: investment-level identity fields
                                if(!idFrontUrl){ idFrontUrl = chooseUrl(inv.identity_image_front) || chooseUrl(inv.id_front_url); }
                                if(!idBackUrl){ idBackUrl = chooseUrl(inv.identity_image_back) || chooseUrl(inv.id_back_url); }

                                // Fallback: single canonical identity image on investor or user-level
                                if(!idFrontUrl && invr.identity_image){ idFrontUrl = chooseUrl(invr.identity_image); }
                                if(!idBackUrl && invr.identity_image){ idBackUrl = chooseUrl(invr.identity_image); }

                                if(idFrontUrl){ $('#invdet-id-front').attr('src', idFrontUrl).removeClass('hidden'); }
                                if(idBackUrl){ $('#invdet-id-back').attr('src', idBackUrl).removeClass('hidden'); }

                                // Add open/download buttons UNDER each square box (front/back)
                                try{
                                    const frontEl = document.getElementById('invdet-id-front');
                                    const backEl  = document.getElementById('invdet-id-back');
                                    // helper to fill the dedicated actions container under each image
                                    function fillActions(imgId, url){
                                        var actionsId = (imgId === 'invdet-id-front') ? 'invdet-id-front-actions' : 'invdet-id-back-actions';
                                        var actions = document.getElementById(actionsId);
                                        if(!actions) return;
                                        // reset
                                        actions.innerHTML = '';
                                        if(!url) return;
                                        // label on first row
                                        var label = document.createElement('div');
                                        label.className = 'text-xs text-gray-400';
                                        label.textContent = (imgId === 'invdet-id-front') ? 'صورة الهوية الأمامية:' : 'صورة الهوية الخلفية:';
                                        actions.appendChild(label);

                                        // buttons on second row
                                        var btnRow = document.createElement('div');
                                        btnRow.className = 'flex items-center gap-2';

                                        // Open button
                                        var aOpen = document.createElement('a');
                                        aOpen.href = url; aOpen.target = '_blank'; aOpen.rel = 'noopener';
                                        aOpen.className = 'text-xs px-2 py-1 rounded border border-cyan-600 text-cyan-300 hover:bg-cyan-600/10';
                                        aOpen.textContent = 'فتح';
                                        aOpen.title = 'فتح الصورة في تبويب جديد';
                                        // Download button
                                        var aDl = document.createElement('a');
                                        aDl.href = url; aDl.setAttribute('download','');
                                        aDl.className = 'text-xs px-2 py-1 rounded border border-cyan-600 text-cyan-300 hover:bg-cyan-600/10';
                                        aDl.textContent = 'تنزيل';
                                        aDl.title = 'تنزيل الصورة';
                                        btnRow.appendChild(aOpen);
                                        btnRow.appendChild(aDl);
                                        actions.appendChild(btnRow);
                                    }

                                    if(frontEl){ fillActions('invdet-id-front', idFrontUrl || null); }
                                    if(backEl){ fillActions('invdet-id-back', idBackUrl || null); }
                                }catch(e){ /* ignore UI enhancement errors */ }
                            }catch(e){
                                // if any error, silently continue without blocking modal
                                console.warn('Failed to resolve identity images for investment modal', e);
                            }

                            // package: show only for small investments
                            try{
                                var invTypeRaw = (inv.investment_type || inv.type || '').toString();
                                var invType = invTypeRaw.trim().toLowerCase();
                                // fallback: if not provided, infer small when package exists
                                var isSmall = invType ? (invType === 'small' || invType === 'صغير' || invType === 'small_investment') : !!(inv.package_id || (pkg && Object.keys(pkg).length));
                                if(isSmall){
                                    $('#invdet-package-card').removeClass('hidden');
                                    if (pkg && Object.keys(pkg).length){
                                        $('#invdet-package-id').text(pkg.id || '—');
                                        if (pkg.amount !== undefined) $('#invdet-package-amount').text(fmtMoney(pkg.amount));
                                        if (pkg.profit_rate !== undefined) $('#invdet-package-rate').text(pkg.profit_rate + '%');
                                    }
                                } else {
                                    $('#invdet-package-card').addClass('hidden');
                                }
                            }catch(e){ /* safely ignore */ }

                            // payments
                            const $p = $('#invdet-payments');
                            (d.payments || []).forEach(p => {
                                const badge = p.status === 'completed' ? 'bg-green-600' : (p.status === 'failed' ? 'bg-red-600' : 'bg-yellow-600');
                                const method = p.method || p.payment_method || '—';
                                const amt = fmtMoney(p.amount);
                                const ref = p.reference || p.payment_reference || p.transaction_reference || '—';
                                // sanitize incoming text to remove literal "\\n" or newline chars which may appear in some payloads
                                const senderRaw = p.sender_name || '';
                                const trAtRaw = p.transfer_at || '';
                                const sender = senderRaw.toString().replace(/\\\\n/g,' ').replace(/\\n/g,' ').replace(/\n/g,' ').trim();
                                const trAt = trAtRaw.toString().replace(/\\\\n/g,' ').replace(/\\n/g,' ').replace(/\n/g,' ').trim();
                                // normalize receipt value: controller exposes receipt_url and receipt_path and legacy 'receipt'
                                let receipt = p.receipt || p.receipt_url || p.receipt_path || null;
                                try{
                                    if (receipt && typeof receipt === 'string' && !/^https?:\/\//i.test(receipt)) {
                                        // if it's a relative path like 'receipts/...' or 'storage/receipts/...', ensure it maps to public storage
                                        if (receipt.startsWith('/')) {
                                            // absolute path - leave as-is
                                            receipt = receipt;
                                        } else if (receipt.startsWith('storage/')) {
                                            receipt = '/' + receipt;
                                        } else {
                                            // e.g., receipts/xxx.jpg -> /storage/receipts/xxx.jpg
                                            receipt = '/storage/' + receipt.replace(/^\/+/, '');
                                        }
                                    }
                                }catch(e){ /* ignore normalization errors */ }
                                // translate payment status to Arabic
                                const pStatus = (p.status || '').toLowerCase();
                                const pStatusLabel = pStatus === 'completed' ? 'مكتمل' : (pStatus === 'failed' ? 'فشل' : (pStatus === 'pending' ? 'قيد المراجعة' : (p.status || '—')));
                                const receiptHtml = receipt ? (
                                    '<a href="'+receipt+'" target="_blank" class="text-xs underline text-cyan-300">عرض الإيصال</a>'+
                                    ' <span class="text-gray-500">•</span> '
                                    +'<a href="'+receipt+'" download class="text-xs underline text-cyan-300">تحميل</a>'
                                ) : '';
                                const thumb = receipt ? ('<a href="'+receipt+'" target="_blank"><img src="'+receipt+'" class="w-16 h-10 object-cover rounded border border-gray-700"/></a>') : '';
                                // Render payment as a compact, readable card: 3 lines (method/ref, sender, transfer date)
                                // and a square on the right with View / Download buttons for the receipt.
                                $p.append(
                                    '<div class="bg-gray-900/50 border border-gray-700 rounded-md p-3">' +
                                        '<div class="flex items-center justify-between gap-3">' +
                                            '<div class="flex-1">' +
                                                '<div class="text-sm text-white font-medium">' + method + ' • <span class="text-gray-300">' + ref + '</span></div>' +
                                                '<div class="text-xs text-gray-400 mt-1">' + (sender || '—') + '</div>' +
                                                '<div class="text-xs text-gray-400 mt-1">' + (trAt || '—') + '</div>' +
                                                '<div class="mt-2">' +
                                                    '<span class="text-xs text-white rounded-md px-2 py-1 ' + badge + '">'+ pStatusLabel +'</span>' +
                                                    ' <span class="text-sm text-white font-bold">' + amt + '</span>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="w-28 flex flex-col items-center gap-2">' +
                                                (receipt ? ('<a href="' + receipt + '" target="_blank" class="w-full text-xs text-center px-2 py-1 rounded border border-cyan-600 text-cyan-300">عرض</a>' +
                                                            '<a href="' + receipt + '" download class="w-full text-xs text-center px-2 py-1 rounded border border-cyan-600 text-cyan-300">تحميل</a>')
                                                        : '<div class="text-xs text-gray-500">لا يوجد إيصال</div>') +
                                            '</div>' +
                                        '</div>' +
                                    '</div>'
                                );
                            });

                            // transactions
                            const $t = $('#invdet-trans');
                            $t.empty();
                            const txs = Array.isArray(d.transactions) ? d.transactions : [];
                            if (!txs.length) {
                                $t.append('<div class="text-gray-400 text-sm">لا توجد حركات حالياً</div>');
                            } else {
                                txs.forEach(tr => {
                                    const typeBadge = (tr.type || '').toLowerCase() === 'credit' ? 'bg-emerald-600' : ((tr.type || '').toLowerCase() === 'debit' ? 'bg-red-600' : 'bg-gray-700');
                                    const amt = fmtMoney(tr.amount);
                                    const tType = (tr.type || '').toLowerCase();
                                    const tTypeLabel = tType === 'credit' ? 'إيداع' : (tType === 'debit' ? 'سحب' : (tr.type || '—'));
                                    const desc = tr.description || tr.note || tr.summary || '—';
                                    const at = tr.created_at ? (' • ' + tr.created_at) : '';
                                    $t.append('<div class="bg-gray-900/50 border border-gray-700 rounded p-2 flex items-center justify-between">\\\n'
                                        + '<div class="text-white text-sm">'+ desc + at +'</div>'
                                        + '<div class="flex items-center gap-2">'
                                            + '<span class="text-xs text-white rounded-md px-2 py-1 '+typeBadge+'">'+ tTypeLabel +'</span>'
                                            + '<span class="text-xs text-white rounded-md px-2 py-1 bg-gray-700">'+ amt +'</span>'
                                        + '</div>'
                                    + '</div>');
                                });
                            }

                            $m.removeClass('hidden').addClass('flex');
                            
                                    // Approve/payment/reject handlers removed per request — UI no longer exposes these actions here.
                    
                        // attach reject button handler (kept isolated; no backend calls to avoid conflicts)
                        try{
                            $('#investment-modal-reject').off('click').on('click', function(e){
                                e.preventDefault();
                                var invId = $(this).attr('data-id') || ($('#invdet-id').text()||'').trim();
                                if(!invId){ alert('لا يوجد معرف للاستثمار'); return; }
                                // open reject modal and prefill
                                var $rm = $('#investment-reject-modal');
                                $rm.find('#invreject-id').text(invId);
                                $rm.find('#invreject-reason').val('');
                                $rm.removeClass('hidden').addClass('flex');
                            });

                            // approve button handler (direct approve without extra modal)
                            $('#investment-modal-approve').off('click').on('click', function(e){
                                e.preventDefault();
                                var invId = $(this).attr('data-id') || ($('#invdet-id').text()||'').trim();
                                if(!invId){ alert('لا يوجد معرف للاستثمار'); return; }
                                try { document.dispatchEvent(new CustomEvent('admin:investment:approve', { detail: { id: invId } })); } catch(_){}
                            });

                            // active button handler — mirror approve flow (dispatch event; central listener persists + updates UI)
                            $('#investment-modal-active').off('click').on('click', function(e){
                                e.preventDefault();
                                var invId = $(this).attr('data-id') || ($('#invdet-id').text()||'').trim();
                                if(!invId){ alert('لا يوجد معرف للاستثمار'); return; }
                                try { document.dispatchEvent(new CustomEvent('admin:investment:set-active', { detail: { id: invId } })); } catch(_){ }
                            });

                            // completed button handler — same pattern
                            $('#investment-modal-complete').off('click').on('click', function(e){
                                e.preventDefault();
                                var invId = $(this).attr('data-id') || ($('#invdet-id').text()||'').trim();
                                if(!invId){ alert('لا يوجد معرف للاستثمار'); return; }
                                try { document.dispatchEvent(new CustomEvent('admin:investment:set-completed', { detail: { id: invId } })); } catch(_){ }
                            });

                            // reject modal handlers (idempotent)
                            $(document).off('click.invrejectClose').on('click.invrejectClose', '#invreject-close, #invreject-cancel', function(e){
                                e.preventDefault();
                                $('#investment-reject-modal').addClass('hidden').removeClass('flex');
                            });

                            $(document).off('click.invrejectConfirm').on('click.invrejectConfirm', '#invreject-confirm', function(e){
                                e.preventDefault();
                                var invId = ($('#invreject-id').text()||'').trim();
                                var reason = ($('#invreject-reason').val()||'').trim();
                                if(!invId){ $('#investment-reject-modal').addClass('hidden').removeClass('flex'); return; }
                                try { document.dispatchEvent(new CustomEvent('admin:investment:reject', { detail: { id: invId, reason: reason } })); } catch(_){}
                                // Toast will be handled after server call success via the event listener below
                                // close both reject and investment modals
                                $('#investment-reject-modal').addClass('hidden').removeClass('flex');
                                $('#investment-modal').addClass('hidden').removeClass('flex');
                            });
                            
                            // ensure reject modal is attached to body and backdrop click closes it
                            (function(){
                                var $rm = $('#investment-reject-modal');
                                try{
                                    if($rm.length && $rm.parent()[0] !== document.body){ $('body').append($rm); }
                                    $rm.off('click.invbackdrop').on('click.invbackdrop', function(ev){ if(ev.target === this){ $(this).addClass('hidden').removeClass('flex'); } });
                                }catch(_){/* ignore */}
                            })();
                        }catch(e){ console.warn('reject button wiring failed', e); }
                        })
                        .fail(function(xhr){
                            alert('تعذر جلب تفاصيل الاستثمار' + (xhr && xhr.status ? ' ('+xhr.status+')' : ''));
                        });

                    $('#investment-modal-close, #investment-modal-close2').off('click').on('click', function(){
                        // capture the current shown investment id
                        var invId = $('#invdet-id').text() || null;
                        $('#investment-modal').addClass('hidden').removeClass('flex');

                        try {
                            // clear pay button data when modal closes (button remains present but without id)
                            $('#investment-modal-pay').attr('data-__adm_pay_id','');
                            // clear reject button data
                            $('#investment-modal-reject').attr('data-id','');
                            // clear approve button data
                            $('#investment-modal-approve').attr('data-id','');
                            // clear active/completed button data
                            try { $('#investment-modal-active').attr('data-id',''); } catch(_){ }
                            try { $('#investment-modal-complete').attr('data-id',''); } catch(_){ }
                            if(invId){
                                // locate the investment card in the list and add a notify/pay button if missing
                                var $cards = $('#investments-list').children();
                                $cards.each(function(){
                                    var $card = $(this);
                                    // find view button with data-id
                                    var $view = $card.find('[data-action="view"][data-id="'+invId+'"]');
                                    if($view && $view.length){
                                        // find or create controls container
                                        var $right = $card.find('.flex.items-center.gap-2').first();
                                        if(!$right || $right.length===0){ $right = $card.find('> div').last(); }
                                        
                                    }
                                });
                            }
                        } catch(e){}
                    });

                    // Save scroll position for current tab on unload so refresh restores position
                    window.addEventListener('beforeunload', function() {
                        try {
                            var activeTab = sessionStorage.getItem('admin.activeTab') || (location.hash ? location.hash.replace('#','') : 'overview');
                            sessionStorage.setItem('admin.scroll.' + activeTab, String(window.scrollY || 0));
                        } catch (e) {}
                    });
                }

                // License modal handlers
                $(document).on('click', '#lic-modal-close, #lic-modal-cancel', function(){
                    $('#lic-modal').addClass('hidden').removeClass('flex');
                });

                // License Approve: minimal handler — change text only, no layout/class/list changes
                (function(){
                    try {
                        if(!window.__admLicApproveAttach){
                            window.__admLicApproveAttach = 1;
                            document.addEventListener('click', function(e){
                                var t = e && e.target ? e.target : null;
                                if(!t) return;
                                var isApprove = (t.id === 'licdet-approve') || (t.closest && t.closest('#licdet-approve'));
                                if(!isApprove) return;
                                try { e.preventDefault(); } catch(_){ }

                                // Read license id from detail modal
                                var idEl = document.getElementById('licdet-id');
                                var licId = idEl ? (idEl.textContent || idEl.innerText || '').trim() : '';
                                if(!licId){ try { alert('معرّف الترخيص مفقود'); } catch(_){ } return; }

                                // Prepare request
                                var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));
                                var params = 'status=' + encodeURIComponent('approved') + (token ? '&_token=' + encodeURIComponent(token) : '');
                                var url = '/api/admin/licenses/' + encodeURIComponent(licId) + '/status';

                                // Optional: brief button feedback without layout change
                                var btn = (t.id === 'licdet-approve') ? t : (t.closest ? t.closest('#licdet-approve') : t);
                                var prevTxt = btn && btn.innerText ? btn.innerText : '';
                                if(btn){ btn.disabled = true; btn.innerText = '...'; }

                                var xhr = new XMLHttpRequest();
                                xhr.open('PUT', url, true);
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                                try { if(token) xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(_){ }
                                xhr.onreadystatechange = function(){
                                    if(xhr.readyState !== 4) return;
                                    if(btn){ btn.disabled = false; btn.innerText = prevTxt; }
                                    var ok = (xhr.status >= 200 && xhr.status < 300);
                                    if(ok){
                                        // Only update the visible text for status
                                        try {
                                            var res = {};
                                            try { res = JSON.parse(xhr.responseText || '{}'); } catch(_){ res = {}; }
                                            var s = (res && res.status) ? String(res.status).toLowerCase() : 'approved';
                                            var label = (typeof statusLabel === 'function') ? statusLabel(s) : (s === 'approved' ? 'موافق عليه' : (s === 'pending' ? 'معلق' : (s === 'rejected' ? 'مرفوض' : (res.status || s))));
                                            // Modal: new-style id
                                            var statusEl = document.getElementById('licdet-status');
                                            if(statusEl){
                                                statusEl.textContent = label || '—';
                                                // also ensure approved color reflects (green text)
                                                try{
                                                    var addCls = (typeof statusTextClass === 'function') ? statusTextClass('approved') : 'text-emerald-400';
                                                    statusEl.classList.remove('text-emerald-400','text-red-400','text-yellow-300','text-gray-300');
                                                    if(addCls) statusEl.classList.add(addCls);
                                                }catch(_){}
                                            }
                                            // Modal: license request modal id
                                            var lreqEl = document.getElementById('lreq-status');
                                            if(lreqEl){ lreqEl.textContent = label || '—'; }
                                            // Table row in License Requests list: update text only
                                            try {
                                                var btnView = document.querySelector('.btn-lreq-view[data-id="' + licId + '"]');
                                                var row = btnView ? btnView.closest('tr') : null;
                                                if(row){
                                                    var tds = row.querySelectorAll('td');
                                                    if(tds && tds.length >= 5){
                                                        var statusTd = tds[4]; // 0:#,1:المنشأة,2:النشاط,3:الموقع,4:الحالة
                                                        var sp = statusTd.querySelector('span');
                                                        if(sp){
                                                            // Only replace the text, preserve any nested icon
                                                            var tn = null; var nodes = sp.childNodes || [];
                                                            for(var i=nodes.length-1;i>=0;i--){ if(nodes[i].nodeType===3){ tn = nodes[i]; break; } }
                                                            if(tn){ tn.nodeValue = label || '—'; }
                                                            else { sp.appendChild(document.createTextNode(label || '—')); }
                                                            // Update badge color classes to green for approved
                                                            try{
                                                                sp.classList.remove('bg-yellow-600','bg-red-600','bg-green-600','bg-yellow-500','bg-red-500','bg-green-500');
                                                                sp.classList.add('bg-green-600');
                                                            }catch(_){}
                                                        }
                                                    }
                                                    // keep button data-status in sync for future modal opens
                                                    try { if(btnView){ btnView.setAttribute('data-status', (res.status || 'approved')); } } catch(_){ }
                                                }
                                            } catch(_){ }

                                            // Licenses cards list: update the badge text-only (preserve icon and classes)
                                            try {
                                                var card = document.querySelector('#licenses-list [data-inv-id="' + licId + '"]');
                                                if(card){
                                                    // find the status badge inside the right-side actions (first matching badge)
                                                    var badge = card.querySelector('.inline-flex.items-center.gap-1.rounded-md');
                                                    if(badge){
                                                        var nodes = badge.childNodes || [];
                                                        var textNode = null;
                                                        for(var j = nodes.length - 1; j >= 0; j--){ if(nodes[j].nodeType === 3){ textNode = nodes[j]; break; } }
                                                        if(textNode){ textNode.nodeValue = label || '—'; }
                                                        else { badge.appendChild(document.createTextNode(label || '—')); }
                                                        // Update badge background color to green for approved
                                                        try{
                                                            badge.classList.remove('bg-yellow-500','bg-red-500','bg-green-500','bg-yellow-600','bg-red-600','bg-green-600');
                                                            badge.classList.add('bg-green-500');
                                                        }catch(_){}
                                                    }
                                                }
                                            } catch(_){ }

                                            // Success message toast (approval-specific)
                                            try {
                                                var ownerNm = (document.getElementById('licdet-owner-name')?.textContent || '').trim();
                                                if (window.__showLicenseApprovedToast) {
                                                    window.__showLicenseApprovedToast({ ownerName: ownerNm, message: 'تم اعتماد الترخيص بنجاح', duration: 3000 });
                                                } else if (window.__showLicenseSuccessToast) {
                                                    window.__showLicenseSuccessToast({ message: 'تم اعتماد الترخيص بنجاح', duration: 3000 });
                                                } else {
                                                    alert('تم الاعتماد بنجاح');
                                                }
                                            } catch(_){ }
                                        } catch(_){ }
                                        // Do NOT change classes, close modal, or refresh list
                                    } else {
                                        try { alert('تعذّر اعتماد الترخيص'); } catch(_){ }
                                    }
                                };
                                xhr.onerror = function(){ if(btn){ btn.disabled = false; btn.innerText = prevTxt; } try { alert('فشل في الاتصال'); } catch(_){ } };
                                xhr.send(params);
                            }, true);
                        }
                    } catch(err){ console.warn('license approve attach error', err); }
                })();

                function fetchAll() {
                    fetchStats();
                    fetchAnalytics();
                    fetchInvestments();
                    fetchLicenses();
                    fetchUsers();
                    fetchInvestors();
                    fetchPackages();
                    fetchNavigation();
                    fetchBackups();
                    fetchSettings();
                    fetchDocuments();
                    fetchLicenseRequests();
                }

                // Investors: demo data and rendering
                function getInvestorsDemo() {
                    return [
                        {
                            id: 1,
                            name: 'خالد بن فيصل',
                            email: 'khaled@example.com',
                            phone: '+966501234567',
                            avatar: '',
                            investments: [
                                { id: 9001, amount: 15000, status: 'نشط', package: 'Bronze' },
                                { id: 9002, amount: 30000, status: 'مكتمل', package: 'Silver' }
                            ]
                        },
                        {
                            id: 2,
                            name: 'منى العتيبي',
                            email: 'mona@example.com',
                            phone: '+966511223344',
                            avatar: '',
                            investments: [
                                { id: 9003, amount: 50000, status: 'نشط', package: 'Gold' }
                            ]
                        }
                    ];
                }

                // Global helper: deterministic hash -> gradient style and avatar html
                function hashCodeGlobal(str){
                    var h = 0;
                    for (var i = 0; i < str.length; i++) { h = ((h << 5) - h) + str.charCodeAt(i); h |= 0; }
                    return Math.abs(h);
                }
                function makeAvatarHtmlGlobal(name, sizeClass, textClass){
                    var clean = (name || '').toString().replace(/\s+/g,'');
                    var initials = clean ? clean.slice(0,2).toUpperCase() : 'IN';
                    var h = hashCodeGlobal(clean || initials) % 360;
                    var h2 = (h + 40) % 360;
                    var style = 'background: linear-gradient(135deg, hsl('+h+',70%,45%), hsl('+h2+',70%,50%));';
                    return '<div class="'+(sizeClass||'w-10 h-10')+' rounded-full flex items-center justify-center '+(textClass||'text-white font-bold')+'" style="'+style+'">'+initials+'</div>';
                }

                function fetchInvestors(q) {
                    // Use server-rendered partial (Blade) so admin sees DB-driven view immediately
                    try{
                        const url = '{{ route('admin.search.investors') }}';
                        return $.get(url, { query: q || '' }).done(function(html){
                            $('#investor-results').html(html);
                            // update count based on returned rows/cards
                            var tableRows = $('#investor-results').find('table tbody tr').length || 0;
                            var cards = $('#investor-results').find('.investor-card').length || 0;
                            var total = tableRows + cards;
                            $('#investors-count').text((total || 0) + ' مستثمر');
                        }).fail(function(){
                            // fallback to demo renderer
                            const demo = getInvestorsDemo();
                            renderInvestors(demo);
                        });
                    }catch(e){
                        console.warn('fetchInvestors error', e);
                        const demo = getInvestorsDemo();
                        renderInvestors(demo);
                    }
                }

                function renderInvestors(items) {
                    const $wrap = $('#investor-results').empty();
                    $('#investors-count').text((items?.length || 0) + ' مستثمر');
                    if (!items || !items.length) {
                        $wrap.append('<div class="text-gray-400">لا يوجد مستثمرون حالياً</div>');
                        return;
                    }
                    items.forEach(it => {
                        const total = (it.investments || []).reduce((s, x) => s + (x.amount || 0), 0);
                        const active = (it.investments || []).filter(x => x.status === 'نشط').length;
                        const $row = $('<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3 flex items-center justify-between gap-3"></div>');
                        const $left = $('<div class="flex items-center gap-3"></div>')
                            .append(makeAvatarHtmlGlobal(it.name || 'IN', 'w-12 h-12', 'text-white font-bold text-lg'))
                            .append('<div class="text-white"><div class="font-semibold">' + it.name + '</div><div class="text-gray-300 text-sm">' + (it.email || '') + '</div></div>');
                        const $right = $('<div class="flex items-center gap-3"></div>')
                            .append('<span class="text-xs text-white rounded-md px-2 py-1 bg-orange-600">' + active + ' نشط</span>')
                            .append('<span class="text-xs text-white rounded-md px-2 py-1 bg-gray-700">' + total.toLocaleString('ar-EG') + ' ريال</span>')
                            .append('<button class="btn-inv-view px-3 py-1.5 text-xs rounded-md bg-blue-600 text-white" data-id="' + it.id + '">عرض</button>');
                        $row.append($left).append($right);
                        $wrap.append($row);
                    });

                    $wrap.off('click.inv').on('click.inv', '.btn-inv-view', function() {
                        const id = parseInt($(this).data('id'), 10);
                        // try to find in current set first
                        let item = (items || []).find(x => x.id === id);
                        if (item && item.investments && item.investments.length) {
                            // has investments already – open directly
                            openInvestorModal(item);
                            return;
                        }
                        // fetch detail from server (safe, additive) and open modal
                        $.get(api + '/admin/investors/' + encodeURIComponent(id)).done(function(resp){
                            const inv = resp && resp.investor ? resp.investor : resp || item || {};
                            // attach investments if provided
                            if (Array.isArray(resp.investments)) inv.investments = resp.investments;
                            openInvestorModal(inv);
                        }).fail(function(){
                            // fallback to local item if available
                            if (item) openInvestorModal(item);
                        });
                    });

                    // UI-only handlers for User Permissions tab
                    $(document).on('change', '#up-select-all', function(){
                        const checked = $(this).is(':checked');
                        $('#up-perms .up-item').prop('checked', checked);
                        $('#up-perms .up-cat').prop('checked', checked);
                    });
                    $(document).on('change', '.up-cat', function(){
                        const cat = $(this).data('cat');
                        const checked = $(this).is(':checked');
                        $('#up-perms [data-cat="'+cat+'"] .up-item').prop('checked', checked);
                    });
                }

                function openInvestorModal(inv) {
                    $('#inv-modal-title').text('المستثمر #' + inv.id);
                    $('#inv-name').text(inv.name || '—');
                    $('#inv-email').text(inv.email || '—');
                    $('#inv-phone').text(inv.phone || '—');
                    // compute initials and background gradient for avatar
                    function hashCodeLocal(str){
                        var h = 0; for (var i=0;i<str.length;i++){ h = ((h<<5)-h)+str.charCodeAt(i); h |= 0; } return Math.abs(h);
                    }
                    var cleanName = (inv.name || '').toString().replace(/\s+/g,'');
                    var initials = cleanName ? cleanName.slice(0,2).toUpperCase() : 'IN';
                    var h = hashCodeLocal(cleanName || initials) % 360;
                    var h2 = (h + 40) % 360;
                    var style = 'background: linear-gradient(135deg, hsl('+h+',70%,45%), hsl('+h2+',70%,50%));';
                    $('#inv-avatar').text(initials).attr('style', style);
                    const items = inv.investments || [];
                    const total = items.reduce((s, x) => s + (x.amount || 0), 0);
                    const active = items.filter(x => x.status === 'نشط').length;
                    const completed = items.filter(x => x.status === 'مكتمل').length;
                    $('#inv-stats-total').text(items.length);
                    $('#inv-stats-active').text(active);
                    $('#inv-stats-completed').text(completed);
                    $('#inv-stats-amount').text(total.toLocaleString('ar-EG'));
                    const $list = $('#inv-investments').empty();
                    items.forEach(x => {
                        const badge = x.status === 'نشط' ? 'bg-orange-600' : (x.status === 'مكتمل' ? 'bg-blue-600' : 'bg-yellow-600');
                        $list.append('<div class="bg-gray-900/50 border border-gray-700 rounded p-2 flex items-center justify-between"><div class="text-white text-sm">#' + x.id + ' • ' + x.package + '</div><div class="flex items-center gap-2"><span class="text-xs text-white rounded-md px-2 py-1 ' + badge + '">' + x.status + '</span><span class="text-xs text-white rounded-md px-2 py-1 bg-gray-700">' + (x.amount || 0).toLocaleString('ar-EG') + ' ريال</span></div></div>');
                    });
                    $('#inv-modal').removeClass('hidden').addClass('flex');
                    $('#inv-modal-close, #inv-modal-cancel').off('click').on('click', function() {
                        $('#inv-modal').addClass('hidden').removeClass('flex');
                    });
                }

                // License Requests: demo data + rendering
                function getLicenseRequestsDemo() {
                    return [
                        {
                            id: 101,
                            business_name: 'مؤسسة النور للتجارة',
                            activity_type: 'تجارة الجملة',
                            location: 'الرياض - السعودية',
                            status: 'قيد المراجعة',
                            owner: { name: 'أحمد محمد', national_id: '1029384756', nationality: 'سعودي', phone: '+966500000000' },
                            docs: [
                                { name: 'السجل التجاري', type: 'pdf', url: '/storage/demo/cr.pdf' },
                                { name: 'هوية المالك', type: 'image', url: '/storage/demo/id1.jpg' },
                                { name: 'عقد الإيجار', type: 'pdf', url: '/storage/demo/lease.pdf' }
                            ]
                        },
                        {
                            id: 102,
                            business_name: 'مطاعم ذا تست',
                            activity_type: 'مطاعم ومقاهي',
                            location: 'جدة - السعودية',
                            status: 'مكتمل',
                            owner: { name: 'سارة علي', national_id: '2233445566', nationality: 'سعودية', phone: '+966511111111' },
                            docs: [
                                { name: 'رخصة البلدية', type: 'image', url: '/storage/demo/permit.png' },
                                { name: 'صور الموقع', type: 'image', url: '/storage/demo/site.jpg' }
                            ]
                        }
                    ];
                }

                function fetchLicenseRequests() {
                    // إذا كانت بيانات التراخيص متوفرة من الخادم، نستخدمها مباشرة لتفادي مشاكل الجلسة في بعض البيئات
                    if (window.__INIT_LICENSES__ && Array.isArray(window.__INIT_LICENSES__) && window.__INIT_LICENSES__.length) {
                        renderLicenseRequests(window.__INIT_LICENSES__);
                        return Promise.resolve();
                    }
                    return $.get(api + '/business-licenses')
                        .done(function(d){ renderLicenseRequests(d); })
                        .fail(function(xhr){
                            // لا تستخدم بيانات وهمية
                            if (xhr && xhr.status === 401) {
                                $('#license-req-list').empty().append('<div class="text-gray-400">غير مصرح. سجّل الدخول كمسؤول لعرض البيانات.</div>');
                                $('#license-req-count').text('0 طلب');
                                return;
                            }
                            $('#license-req-list').empty().append('<div class="text-gray-400">تعذر تحميل البيانات حالياً</div>');
                            $('#license-req-count').text('0 طلب');
                        });
                }

                function renderLicenseRequests(items) {
                    const $wrap = $('#license-req-list').empty();
                    $('#license-req-count').text((items?.length || 0) + ' طلب');
                    if (!items || !items.length) {
                        $wrap.append('<div class="text-gray-400 px-2 py-3">لا توجد طلبات حالياً</div>');
                        return;
                    }
                    // Build table
                    const $table = $('<table class="w-full text-sm text-left rtl:text-right text-gray-300"></table>');
                    const $thead = $('<thead class="text-xs bg-gray-800/70 text-gray-200 sticky top-0"></thead>');
                    $thead.append('<tr>' +
                        '<th class="px-3 py-2">#</th>' +
                        '<th class="px-3 py-2">المنشأة</th>' +
                        '<th class="px-3 py-2">النشاط</th>' +
                        '<th class="px-3 py-2">الموقع</th>' +
                        '<th class="px-3 py-2">الحالة</th>' +
                        '<th class="px-3 py-2">اسم المالك الكامل</th>' +
                        '<th class="px-3 py-2">رقم الهوية</th>' +
                        '<th class="px-3 py-2">الهاتف</th>' +
                        '<th class="px-3 py-2">رقم الحساب</th>' +
                        '<th class="px-3 py-2">عرض</th>' +
                    '</tr>');
                    const $tbody = $('<tbody class="divide-y divide-gray-700/60 bg-gray-900/40"></tbody>');
                    items.forEach(it => {
                        const st = (it.status || '').toLowerCase();
                        const badge = st === 'approved' ? 'bg-green-600' : (st === 'rejected' ? 'bg-red-600' : 'bg-yellow-600');
                        const statusText = (typeof statusLabel === 'function') ? statusLabel(st || it.status || '') : (st === 'approved' ? 'موافق عليه' : (st === 'rejected' ? 'مرفوض' : (it.status || 'قيد المراجعة')));
                        const activity = it.activity_description || it.activity_type || it.business_type || '—';
                        const $tr = $('<tr class="hover:bg-gray-800/60 transition"></tr>');
                        $tr.append('<td class="px-3 py-2">' + (it.id) + '</td>');
                        $tr.append('<td class="px-3 py-2 font-semibold text-white">' + (it.business_name || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2">' + activity + '</td>');
                        $tr.append('<td class="px-3 py-2">' + (it.location || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2"><span class="text-xs text-white rounded-md px-2 py-1 ' + badge + '">' + statusText + '</span></td>');
                        $tr.append('<td class="px-3 py-2">' + (it.user_name || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2">' + (it.user_national_id || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2">' + (it.user_phone || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2">' + (it.user_bank_account || '—') + '</td>');
                        $tr.append('<td class="px-3 py-2"><button class="btn-lreq-view px-3 py-1.5 text-xs rounded-md bg-blue-600 text-white" data-id="' + it.id + '" ' +
                            'data-business-name="' + (it.business_name || '') + '" ' +
                            'data-business-type="' + (it.business_type || '') + '" ' +
                            'data-activity-description="' + (it.activity_description || '') + '" ' +
                            'data-location="' + (it.location || '') + '" ' +
                            'data-status="' + (it.status || '') + '" ' +
                            'data-user-name="' + (it.user_name || '') + '" ' +
                            'data-user-nid="' + (it.user_national_id || '') + '" ' +
                            'data-user-phone="' + (it.user_phone || '') + '" ' +
                            'data-user-nationality="' + (it.user_nationality || '') + '" ' +
                            'data-user-bank="' + (it.user_bank_account || '') + '" ' +
                            'data-user-image="' + (it.user_identity_image || '') + '" ' +
                            '>عرض</button></td>');
                        $tbody.append($tr);
                    });
                    $table.append($thead).append($tbody);
                    $wrap.append($table);

                    // Bind click for view (delegated on tbody)
                    $tbody.off('click.lreq').on('click.lreq', '.btn-lreq-view', function() {
                        const id = parseInt($(this).data('id'), 10);
                        let item = (items || []).find(x => x.id === id);
                        if (!item) {
                            const $btn = $(this);
                            item = {
                                id: id,
                                business_name: $btn.data('business-name'),
                                business_type: $btn.data('business-type'),
                                activity_description: $btn.data('activity-description'),
                                location: $btn.data('location'),
                                status: $btn.data('status'),
                                user_name: $btn.data('user-name'),
                                user_national_id: $btn.data('user-nid'),
                                user_nationality: $btn.data('user-nationality'),
                                user_phone: $btn.data('user-phone'),
                                user_bank_account: $btn.data('user-bank'),
                                user_identity_image: $btn.data('user-image')
                            };
                        }
                        openLicenseReqModal(item);
                    });
                }

                function openLicenseReqModal(item) {
                    $('#lreq-modal-title').text('الطلب #' + item.id);
                    $('#lreq-business-name').text(item.business_name || '—');
                    $('#lreq-activity-type').text(item.activity_description || item.activity_type || item.business_type || '—');
                    $('#lreq-location').text(item.location || '—');
                    const st = (item.status || '').toLowerCase();
                    $('#lreq-status').text((typeof statusLabel === 'function') ? statusLabel(st || item.status || '') : (st === 'approved' ? 'موافق عليه' : (st === 'rejected' ? 'مرفوض' : (item.status || 'قيد المراجعة'))));
                    $('#lreq-owner-name').text(item.user_name || '—');
                    $('#lreq-owner-id').text(item.user_national_id ? item.user_national_id : '—');
                    // لا يوجد حقل جنسية في الاستعلام الحالي، يمكن إضافته لاحقاً users.nationality
                    $('#lreq-owner-nationality').text(item.user_nationality || '—');
                    $('#lreq-owner-phone').text(item.user_phone || '—');
                    // عرض الحساب البنكي إن وجد
                    if (item.user_bank_account) {
                        if (!$('#lreq-owner-bank').length) {
                            $('#lreq-owner-phone').parent().after('<div><span class="text-gray-400">الحساب البنكي:</span> <span id="lreq-owner-bank" class="text-white"></span></div>');
                        }
                        $('#lreq-owner-bank').text(item.user_bank_account);
                    }

                    // Docs grid (لا يوجد تخزين للمستندات حالياً)
                    const $docs = $('#lreq-docs').empty();
                    const $prev = $('#lreq-preview').empty();
                    if (item.user_identity_image) {
                        $prev.append('<img src="' + item.user_identity_image + '" alt="هوية" class="max-h-48 rounded border border-gray-700" style="object-fit:cover;"/>');
                    } else {
                        $prev.append('<p class="text-gray-400">لا توجد مستندات مرفوعة</p>');
                    }

                    // Approve/reject handling removed per request; server-side endpoints remain but UI action hidden

                    $('#lreq-modal').removeClass('hidden').addClass('flex');
                    $('#lreq-modal-close, #lreq-modal-cancel').off('click').on('click', function() {
                        $('#lreq-modal').addClass('hidden').removeClass('flex');
                    });
                }

                $(function() {
                    if (window.lucide && lucide.createIcons) {
                        lucide.createIcons();
                    }
                    initTabs();
                    fetchAll();

                    // ===== Chat (Admin) =====
                    const chatState = {
                        users: [],
                        conversations: {}, // userId -> { messages: [{who:'me'|'user', text, ts}], unread: number }
                        activeUserId: null
                    };

                    function chatFormatTime(d) {
                        try {
                            const dt = d ? new Date(d) : new Date();
                            return dt.toLocaleTimeString('ar-SA', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        } catch (e) {
                            return '';
                        }
                    }

                    function chatBubble(msg) {
                        const side = msg.who === 'me' ? 'justify-end' : 'justify-start';
                        const cls = msg.who === 'me' ? 'bg-blue-600/20 border-blue-400/40' :
                            'bg-gray-700/60 border-gray-600';
                        return `<div class="flex ${side}"><div class="max-w-[80%] text-sm text-white border ${cls} rounded-lg px-3 py-2">${msg.text}<div class="text-[10px] text-gray-300 mt-1">${chatFormatTime(msg.ts)}</div></div></div>`;
                    }

                    function renderChatUsers() {
                        const $list = $('#chat-users-list').empty();
                        let totalUnread = 0;
                        chatState.users.forEach(u => {
                            const conv = chatState.conversations[u.id] || {
                                unread: 0
                            };
                            totalUnread += conv.unread || 0;
                            const unreadBadge = conv.unread ?
                                `<span class="ml-auto text-[10px] bg-red-600 text-white rounded-full px-1.5 py-0.5">${conv.unread}</span>` :
                                '';
                                                        const avatarHtml = makeAvatarHtmlGlobal(u.full_name || u.name || ('مستخدم #' + u.id), 'w-8 h-8', 'text-white');
                                                        const $row = $(`<button class="w-full text-right bg-gray-900/40 hover:bg-gray-900/60 border border-gray-700 rounded-md p-2 flex items-center gap-2" data-user-id="${u.id}">
                            ${avatarHtml}
                            <div class="flex-1">
                                <div class="text-white text-sm font-semibold">${u.full_name || u.name || 'مستخدم #' + u.id}</div>
                                <div class="text-gray-400 text-xs">${u.email || ''}</div>
                            </div>
                            ${unreadBadge}
                                    </button>`);
                            $row.on('click', function() {
                                openConversation(u.id);
                            });
                            $list.append($row);
                        });
                        $('#chat-total-unread').toggleClass('hidden', totalUnread === 0).text(totalUnread);
                        if (window.lucide && lucide.createIcons) {
                            lucide.createIcons();
                        }
                    }

                    // admin id for identifying own messages in thread
                    const adminId = "{{ auth()->id() ?? '' }}" || '';

                    // Load conversations from server and keep in chatState.users
                    function loadConversations() {
                        return fetch('/admin/messages/conversations', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                            .then(r => r.json())
                            .then(data => {
                                if (!data || !data.conversations) { chatState.users = []; renderChatUsers(); return; }
                                chatState.users = data.conversations.map(c => ({ id: c.user_id, full_name: c.full_name, email: c.email, conversation_id: c.conversation_id }));
                                // populate conversation metadata
                                chatState.conversations = {};
                                data.conversations.forEach(c => {
                                    chatState.conversations[c.user_id] = { conversation_id: c.conversation_id, unread: parseInt(c.unread) || 0, messages: [] };
                                });
                                renderChatUsers();
                                // open first conversation if any
                                if (chatState.users[0]) openConversation(chatState.users[0].id);
                            }).catch(err => { console.warn('loadConversations failed', err); chatState.users = []; renderChatUsers(); });
                    }

                    function loadMessagesByConversation(conversationId, userId) {
                        if (!conversationId) return Promise.resolve([]);
                        return fetch('/admin/messages/conversations/' + encodeURIComponent(conversationId) + '/messages', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                            .then(r => r.json()).then(resp => {
                                if (!resp || !resp.messages) return [];
                                // map messages to chat UI shape
                                const msgs = resp.messages.map(m => ({ who: (m.sender_id && String(m.sender_id) === String(adminId)) ? 'me' : 'user', text: m.content, ts: m.created_at }));
                                // store
                                if (chatState.conversations[userId]) {
                                    chatState.conversations[userId].messages = msgs;
                                    chatState.conversations[userId].unread = 0; // clear local unread (admin saw them)
                                }
                                return msgs;
                            }).catch(err => { console.warn('loadMessages failed', err); return []; });
                    }

                    function renderThreadForUser(userId) {
                        const conv = chatState.conversations[userId] || { messages: [] };
                        const $thread = $('#chat-thread').empty();
                        (conv.messages || []).forEach(m => { $thread.append(chatBubble(m)); });
                        $thread.scrollTop($thread[0].scrollHeight);
                    }

                    function openConversation(userId) {
                        chatState.activeUserId = userId;
                        const user = chatState.users.find(x => x.id === userId) || {};
                        $('#chat-active-name').text(user.full_name || user.name || ('مستخدم #' + userId));
                        const convMeta = chatState.conversations[userId] || {};
                        $('#chat-active-unread').toggleClass('hidden', !(convMeta.unread > 0)).text(convMeta.unread || '');
                        // load messages and render
                        const convId = convMeta.conversation_id;
                        loadMessagesByConversation(convId, userId).then(function(){
                            renderThreadForUser(userId);
                            // mark as read for admin on server
                            try { fetch('/admin/messages/conversations/' + encodeURIComponent(convId) + '/mark-read', { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } }).catch(()=>{}); } catch(e){}
                            renderChatUsers();
                        });
                    }

                    function sendMessage(text) {
                        const uid = chatState.activeUserId;
                        if (!uid) return;
                        const convMeta = chatState.conversations[uid] || {};
                        // optimistic UI
                        const localMsg = { who: 'me', text: text, ts: new Date() };
                        if (!convMeta.messages) convMeta.messages = [];
                        convMeta.messages.push(localMsg);
                        renderThreadForUser(uid);

                        // send to server; server will create conversation if needed and save message
                        try {
                            const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                            fetch('/admin/messages/send', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({ recipient_user_id: uid, message: text })
                            }).then(function(res){ return res.json().catch(function(){ return { ok: !!res.ok }; }); }).then(function(body){
                                if (body && body.ok) {
                                    // refresh messages from server (to get canonical timestamps/ids and any replies)
                                    if (body.conversation_id) {
                                        loadMessagesByConversation(body.conversation_id, uid).then(function(){ renderThreadForUser(uid); loadConversations(); });
                                    } else {
                                        // fallback: refresh conversations and messages
                                        loadConversations();
                                    }
                                }
                            }).catch(function(err){ console.warn('send message failed', err); loadConversations(); });
                        } catch (e) {
                            console.warn('send message error', e);
                        }
                    }

                    $('#chat-send-form').on('submit', function(e) {
                        e.preventDefault();
                        const txt = ($('#chat-input').val() || '').trim();
                        if (!txt) return;
                        $('#chat-input').val('');
                        sendMessage(txt);
                    });

                    function fetchChatUsers() {
                        // legacy wrapper - use loadConversations
                        return loadConversations();
                    }

                    // Render chat when the tab becomes active first time
                    let chatBooted = false;
                    $(document).on('click', '.tab-trigger[data-tab="chat"]', function() {
                        if (!chatBooted) {
                            chatBooted = true;
                            loadConversations();
                        }
                    });

                    // If the chat tab is already active on page load (via hash or sessionStorage), boot it automatically
                    setTimeout(function(){
                        try {
                            var hashTab = (location.hash && location.hash.length>1) ? location.hash.replace('#','') : null;
                            var stored = null;
                            try { stored = sessionStorage.getItem('admin.activeTab'); } catch(e) { stored = null; }
                            var activeIsChat = (hashTab === 'chat') || (stored === 'chat') || ($('#tab-chat').is(':visible'));
                            if (activeIsChat && !chatBooted) {
                                chatBooted = true;
                                loadConversations();
                            }
                        } catch (e) { /* ignore */ }
                    }, 80);


                    // Packages modal bindings
                    function showPkgModalElement() {
                        var $modal = $('#pkg-modal');
                        try {
                            // move modal to body to avoid stacking-context issues
                            if ($modal.length && $modal.parent()[0] !== document.body) {
                                $modal.appendTo(document.body);
                            }
                        } catch (e) { /* ignore */ }
                        // ensure overlay fills viewport and centers content
                        $modal.css({ position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, display: 'flex', 'align-items': 'center', 'justify-content': 'center', 'background': 'rgba(0,0,0,0.6)', 'z-index': 99999 });
                        // prevent background scroll
                        try { document.body.style.overflow = 'hidden'; } catch (e) {}
                        $modal.removeClass('hidden').addClass('flex');
                    }

                    function hidePkgModalElement(){
                        var $modal = $('#pkg-modal');
                        $modal.addClass('hidden').removeClass('flex');
                        try { document.body.style.overflow = ''; } catch(e) {}
                    }

                    function openPkgModal(mode, item) {
                        try {
                            console.debug('openPkgModal called', { mode: mode, item: item });
                            $('#pkg-modal-title').text(mode === 'edit' ? 'تعديل حزمة' : 'حزمة جديدة');
                            showPkgModalElement();
                            if (mode === 'edit' && item) {
                                $('#pkg-modal').data('mode', 'edit').data('id', item.id);
                                $('#pkg-amount').val(item.amount);
                                $('#pkg-profit').val(item.profit_rate);
                                $('#pkg-name').val(item.name || '');
                                $('#pkg-duration').val(item.duration_months ?? 12);
                                $('#pkg-desc').val(item.description || '');
                                try { $('#pkg-active').prop('checked', (typeof item.is_active !== 'undefined') ? !!item.is_active : true); } catch(e){}
                            } else {
                                $('#pkg-modal').data('mode', 'create').removeData('id');
                                $('#pkg-amount').val('');
                                $('#pkg-profit').val('');
                                $('#pkg-name').val('');
                                $('#pkg-duration').val(12);
                                $('#pkg-desc').val('');
                                $('#pkg-active').prop('checked', true);
                            }
                        } catch (err) {
                            console.error('openPkgModal error', err, mode, item);
                            alert('خطأ داخلي عند فتح نافذة الحزمة — افتح Console للمزيد من التفاصيل');
                        }
                    }
                    // Direct helper used by inline onclick when delegated handlers fail
                    window.__openPkgEditFromBtn = function(el){
                        try{
                            var $btn = $(el);
                            var id = $btn.attr('data-id') || $btn.data('id');
                            var item = {
                                id: id,
                                amount: (function(){ var v = $btn.attr('data-amount') || $btn.data('amount'); return (typeof v !== 'undefined' && v !== '') ? parseFloat(v) : 0; })(),
                                profit_rate: (function(){ var v = $btn.attr('data-profit-rate') || $btn.data('profit-rate'); return (typeof v !== 'undefined' && v !== '') ? parseFloat(v) : null; })(),
                                name: $btn.attr('data-name') || $btn.data('name') || '',
                                duration_months: parseInt($btn.attr('data-duration') || $btn.data('duration') || 12, 10) || 12,
                                description: $btn.attr('data-desc') || $btn.data('desc') || '',
                                is_active: (($btn.attr('data-active') || $btn.data('active')) == '1' || $btn.attr('data-active') === 'true' || $btn.data('active') === 1)
                            };
                            openPkgModal('edit', item);
                        }catch(e){ console.error('openPkgEditFromBtn failed', e); }
                        return false;
                    };
                    $('#new-package-toggle').on('click', function() {
                        openPkgModal('create');
                    });
                    $('#pkg-modal-close, #pkg-modal-cancel').on('click', function() {
                        hidePkgModalElement();
                    });
                    $('#pkg-modal-save').on('click', function() {
                        const mode = $('#pkg-modal').data('mode');
                        const amount = parseFloat($('#pkg-amount').val() || 0);
                        const profit_rate = parseFloat($('#pkg-profit').val() || 0);
                        const name = ($('#pkg-name').val() || '').trim();
                        const duration_months = parseInt($('#pkg-duration').val() || 12, 10) || 12;
                        const description = ($('#pkg-desc').val() || '').trim();
                        const is_active = !!$('#pkg-active').is(':checked');
                        if (mode === 'edit') {
                            const id = $('#pkg-modal').data('id');
                            $.ajax({
                                    url: api + '/admin/investment-packages/' + id,
                                    method: 'PUT',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        amount,
                                        profit_rate,
                                        name,
                                        duration_months,
                                        description,
                                        is_active
                                    }),
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .done(function() {
                                    hidePkgModalElement();
                                    fetchInvestmentPackages();
                                })
                                .fail(function() {
                                    alert('تعذر الحفظ');
                                });
                        } else {
                            $.ajax({
                                    url: api + '/admin/investment-packages',
                                    method: 'POST',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        amount,
                                        profit_rate,
                                        name,
                                        duration_months,
                                        description,
                                        is_active
                                    }),
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .done(function() {
                                    hidePkgModalElement();
                                    fetchInvestmentPackages();
                                })
                                .fail(function() {
                                    alert('تعذر الإنشاء');
                                });
                        }
                    });

                    function updatePkgPreview() {
                        const amount = parseFloat($('#pkg-amount').val() || 0);
                        const rate = parseFloat($('#pkg-profit').val() || 0);
                        const profitVal = Math.round(amount * (rate / 100));
                        const total = amount + profitVal;
                        $('#pkg-prev-amount').text((isFinite(amount) ? amount : 0).toLocaleString() + ' ريال');
                        $('#pkg-prev-rate').text((isFinite(rate) ? rate : 0) + '%');
                        $('#pkg-prev-profit').text('+' + (isFinite(profitVal) ? profitVal : 0).toLocaleString() +
                            ' ريال');
                        $('#pkg-prev-total').text((isFinite(total) ? total : 0).toLocaleString() + ' ريال');
                        $('#pkg-prev-profit-line').text('+' + (isFinite(profitVal) ? profitVal : 0)
                        .toLocaleString() + ' ريال');
                        $('#pkg-prev-total-line').text((isFinite(total) ? total : 0).toLocaleString() + ' ريال');
                    }
                    $(document).on('input', '#pkg-amount, #pkg-profit', updatePkgPreview);

                    $('#run-backup').on('click', function() {
                        $('#backup-status').text('... جاري تشغيل النسخة الاحتياطية');
                        $.post(api + '/admin/backups/run', {
                            _token: '{{ csrf_token() }}'
                        }).done(function(r) {
                            $('#backup-status').text('تم بدء النسخ: ' + (r.file || ''));
                            fetchBackups();
                        }).fail(function() {
                            $('#backup-status').text('تعذر تشغيل النسخة الاحتياطية');
                        });
                    });

                    // Enable restore button when a file is selected
                    $('#restore-file').on('change', function() {
                        $('#restore-backup').prop('disabled', !this.files || !this.files.length);
                    });
                    // Restore from uploaded file
                    $('#restore-backup').on('click', function() {
                        const f = $('#restore-file')[0].files[0];
                        if (!f) {
                            alert('اختر ملف النسخة الاحتياطية أولاً');
                            return;
                        }
                        const fd = new FormData();
                        fd.append('file', f);
                        fd.append('_token', '{{ csrf_token() }}');
                        $('#backup-status').text('... جاري الاستعادة');
                        $.ajax({
                                url: api + '/admin/backups/restore',
                                method: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false
                            })
                            .done(function() {
                                $('#backup-status').text('تمت الاستعادة بنجاح');
                                fetchSettings();
                                fetchInvestmentPackages();
                            })
                            .fail(function() {
                                $('#backup-status').text('تعذر الاستعادة');
                            });
                    });
                    // Restore from existing listed backup
                    $('#backups-list').on('click', '.btn-restore', function() {
                        const filename = $(this).data('file');
                        if (!confirm('هل تريد استعادة النسخة: ' + filename + '؟')) return;
                        $('#backup-status').text('... جاري الاستعادة');
                        $.post(api + '/admin/backups/restore', {
                                filename: filename,
                                _token: '{{ csrf_token() }}'
                            })
                            .done(function() {
                                $('#backup-status').text('تمت الاستعادة بنجاح');
                                fetchSettings();
                                fetchInvestmentPackages();
                            })
                            .fail(function() {
                                $('#backup-status').text('تعذر الاستعادة');
                            });
                    });

                    // Refresh-logs handler removed

                    // Navigation: add new
                    $('#nav-new-save').on('click', function() {
                        const payload = {
                            label: $('#nav-new-label').val(),
                            path: $('#nav-new-path').val(),
                            icon: $('#nav-new-icon').val(),
                            order: parseInt($('#nav-new-order').val() || 0, 10),
                            active: $('#nav-new-active').is(':checked')
                        };
                        $.ajax({
                                url: api + '/admin/navigation',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify(payload),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                $('#nav-new-label, #nav-new-path, #nav-new-icon, #nav-new-order').val(
                                    '');
                                $('#nav-new-active').prop('checked', true);
                                fetchNavigation();
                            })
                            .fail(function() {
                                alert('تعذر الإضافة');
                            });
                    });

                    // Navigation: actions in list
                    $('#nav-list').on('click', '.nav-edit', function() {
                        const $card = $(this).closest('.nav-card');
                        $card.find('.view').addClass('hidden');
                        $card.find('.edit').removeClass('hidden');
                    }).on('click', '.nav-cancel', function() {
                        const $card = $(this).closest('.nav-card');
                        $card.find('.edit').addClass('hidden');
                        $card.find('.view').removeClass('hidden');
                    }).on('click', '.nav-save', function() {
                        const $card = $(this).closest('.nav-card');
                        const id = $card.data('id');
                        const payload = {
                            label: $card.find('.f-label').val(),
                            path: $card.find('.f-path').val(),
                            icon: $card.find('.f-icon').val(),
                            order: parseInt($card.find('.f-order').val() || 0, 10),
                            active: $card.find('.f-active').is(':checked')
                        };
                        $.ajax({
                                url: api + '/admin/navigation/' + id,
                                method: 'PUT',
                                contentType: 'application/json',
                                data: JSON.stringify(payload),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                fetchNavigation();
                            })
                            .fail(function() {
                                alert('تعذر الحفظ');
                            });
                    }).on('click', '.nav-delete', function() {
                        if (!confirm('حذف هذا العنصر؟')) return;
                        const id = $(this).closest('.nav-card').data('id');
                        $.ajax({
                                url: api + '/admin/navigation/' + id,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                fetchNavigation();
                            })
                            .fail(function() {
                                alert('تعذر الحذف');
                            });
                    }).on('change', '.nav-toggle-active', function() {
                        const id = $(this).closest('.nav-card').data('id');
                        const active = this.checked;
                        $.ajax({
                                url: api + '/admin/navigation/' + id,
                                method: 'PUT',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    active
                                }),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .done(function() {
                                fetchNavigation();
                            })
                            .fail(function() {
                                alert('تعذر التحديث');
                            });
                    });

                            $('#save-settings').on('click', function(){
                        const payload = {
                            platform: {
                                name: $('#set-platform-name').val(),
                                url: $('#set-platform-url').val(),
                                locale: $('#set-platform-locale').val(),
                                description: $('#set-platform-description').val(),
                            },
                            contact: {
                                email: $('#set-contact-email').val(),
                                phone: $('#set-contact-phone').val(),
                                whatsapp: $('#set-contact-whatsapp').val(),
                                address: $('#set-contact-address').val(),
                            },
                                    admin: {
                                        name: $('#set-admin-name').val(),
                                        email: $('#set-admin-email').val(),
                                        phone: $('#set-admin-phone').val(),
                                        whatsapp: $('#set-admin-whatsapp').val(),
                                        title: $('#set-admin-title').val(),
                                        avatar: $('#set-admin-avatar').val(),
                                        note: $('#set-admin-note').val(),
                                    },
                            bank: {
                                account_name: $('#set-bank-account-name').val(),
                                bank_name: $('#set-bank-name').val(),
                                account_number: $('#set-bank-account-number').val(),
                                iban: (window._stripIBAN ? window._stripIBAN($('#set-bank-iban').val()) : ($('#set-bank-iban').val()||'')),
                                swift: $('#set-bank-swift').val(),
                                instructions: $('#set-bank-instructions').val(),
                                ask_sender: $('#bank-ask-sender').is(':checked'),
                                ask_datetime: $('#bank-ask-datetime').is(':checked'),
                                require_receipt: $('#bank-require-receipt').is(':checked'),
                            },
                            advanced: {
                                maintenance_mode: $('#adv-maintenance').is(':checked'),
                                registration_enabled: $('#adv-registration').is(':checked'),
                                investment_enabled: $('#adv-investment').is(':checked'),
                                default_success_rate: parseInt($('#adv-success-rate').val() || 98, 10)
                            },
                            _token: '{{ csrf_token() }}'
                        };
                        // Attach per-page overrides into payload.pages when requested
                        try {
                            const applyGlobal = $('#apply-global').is(':checked');
                            const applyHome = $('#apply-home').is(':checked');
                            const applyAdmin = $('#apply-admin').is(':checked');
                            const applyNavbar = $('#apply-navbar').is(':checked');
                            const applyTitle = $('#apply-title').is(':checked');
                            if (applyHome || applyAdmin || applyNavbar || applyTitle) {
                                payload.pages = payload.pages || {};
                                if (applyHome) payload.pages.home = payload.pages.home || {}, payload.pages.home.platform = payload.platform;
                                if (applyAdmin) payload.pages.admin = payload.pages.admin || {}, payload.pages.admin.platform = payload.platform;
                                if (applyNavbar) payload.pages.navbar = payload.pages.navbar || {}, payload.pages.navbar.platform = payload.platform;
                                if (applyTitle) payload.pages.title = payload.pages.title || {}, payload.pages.title.platform = payload.platform;
                            }
                        } catch(e){ console.warn('apply-checkbox read error', e); }
                        $('#settings-status').text('... جاري الحفظ');
                        $.ajax({
                            url: api + '/admin/settings',
                            method: 'PUT',
                            data: payload
                        }).done(function() {
                            $('#settings-status').text('تم الحفظ (تجريبي)');
                            fetchAnalytics();
                            fetchPagesSettings();

                            // Persist platform-specific fields to the dedicated table as well
                            // This keeps legacy `admin_settings` JSON and the new table in sync.
                            try {
                                // Only update global DB row if apply-global is checked
                                if ($('#apply-global').is(':checked')) {
                                    $.ajax({
                                        url: api + '/admin/platform-settings',
                                        method: 'PUT',
                                        data: Object.assign({_token: '{{ csrf_token() }}'}, payload.platform)
                                    }).done(function() {
                                        console.log('platform-settings saved');
                                    }).fail(function() {
                                        console.warn('فشل حفظ إعدادات المنصة المخصصة');
                                    });
                                }
                            // Refresh settings to ensure UI shows the latest values and reset edit mode
                            try { fetchSettings(); } catch (e) { console.warn('fetchSettings unavailable', e); }
                            // reset edit UI (inputs will be disabled again by fetchSettings)
                            $('#cancel-edit-platform').addClass('hidden');
                            $('#edit-platform-btn').removeClass('hidden');
                            } catch (err) {
                                console.warn('خطأ أثناء محاولة حفظ إعدادات المنصة المخصصة', err);
                            }

                        }).fail(function() {
                            $('#settings-status').text('تعذر الحفظ');
                        });
                    });

                    // Branding save
                    $('#save-branding').on('click', function() {
                        const payload = {
                            platform: {
                                logo_url: $('#brand-logo-url').val(),
                                footer_text: $('#brand-footer-text').val(),
                            },
                            contact: {
                                social: {
                                    twitter: $('#social-twitter').val(),
                                    instagram: $('#social-instagram').val(),
                                    linkedin: $('#social-linkedin').val(),
                                    facebook: $('#social-facebook').val(),
                                    youtube: $('#social-youtube').val(),
                                }
                            },
                            _token: '{{ csrf_token() }}'
                        };
                        $('#branding-status').text('... جاري الحفظ');
                        $.ajax({
                            url: api + '/admin/settings',
                            method: 'PUT',
                            data: payload
                        }).done(function() {
                            $('#branding-status').text('تم حفظ المظهر');
                            fetchSettings();
                        }).fail(function() {
                            $('#branding-status').text('تعذر حفظ المظهر');
                        });
                    });

                    // toggle switches visual dot move
                    $(document).on('change', '#tab-settings input[type="checkbox"]', function() {
                        const $span = $(this).next('span');
                        const $dot = $span.find('.dot');
                        if (this.checked) {
                            $dot.css({
                                transform: 'translateX(20px)'
                            });
                            $span.removeClass('bg-gray-600').addClass('bg-green-600');
                        } else {
                            $dot.css({
                                transform: 'translateX(0px)'
                            });
                            $span.removeClass('bg-green-600').addClass('bg-gray-600');
                        }
                    });
                    // Platform edit/save/cancel handlers
                    $('#edit-platform-btn').data('editing', false).on('click', function(){
                        var $btn = $(this);
                        var editing = !!$btn.data('editing');
                        var selectors = ['#set-platform-name','#set-platform-url','#set-platform-locale','#set-platform-description'];

                        if (editing) {
                            // Save mode: collect values and persist
                            var payloadPlatform = {
                                name: $('#set-platform-name').val(),
                                url: $('#set-platform-url').val(),
                                locale: $('#set-platform-locale').val(),
                                description: $('#set-platform-description').val()
                            };

                            $btn.prop('disabled', true).text('جاري الحفظ...');

                            // Decide where to persist changes based on apply checkboxes
                            var applyGlobal = $('#apply-global').is(':checked');
                            var applyHome = $('#apply-home').is(':checked');
                            var applyAdmin = $('#apply-admin').is(':checked');
                            var applyNavbar = $('#apply-navbar').is(':checked');
                            var applyTitle = $('#apply-title').is(':checked');

                            // Prepare admin/settings payload (always used to keep per-page overrides)
                            var settingsPayload = Object.assign({_token: '{{ csrf_token() }}'}, { platform: payloadPlatform });
                            if (applyHome || applyAdmin || applyNavbar || applyTitle) {
                                settingsPayload.pages = settingsPayload.pages || {};
                                if (applyHome) settingsPayload.pages.home = settingsPayload.pages.home || {}, settingsPayload.pages.home.platform = payloadPlatform;
                                if (applyAdmin) settingsPayload.pages.admin = settingsPayload.pages.admin || {}, settingsPayload.pages.admin.platform = payloadPlatform;
                                if (applyNavbar) settingsPayload.pages.navbar = settingsPayload.pages.navbar || {}, settingsPayload.pages.navbar.platform = payloadPlatform;
                                if (applyTitle) settingsPayload.pages.title = settingsPayload.pages.title || {}, settingsPayload.pages.title.platform = payloadPlatform;
                            }

                            // Helper to finalize UI after saves
                            function finalizeSave() {
                                selectors.forEach(function(sel){ $(sel).prop('disabled', true); });
                                $btn.data('editing', false).text('تعديل').prop('disabled', false);
                                $('#cancel-edit-platform').addClass('hidden');
                                try { fetchSettings(); } catch(e) { console.warn(e); }
                            }

                            // If applyGlobal is true, update the DB-backed platform_settings row first,
                            // otherwise only write per-page overrides to admin settings.
                            if (applyGlobal) {
                                $.ajax({
                                    url: api + '/admin/platform-settings',
                                    method: 'PUT',
                                    data: Object.assign({_token: '{{ csrf_token() }}'}, payloadPlatform)
                                }).done(function() {
                                    // keep legacy admin_settings in sync (platform key and any pages overrides)
                                    $.ajax({
                                        url: api + '/admin/settings',
                                        method: 'PUT',
                                        data: settingsPayload
                                    }).always(function(){ finalizeSave(); });
                                }).fail(function() {
                                    alert('تعذر حفظ إعدادات المنصة');
                                    $btn.prop('disabled', false).text('حفظ');
                                });
                            } else {
                                // Not applying globally: only persist to admin/settings so frontend pages can pick up per-page overrides
                                $.ajax({
                                    url: api + '/admin/settings',
                                    method: 'PUT',
                                    data: settingsPayload
                                }).done(function() {
                                    finalizeSave();
                                }).fail(function() {
                                    alert('تعذر حفظ إعدادات المنصة');
                                    $btn.prop('disabled', false).text('حفظ');
                                });
                            }

                        } else {
                            // Enter edit mode
                            selectors.forEach(function(sel){
                                $(sel).prop('disabled', false).removeAttr('readonly').css('pointer-events','auto');
                            });
                            $btn.data('editing', true).text('حفظ');
                            $('#cancel-edit-platform').removeClass('hidden');
                            setTimeout(function(){ $('#set-platform-name').focus(); }, 50);
                        }
                    });

                    $('#cancel-edit-platform').on('click', function(){
                        const p = window._lastPlatformData || {};
                        $('#set-platform-name').val(p.name || '');
                        $('#set-platform-url').val(p.url || '');
                        $('#set-platform-locale').val(p.locale || '');
                        $('#set-platform-description').val(p.description || '');
                        $('#set-platform-name,#set-platform-url,#set-platform-locale,#set-platform-description').prop('disabled', true);
                        $('#edit-platform-btn').data('editing', false).text('تعديل');
                        $(this).addClass('hidden');
                    });
                    // Contact edit/save/cancel handlers
                    $('#edit-contact-btn').data('editing', false).on('click', function(){
                        var $btn = $(this);
                        var editing = !!$btn.data('editing');
                        var selectors = ['#set-contact-email','#set-contact-phone','#set-contact-whatsapp','#set-contact-address'];

                        if (editing) {
                            // Save mode: collect values and persist contact only
                            var payloadContact = {
                                email: $('#set-contact-email').val(),
                                phone: $('#set-contact-phone').val(),
                                whatsapp: $('#set-contact-whatsapp').val(),
                                address: $('#set-contact-address').val()
                            };

                            $btn.prop('disabled', true).text('جاري الحفظ...');

                            $.ajax({
                                url: api + '/admin/contact-info',
                                method: 'PUT',
                                data: Object.assign({_token: '{{ csrf_token() }}'}, payloadContact)
                            }).done(function(res) {
                                try { fetchSettings(); } catch(e) { console.warn(e); }
                                // finalize UI
                                selectors.forEach(function(sel){ $(sel).prop('disabled', true); });
                                $btn.data('editing', false).text('تعديل').prop('disabled', false);
                                $('#cancel-edit-contact').addClass('hidden');
                            }).fail(function() {
                                alert('تعذر حفظ معلومات الاتصال');
                                $btn.prop('disabled', false).text('حفظ');
                            });

                        } else {
                            // Enter edit mode - force-enable inputs (covers attribute and attribute-removal edge cases)
                            selectors.forEach(function(sel){
                                try {
                                    $(sel).prop('disabled', false).removeAttr('disabled').removeAttr('readonly').css('pointer-events','auto');
                                    // also ensure any sibling/parent wrappers allow pointer events
                                    $(sel).closest('div').css('pointer-events','auto');
                                } catch(e) { console.warn('enable contact input failed', sel, e); }
                            });
                            $btn.data('editing', true).text('حفظ');
                            $('#cancel-edit-contact').removeClass('hidden');
                            setTimeout(function(){ try { $('#set-contact-email').focus(); } catch(e){} }, 50);
                        }
                    });

                    $('#cancel-edit-contact').on('click', function(){
                        const c = window._lastContactData || {};
                        $('#set-contact-email').val(c.email || '');
                        $('#set-contact-phone').val(c.phone || '');
                        $('#set-contact-whatsapp').val(c.whatsapp || '');
                        $('#set-contact-address').val(c.address || '');
                        $('#set-contact-email,#set-contact-phone,#set-contact-whatsapp,#set-contact-address').prop('disabled', true);
                        $('#edit-contact-btn').data('editing', false).text('تعديل');
                        $(this).addClass('hidden');
                    });
                    // Users modal bindings
                    $('#user-open-modal').on('click', function() { openUserModal(null); });
                    $('#user-modal-close, #user-modal-cancel').on('click', function() {
                        $('#user-modal').addClass('hidden').removeClass('flex');
                    });

                    // Role filter: re-render users list when changed
                    $(document).on('change', '#user-role-filter', function(){
                        var val = $(this).val();
                        var all = window.__USERS__ && Array.isArray(window.__USERS__) ? window.__USERS__ : [];
                        var filtered = all;
                        if (val === 'users') filtered = all.filter(u => (u.role === 'admin' || u.role === 'staff'));
                        else if (val === 'investors') filtered = all.filter(u => (u.role === 'investor'));
                        renderUsers(filtered);
                    });

                    $('#user-modal-save').on('click', function() {
                        const mode = $('#user-modal').data('mode');
                        const id = $('#user-modal').data('id');
                        const payload = {
                            full_name: $('#user-full-name').val().trim(),
                            username: $('#user-username').val().trim(),
                            email: $('#user-email').val().trim(),
                            phone: $('#user-phone').val().trim(),
                            role: $('#user-role').val(),
                            is_active: $('#user-active').is(':checked') ? 1 : 0,
                            password: $('#user-password').val()
                        };
                        const selectedPerms = [];
                        $('#user-perms .user-perm:checked').each(function(){ selectedPerms.push($(this).data('key')); });
                        if (!payload.full_name || !payload.email) {
                            alert('الاسم الكامل والبريد الإلكتروني مطلوبان');
                            return;
                        }
                        if (mode === 'edit') {
                            $.ajax({
                                url: api + '/admin/users/' + id,
                                method: 'PUT',
                                data: Object.assign({_token: '{{ csrf_token() }}'}, payload)
                            }).done(function() {
                                // save user-specific permissions after successful update
                                $.ajax({
                                    url: api + '/admin/users/' + id + '/permissions',
                                    method: 'PUT',
                                    contentType: 'application/json',
                                    data: JSON.stringify({ permissions: selectedPerms }),
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                }).always(function(){
                                    $('#user-modal').addClass('hidden').removeClass('flex');
                                    fetchUsers();
                                });
                            }).fail(function() {
                                alert('تعذر حفظ التعديلات (عرض تجريبي)');
                                $('#user-modal').addClass('hidden').removeClass('flex');
                                fetchUsers();
                            });
                        } else {
                            $.ajax({
                                url: api + '/admin/users',
                                method: 'POST',
                                data: Object.assign({_token: '{{ csrf_token() }}'}, payload)
                            }).done(function(res) {
                                const newId = res && res.id ? res.id : id;
                                // save user-specific permissions if any
                                $.ajax({
                                    url: api + '/admin/users/' + newId + '/permissions',
                                    method: 'PUT',
                                    contentType: 'application/json',
                                    data: JSON.stringify({ permissions: selectedPerms }),
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                }).always(function(){
                                $('#user-modal').addClass('hidden').removeClass('flex');
                                fetchUsers();
                                });
                            }).fail(function() {
                                alert('تعذر إضافة المستخدم (عرض تجريبي)');
                                $('#user-modal').addClass('hidden').removeClass('flex');
                                fetchUsers();
                            });
                        }
                    });

                    // Global select all
                    $(document).on('change', '#user-perms-select-all', function(){
                        const checked = $(this).is(':checked');
                        $('#user-perms .user-perm').prop('checked', checked);
                        $('#user-perms .user-perm-cat').prop('checked', checked);
                    });
                    // Category select all
                    $(document).on('change', '.user-perm-cat', function(){
                        const cat = $(this).data('cat');
                        const checked = $(this).is(':checked');
                        $('#user-perms [data-cat="'+cat+'"] .user-perm').prop('checked', checked);
                    });
                    // no search filter per request
                });
            })();

            // Extend helpers below current closure
            function getDocumentsDemo() {
                return [{
                        id: 1,
                        filename: 'commercial_license.pdf',
                        type: 'رخصة',
                        user_full_name: 'شركة الأفق',
                        user_email: 'info@alofuq.com',
                        created_at: '2025-10-01 09:30',
                        url: '#'
                    },
                    {
                        id: 2,
                        filename: 'id_national_card.jpg',
                        type: 'هوية وطنية',
                        user_full_name: 'محمد الأحمد',
                        user_email: 'm.ahmad@example.com',
                        created_at: '2025-10-02 14:12',
                        url: '#'
                    },
                    {
                        id: 3,
                        filename: 'bank_statement_q3.pdf',
                        type: 'كشف حساب',
                        user_full_name: 'نورة القحطاني',
                        user_email: 'n.qahtani@example.com',
                        created_at: '2025-10-03 16:05',
                        url: '#'
                    }
                ];
            }

            function fetchDocuments() {
                return $.get('/api/admin/documents').done(function(items) {
                    if (!items || !items.length) {
                        renderDocuments(getDocumentsDemo());
                    } else {
                        renderDocuments(items);
                    }
                }).fail(function() {
                    renderDocuments(getDocumentsDemo());
                });
            }

            function renderDocuments(items) {
                const $wrap = $('#documents-list').empty();
                $('#documents-count').text((items?.length || 0) + ' مستند');
                (items || []).forEach(function(d) {
                    const $row = $(
                        '<div class="bg-gray-800/60 border border-gray-700 rounded-md p-3 flex items-center justify-between"></div>'
                        );
                    const $left = $('<div></div>');
                    $left.append('<p class="text-white">' + (d.filename || '-') +
                        ' <span class="text-xs text-gray-400">(' + (d.type || '-') + ')</span></p>');
                    $left.append('<p class="text-gray-400 text-xs mt-1">المستخدم: ' + (d.user_full_name || '-') +
                        ' · ' + (d.user_email || '-') + ' · ' + (d.created_at || '') + '</p>');
                    const $actions = $('<div class="flex items-center gap-2"></div>');
                    if (d.url) {
                        const $viewBtn = $('<button/>')
                            .addClass(
                                'btn-doc-view border border-blue-500/50 text-blue-400 hover:bg-blue-500/10 text-xs rounded-md px-3 py-1.5'
                                )
                            .text('عرض')
                            .attr('data-url', d.url || '')
                            .attr('data-filename', d.filename || '')
                            .attr('data-type', d.type || '')
                            .attr('data-user', d.user_full_name || '')
                            .attr('data-email', d.user_email || '')
                            .attr('data-date', d.created_at || '');
                        $actions.append($viewBtn);
                    }
                    $actions.append(
                        '<button class="btn-del-doc border border-red-500/50 text-red-400 hover:bg-red-500/10 text-xs rounded-md px-3 py-1.5" data-id="' +
                        d.id + '">حذف</button>');
                    $row.append($left).append($actions);
                    $wrap.append($row);
                });
                $('#documents-list').off('click.doc').on('click.doc', '.btn-del-doc', function() {
                    const id = $(this).data('id');
                    if (!confirm('حذف هذا المستند؟')) return;
                    $.ajax({
                            url: '/api/admin/documents/' + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .done(function() {
                            fetchDocuments();
                        })
                        .fail(function() {
                            alert('تعذر الحذف');
                        });
                }).on('click.doc', '.btn-doc-view', function() {
                    const $btn = $(this);
                    openDocModal({
                        url: $btn.data('url') || '',
                        filename: $btn.data('filename') || '',
                        type: $btn.data('type') || '',
                        user_full_name: $btn.data('user') || '',
                        user_email: $btn.data('email') || '',
                        created_at: $btn.data('date') || ''
                    });
                });
            }

            function openDocModal(doc) {
                $('#doc-filename').text(doc.filename || '-');
                $('#doc-type').text(doc.type || '-');
                const userTxt = [doc.user_full_name, doc.user_email].filter(Boolean).join(' · ');
                $('#doc-user').text(userTxt || '-');
                $('#doc-date').text(doc.created_at || '');
                $('#doc-download').attr('href', doc.url || '#').toggle(!!doc.url);
                // Preview
                const $prev = $('#doc-preview').empty();
                const url = doc.url || '';
                if (!url) {
                    $prev.append('<p class="text-gray-400">لا توجد معاينة متاحة</p>');
                } else if (/\.(png|jpg|jpeg|gif|webp)$/i.test(url)) {
                    $prev.append('<img src="' + url + '" alt="preview" class="max-h-[70vh] rounded" />');
                } else if (/\.(pdf)$/i.test(url)) {
                    $prev.append('<iframe src="' + url + '" class="w-full h-[70vh] rounded" ></iframe>');
                } else if (/\.(txt|log)$/i.test(url)) {
                    $prev.append('<iframe src="' + url + '" class="w-full h-[70vh] rounded" ></iframe>');
                } else {
                    $prev.append('<p class="text-gray-400">لا توجد معاينة لهذا النوع. استخدم "فتح في تبويب".</p>');
                }
                $('#doc-modal').removeClass('hidden').addClass('flex');
            }

            $(function() {
                $('#doc-modal-close, #doc-modal-cancel').on('click', function() {
                    $('#doc-modal').addClass('hidden').removeClass('flex');
                });
            });

            // ===== Pages (Investor) settings state =====
            window.__PAGES = {
                market_insights: {
                    indicators: [],
                    sectors: [],
                    news: [],
                    trends: []
                },
                success_stories: {
                    top_featured: [],
                    stories: []
                },
                trust_compliance: {
                    regulations: [],
                    security_features: [],
                    risk_levels: [],
                    certifications: [],
                    audit_reports: []
                }
            };

            function seedDemoPages() {
                window.__PAGES.market_insights = {
                    indicators: [{
                            name: 'مؤشر السوق العام',
                            value: '12,450.67',
                            change: '+2.45%',
                            trend: 'up'
                        },
                        {
                            name: 'مؤشر الطاقة',
                            value: '8,890.23',
                            change: '+4.12%',
                            trend: 'up'
                        },
                        {
                            name: 'مؤشر البنوك',
                            value: '5,670.89',
                            change: '-1.23%',
                            trend: 'down'
                        }
                    ],
                    sectors: [{
                            name: 'التقنية والاتصالات',
                            performance: '+8.5%',
                            volume: '2.4B ريال',
                            trend: 'صاعد بقوة',
                            color: '#22c55e'
                        },
                        {
                            name: 'الطاقة المتجددة',
                            performance: '+6.2%',
                            volume: '1.8B ريال',
                            trend: 'نمو مستدام',
                            color: '#3b82f6'
                        }
                    ],
                    news: [{
                            title: 'صندوق الاستثمارات العامة يعلن عن استثمارات جديدة',
                            summary: 'تنويع الاستثمارات في القطاعات الناشئة',
                            date: '2025-10-06',
                            category: 'استثمارات حكومية',
                            readTime: '3 دقائق'
                        },
                        {
                            title: 'ارتفاع مؤشر السوق السعودي',
                            summary: 'مكاسب قوية مدفوعة بقطاعي الطاقة والتقنية',
                            date: '2025-10-05',
                            category: 'أسواق مالية',
                            readTime: '2 دقائق'
                        }
                    ],
                    trends: [{
                            trend: 'الاستثمار في التقنيات الناشئة',
                            growth: '+15.3%',
                            description: 'زيادة الاهتمام بالذكاء الاصطناعي والبلوك تشين'
                        },
                        {
                            trend: 'الاستثمار المستدام',
                            growth: '+12.7%',
                            description: 'نمو الاستثمارات في الطاقة المتجددة'
                        }
                    ]
                };
                window.__PAGES.success_stories = {
                    top_featured: [{
                            id: 101,
                            name: 'عبدالرحمن بن سعد المطيري',
                            avatar: 'ع.س',
                            location: 'الرياض',
                            investmentAmount: '750,000 ريال',
                            returns: '375,000 ريال',
                            duration: 'شهر واحد',
                            profitRate: '50%',
                            category: 'large',
                            featured: true,
                            isTop: true,
                            story: 'استثمار استثنائي حقق أرباحاً تفوق التوقعات.',
                            testimonial: 'المنصة رائدة في مجالها.'
                        },
                        {
                            id: 102,
                            name: 'هند بنت علي الشهري',
                            avatar: 'ه.ع',
                            location: 'مكة المكرمة',
                            investmentAmount: '200,000 ريال',
                            returns: '96,000 ريال',
                            duration: 'شهر واحد',
                            profitRate: '48%',
                            category: 'large',
                            featured: true,
                            isTop: true,
                            story: 'فرصة استثمارية موثوقة.',
                            testimonial: 'وجدت ضالتي هنا.'
                        }
                    ],
                    stories: [{
                            id: 1,
                            name: 'أحمد محمد الغامدي',
                            avatar: 'أ.م',
                            location: 'الرياض',
                            investmentAmount: '15,000 ريال',
                            returns: '5,250 ريال',
                            duration: 'شهر واحد',
                            profitRate: '35%',
                            category: 'small',
                            featured: true,
                            story: 'بدأت استثماري...',
                            testimonial: 'أفضل قرار استثماري...'
                        },
                        {
                            id: 2,
                            name: 'سعد بن محمد الزهراني',
                            avatar: 'س.م',
                            location: 'الدمام',
                            investmentAmount: '100,000 ريال',
                            returns: '47,000 ريال',
                            duration: 'شهر واحد',
                            profitRate: '47%',
                            category: 'large',
                            featured: true,
                            story: 'قررت الاستثمار...',
                            testimonial: 'استثمار آمن...'
                        },
                        {
                            id: 3,
                            name: 'نورا أحمد الحربي',
                            avatar: 'ن.أ',
                            location: 'الطائف',
                            investmentAmount: '7,200 ريال',
                            returns: '2,088 ريال',
                            duration: 'شهر واحد',
                            profitRate: '29%',
                            category: 'small',
                            featured: false,
                            story: 'بدأت بمبلغ صغير...',
                            testimonial: 'منصة موثوقة...'
                        }
                    ]
                };
                window.__PAGES.trust_compliance = {
                    regulations: [{
                            name: 'هيئة السوق المالية',
                            nameEn: 'Capital Market Authority (CMA)',
                            license: 'ترخيص رقم: 37-07073',
                            status: 'ساري المفعول',
                            validUntil: '2026-12-31',
                            icon: '🏛️'
                        },
                        {
                            name: 'ساما',
                            nameEn: 'Saudi Central Bank (SAMA)',
                            license: 'INV-2024-001',
                            status: 'ساري المفعول',
                            validUntil: '2026-06-30',
                            icon: '🏦'
                        }
                    ],
                    security_features: [{
                            title: 'تشفير البيانات',
                            description: 'تشفير AES-256 لجميع البيانات',
                            level: 'متقدم',
                            icon: '🔒'
                        },
                        {
                            title: 'حماية متعددة العوامل',
                            description: 'حماية متعددة المراحل',
                            level: 'عالي',
                            icon: '🛡️'
                        }
                    ],
                    risk_levels: [{
                            level: 'منخفض',
                            percentage: '15-25%',
                            description: 'عوائد آمنة',
                            color: '#22c55e'
                        },
                        {
                            level: 'متوسط',
                            percentage: '25-35%',
                            description: 'مخاطر محسوبة',
                            color: '#f59e0b'
                        }
                    ],
                    certifications: [{
                        name: 'ISO 27001',
                        description: 'شهادة أمان المعلومات',
                        issueDate: '2024-01-15',
                        validUntil: '2027-01-15',
                        status: 'ساري'
                    }],
                    audit_reports: [{
                        title: 'تقرير المراجعة السنوية 2024',
                        date: '2024-12-31',
                        type: 'مراجعة شاملة',
                        status: 'مكتمل',
                        fileSize: '2.4 MB'
                    }]
                };
            }

            function fetchPagesSettings() {
                return $.get('/api/admin/settings').done(function(d) {
                    const p = (d && d.pages) || (d && d.settings && d.settings.pages) || (d && d.platform && d.platform
                        .pages) || (d && d);
                    // Try nested under settings.pages as our controller uses
                    const pages = (d && d.pages) || (d && d.settings && d.settings.pages) || {};
                    const mi = pages.market_insights || {};
                    const ss = pages.success_stories || {};
                    const tc = pages.trust_compliance || {};
                    window.__PAGES.market_insights = {
                        indicators: mi.indicators || [],
                        sectors: mi.sectors || [],
                        news: mi.news || [],
                        trends: mi.trends || [],
                    };
                    window.__PAGES.success_stories = {
                        top_featured: ss.top_featured || [],
                        stories: ss.stories || [],
                    };
                    // Merge trust_compliance data instead of replacing it to avoid wiping
                    // client-side additions that haven't been persisted to the server.
                    window.__PAGES.trust_compliance = window.__PAGES.trust_compliance || {};
                    window.__PAGES.trust_compliance.regulations = (Array.isArray(tc.regulations) && tc.regulations.length) ? tc.regulations : (window.__PAGES.trust_compliance.regulations || []);
                    window.__PAGES.trust_compliance.security_features = (Array.isArray(tc.security_features) && tc.security_features.length) ? tc.security_features : (window.__PAGES.trust_compliance.security_features || []);
                    window.__PAGES.trust_compliance.risk_levels = (Array.isArray(tc.risk_levels) && tc.risk_levels.length) ? tc.risk_levels : (window.__PAGES.trust_compliance.risk_levels || []);
                    window.__PAGES.trust_compliance.certifications = (Array.isArray(tc.certifications) && tc.certifications.length) ? tc.certifications : (window.__PAGES.trust_compliance.certifications || []);
                    window.__PAGES.trust_compliance.audit_reports = (Array.isArray(tc.audit_reports) && tc.audit_reports.length) ? tc.audit_reports : (window.__PAGES.trust_compliance.audit_reports || []);
                    // Preserve/merge metrics (may be DB-backed) so settings fetch doesn't wipe them
                    window.__PAGES.trust_compliance.metrics = (Array.isArray(tc.metrics) && tc.metrics.length) ? tc.metrics : (window.__PAGES.trust_compliance.metrics || []);
                    // If data is empty, seed with demo for visual preview
                    const miEmpty = !window.__PAGES.market_insights.indicators.length && !window.__PAGES.market_insights
                        .sectors.length && !window.__PAGES.market_insights.news.length && !window.__PAGES
                        .market_insights.trends.length;
                    const ssEmpty = !window.__PAGES.success_stories.top_featured.length && !window.__PAGES
                        .success_stories.stories.length;
                    const tcEmpty = !window.__PAGES.trust_compliance.regulations.length && !window.__PAGES
                        .trust_compliance.security_features.length && !window.__PAGES.trust_compliance.risk_levels
                        .length && !window.__PAGES.trust_compliance.certifications.length && !window.__PAGES
                        .trust_compliance.audit_reports.length;
                    if (miEmpty && ssEmpty && tcEmpty) {
                        seedDemoPages();
                    }
                    // Load DB-backed success stories and market insights (if available) then render
                    adminSSFetch();
                    // load market insights from DB-backed admin API if available
                    adminMIFetch().done(function(){
                        renderMarketInsightsAdmin();
                        renderTrustComplianceAdmin();
                    }).fail(function(){
                        // fallback to settings-based data if admin API unavailable
                        renderMarketInsightsAdmin();
                        renderTrustComplianceAdmin();
                    });
                }).fail(function() {
                    // Prefill demo data so admin can see shapes
                    seedDemoPages();
                    adminSSFetch();
                    renderMarketInsightsAdmin();
                    renderTrustComplianceAdmin();
                });
            }

            // Fetch success stories from DB-backed admin API and populate window.__PAGES.success_stories
            function adminSSFetch() {
                return $.get('/api/admin/success-stories').done(function(d) {
                    if (d && typeof d === 'object') {
                        window.__PAGES.success_stories = {
                            top_featured: d.top_featured || [],
                            stories: d.stories || []
                        };
                    }
                    renderSuccessStoriesAdmin();
                }).fail(function() {
                    // fallback to existing in-memory stories
                    renderSuccessStoriesAdmin();
                });
            }

            // Fetch market insights from DB-backed admin API and populate window.__PAGES.market_insights
            function adminMIFetch() {
                return $.get('/api/admin/market-insights').done(function(d) {
                    if (d && typeof d === 'object') {
                        window.__PAGES.market_insights = {
                            indicators: d.indicators || [],
                            sectors: d.sectors || [],
                            news: d.news || [],
                            trends: d.trends || []
                        };
                    }
                    renderMarketInsightsAdmin();
                }).fail(function() {
                    // fallback to settings-based data handled by fetchPagesSettings caller
                });
            }

            function savePagesSection(sectionPath, dataObj, $statusEl) {
                const payload = {
                    pages: {}
                };
                // sectionPath like 'success_stories.top_featured'
                const [group, key] = sectionPath.split('.');
                payload.pages[group] = {};
                payload.pages[group][key] = dataObj;
                payload._token = '{{ csrf_token() }}';
                if ($statusEl) {
                    $statusEl.text('... جاري الحفظ');
                }
                // If saving market_insights, send to DB-backed endpoint with full object
                if (group === 'market_insights') {
                    const miPayload = {
                        indicators: window.__PAGES.market_insights.indicators || [],
                        sectors: window.__PAGES.market_insights.sectors || [],
                        news: window.__PAGES.market_insights.news || [],
                        trends: window.__PAGES.market_insights.trends || []
                    };
                    return $.ajax({
                        url: '/api/admin/market-insights',
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(miPayload),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function() {
                        if ($statusEl) { $statusEl.text('تم الحفظ'); }
                        // refresh from DB to get canonical IDs
                        adminMIFetch();
                    }).fail(function() {
                        if ($statusEl) { $statusEl.text('تعذر الحفظ'); }
                    });
                }

                return $.ajax({
                        url: '/api/admin/settings',
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .done(function() {
                        if ($statusEl) {
                            $statusEl.text('تم الحفظ');
                        }
                        fetchPagesSettings();
                    })
                    .fail(function() {
                        if ($statusEl) {
                            $statusEl.text('تعذر الحفظ');
                        }
                    });
            }

            // ===== Success Stories: UI & Logic =====
            let __SS_EDIT = {
                mode: 'create',
                index: -1,
                top: false
            };

            function ssStoryRow(story, idx, top) {
                const badge = story.featured ?
                    '<span class="text-xs bg-yellow-600/40 text-yellow-200 rounded px-2 py-0.5">مميز</span>' : '';
                return [
                    '<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3">',
                    '<div class="flex items-start justify-between gap-3">',
                    '<div>',
                    '<p class="text-white font-semibold">', (story.name || '-'), ' ', badge, '</p>',
                    '<p class="text-gray-400 text-xs">', (story.location || '-'), ' · ', (story.investmentAmount || '-'),
                    ' · ', (story.returns || '-'), '</p>',
                    '<p class="text-gray-300 text-xs mt-1">', (story.story ? (story.story.substring(0, 80) + '...') : ''),
                    '</p>',
                    '</div>',
                    '<div class="flex items-center gap-2">',
                    '<button class="ss-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs" data-idx="',
                    idx, '" data-top="', (top ? 1 : 0), '">تعديل</button>',
                    '<button class="ss-del border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs" data-idx="',
                    idx, '" data-top="', (top ? 1 : 0), '">حذف</button>',
                    '</div>',
                    '</div>',
                    '</div>'
                ].join('');
            }

            function renderSuccessStoriesAdmin() {
                const ss = window.__PAGES.success_stories;
                const $top = $('#ss-top-list').empty();
                const $list = $('#ss-stories-list').empty();
                (ss.top_featured || []).forEach(function(s, i) {
                    $top.append(ssStoryRow(s, i, true));
                });
                (ss.stories || []).forEach(function(s, i) {
                    $list.append(ssStoryRow(s, i, false));
                });
                $('#ss-count').text(((ss.stories || []).length) + ' قصة');

                $('#ss-top-list, #ss-stories-list').off('click.ss').on('click.ss', '.ss-edit', function() {
                    const idx = parseInt($(this).data('idx'), 10);
                    const top = $(this).data('top') == 1;
                    __SS_EDIT = {
                        mode: 'edit',
                        index: idx,
                        top
                    };
                    const item = top ? (ss.top_featured[idx] || {}) : (ss.stories[idx] || {});
                    fillSSModal(item);
                    openSSModal();
                }).on('click.ss', '.ss-del', function() {
                    const idx = parseInt($(this).data('idx'), 10);
                    const top = $(this).data('top') == 1;
                    if (!confirm('حذف هذه القصة؟')) return;
                    const item = top ? (ss.top_featured[idx] || {}) : (ss.stories[idx] || {});
                    if (item && item.id) {
                        $.ajax({
                            url: '/api/admin/success-stories/' + encodeURIComponent(item.id),
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).done(function() {
                            adminSSFetch();
                        }).fail(function() {
                            alert('تعذر حذف القصة');
                        });
                    } else {
                        // fallback to in-memory removal & settings save
                        if (top) {
                            ss.top_featured.splice(idx, 1);
                            savePagesSection('success_stories.top_featured', ss.top_featured);
                        } else {
                            ss.stories.splice(idx, 1);
                            savePagesSection('success_stories.stories', ss.stories);
                        }
                    }
                });

                if (window.lucide && lucide.createIcons) {
                    lucide.createIcons();
                }
            }

            function openSSModal() {
                $('#ss-modal').removeClass('hidden').addClass('flex');
            }

            function closeSSModal() {
                $('#ss-modal').addClass('hidden').removeClass('flex');
            }

            function clearSSModal() {
                $('#ss-name,#ss-avatar,#ss-location,#ss-amount,#ss-returns,#ss-duration,#ss-profit,#ss-story,#ss-testimonial')
                    .val('');
                $('#ss-category').val('small');
                $('#ss-featured').prop('checked', false);
                $('#ss-istop').prop('checked', false);
            }

            function fillSSModal(item) {
                $('#ss-name').val(item.name || '');
                $('#ss-avatar').val(item.avatar || '');
                $('#ss-location').val(item.location || '');
                $('#ss-amount').val(item.investmentAmount || '');
                $('#ss-returns').val(item.returns || '');
                $('#ss-duration').val(item.duration || '');
                $('#ss-profit').val(item.profitRate || '');
                $('#ss-category').val(item.category || 'small');
                $('#ss-featured').prop('checked', !!item.featured);
                $('#ss-istop').prop('checked', !!item.isTop);
                $('#ss-story').val(item.story || '');
                $('#ss-testimonial').val(item.testimonial || '');
            }

            function getSSFromModal() {
                return {
                    id: Date.now(),
                    name: $('#ss-name').val(),
                    avatar: $('#ss-avatar').val(),
                    location: $('#ss-location').val(),
                    investmentAmount: $('#ss-amount').val(),
                    returns: $('#ss-returns').val(),
                    duration: $('#ss-duration').val(),
                    profitRate: $('#ss-profit').val(),
                    category: $('#ss-category').val(),
                    featured: $('#ss-featured').is(':checked'),
                    isTop: $('#ss-istop').is(':checked'),
                    story: $('#ss-story').val(),
                    testimonial: $('#ss-testimonial').val()
                };
            }

            $(function() {
                // Modal bindings
                $('#ss-open-modal').on('click', function() {
                    __SS_EDIT = {
                        mode: 'create',
                        index: -1,
                        top: false
                    };
                    clearSSModal();
                    openSSModal();
                });
                $('#ss-modal-close, #ss-modal-cancel').on('click', function() {
                    closeSSModal();
                });
                $('#ss-modal-save').on('click', function() {
                    const ss = window.__PAGES.success_stories;
                    const data = getSSFromModal();
                    // build payload with expected server keys
                    const payload = {
                        name: data.name,
                        avatar: data.avatar,
                        location: data.location,
                        investmentAmount: data.investmentAmount,
                        returns: data.returns,
                        duration: data.duration,
                        profitRate: data.profitRate,
                        category: data.category,
                        featured: data.featured ? 1 : 0,
                        isTop: data.isTop ? 1 : 0,
                        story: data.story,
                        testimonial: data.testimonial
                    };

                    if (__SS_EDIT.mode === 'edit') {
                        const item = __SS_EDIT.top ? (ss.top_featured[__SS_EDIT.index] || {}) : (ss.stories[__SS_EDIT.index] || {});
                        if (item && item.id) {
                            $.ajax({
                                url: '/api/admin/success-stories/' + encodeURIComponent(item.id),
                                method: 'PUT',
                                contentType: 'application/json',
                                data: JSON.stringify(payload),
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            }).done(function() {
                                adminSSFetch();
                            }).fail(function() {
                                alert('تعذر تحديث القصة');
                            });
                        } else {
                            // fallback: update in-memory and save to settings
                            if (__SS_EDIT.top) {
                                ss.top_featured[__SS_EDIT.index] = data;
                                savePagesSection('success_stories.top_featured', ss.top_featured);
                            } else {
                                ss.stories[__SS_EDIT.index] = data;
                                savePagesSection('success_stories.stories', ss.stories);
                            }
                        }
                    } else {
                        // create new story
                        $.ajax({
                            url: '/api/admin/success-stories',
                            method: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify(payload),
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).done(function() {
                            adminSSFetch();
                        }).fail(function() {
                            alert('تعذر إنشاء القصة');
                        });
                    }
                    closeSSModal();
                });
                $('#ss-save-all').on('click', function() {
                    const ss = window.__PAGES.success_stories;
                    // Save both arrays
                    $.when(
                        savePagesSection('success_stories.top_featured', ss.top_featured),
                        savePagesSection('success_stories.stories', ss.stories)
                    ).done(function() {
                        /* no-op */ });
                });
            });

            // ===== Market Insights: simple forms & list items =====
            let __MI_EDIT = {
                mode: 'create',
                index: -1,
                type: 'indicator'
            };

            function miRow(obj, idx, type) {
                const title = (obj.name || obj.title || obj.trend || obj.category || '-');
                const sub = (obj.value || obj.performance || obj.description || obj.summary || obj.growth || '');
                // Special rendering for news: make card width responsive (fills column) and allow long text to wrap vertically
                if (type === 'news') {
                    const summary = (obj.summary || '').replace(/(\S{40})(?=\S)/g, '$1&#8203;');
                    const date = obj.date || '';
                    const readTime = obj.readTime || obj.read_time || '';
                    return [
                        '<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3" style="width:100%;box-sizing:border-box;overflow-wrap:anywhere;word-break:break-word;">',
                        '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">',
                            '<span style="color:#22d3ee;border:1px solid #22d3ee;border-radius:8px;padding:4px 8px;font-size:12px;">', (obj.category || ''), '</span>',
                            '<div style="color:#94a3b8;font-size:12px;">', date, ' • ', readTime, '</div>',
                        '</div>',
                        '<h3 style="color:#fff;font-size:16px;font-weight:800;margin-bottom:6px;">', title, '</h3>',
                        '<div style="color:#cbd5e1;margin-bottom:10px;">', summary, '</div>',
                        '<div style="display:flex;justify-content:flex-end;gap:8px;">',
                            '<button class="mi-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-id="', (obj.id || ''), '" data-type="', type, '">تعديل</button>',
                            '<button class="mi-del border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-id="', (obj.id || ''), '" data-type="', type, '">حذف</button>',
                        '</div>',
                        '</div>'
                    ].join('');
                }

                return [
                    '<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3 mi-card">',
                    '<div style="margin-bottom:8px;"><p class="text-white font-semibold">', title, '</p></div>',
                    '<div class="mi-summary text-gray-400 text-sm" style="margin-bottom:10px;">', sub, '</div>',
                    '<div style="display:flex;justify-content:flex-end;gap:8px;">',
                    '<button class="mi-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-id="', (obj.id || ''), '" data-type="', type, '">تعديل</button>',
                    '<button class="mi-del border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-id="', (obj.id || ''), '" data-type="', type, '">حذف</button>',
                    '</div>',
                    '</div>'
                ].join('');
            }

            function renderMarketInsightsAdmin() {
                const mi = window.__PAGES.market_insights;
                const $ind = $('#mi-indicators-list').empty();
                const $sec = $('#mi-sectors-list').empty();
                const $news = $('#mi-news-list').empty();
                const $tr = $('#mi-trends-list').empty();
                (mi.indicators || []).forEach((o, i) => $ind.append(miRow(o, i, 'indicator')));
                (mi.sectors || []).forEach((o, i) => $sec.append(miRow(o, i, 'sector')));
                (mi.news || []).forEach((o, i) => $news.append(miRow(o, i, 'news')));
                (mi.trends || []).forEach((o, i) => $tr.append(miRow(o, i, 'trend')));
            }

            function openMIModal(type, item) {
                __MI_EDIT.type = type;
                __MI_EDIT.mode = item ? 'edit' : 'create';
                $('#mi-modal-title').text(item ? 'تعديل' : 'إضافة');
                __MI_EDIT.id = item && item.id ? item.id : null;
                const $f = $('#mi-form').empty();
                if (type === 'indicator') {
                    $f.append(input('name', 'اسم المؤشر', item?.name));
                    $f.append(input('value', 'القيمة', item?.value));
                    $f.append(input('change', 'التغير', item?.change));
                    $f.append(select('trend', 'الاتجاه', ['up', 'down'], item?.trend || 'up'));
                } else if (type === 'sector') {
                    $f.append(input('name', 'اسم القطاع', item?.name));
                    $f.append(input('performance', 'الأداء', item?.performance));
                    $f.append(input('volume', 'حجم التداول', item?.volume));
                    $f.append(input('trend', 'الاتجاه', item?.trend));
                    $f.append(input('color', 'اللون', item?.color || '#22c55e'));
                } else if (type === 'news') {
                    $f.append(input('title', 'العنوان', item?.title));
                    $f.append(input('summary', 'الملخص', item?.summary));
                    $f.append(input('date', 'التاريخ', item?.date));
                    $f.append(input('category', 'التصنيف', item?.category));
                    $f.append(input('readTime', 'زمن القراءة', item?.readTime || '3 دقائق'));
                    // reading duration: value + unit (minutes/hours/days/weeks/months)
                    const rpVal = item && (item.reading_period_value || item.reading_period_value === 0) ? item.reading_period_value : '';
                    const rpUnit = item && (item.reading_period_unit) ? item.reading_period_unit : 'days';
                    $f.append('<div><label class="block text-sm text-gray-300 mb-1">مدة العرض (بعدها تختفي الأخبار)</label>' +
                        '<div class="flex gap-2">' +
                        '<input data-key="reading_period_value" type="number" min="0" value="' + rpVal + '" class="w-1/3 bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" />' +
                        '<select data-key="reading_period_unit" class="w-2/3 bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white">' +
                        '<option value="minutes" ' + (rpUnit === 'minutes' ? 'selected' : '') + '>دقائق</option>' +
                        '<option value="hours" ' + (rpUnit === 'hours' ? 'selected' : '') + '>ساعات</option>' +
                        '<option value="days" ' + (rpUnit === 'days' ? 'selected' : '') + '>أيام</option>' +
                        '<option value="weeks" ' + (rpUnit === 'weeks' ? 'selected' : '') + '>أسابيع</option>' +
                        '<option value="months" ' + (rpUnit === 'months' ? 'selected' : '') + '>شهور</option>' +
                        '</select></div></div>');
                } else if (type === 'trend') {
                    $f.append(input('trend', 'الاتجاه', item?.trend));
                    $f.append(input('growth', 'النمو', item?.growth));
                    $f.append(input('description', 'الوصف', item?.description));
                }
                $('#mi-modal').removeClass('hidden').addClass('flex');
            }

            function input(key, label, val) {
                // If the field is a date-like key, render an input[type=date] with a calendar icon and yellow style
                const dateKeys = ['date','validUntil','issueDate','issued_at','issuedAt','expires_at','audit_date','valid_until'];
                if (dateKeys.indexOf(key) !== -1) {
                    let v = val || '';
                    try { if (v && typeof v === 'string') v = v.slice(0,10); } catch(e) {}
                    return '<div class="relative">'
                        + '<label class="block text-sm text-gray-300 mb-1">' + label + '</label>'
                        + '<input data-key="' + key + '" type="date" value="' + v + '" '
                        + 'class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 pr-10 text-white" '
                        + 'style="background: linear-gradient(90deg,#b8860b,#f6c85f); border-color: rgba(180,134,11,0.9);" />'
                        + '<div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">'
                        + '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">'
                        + '<path fill-rule="evenodd" d="M6 2a1 1 0 011 1v1h6V3a1 1 0 112 0v1h1a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm1 8a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />'
                        + '</svg>'
                        + '</div>'
                        + '</div>';
                }

                // Special-case percentage -> use number input and helper text
                if (key === 'percentage') {
                    const v = (val !== undefined && val !== null) ? String(val) : '';
                    return '<div>' +
                        '<label class="block text-sm text-gray-300 mb-1">' + label + '</label>' +
                        '<input data-key="' + key + '" type="number" step="0.01" min="0" '
                            + 'class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' + v + '" '
                            + 'placeholder="مثال: 50 أو 12.5" />' +
                        '<div class="text-xs text-gray-400 mt-1">أدخل قيمة رقمية (بدون %). مثال: 50 أو 12.5</div>' +
                        '</div>';
                }

                // percentage_text: textual range (e.g. "35-50%")
                if (key === 'percentage_text') {
                    const v = (val !== undefined && val !== null) ? String(val) : '';
                    return '<div>' +
                        '<label class="block text-sm text-gray-300 mb-1">' + label + '</label>' +
                        '<input data-key="' + key + '" type="text" '
                            + 'class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' + v + '" '
                            + 'placeholder="مثال: 35-50%" />' +
                        '<div class="text-xs text-gray-400 mt-1">اكتب نطاق النسبة مع رمز % أو بدونه، سيُخزن نصيًا.</div>' +
                        '</div>';
                }

                return '<div><label class="block text-sm text-gray-300 mb-1">' + label + '</label><input data-key="' + key +
                    '" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" value="' + (val || '') +
                    '" /></div>';
            }

            function select(key, label, opts, val) {
                let html = '<div><label class="block text-sm text-gray-300 mb-1">' + label + '</label><select data-key="' +
                    key + '" class="w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white">';
                opts.forEach(o => {
                    html += '<option ' + (String(o) === String(val) ? 'selected' : '') + ' value="' + o + '">' + o +
                        '</option>';
                });
                html += '</select></div>';
                return html;
            }

            function gatherForm($scope) {
                const obj = {};
                $scope.find('[data-key]').each(function() {
                    obj[$(this).data('key')] = $(this).val();
                });
                return obj;
            }

            $(function() {
                // Market Insights bindings
                $('#mi-modal-close, #mi-modal-cancel').on('click', function() {
                    $('#mi-modal').addClass('hidden').removeClass('flex');
                });
                $('#mi-modal-save').on('click', function() {
                    const type = __MI_EDIT.type;
                    const $f = $('#mi-form');
                    const obj = gatherForm($f);
                    const mi = window.__PAGES.market_insights;
                    const map = {
                        indicator: 'indicators',
                        sector: 'sectors',
                        news: 'news',
                        trend: 'trends'
                    };
                    const arrKey = map[type];

                    if (__MI_EDIT.mode === 'edit') {
                        // if we have a DB id, update single item via API
                        const id = __MI_EDIT.id || (mi[arrKey] && mi[arrKey][__MI_EDIT.index] && mi[arrKey][__MI_EDIT.index].id) || null;
                        if (id) {
                            $.ajax({
                                url: '/api/admin/market-insights/' + type + '/' + id,
                                method: 'PUT',
                                contentType: 'application/json',
                                data: JSON.stringify(obj),
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            }).done(function(updated) {
                                mi[arrKey][__MI_EDIT.index] = updated;
                                renderMarketInsightsAdmin();
                                try { showAdminToast('success', 'تم التعديل بنجاح'); } catch(e) {}
                            }).fail(function(xhr) {
                                alert('فشل تحديث العنصر: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'خطأ'));
                            }).always(function() {
                                $('#mi-modal').addClass('hidden').removeClass('flex');
                            });
                            return;
                        }

                        // fallback: update in-memory and bulk save
                        mi[arrKey][__MI_EDIT.index] = obj;
                        savePagesSection('market_insights.' + arrKey, mi[arrKey]).done(function(){ try{ showAdminToast('success', 'تم الحفظ بنجاح'); }catch(e){} });
                        $('#mi-modal').addClass('hidden').removeClass('flex');
                        return;
                    }

                    // Create mode: send single-item create request to admin API
                    $.ajax({
                        url: '/api/admin/market-insights/' + type,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(obj),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function(created) {
                        // ensure arrays exist
                        mi[arrKey] = mi[arrKey] || [];
                        // push created canonical object returned by server
                        mi[arrKey].push(created);
                        renderMarketInsightsAdmin();
                        try { showAdminToast('success', 'تم الإنشاء بنجاح'); } catch(e) {}
                        // refresh public canonical data by re-fetching from DB if needed
                        // (the public page reads from DB on load; if you need live push we can add a cache-bust or websocket)
                    }).fail(function(xhr) {
                        alert('فشل إنشاء العنصر: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'خطأ'));
                    }).always(function() {
                        $('#mi-modal').addClass('hidden').removeClass('flex');
                    });
                });
                $('#mi-add-indicator').on('click', function() {
                    __MI_EDIT.index = -1;
                    openMIModal('indicator');
                });
                $('#mi-add-sector').on('click', function() {
                    __MI_EDIT.index = -1;
                    openMIModal('sector');
                });
                $('#mi-add-news').on('click', function() {
                    __MI_EDIT.index = -1;
                    openMIModal('news');
                });
                $('#mi-add-trend').on('click', function() {
                    __MI_EDIT.index = -1;
                    openMIModal('trend');
                });
                // Mobile MI menu
                $('#mi-menu-toggle').on('click', function() {
                    $('#mi-menu').toggleClass('hidden');
                });
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#mi-menu, #mi-menu-toggle').length) {
                        $('#mi-menu').addClass('hidden');
                    }
                });
                $('#mi-menu').on('click', '.mi-menu-item', function() {
                    const type = $(this).data('type');
                    __MI_EDIT.index = -1;
                    openMIModal(type);
                    $('#mi-menu').addClass('hidden');
                });
                // delegated handler for per-panel add buttons
                $(document).on('click', '.mi-add', function(e){
                    e.preventDefault();
                    const type = $(this).data('type');
                    __MI_EDIT.index = -1;
                    openMIModal(type);
                });
                $('#mi-indicators-list, #mi-sectors-list, #mi-news-list, #mi-trends-list').on('click', '.mi-edit', function() {
                    const type = $(this).data('type');
                    const idx = parseInt($(this).data('idx'), 10);
                    __MI_EDIT.index = idx;
                    __MI_EDIT.mode = 'edit';
                    const mi = window.__PAGES.market_insights;
                    const map = {
                        indicator: 'indicators',
                        sector: 'sectors',
                        news: 'news',
                        trend: 'trends'
                    };
                    const item = mi[map[type]][idx];
                    __MI_EDIT.id = item && item.id ? item.id : null;
                    openMIModal(type, item);
                }).on('click', '.mi-del', function() {
                    const type = $(this).data('type');
                    const idx = parseInt($(this).data('idx'), 10);
                    if (!confirm('حذف هذا العنصر؟')) return;
                    const mi = window.__PAGES.market_insights;
                    const map = {
                        indicator: 'indicators',
                        sector: 'sectors',
                        news: 'news',
                        trend: 'trends'
                    };
                    const key = map[type];
                    const item = mi[key][idx];
                    const id = item && item.id ? item.id : null;
                    if (!id) {
                        // fallback to array removal + bulk save
                        mi[key].splice(idx, 1);
                        savePagesSection('market_insights.' + key, mi[key]).done(function(){ try{ showAdminToast('success', 'تم الحذف بنجاح'); }catch(e){} });
                        return;
                    }
                    // delete single item via API
                    $.ajax({
                        url: '/api/admin/market-insights/' + type + '/' + id,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function() {
                        mi[key].splice(idx, 1);
                        renderMarketInsightsAdmin();
                        try { showAdminToast('success', 'تم الحذف بنجاح'); } catch(e) {}
                    }).fail(function() {
                        alert('فشل حذف العنصر');
                    });
                });
            });

            // ===== Trust & Compliance =====
            let __TC_EDIT = {
                mode: 'create',
                index: -1,
                type: 'reg'
            };

            function tcRow(obj, idx, type) {
                const title = obj.name || obj.title || obj.level || '-';
                const sub = obj.description || obj.license || obj.type || '';
                return [
                    '<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3 flex items-center justify-between">',
                    '<div><p class="text-white font-semibold">', title, '</p><p class="text-gray-400 text-xs">', sub,
                    '</p></div>',
                    '<div class="flex items-center gap-2">',
                    '<button class="tc-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs" data-idx="',
                    idx, '" data-type="', type, '">تعديل</button>',
                    ((type === 'cert' || type === 'reg') && (obj.file_path || obj.file || obj.path) ? ('<button class="tc-view border border-sky-500/50 text-sky-400 rounded-md px-2 py-1 text-xs" data-path="' + (obj.file_path || obj.file || obj.path) + '">عرض</button>') : ''),
                    '<button class="tc-del border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs" data-idx="',
                    idx, '" data-type="', type, '">حذف</button>',
                    '</div>',
                    '</div>'
                ].join('');
            }

            function tcMetricRow(obj, idx, type) {
                const label = obj.label || obj.key || '—';
                const value = obj.value || '';
                return [
                    '<div class="bg-gray-800/50 border border-gray-700 rounded-md p-3 flex items-center justify-between">',
                    '<div>',
                        '<div class="text-sm text-gray-400">', label, '</div>',
                        '<div class="text-2xl font-bold text-white">', value, '</div>',
                    '</div>',
                    '<div class="flex items-center gap-2">',
                        '<button class="tc-edit border border-blue-500/50 text-blue-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-type="', type, '">تعديل</button>',
                        '<button class="tc-del border border-red-500/50 text-red-400 rounded-md px-2 py-1 text-xs" data-idx="', idx, '" data-type="', type, '">حذف</button>',
                    '</div>',
                    '</div>'
                ].join('');
            }

            function renderTrustComplianceAdmin() {
                const tc = window.__PAGES.trust_compliance;
                const $reg = $('#tc-reg-list').empty();
                const $sec = $('#tc-sec-list').empty();
                const $risk = $('#tc-risk-list').empty();
                const $cert = $('#tc-cert-list').empty();
                const $audit = $('#tc-audit-list').empty();
                (tc.regulations || []).forEach((o, i) => $reg.append(tcRow(o, i, 'reg')));
                (tc.security_features || []).forEach((o, i) => $sec.append(tcRow(o, i, 'sec')));
                (tc.risk_levels || []).forEach((o, i) => $risk.append(tcRow(o, i, 'risk')));
                (tc.certifications || []).forEach((o, i) => $cert.append(tcRow(o, i, 'cert')));
                (tc.audit_reports || []).forEach((o, i) => $audit.append(tcRow(o, i, 'audit')));
                // metrics removed from client-side view
            }

            function openTCModal(type, item) {
                __TC_EDIT.type = type;
                __TC_EDIT.mode = item ? 'edit' : 'create';
                $('#tc-modal-title').text(item ? 'تعديل' : 'إضافة');
                const $f = $('#tc-form').empty();
                if (type === 'reg') {
                    // prefer DB keys (title, name_en, valid_until) but accept legacy client keys
                    // Order and grouping: name, nameEn, icon, license, status, validUntil, file
                    $f.append(input('name', 'الاسم (ar)', item?.title || item?.name));
                    $f.append(input('nameEn', 'الاسم (en)', item?.name_en || item?.nameEn));
                    $f.append(input('icon', 'أيقونة', item?.icon || '🏛️'));
                    $f.append(input('license', 'الترخيص/السجل', item?.license));
                    $f.append(input('status', 'الحالة', item?.status));
                    $f.append(input('validUntil', 'ساري حتى', item?.valid_until || item?.validUntil));

                    // file upload for regulation (PDF) - styled block placed inside the form
                    var existingRegFile = item?.file_path || item?.file || item?.path || null;
                    var regFileHtml = '<div class="md:col-span-2">' +
                        '<label class="block text-sm text-gray-300 mb-1">ملف الترخيص/اللائحة (PDF)</label>' +
                        '<div class="mt-1 flex items-center gap-3">' +
                            '<input type="file" accept=".pdf" name="certificate_file" id="certificate_file" class="text-sm text-white bg-gray-900/50 border border-gray-700 rounded-md p-2" />' +
                            (existingRegFile ? ('<button type="button" class="tc-existing-file text-sky-400 text-sm underline" data-path="' + existingRegFile + '">عرض</button>') : '') +
                        '</div>' +
                    '</div>';
                    $f.append(regFileHtml);
                    // Header file actions: show upload input when creating, show download button when editing
                    var fileActionsHtml = '';
                    // Do not show file actions in modal header; use only the file input inside modal body
                    fileActionsHtml = '';
                } else if (type === 'sec') {
                    $f.append(input('title', 'العنوان', item?.title));
                    $f.append(input('description', 'الوصف', item?.description));
                    $f.append(input('level', 'المستوى', item?.level));
                    $f.append(input('icon', 'أيقونة', item?.icon || '🔒'));
                } else if (type === 'risk') {
                    $f.append(input('level', 'المستوى', item?.level));
                    $f.append(input('percentage', 'النسبة', item?.percentage));
                    // description may be stored as `label` on the server; prefer description but fall back to label
                    $f.append(input('description', 'الوصف', item?.description || item?.label));
                    // textual percentage range (e.g. 35-50%)
                    $f.append(input('percentage_text', 'نطاق النسبة', item?.percentage_text || item?.percentageText || ''));
                    $f.append(input('color', 'اللون', item?.color || '#22c55e'));
                } else if (type === 'cert') {
                    // Order: name, issueDate, validUntil, status, description, file
                    $f.append(input('name', 'الشهادة', item?.name));
                    $f.append(input('issueDate', 'تاريخ الإصدار', item?.issueDate));
                    $f.append(input('validUntil', 'تاريخ الانتهاء', item?.validUntil));
                    $f.append(input('status', 'الحالة', item?.status || 'ساري'));
                    $f.append(input('description', 'الوصف', item?.description));

                    // file upload for certificate (PDF) - styled block
                    var existingFile = item?.file_path || item?.file || item?.path || null;
                    var fileHtml = '<div class="md:col-span-2">' +
                        '<label class="block text-sm text-gray-300 mb-1">ملف الشهادة (PDF)</label>' +
                        '<div class="mt-1 flex items-center gap-3">' +
                            '<input type="file" accept=".pdf" name="certificate_file" id="certificate_file" class="text-sm text-white bg-gray-900/50 border border-gray-700 rounded-md p-2" />' +
                            (existingFile ? ('<button type="button" class="tc-existing-file text-sky-400 text-sm underline" data-path="' + existingFile + '">عرض</button>') : '') +
                        '</div>' +
                    '</div>';
                    $f.append(fileHtml);
                    var fileActionsHtml = '';
                    // Do not show file actions in modal header; use only the file input inside modal body
                    fileActionsHtml = '';
                } else if (type === 'audit') {
                    $f.append(input('title', 'العنوان', item?.title));
                    $f.append(input('date', 'التاريخ', item?.date));
                    $f.append(input('type', 'النوع', item?.type));
                    $f.append(input('status', 'الحالة', item?.status));

                    // File upload for audit report
                    var existingFile = item?.file_path || item?.file || item?.path || null;
                    var auditFileHtml = '<div class="md:col-span-2">' +
                        '<label class="block text-sm text-gray-300 mb-1">ملف التقرير (PDF أو صورة)</label>' +
                        '<div class="mt-1 flex items-center gap-3">' +
                            '<input type="file" accept=".pdf,image/*" name="audit_file" id="audit_file" class="text-sm text-white bg-gray-900/50 border border-gray-700 rounded-md p-2" />' +
                            (existingFile ? ('<button type="button" class="tc-existing-file text-sky-400 text-sm underline" data-path="' + existingFile + '">عرض</button>') : '') +
                        '</div>' +
                    '</div>';
                    $f.append(auditFileHtml);

                    // keep the file size field (read-only input) so UI shows size if present
                    $f.append(input('fileSize', 'حجم الملف', item?.file_size || item?.fileSize));
                }
                // inject header file actions if available
                try {
                    var fa = document.getElementById('tc-modal-file-actions');
                    if (fa) fa.innerHTML = fileActionsHtml || '';
                } catch(e) { console.warn('set tc modal file actions failed', e); }

                $('#tc-modal').removeClass('hidden').addClass('flex items-center justify-center');
            }

            // Helper: modal save/upload progress overlay
            function _tcEnsureOverlay(){
                if (document.getElementById('tc-save-overlay')) return;
                var d = document.createElement('div');
                d.id = 'tc-save-overlay';
                d.style.position = 'fixed';
                d.style.left = '0';
                d.style.top = '0';
                d.style.width = '100%';
                d.style.height = '100%';
                d.style.display = 'flex';
                d.style.alignItems = 'center';
                d.style.justifyContent = 'center';
                d.style.background = 'rgba(2,6,23,0.6)';
                d.style.zIndex = '9999';
                d.innerHTML = '<div id="tc-save-box" style="min-width:260px;max-width:90%;background:#0b1220;padding:18px;border-radius:8px;border:1px solid rgba(148,163,184,0.06);color:#fff;text-align:center;font-family:inherit">'<
                    + '<div id="tc-save-msg" style="font-size:14px;margin-bottom:8px">جاري الحفظ...</div>'
                    + '<div style="width:100%;height:10px;background:#0f1724;border-radius:6px;overflow:hidden;margin-top:6px">'
                        + '<div id="tc-save-bar" style="width:0%;height:100%;background:#0ea5e9;transition:width 200ms ease"></div>'
                    + '</div>'
                + '</div>';
                // Fix malformed concatenation above
                d.innerHTML = '<div id="tc-save-box" style="min-width:260px;max-width:90%;background:#0b1220;padding:18px;border-radius:8px;border:1px solid rgba(148,163,184,0.06);color:#fff;text-align:center;font-family:inherit">' +
                    '<div id="tc-save-msg" style="font-size:14px;margin-bottom:8px">جاري الحفظ...</div>' +
                    '<div style="width:100%;height:10px;background:#0f1724;border-radius:6px;overflow:hidden;margin-top:6px">' +
                        '<div id="tc-save-bar" style="width:0%;height:100%;background:#0ea5e9;transition:width 200ms ease"></div>' +
                    '</div>' +
                '</div>';
                document.body.appendChild(d);
            }

            function showTcProgress(percent, msg){
                try{
                    _tcEnsureOverlay();
                    var bar = document.getElementById('tc-save-bar');
                    var m = document.getElementById('tc-save-msg');
                    if (bar) bar.style.width = (percent || 0) + '%';
                    if (m && msg) m.innerText = msg;
                    document.getElementById('tc-save-overlay').style.display = 'flex';
                }catch(e){ console.warn(e); }
            }

            function hideTcProgress(){
                try{
                    var o = document.getElementById('tc-save-overlay');
                    if (o) o.style.display = 'none';
                }catch(e){ }
            }

            $(function() {
                // Trust modal bindings
                $('#tc-modal-close, #tc-modal-cancel').on('click', function() {
                    $('#tc-modal').addClass('hidden').removeClass('flex items-center justify-center');
                });
                $('#tc-modal-save').on('click', function() {
                    const type = __TC_EDIT.type;
                    const $f = $('#tc-form');
                    const raw = gatherForm($f);
                    const tc = window.__PAGES.trust_compliance;
                    const map = {
                        reg: { key: 'regulations', url: '/api/admin/tc/regulations' },
                        sec: { key: 'security_features', url: '/api/admin/tc/security-features' },
                        risk: { key: 'risk_levels', url: '/api/admin/tc/risk-levels' },
                        cert: { key: 'certifications', url: '/api/admin/tc/certificates' },
                        audit: { key: 'audit_reports', url: '/api/admin/tc/audit-reports' },
                        metric: { key: 'metrics', url: '/api/admin/tc/metrics' }
                    };
                    const meta = map[type] || map.reg;
                    const key = meta.key;

                    // Build API payload mapping UI fields to controller fields
                    function buildPayload(type, raw) {
                        if (type === 'reg') {
                            return {
                                title: raw.name || raw.title || '',
                                name_en: raw.nameEn || raw.name_en || null,
                                license: raw.license || null,
                                status: raw.status || null,
                                valid_until: raw.validUntil || raw.valid_until || null,
                                icon: raw.icon || null,
                                content: raw.content || raw.description || null
                            };
                        }
                        if (type === 'sec') {
                            return { title: raw.title || '', description: raw.description || null, level: raw.level || null, icon: raw.icon || null };
                        }
                        if (type === 'risk') {
                            return { level: raw.level || null, label: raw.description || raw.label || raw.level || '', percentage: raw.percentage || null, color: raw.color || null, percentage_text: raw.percentage_text || raw.percentageText || null };
                        }
                        if (type === 'cert') {
                            return { name: raw.name || '', description: raw.description || null, issuer: raw.issuer || null, issued_at: raw.issueDate || raw.issued_at || null, expires_at: raw.validUntil || raw.expires_at || null, status: raw.status || null };
                        }
                        if (type === 'audit') {
                            return { title: raw.title || '', summary: raw.summary || raw.description || null, date: raw.date || raw.audit_date || null, audit_date: raw.date || raw.audit_date || null, type: raw.type || null, status: raw.status || null, file_size: raw.fileSize || raw.file_size || null };
                        }
                        if (type === 'metric') {
                            let metaVal = raw.meta || null;
                            try { if (metaVal && typeof metaVal === 'string') metaVal = JSON.parse(metaVal); } catch(e) { /* keep as string */ }
                            return { key: raw.key || null, label: raw.label || '', value: raw.value || null, icon: raw.icon || null, meta: metaVal };
                        }
                        return raw;
                    }

                    let payload = buildPayload(type, raw);

                    // Normalize payload: convert empty strings to null (helps nullable|date validation)
                    function normalize(o) {
                        if (!o || typeof o !== 'object') return o;
                        Object.keys(o).forEach(function(k){
                            if (o[k] === '') o[k] = null;
                            else if (o[k] && typeof o[k] === 'object') normalize(o[k]);
                        });
                        return o;
                    }
                    payload = normalize(payload);

                    // Convert percentage input to a numeric value:
                    // - strip percent sign, trim whitespace
                    // - convert Arabic-Indic digits to Latin digits
                    // - replace comma with dot
                    // - parseFloat and set numeric value or null
                    (function normalizePercentage(){
                        try {
                            if (payload && payload.hasOwnProperty('percentage') && payload.percentage !== null && payload.percentage !== undefined) {
                                var p = payload.percentage;
                                if (typeof p !== 'string') p = String(p);
                                p = p.replace(/%/g, '').trim();
                                // map Arabic-Indic numerals to Latin
                                p = p.replace(/[٠-٩]/g, function(d){ return String('٠١٢٣٤٥٦٧٨٩').indexOf(d); });
                                p = p.replace(/،/g, '.'); // Arabic decimal comma
                                p = p.replace(/,/g, '.');
                                // remove non-numeric except dot and minus
                                p = p.replace(/[^0-9.\-]/g, '');
                                if (p === '') { payload.percentage = null; }
                                else {
                                    var n = parseFloat(p);
                                    if (!isNaN(n)) payload.percentage = n;
                                }
                            }
                        } catch(e){ console.warn('percentage normalize failed', e); }
                    })();

                    // Basic client-side validation to avoid server 422s
                    function validatePayload(type, payload) {
                        if (!payload || typeof payload !== 'object') return { ok: true };
                        if (type === 'reg') {
                            if (!payload.title || String(payload.title).trim() === '') return { ok: false, msg: 'حقل الاسم/التسمية مطلوب.' };
                        }
                        if (type === 'sec') {
                            if (!payload.title || String(payload.title).trim() === '') return { ok: false, msg: 'حقل العنوان مطلوب.' };
                        }
                        if (type === 'risk') {
                            if (!payload.label || String(payload.label).trim() === '') return { ok: false, msg: 'حقل التسمية لمستوى المخاطر مطلوب.' };
                        }
                        if (type === 'cert') {
                            if (!payload.name || String(payload.name).trim() === '') return { ok: false, msg: 'حقل اسم الشهادة مطلوب.' };
                        }
                        if (type === 'audit') {
                            if (!payload.title || String(payload.title).trim() === '') return { ok: false, msg: 'حقل العنوان مطلوب لتقرير المراجعة.' };
                        }
                        if (type === 'metric') {
                            if (!payload.label || String(payload.label).trim() === '') return { ok: false, msg: 'حقل التسمية مطلوب للمؤشر.' };
                        }
                        return { ok: true };
                    }

                    const validation = validatePayload(type, payload);
                    if (!validation.ok) { alert(validation.msg); return; }

                    // Determine if we should call API (prefer DB-backed endpoints)
                    const currentIndex = __TC_EDIT.index;
                    const currentItem = (tc[key] && tc[key][currentIndex]) || null;
                    const id = (__TC_EDIT.mode === 'edit' && currentItem && currentItem.id) ? currentItem.id : null;

                    // Helper to update local state after API success
                    function upsertLocal(returned) {
                        try {
                            if (__TC_EDIT.mode === 'edit' && currentIndex >= 0) {
                                // merge returned fields if present
                                tc[key][currentIndex] = Object.assign({}, tc[key][currentIndex] || {}, (returned && typeof returned === 'object') ? returned : payload);
                            } else {
                                const toPush = (returned && typeof returned === 'object') ? returned : Object.assign({}, payload);
                                if (!toPush.id && (returned && returned.id)) toPush.id = returned.id;
                                // ensure array exists
                                tc[key] = tc[key] || [];
                                tc[key].push(toPush);
                            }
                        } catch (e) { console.warn('upsertLocal error', e); }
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    // perform actual save (create/update) after optional file upload
                    function performSave(finalPayload) {
                        // show saving overlay
                        showTcProgress(70, 'جارٍ حفظ البيانات...');
                        if (meta && meta.url && type !== 'metric') {
                            if (id) {
                                // update
                                $.ajax({ url: meta.url + '/' + encodeURIComponent(id), method: 'PUT', contentType: 'application/json', data: JSON.stringify(finalPayload), headers: { 'X-CSRF-TOKEN': token } })
                                    .done(function(resp){ upsertLocal(resp); renderTrustComplianceAdmin(); try{ showAdminToast('success','تم الحفظ'); }catch(e){} })
                                    .fail(function(jqXHR){
                                        var msg = 'فشل حفظ البيانات';
                                        try {
                                            if (jqXHR && jqXHR.status === 422 && jqXHR.responseJSON) {
                                                var r = jqXHR.responseJSON;
                                                if (r.errors) {
                                                    msg = Object.values(r.errors).flat().join('; ');
                                                } else if (r.message) {
                                                    msg = r.message;
                                                }
                                            } else if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                                msg = jqXHR.responseJSON.message;
                                            }
                                        } catch (e) {}
                                        alert(msg);
                                    }).always(function(){ hideTcProgress(); try{ $('#tc-modal').addClass('hidden').removeClass('flex items-center justify-center'); }catch(e){} });
                            } else {
                                // create
                                $.ajax({ url: meta.url, method: 'POST', contentType: 'application/json', data: JSON.stringify(finalPayload), headers: { 'X-CSRF-TOKEN': token } })
                                    .done(function(resp){ upsertLocal(resp); renderTrustComplianceAdmin(); try{ showAdminToast('success','تم الإنشاء'); }catch(e){} })
                                    .fail(function(jqXHR){
                                        var msg = 'فشل إنشاء السجل';
                                        try {
                                            if (jqXHR && jqXHR.status === 422 && jqXHR.responseJSON) {
                                                var r = jqXHR.responseJSON;
                                                if (r.errors) msg = Object.values(r.errors).flat().join('; ');
                                                else if (r.message) msg = r.message;
                                            } else if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
                                        } catch (e) {}
                                        alert(msg);
                                    }).always(function(){ hideTcProgress(); try{ $('#tc-modal').addClass('hidden').removeClass('flex items-center justify-center'); }catch(e){} });
                            }
                        } else if (type === 'metric' && meta && meta.url) {
                            // metrics: use metrics endpoints we added
                            if (id) {
                                $.ajax({ url: meta.url + '/' + encodeURIComponent(id), method: 'PUT', contentType: 'application/json', data: JSON.stringify(finalPayload), headers: { 'X-CSRF-TOKEN': token } })
                                    .done(function(resp){ upsertLocal(resp); renderTrustComplianceAdmin(); try{ showAdminToast('success','تم الحفظ'); }catch(e){} })
                                    .fail(function(jqXHR){
                                        var msg = 'فشل حفظ المؤشر';
                                        try {
                                            if (jqXHR && jqXHR.status === 422 && jqXHR.responseJSON) {
                                                var r = jqXHR.responseJSON;
                                                if (r.errors) msg = Object.values(r.errors).flat().join('; ');
                                                else if (r.message) msg = r.message;
                                            } else if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
                                        } catch (e) {}
                                        alert(msg);
                                    }).always(function(){ hideTcProgress(); try{ $('#tc-modal').addClass('hidden').removeClass('flex items-center justify-center'); }catch(e){} });
                            } else {
                                $.ajax({ url: meta.url, method: 'POST', contentType: 'application/json', data: JSON.stringify(finalPayload), headers: { 'X-CSRF-TOKEN': token } })
                                    .done(function(resp){ upsertLocal(resp); renderTrustComplianceAdmin(); try{ showAdminToast('success','تم الإنشاء'); }catch(e){} })
                                    .fail(function(jqXHR){
                                        var msg = 'فشل إنشاء المؤشر';
                                        try {
                                            if (jqXHR && jqXHR.status === 422 && jqXHR.responseJSON) {
                                                var r = jqXHR.responseJSON;
                                                if (r.errors) msg = Object.values(r.errors).flat().join('; ');
                                                else if (r.message) msg = r.message;
                                            } else if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
                                        } catch (e) {}
                                        alert(msg);
                                    }).always(function(){ hideTcProgress(); try{ $('#tc-modal').addClass('hidden').removeClass('flex items-center justify-center'); }catch(e){} });
                            }
                        } else {
                            // fallback to legacy settings-based storage
                            if (__TC_EDIT.mode === 'edit') {
                                tc[key][__TC_EDIT.index] = raw;
                            } else {
                                (tc[key] = tc[key] || []).push(raw);
                            }
                            try { renderTrustComplianceAdmin(); } catch (err) { console.warn('render TC after save failed', err); }
                            savePagesSection('trust_compliance.' + key, tc[key]);
                        }

                        // modal hide is handled in AJAX .always callbacks; ensure overlay hidden
                        hideTcProgress();
                    }

                    // If certificate, regulation or audit type and a file was selected, upload it first then perform save
                    if (type === 'cert' || type === 'reg' || type === 'audit') {
                        var fileEl = document.getElementById('certificate_file') || document.getElementById('audit_file') || document.getElementById('tc_modal_file_input');
                        if (fileEl && fileEl.files && fileEl.files.length) {
                            var f = fileEl.files[0];
                            var fd = new FormData();
                            fd.append('file', f);
                            // upload via DocumentsController endpoint which returns { ok, id, url, path }
                            // show progress overlay and upload with progress handler
                            showTcProgress(5, 'جارٍ رفع الملف...');
                            $.ajax({
                                url: '/documents/upload',
                                method: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false,
                                headers: { 'X-CSRF-TOKEN': token },
                                xhr: function() {
                                    var xhr = new window.XMLHttpRequest();
                                    xhr.upload.addEventListener('progress', function(evt){
                                        if (evt.lengthComputable) {
                                            var p = Math.round((evt.loaded/evt.total) * 50); // map to 0-50%
                                            showTcProgress(p, 'جارٍ رفع الملف (' + p + '%)');
                                        }
                                    }, false);
                                    return xhr;
                                }
                            })
                                .done(function(res){
                                    if (res && (res.path || res.url)) {
                                        payload.file_path = res.path || res.url;
                                        // attach extra metadata when provided by upload endpoint
                                        if (res.filename) payload.file_name = res.filename;
                                        if (res.mime_type) payload.mime_type = res.mime_type;
                                        if (res.size) payload.file_size = res.size;
                                    }
                                    // fallback: if upload response didn't include size, use client file size
                                    if (!payload.file_size && f && f.size) payload.file_size = f.size;
                                    // advance progress to indicate upload done
                                    showTcProgress(60, 'جارٍ حفظ البيانات...');
                                    performSave(payload);
                                })
                                .fail(function(jq){
                                    var msg = 'فشل رفع الملف';
                                    try { if (jq && jq.responseJSON && jq.responseJSON.message) msg = jq.responseJSON.message; } catch(e){}
                                    hideTcProgress();
                                    alert(msg);
                                });
                            return; // wait for upload
                        }
                    }

                    // no upload needed -> perform save immediately
                    performSave(payload);
                });
                $('#tc-add-reg').on('click', function() {
                    __TC_EDIT.index = -1;
                    openTCModal('reg');
                });
                $('#tc-add-sec').on('click', function() {
                    __TC_EDIT.index = -1;
                    openTCModal('sec');
                });
                $('#tc-add-risk').on('click', function() {
                    __TC_EDIT.index = -1;
                    openTCModal('risk');
                });
                $('#tc-add-cert').on('click', function() {
                    __TC_EDIT.index = -1;
                    openTCModal('cert');
                });
                $('#tc-add-audit').on('click', function() {
                    __TC_EDIT.index = -1;
                    openTCModal('audit');
                });
                // metrics add button removed
                // Panel inline add buttons (header buttons inside each panel)
                $(document).on('click', '.tc-panel-add', function(e){
                    e.preventDefault();
                    __TC_EDIT.index = -1;
                    const t = $(this).data('type');
                    openTCModal(t || 'reg');
                });
                // Mobile TC menu
                $('#tc-menu-toggle').on('click', function() {
                    $('#tc-menu').toggleClass('hidden');
                });
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#tc-menu, #tc-menu-toggle').length) {
                        $('#tc-menu').addClass('hidden');
                    }
                });
                $('#tc-menu').on('click', '.tc-menu-item', function() {
                    const type = $(this).data('type');
                    __TC_EDIT.index = -1;
                    openTCModal(type);
                    $('#tc-menu').addClass('hidden');
                });
                $('#tc-reg-list, #tc-sec-list, #tc-risk-list, #tc-cert-list, #tc-audit-list').on('click', '.tc-edit',
                    function() {
                        const type = $(this).data('type');
                        const idx = parseInt($(this).data('idx'), 10);
                        __TC_EDIT.index = idx;
                        __TC_EDIT.mode = 'edit';
                        const tc = window.__PAGES.trust_compliance;
                        const map = {
                            reg: 'regulations',
                            sec: 'security_features',
                            risk: 'risk_levels',
                            cert: 'certifications',
                            audit: 'audit_reports'
                        };
                        const item = tc[map[type]][idx];
                        openTCModal(type, item);
                    }).on('click', '.tc-del', function() {
                    const type = $(this).data('type');
                    const idx = parseInt($(this).data('idx'), 10);
                    if (!confirm('حذف هذا العنصر؟')) return;
                    const tc = window.__PAGES.trust_compliance;
                    const map = {
                        reg: 'regulations',
                        sec: 'security_features',
                        risk: 'risk_levels',
                        cert: 'certifications',
                        audit: 'audit_reports',
                        metric: 'metrics'
                    };
                    const key = map[type];
                    // If metrics, call API to delete the DB row; otherwise use legacy savePagesSection
                    if (type === 'metric') {
                        const current = tc.metrics[idx] || {};
                        const id = current.id;
                        if (!id) {
                            // fallback to array removal
                            tc.metrics.splice(idx, 1);
                            savePagesSection('trust_compliance.metrics', tc.metrics);
                            return;
                        }
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        $.ajax({ url: '/api/admin/tc/metrics/' + encodeURIComponent(id), method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
                            .done(function(){
                                tc.metrics.splice(idx, 1);
                                renderTrustComplianceAdmin();
                                try{ showAdminToast('success', 'تم الحذف'); }catch(e){}
                            }).fail(function(){ alert('فشل حذف المؤشر'); });
                        return;
                    }

                    // If item has DB id, call the DB-backed delete endpoint for this type
                    const current = (tc[key] && tc[key][idx]) || {};
                    const currentId = current.id || null;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const deleteUrls = {
                        reg: '/api/admin/tc/regulations',
                        sec: '/api/admin/tc/security-features',
                        risk: '/api/admin/tc/risk-levels',
                        cert: '/api/admin/tc/certificates',
                        audit: '/api/admin/tc/audit-reports',
                        metric: '/api/admin/tc/metrics'
                    };
                    if (currentId && deleteUrls[type]) {
                        $.ajax({ url: deleteUrls[type] + '/' + encodeURIComponent(currentId), method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
                            .done(function(){
                                tc[key].splice(idx, 1);
                                renderTrustComplianceAdmin();
                                try{ showAdminToast('success','تم الحذف'); }catch(e){}
                            }).fail(function(){ alert('فشل حذف العنصر'); });
                        return;
                    }

                    // fallback to settings-based removal
                    tc[key].splice(idx, 1);
                    savePagesSection('trust_compliance.' + key, tc[key]);
                });

                // View certificate file (delegated)
                $(document).on('click', '.tc-view', function(e){
                    e.preventDefault();
                    var p = $(this).attr('data-path') || $(this).data('path');
                    if (!p) return;
                    var url = p.match(/^https?:\/\//i) ? p : ('/storage/' + String(p).replace(/^\/+/, ''));
                    window.open(url, '_blank');
                });

                // Open existing file link inside modal
                $(document).on('click', '.tc-existing-file', function(e){
                    e.preventDefault();
                    var p = $(this).attr('data-path') || $(this).data('path');
                    if (!p) return;
                    var url = p.match(/^https?:\/\//i) ? p : ('/storage/' + String(p).replace(/^\/+/, ''));
                    window.open(url, '_blank');
                });
            });

            // Kick initial fetch for pages after scripts load
            $(function() {
                fetchPagesSettings();
            });
            // ===== Admin Live Search (additive, non-destructive) =====
            $(function(){
                try {
                    if (!window.jQuery) return;
                    const LIVE_TABS = [
                        {tabId: 'tab-users', fetchFn: fetchUsers, placeholder: 'ابحث بالمستخدم (اسم، بريد، معرّف) ...'},
                        {tabId: 'tab-investors', fetchFn: fetchInvestors, placeholder: 'ابحث بالمستثمر (اسم، هاتف، معرّف) ...'},
                        {tabId: 'tab-packages', fetchFn: fetchPackages, placeholder: 'ابحث بحزم الاستثمار (اسم، مبلغ، معرّف) ...'},
                        {tabId: 'tab-investments', fetchFn: fetchInvestments, placeholder: 'ابحث بطلبات الاستثمار (معرّف، حالة، مبلغ) ...'},
                        {tabId: 'tab-licenses', fetchFn: fetchLicenses, placeholder: 'ابحث بطلبات التراخيص (اسم، نشاط، معرّف) ...'},
                        {tabId: 'tab-documents', fetchFn: fetchDocuments, placeholder: 'ابحث بالمستندات (اسم الملف، المستخدم) ...'},
                        {tabId: 'tab-page-market', fetchFn: null, placeholder: 'ابحث برؤى السوق (عنوان، خبر، قطاع) ...'},
                        {tabId: 'tab-page-success', fetchFn: null, placeholder: 'ابحث بقصص النجاح (اسم، وصف) ...'},
                        {tabId: 'tab-page-trust', fetchFn: null, placeholder: 'ابحث بمركز الثقة (عنوان، تقرير) ...'},
                        {tabId: 'tab-bank', fetchFn: loadBankAccounts, placeholder: 'ابحث بإعدادات البنك (اسم الحساب، رقم) ...'},
                    ];

                    function debounce(fn, wait){ let t; return function(){ const args = arguments; clearTimeout(t); t = setTimeout(()=>fn.apply(this, args), wait); }; }

                    function insertSearchBox(spec){
                        const $tab = $('#' + spec.tabId);
                        if (!$tab || !$tab.length) return;
                        // avoid duplicate
                        if ($tab.find('.admin-live-search-wrapper').length) return;
                        const $box = $('<div class="admin-live-search-wrapper mb-3"><input type="search" class="admin-live-search-input w-full bg-gray-900/50 border border-gray-700 rounded-md p-2 text-white" placeholder="'+spec.placeholder+'"/></div>');
                        $tab.prepend($box);
                        const $inp = $box.find('input');
                        const handler = debounce(function(){ const q = $inp.val().trim(); if(!q){ // reset to default
                                // call fetch without query to restore original data
                                if (typeof spec.fetchFn === 'function') { spec.fetchFn(); }
                                else if (spec.tabId === 'tab-page-market' || spec.tabId === 'tab-page-success' || spec.tabId === 'tab-page-trust') { fetchPagesSettings(); }
                                return; }
                            if (typeof spec.fetchFn === 'function') { spec.fetchFn(q); }
                            else {
                                // client-side filter for pages stored in window.__PAGES
                                try {
                                    const s = q.toLowerCase();
                                    if (spec.tabId === 'tab-page-market') {
                                        // filter market insights arrays
                                        const backup = JSON.parse(JSON.stringify(window.__PAGES || {}));
                                        const mi = backup.market_insights || {};
                                        mi.indicators = (mi.indicators || []).filter(x => JSON.stringify(x).toLowerCase().includes(s));
                                        mi.news = (mi.news || []).filter(x => JSON.stringify(x).toLowerCase().includes(s));
                                        window.__PAGES.market_insights = mi;
                                        renderMarketInsightsAdmin();
                                        window.__PAGES = window.__PAGES || backup; // keep reference
                                    } else if (spec.tabId === 'tab-page-success') {
                                        const backup = JSON.parse(JSON.stringify(window.__PAGES || {}));
                                        const ss = backup.success_stories || {};
                                        ss.top_featured = (ss.top_featured || []).filter(x => JSON.stringify(x).toLowerCase().includes(s));
                                        ss.stories = (ss.stories || []).filter(x => JSON.stringify(x).toLowerCase().includes(s));
                                        window.__PAGES.success_stories = ss;
                                        renderSuccessStoriesAdmin();
                                    } else if (spec.tabId === 'tab-page-trust') {
                                        const backup = JSON.parse(JSON.stringify(window.__PAGES || {}));
                                        const tc = backup.trust_compliance || {};
                                        Object.keys(tc).forEach(k => { tc[k] = (tc[k]||[]).filter(x => JSON.stringify(x).toLowerCase().includes(s)); });
                                        window.__PAGES.trust_compliance = tc;
                                        renderTrustComplianceAdmin();
                                    }
                                } catch(e){ console.warn('pages-filter', e); }
                            }
                        }, 350);
                        $inp.on('input', handler);
                    }

                    LIVE_TABS.forEach(insertSearchBox);
                } catch (e) { console.warn('Admin live search init failed', e); }
            });

            // AJAX-based server partial search for Investors (explicit keyup handler)
            $(function(){
                try {
                    var timer = null;
                    $('#investor-search').on('input', function(){
                        var q = $(this).val() || '';
                        clearTimeout(timer);
                        timer = setTimeout(function(){
                            var url = '{{ route('admin.search.investors') }}';
                            $.get(url, { query: q }).done(function(html){
                                // update the results partial
                                $('#investor-results').html(html);
                            }).fail(function(xhr){
                                if (xhr && xhr.status === 401) {
                                    $('#investor-results').html('<div class="text-gray-400">غير مصرح. سجّل الدخول لعرض النتائج.</div>');
                                } else {
                                    // keep previous content
                                }
                            });
                        }, 250);
                    });
                } catch(e){ console.warn('investor search wiring', e); }
            });

                            // Investor modal: click handler to load investor details (JSON) and populate modal
                            $(function(){
                                try {
                                    // base API url (no trailing slash)
                                    var apiInvestorsBase = '{{ url('/api/admin/investors') }}';

                                    function showInvModal() {
                                        $('#inv-modal').removeClass('hidden').addClass('flex');
                                    }
                                    function hideInvModal() {
                                        $('#inv-modal').removeClass('flex').addClass('hidden');
                                    }

                                    // delegated click so it works for dynamically loaded partials
                                    $(document).on('click', '.investor-view-link', function(e){
                                        e.preventDefault();
                                        var id = $(this).data('id') || null;
                                        if (!id) { return; }

                                        // show modal immediately with loading state
                                        $('#inv-name').text('جارٍ التحميل...');
                                        $('#inv-email').text('');
                                        $('#inv-phone').text('');
                                        $('#inv-stats-total').text('0');
                                        $('#inv-stats-active').text('0');
                                        $('#inv-stats-completed').text('0');
                                        $('#inv-stats-amount').text('0');
                                        $('#inv-investments').html('<div class="text-gray-400 p-3">جارٍ التحميل...</div>');
                                        showInvModal();

                                        var url = apiInvestorsBase + '/' + encodeURIComponent(id);
                                        $.getJSON(url).done(function(resp){
                                            var inv = resp.investor || null;
                                            var investments = resp.investments || [];
                                            if (!inv) {
                                                $('#inv-name').text('غير موجود');
                                                $('#inv-investments').html('<div class="text-gray-400 p-3">لا توجد بيانات.</div>');
                                                return;
                                            }

                                            // basic info
                                            var name = inv.full_name || inv.name || '—';
                                            $('#inv-name').text(name);
                                            $('#inv-email').text(inv.email || '—');
                                            $('#inv-phone').text(inv.phone || '—');

                                            // avatar initials
                                            try {
                                                var initials = 'IN';
                                                if (name && name.trim().length) {
                                                    var parts = name.trim().split(/\s+/);
                                                    initials = parts.slice(0,2).map(function(p){ return (p[0]||'').toUpperCase(); }).join('') || 'IN';
                                                }
                                                $('#inv-avatar').text(initials);
                                            } catch(e){}

                                            // investments summary
                                            var total = investments.length || 0;
                                            var completed = 0;
                                            var active = 0;
                                            var amountSum = 0;
                                            investments.forEach(function(it){
                                                var st = (it.status || '').toString().toLowerCase();
                                                if (st === 'completed' || st === 'completed') completed++;
                                                else active++;
                                                var a = parseFloat(it.amount) || 0;
                                                amountSum += a;
                                            });
                                            $('#inv-stats-total').text(total);
                                            $('#inv-stats-active').text(active);
                                            $('#inv-stats-completed').text(completed);
                                            try { $('#inv-stats-amount').text((amountSum).toLocaleString()); } catch(e){ $('#inv-stats-amount').text(amountSum); }

                                                // helper: map status codes to Arabic labels and color classes
                                                function renderStatusBadge(status){
                                                    if(!status) return '<span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-white">—</span>';
                                                    var s = (''+status).toLowerCase();
                                                    var map = {
                                                        'pending': {label: 'معلق', cls: 'bg-yellow-600'},
                                                        'active': {label: 'نشط', cls: 'bg-cyan-600'},
                                                        'in_progress': {label: 'قيد التنفيذ', cls: 'bg-cyan-600'},
                                                        'completed': {label: 'مكتمل', cls: 'bg-green-600'},
                                                        'paid': {label: 'مدفوع', cls: 'bg-indigo-600'},
                                                        'approved': {label: 'موافق', cls: 'bg-green-500'},
                                                        'rejected': {label: 'مرفوض', cls: 'bg-red-600'},
                                                        'cancelled': {label: 'ملغى', cls: 'bg-gray-600'},
                                                        'failed': {label: 'فشل', cls: 'bg-red-600'}
                                                    };
                                                    var entry = map[s] || null;
                                                    if (!entry) {
                                                        // try some common aliases
                                                        if (s.indexOf('pend') !== -1) entry = map['pending'];
                                                        else if (s.indexOf('comp') !== -1) entry = map['completed'];
                                                        else if (s.indexOf('fail') !== -1) entry = map['failed'];
                                                        else entry = {label: status, cls: 'bg-gray-700'};
                                                    }
                                                    return '<span class="px-2 py-1 text-xs rounded-full ' + entry.cls + ' text-white">' + entry.label + '</span>';
                                                }

                                                // investments list
                                                if (investments.length === 0) {
                                                    $('#inv-investments').html('<div class="text-gray-400 p-3">لا توجد استثمارات لعرضها.</div>');
                                                } else {
                                                    var html = '';
                                                    investments.forEach(function(it){
                                                        var created = it.created_at || it.created || '';
                                                        var amount = (parseFloat(it.amount)||0).toLocaleString ? (parseFloat(it.amount)||0).toLocaleString() : (parseFloat(it.amount)||0);
                                                        var statusHtml = renderStatusBadge(it.status || it.state || '');
                                                        html += '<div class="bg-gray-900/30 border border-gray-800 rounded p-2">';
                                                        html += '<div class="flex items-center justify-between gap-3">';
                                                        html += '<div class="text-sm text-gray-300 font-mono">#' + (it.id||'—') + '</div>';
                                                        html += '<div>' + statusHtml + '</div>';
                                                        html += '<div class="text-sm text-white font-semibold">' + amount + '</div>';
                                                        html += '</div>';
                                                        html += '<div class="text-xs text-gray-400 mt-1">' + (created ? created : '') + '</div>';
                                                        html += '</div>';
                                                    });
                                                    $('#inv-investments').html(html);
                                                }
                                        }).fail(function(xhr){
                                            if (xhr && xhr.status === 401) {
                                                $('#inv-name').text('غير مصرح');
                                                $('#inv-investments').html('<div class="text-gray-400 p-3">غير مصرح. سجّل الدخول.</div>');
                                            } else {
                                                $('#inv-name').text('حدث خطأ');
                                                $('#inv-investments').html('<div class="text-gray-400 p-3">حدث خطأ أثناء جلب البيانات.</div>');
                                            }
                                        });
                                    });

                                    // modal close handlers
                                    $(document).on('click', '#inv-modal-close, #inv-modal-cancel', function(e){
                                        e.preventDefault();
                                        hideInvModal();
                                    });
                                } catch(e){ console.warn('investor modal wiring', e); }
                            });
        </script>
        <script>
            // Listen to reject events and call backend to persist reminder
            (function(){
                try {
                    document.addEventListener('admin:investment:reject', function(ev){
                        try {
                            var d = ev && ev.detail ? ev.detail : {};
                            var id = (d.id || '').toString().trim();
                            var reason = (d.reason || '').toString();
                            if(!id) return;
                            var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));
                            var params = 'reason=' + encodeURIComponent(reason) + '&_token=' + encodeURIComponent(token || '');
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '/api/admin/investments/' + encodeURIComponent(id) + '/reject', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                            try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(_){ }
                            function updateUIAfterReject(invId){
                                try {
                                    // Update modal status label to rejected (red)
                                    try {
                                        var $st = $('#invdet-status');
                                        if($st && $st.length){
                                            $st.text('مرفوض');
                                            $st.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300').addClass('text-red-400');
                                        }
                                    } catch(_){ }

                                    // Disable/hide reject button in modal
                                    try { $('#investment-modal-reject').prop('disabled', true).addClass('opacity-50 cursor-not-allowed'); } catch(_){ }

                                    // Update list card badge immediately
                                    try {
                                        var $card = $('[data-inv-id="' + invId + '"]').first();
                                        if($card && $card.length){
                                            // Find the status badge span inside the right controls area
                                            var $badge = $card.find('span.inline-flex.rounded-md').first();
                                            if(!$badge || !$badge.length){ $badge = $card.find('span.rounded-md').first(); }
                                            if($badge && $badge.length){
                                                // Reset class to rejected styling and set text
                                                $badge.attr('class', 'inline-flex items-center gap-1 rounded-md bg-red-500 text-white text-xs font-semibold px-2.5 py-1.5');
                                                $badge.text('مرفوض');
                                            }
                                        }
                                    } catch(_){ }
                                } catch(_){ }
                            }
                            xhr.onload = function(){
                                if(xhr.status >= 200 && xhr.status < 300){
                                    // Optimistic UI update without full refresh
                                    updateUIAfterReject(id);
                                    try { if (typeof showToast === 'function') showToast('success', 'تم تسجيل الرفض'); else alert('تم تسجيل الرفض'); } catch(_){ }
                                    // Soft refresh in background to keep data in sync (non-blocking)
                                    try { if (typeof fetchInvestments === 'function') fetchInvestments(); } catch(_){ }
                                } else {
                                    try { if (typeof showToast === 'function') showToast('error', 'فشل تسجيل الرفض'); else alert('فشل تسجيل الرفض'); } catch(_){ }
                                }
                            };
                            xhr.onerror = function(){ try { if (typeof showToast === 'function') showToast('error', 'تعذر الاتصال بالخادم'); else alert('تعذر الاتصال بالخادم'); } catch(_){ } };
                            xhr.send(params);
                        } catch(e) { console.warn('reject listener error', e); }
                    });
                } catch(e){ console.warn('reject listener attach error', e); }
            })();
        </script>
        <script>
            // Listen to approve events and call backend to update status=approved
            (function(){
                try {
                    document.addEventListener('admin:investment:approve', function(ev){
                        try {
                            var d = ev && ev.detail ? ev.detail : {};
                            var id = (d.id || '').toString().trim();
                            if(!id) return;
                            var token = (window.__CSRF_TOKEN || window._token || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''));
                            var payload = JSON.stringify({ status: 'approved' });
                            var xhr = new XMLHttpRequest();
                            xhr.open('PUT', '/api/admin/investments/' + encodeURIComponent(id) + '/status', true);
                            xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                            try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch(_){ }

                            function updateUIAfterApprove(invId){
                                try {
                                    // Update modal status label to approved (green)
                                    try {
                                        var $st = $('#invdet-status');
                                        if($st && $st.length){
                                            $st.text('موافق عليه');
                                            $st.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300').addClass('text-emerald-400');
                                        }
                                    } catch(_){ }

                                    // Disable approve button in modal
                                    try { $('#investment-modal-approve').prop('disabled', true).addClass('opacity-50 cursor-not-allowed'); } catch(_){ }

                                    // Update list card badge immediately
                                    try {
                                        var $card = $('[data-inv-id="' + invId + '"]').first();
                                        if($card && $card.length){
                                            var $badge = $card.find('span.inline-flex.rounded-md').first();
                                            if(!$badge || !$badge.length){ $badge = $card.find('span.rounded-md').first(); }
                                            if($badge && $badge.length){
                                                $badge.attr('class', 'inline-flex items-center gap-1 rounded-md bg-green-600 text-white text-xs font-semibold px-2.5 py-1.5');
                                                $badge.text('موافق عليه');
                                            }
                                        }
                                    } catch(_){ }
                                } catch(_){ }
                            }

                            xhr.onload = function(){
                                if(xhr.status >= 200 && xhr.status < 300){
                                    updateUIAfterApprove(id);
                                    try { if (typeof showToast === 'function') showToast('success', '✅ تم اعتماد الاستثمار'); else alert('✅ تم اعتماد الاستثمار'); } catch(_){ }
                                    try { if (typeof fetchInvestments === 'function') fetchInvestments(); } catch(_){ }

                                    // Immediate provisional UI update for next-payout & earnings status
                                    try{
                                        var parsed = null;
                                        try { parsed = JSON.parse(xhr.responseText); } catch(_){ parsed = null; }
                                        var invResp = parsed && parsed.investment ? parsed.investment : null;
                                        if(invResp && invResp.approved_at){
                                            var ap = new Date(invResp.approved_at);
                                            var next = new Date(ap.getTime());
                                            next.setMonth(next.getMonth() + 1);
                                            function pad(n){ return (n < 10 ? '0' + n : String(n)); }
                                            var nextStr = next.getFullYear() + '-' + pad(next.getMonth()+1) + '-' + pad(next.getDate()) + ' ' + pad(next.getHours()) + ':' + pad(next.getMinutes());
                                            $('#invdet-next-payout').text(nextStr);
                                            var $es = $('#invdet-earnings-status');
                                            $es.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300').text('جارٍ التحديث...').addClass('text-gray-300');
                                        }
                                    }catch(_e){}

                                    // Refresh full investment details shortly after so that authoritative diagnostics appear
                                    try { setTimeout(function(){ if(typeof openInvestmentDetail === 'function'){ openInvestmentDetail(id); } }, 700); } catch(_){ }
                                } else {
                                    try { if (typeof showToast === 'function') showToast('error', 'فشل اعتماد الاستثمار'); else alert('فشل اعتماد الاستثمار'); } catch(_){ }
                                }
                            };
                            xhr.onerror = function(){ try { if (typeof showToast === 'function') showToast('error', 'تعذر الاتصال بالخادم'); else alert('تعذر الاتصال بالخادم'); } catch(_){ } };
                            xhr.send(payload);
                        } catch(e) { console.warn('approve listener error', e); }
                    });
                } catch(e){ console.warn('approve listener attach error', e); }
            })();
        </script>
        <script>
            // Listen to set-active and set-completed events and persist status; update UI immediately
            (function(){
                try {
                    // Exact colors to match email notifications mapping
                    window.STATUS_COLORS = window.STATUS_COLORS || {
                        active:    { label: 'نشط',    hex: '#f97316' }, // orange
                        completed: { label: 'مكتمل', hex: '#2563eb' }  // blue
                    };
                    function applyStatusUI(invId, status){
                        try {
                            var key = (status || '').toString().trim().toLowerCase();
                            var label = (typeof statusLabel === 'function') ? statusLabel(key) : key;
                            // Use the unified Arabic label from mapping when available
                            if (STATUS_COLORS[key]) { label = STATUS_COLORS[key].label; }
                            var txtCls = (typeof statusTextClass === 'function') ? statusTextClass(status) : 'text-gray-300';
                            // Modal label
                            try {
                                var $st = $('#invdet-status');
                                if($st && $st.length){
                                    $st.text(label);
                                    // Apply exact hex for active/completed; fallback to class for others
                                    if (STATUS_COLORS[key]) {
                                        $st.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300 text-indigo-400 text-blue-400');
                                        $st.css('color', STATUS_COLORS[key].hex);
                                    } else {
                                        $st.css('color', '');
                                        $st.removeClass('text-emerald-400 text-red-400 text-yellow-300 text-gray-300 text-indigo-400 text-blue-400').addClass(txtCls);
                                    }
                                }
                            } catch(_){ }

                            // Toggle buttons disabled state and visual style to reflect new status (Active=orange filled, Completed=blue filled)
                            try {
                                var isActive = String(status||'').toLowerCase()==='active';
                                var isCompleted = String(status||'').toLowerCase()==='completed';
                                var $btnActive = $('#investment-modal-active');
                                var $btnComplete = $('#investment-modal-complete');
                                // Reset base classes for both buttons
                                var rmActive = 'bg-orange-600 text-black hover:bg-orange-500 border border-orange-600 text-orange-300 hover:bg-orange-600/20 opacity-50 cursor-not-allowed';
                                var rmComplete = 'bg-blue-600 text-black hover:bg-blue-500 border border-blue-600 text-blue-300 hover:bg-blue-600/20 opacity-50 cursor-not-allowed';
                                $btnActive.removeClass(rmActive);
                                $btnComplete.removeClass(rmComplete);
                                // Reset inline styles
                                try { $btnActive.css({ backgroundColor: '', color: '', borderColor: '' }); } catch(_){ }
                                try { $btnComplete.css({ backgroundColor: '', color: '', borderColor: '' }); } catch(_){ }

                                // Apply disabled state
                                $btnActive.prop('disabled', isActive).toggleClass('opacity-50 cursor-not-allowed', isActive);
                                $btnComplete.prop('disabled', isCompleted).toggleClass('opacity-50 cursor-not-allowed', isCompleted);

                                // Apply visual style
                                if(isActive){
                                    // Active selected: active button filled orange, completed outlined blue
                                    $btnActive.addClass('bg-orange-600 text-black hover:bg-orange-500');
                                    try { $btnActive.css({ backgroundColor: STATUS_COLORS.active.hex, color: '#000' }); } catch(_){ }
                                    $btnComplete.addClass('border border-blue-600 text-blue-300 hover:bg-blue-600/20');
                                    try { $btnComplete.css({ borderColor: STATUS_COLORS.completed.hex, color: STATUS_COLORS.completed.hex }); } catch(_){ }
                                } else if(isCompleted){
                                    // Completed selected: completed button filled blue, active outlined orange
                                    $btnComplete.addClass('bg-blue-600 text-black hover:bg-blue-500');
                                    try { $btnComplete.css({ backgroundColor: STATUS_COLORS.completed.hex, color: '#000' }); } catch(_){ }
                                    $btnActive.addClass('border border-orange-600 text-orange-300 hover:bg-orange-600/20');
                                    try { $btnActive.css({ borderColor: STATUS_COLORS.active.hex, color: STATUS_COLORS.active.hex }); } catch(_){ }
                                } else {
                                    // Default state: both outlined
                                    $btnActive.addClass('border border-orange-600 text-orange-300 hover:bg-orange-600/20');
                                    $btnComplete.addClass('border border-blue-600 text-blue-300 hover:bg-blue-600/20');
                                    try { $btnActive.css({ borderColor: STATUS_COLORS.active.hex, color: STATUS_COLORS.active.hex }); } catch(_){ }
                                    try { $btnComplete.css({ borderColor: STATUS_COLORS.completed.hex, color: STATUS_COLORS.completed.hex }); } catch(_){ }
                                }
                            } catch(_){ }

                            // List card badge
                            try {
                                var $card = $('[data-inv-id="' + invId + '"]').first();
                                if($card && $card.length){
                                    var $badge = $card.find('span.inline-flex.rounded-md').first();
                                    if(!$badge || !$badge.length){ $badge = $card.find('span.rounded-md').first(); }
                                    if($badge && $badge.length){
                                        if (STATUS_COLORS[key]) {
                                            // Use hex background directly
                                            $badge.attr('class', 'inline-flex items-center gap-1 rounded-md text-white text-xs font-semibold px-2.5 py-1.5');
                                            try { $badge.css({ backgroundColor: STATUS_COLORS[key].hex, color: '#fff' }); } catch(_){ }
                                        } else {
                                            var cls = (typeof toBadge === 'function' ? (toBadge(String(status).toLowerCase()).cls || 'bg-gray-500 text-white') : 'bg-gray-500 text-white');
                                            $badge.attr('class', 'inline-flex items-center gap-1 rounded-md ' + cls + ' text-xs font-semibold px-2.5 py-1.5');
                                            try { $badge.css({ backgroundColor: '', color: '' }); } catch(_){ }
                                        }
                                        $badge.text(label);
                                    }
                                }
                            } catch(_){ }

                            // Desktop table row inside #investment-results
                            try {
                                var $lnk = $('#investment-results a[data-action="view-investment"][data-id="'+invId+'"]');
                                if($lnk && $lnk.length){
                                    var $tr = $lnk.closest('tr');
                                    if($tr && $tr.length){
                                        var $tds = $tr.find('td');
                                        if($tds && $tds.length >= 3){ $($tds[2]).text(label); }
                                    }
                                    // Mobile card block right above the <tr>
                                    var $cardDiv = $lnk.closest('div.sm\\:hidden');
                                    if($cardDiv && $cardDiv.length){
                                        var $statusLine = $cardDiv.find('div.text-xs.text-gray-400:contains("الحالة")');
                                        if($statusLine && $statusLine.length){
                                            var $span = $statusLine.find('span.text-white').first();
                                            if($span && $span.length){ $span.text(label); }
                                        }
                                    }
                                }
                            } catch(_){ }
                        } catch(_){ }
                    }

                    function sendStatus(id, status){
                        // Resolve API base robustly (global or fallback)
                        var base = (window.API_BASE || window.api || '/api');
                        $.ajax({
                            url: base + '/admin/investments/' + encodeURIComponent(id) + '/status',
                            method: 'PUT',
                            data: { status: status, _token: '{{ csrf_token() }}' }
                        }).done(function(){
                            applyStatusUI(id, status);
                            try { if (typeof showToast === 'function') showToast('success', (status==='active'?'✅ تم تحويل الحالة إلى نشط':'✅ تم تعليم الاستثمار كمكتمل')); else alert('تم تحديث الحالة'); } catch(_){ }
                            // Broadcast a global event so any widgets can react (e.g., counters, lists)
                            try { document.dispatchEvent(new CustomEvent('admin:investment:status-updated', { detail: { id: id, status: status } })); } catch(_){ }
                            try { if (typeof fetchInvestments === 'function') fetchInvestments(); } catch(_){ }
                        }).fail(function(){
                            try { if (typeof showToast === 'function') showToast('error', 'فشل تحديث الحالة'); else alert('فشل تحديث الحالة'); } catch(_){ }
                        });
                    }

                    document.addEventListener('admin:investment:set-active', function(ev){
                        try { var d = ev && ev.detail ? ev.detail : {}; var id = (d.id||'').toString().trim(); if(!id) return; sendStatus(id, 'active'); } catch(_){ }
                    });
                    document.addEventListener('admin:investment:set-completed', function(ev){
                        try { var d = ev && ev.detail ? ev.detail : {}; var id = (d.id||'').toString().trim(); if(!id) return; sendStatus(id, 'completed'); } catch(_){ }
                    });
                } catch(e){ console.warn('set-active/completed attach error', e); }
            })();
        </script>
        <script>
            // Toast container and helper (ensure admin pages have the same green success toast)
            (function(){
                try {
                    if (!document.getElementById('app-toast')) {
                        var toastDiv = document.createElement('div');
                        toastDiv.id = 'app-toast';
                        toastDiv.style.position = 'fixed';
                        toastDiv.style.top = '20px';
                        toastDiv.style.left = '50%';
                        toastDiv.style.transform = 'translateX(-50%)';
                        toastDiv.style.zIndex = '10000';
                        toastDiv.style.display = 'flex';
                        toastDiv.style.flexDirection = 'column';
                        toastDiv.style.gap = '8px';
                        toastDiv.style.alignItems = 'center';
                        toastDiv.style.pointerEvents = 'none';
                        document.body.appendChild(toastDiv);
                    }

                    if (typeof window.showToast !== 'function') {
                        window.showToast = function(type, msg, timeout){
                            try {
                                timeout = parseInt(timeout || 4000, 10);
                                if (type === 'success') {
                                    // create centered modal with check icon and animated ring
                                    var overlay = document.createElement('div');
                                    overlay.setAttribute('role','status');
                                    overlay.style.position = 'fixed';
                                    overlay.style.inset = '0';
                                    overlay.style.display = 'flex';
                                    overlay.style.alignItems = 'center';
                                    overlay.style.justifyContent = 'center';
                                    overlay.style.zIndex = '11000';
                                    overlay.style.background = 'rgba(0,0,0,0.45)';

                                    var box = document.createElement('div');
                                    box.style.background = '#ffffff';
                                    box.style.color = '#111827';
                                    // Make the box taller rather than wider
                                    box.style.padding = '28px 22px';
                                    box.style.borderRadius = '12px';
                                    box.style.display = 'flex';
                                    box.style.flexDirection = 'column';
                                    box.style.alignItems = 'center';
                                    box.style.justifyContent = 'center';
                                    box.style.gap = '12px';
                                    box.style.minWidth = '260px';
                                    box.style.minHeight = '240px';
                                    box.style.maxWidth = '90%';

                                    // SVG ring + check
                                    var svgNS = 'http://www.w3.org/2000/svg';
                                    var svg = document.createElementNS(svgNS, 'svg');
                                    svg.setAttribute('width','96');
                                    svg.setAttribute('height','96');
                                    svg.setAttribute('viewBox','0 0 96 96');

                                    var ring = document.createElementNS(svgNS, 'circle');
                                    ring.setAttribute('cx','48'); ring.setAttribute('cy','48'); ring.setAttribute('r','36');
                                    ring.setAttribute('fill','none');
                                    ring.setAttribute('stroke','#065f46');
                                    ring.setAttribute('stroke-width','6');
                                    ring.setAttribute('stroke-linecap','round');
                                    ring.style.opacity = '0.18';

                                    var ringAnim = document.createElementNS(svgNS, 'circle');
                                    ringAnim.setAttribute('cx','48'); ringAnim.setAttribute('cy','48'); ringAnim.setAttribute('r','36');
                                    ringAnim.setAttribute('fill','none');
                                    ringAnim.setAttribute('stroke','#10b981');
                                    ringAnim.setAttribute('stroke-width','6');
                                    ringAnim.setAttribute('stroke-linecap','round');
                                    ringAnim.setAttribute('stroke-dasharray','226.1946710584651');
                                    ringAnim.setAttribute('stroke-dashoffset','226.1946710584651');

                                    var check = document.createElementNS(svgNS, 'path');
                                    check.setAttribute('d','M34 50 L44 60 L64 40');
                                    check.setAttribute('fill','none');
                                    check.setAttribute('stroke','#10b981');
                                    check.setAttribute('stroke-width','6');
                                    check.setAttribute('stroke-linecap','round');
                                    check.setAttribute('stroke-linejoin','round');
                                    check.style.opacity = '0';

                                    svg.appendChild(ring);
                                    svg.appendChild(ringAnim);
                                    svg.appendChild(check);

                                    box.appendChild(svg);

                                    var text = document.createElement('div');
                                    text.style.color = '#111827';
                                    text.style.fontSize = '15px';
                                    text.style.textAlign = 'center';
                                    text.textContent = msg || 'تمت العملية بنجاح';
                                    box.appendChild(text);

                                    overlay.appendChild(box);
                                    document.body.appendChild(overlay);

                                    // animate ring stroke
                                    setTimeout(function(){
                                        try {
                                            ringAnim.style.transition = 'stroke-dashoffset ' + (timeout) + 'ms linear';
                                            ringAnim.setAttribute('stroke-dashoffset', '0');
                                            // show check after small delay
                                            setTimeout(function(){ try{ check.style.transition = 'opacity 300ms ease'; check.style.opacity = '1'; }catch(_){} }, Math.max(200, timeout - 400));
                                        } catch(_){}
                                    }, 20);

                                    // remove after timeout + small fade
                                    setTimeout(function(){
                                        try { overlay.style.transition = 'opacity 300ms ease'; overlay.style.opacity = '0'; setTimeout(function(){ try{ overlay.remove(); }catch(_){} }, 300); } catch(_){}
                                    }, timeout + 300);
                                    return;
                                }

                                // fallback: small top toast for other types
                                timeout = timeout || 7000;
                                var bg = type === 'error' ? '#dc2626' : '#0ea5e9';
                                var t = document.createElement('div');
                                t.style.background = bg;
                                t.style.color = '#fff';
                                t.style.padding = '10px 14px';
                                t.style.borderRadius = '8px';
                                t.style.boxShadow = '0 6px 18px rgba(2,6,23,0.6)';
                                t.style.maxWidth = '90%';
                                t.style.pointerEvents = 'auto';
                                t.textContent = msg;
                                document.getElementById('app-toast').appendChild(t);
                                setTimeout(function(){ try { t.style.transition = 'opacity 0.3s'; t.style.opacity = '0'; setTimeout(function(){ try{ t.remove(); }catch(_){} }, 300); } catch(_){} }, timeout);
                            } catch(e) { try { alert(msg); } catch(_){} }
                        };
                    }
                } catch(e){ console.warn('showToast init error', e); }
            })();
        </script>
    @endpush

@endsection
