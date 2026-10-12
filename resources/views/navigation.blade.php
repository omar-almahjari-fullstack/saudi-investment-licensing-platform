<nav class="hidden lg:block fixed top-0 left-0 w-80 h-full bg-gray-900/95 backdrop-blur-sm border-r border-gray-700 z-40 overflow-y-auto">
  <div class="p-6">
    <!-- Logo -->
    <a href="/" class="block mb-8">
      <div class="text-center">
        <h2 class="text-2xl font-bold text-cyan-400 glow-text">
          عوائد الخليج للاستثمار السعودي 🇸🇦
        </h2>
        <p class="text-sm text-gray-400 mt-1">منصة التراخيص الموحدة</p>
      </div>
    </a>

    <!-- Navigation Items -->
    <div class="space-y-2">
      @php
        $navigationItems = [
          ["path" => "/", "label" => "الصفحة الرئيسية", "icon" => "🏠"],
          ["path" => "/register-investor", "label" => "تسجيل مستثمر جديد", "icon" => "👤"],
          ["path" => "/investment-levels", "label" => "مركز مستويات الاستثمار", "icon" => "📊"],
          ["path" => "/small-investment", "label" => "بوابة الاستثمار الصغيرة", "icon" => "💰"],
          ["path" => "/large-investment", "label" => "مركز استثماري كبير", "icon" => "🏦"],
          ["path" => "/dashboard", "label" => "لوحة معلومات الاستثمار", "icon" => "📈"],
          ["path" => "/documents", "label" => "تحميل المستندات", "icon" => "📄"],
          ["path" => "/market-insights", "label" => "مركز رؤى السوق", "icon" => "📊"],
          ["path" => "/success-stories", "label" => "معرض قصص النجاح", "icon" => "🏆"],
          ["path" => "/trust-compliance", "label" => "مركز الثقة والامتثال", "icon" => "🛡️"]
        ];
      @endphp

      @foreach ($navigationItems as $item)
        @php
          $origPath = $item['path'] ?? '/';
          $href = $origPath;
          $dataAttrs = '';

          // Special handling for documents and register-investor: require auth/session
          if (isset($item['path']) && in_array($item['path'], ['/documents', '/register-investor'])) {
            $isAuth = \Illuminate\Support\Facades\Auth::check() || session('user_id');
            $intended = $item['path'];
            $dataAttrs = 'data-auth="' . ($isAuth ? '1' : '0') . '" data-intended="' . $intended . '" data-role="investor"';
            if (!$isAuth) {
              // redirect to login first, with intended path
              $href = route('auth.login.form', ['intended' => $intended]);
            }
          }
        @endphp
        <a href="{{ $href }}" {!! $dataAttrs !!} class="flex items-center w-full p-3 rounded-lg transition-colors text-right {{ request()->is(ltrim($origPath, '/')) ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-cyan-400' }}">
          <span class="text-xl ml-3">{{ $item['icon'] }}</span>
          <span class="flex-1">{{ $item['label'] }}</span>
          @if (request()->is(ltrim($origPath, '/')))
            <span class="bg-cyan-500 text-black text-xs px-2 py-1 rounded">نشط</span>
          @endif
        </a>
      @endforeach
    </div>
  </div>
</nav>