<style>
  /* Keep inner level fields two columns even on small screens */
  .card-inner-2{ display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }
  /* allow two columns down to very small widths to meet the requested layout */
  @media (max-width:320px){ .card-inner-2{ grid-template-columns:repeat(2,1fr); } }

  /* Practical example responsive: 3 columns -> 2 -> 1 */
  .example-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:8px;text-align:center;font-size:14px; }
  /* Keep the three example items side-by-side on smaller screens by switching
     to a horizontal flex row with touch scrolling. This ensures Investment / Rate / Profit
     appear in a single row even on phones. */
  @media (max-width:900px){
    .example-grid{
      display:flex;
      flex-direction:row;
      gap:8px;
      overflow-x:auto;
      -webkit-overflow-scrolling:touch;
      padding-bottom:6px;
    }
    .example-grid > *{ min-width:120px; flex:0 0 auto; }
  }
  /* center text inside each level card */
  .levels-grid .glass-card{ text-align: center; }
  .card-inner-2 > div{ text-align: center; }
  .glass-card p{ text-align: center; }

  /* Compact variant: reduce vertical space and font-sizes to make cards shorter */
  .levels-compact { padding: 8px 0; }
  .levels-compact .glass-card { padding: 10px !important; }
  .levels-compact .glass-card > div { padding: 6px 8px !important; }
  .levels-compact .glass-card h3 { font-size: 18px !important; }
  .levels-compact .glass-card p { font-size: 13px !important; }
  .levels-compact .card-inner-2 { gap:6px; }
  .levels-compact .example-grid { gap:6px; font-size:13px; }
  .levels-compact .example-grid > *{ min-width:100px; }
  .levels-compact .glass-card .btn-primary, .levels-compact .glass-card .btn-secondary { padding:8px 10px; font-size:14px; }
  .levels-compact .glass-card .digital-badges { padding:4px 6px; }
</style>

<div class="levels-compact" style="margin-right:260px;">
  <div class="digital-background"></div>

  <!-- Header (compact centered) -->
  <header style="position:relative;z-index:10;min-height:auto;display:flex;align-items:center;justify-content:center;padding:8px 16px;">
    <div style="text-align:center;max-width:900px;width:100%">
      <h1 style="font-size:32px;color:#ffffff;margin:0;text-align:center;text-shadow:none;">مركز مستويات الاستثمار</h1>
      <p style="color:#cbd5e1;font-size:18px;margin-top:6px;margin-bottom:6px;">اختر مستوى الاستثمار المناسب لك</p>
    </div>
  </header>

  @php
    $levels = [
      [
        'id' => 'beginner',
        'title' => 'مستوى المبتدئ',
        'description' => 'للمستثمرين الجدد الذين يبدأون رحلتهم الاستثمارية',
        'investmentRange' => '1,000 - 10,000 ريال',
        'profitRange' => '15% - 33%',
        'riskLevel' => 'منخفض',
        'duration' => '1 شهر',
        'borderColor' => '#22c55e',
        'riskPercentage' => 13,
        'exampleAmount' => 10000,
        'exampleRate' => 33,
        'exampleReturn' => 3300,
      ],
      [
        'id' => 'intermediate',
        'title' => ' مستوى متوسط',
        'description' => 'للمستثمرين الذين لديهم خبرة محدودة ويريدون تطوير استثماراتهم',
        'investmentRange' => '15,000 - 45,000 ريال',
        'profitRange' => '35% - 47%',
        'riskLevel' => 'منخفض',
        'duration' => '1 شهر',
        'borderColor' => '#3b82f6',
        'riskPercentage' => 13,
        'exampleAmount' => 45000,
        'exampleRate' => 47,
        'exampleReturn' => 21150,
      ],
      [
        'id' => 'advanced',
        'title' => 'مستوى متقدم',
        'description' => 'للمستثمرين ذوي الخبرة الذين يبحثون عن فرص استثمارية متقدمة',
        'investmentRange' => '50,000 - 200,000 ريال',
        'profitRange' => '48% - 50%',
        'riskLevel' => 'منخفض',
        'duration' => '1 شهر',
        'borderColor' => '#f59e0b',
        'riskPercentage' => 13,
        'exampleAmount' => 100000,
        'exampleRate' => 48,
        'exampleReturn' => 48000,
      ],
      [
        'id' => 'expert',
        'title' => 'مستوى خبير',
        'description' => 'للمستثمرين المحترفين الذين يديرون محافظ استثمارية كبيرة',
        'investmentRange' => '500,000+ ريال',
        'profitRange' => '52%+',
        'riskLevel' => 'منخفض ',
        'duration' => '1 شهر',
        'borderColor' => '#a855f7',
        'riskPercentage' => 13,
        'exampleAmount' => 500000,
        'exampleRate' => 52,
        'exampleReturn' => 260000,
      ],
    ];
    $stats = $stats ?? null;
  @endphp

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @foreach($levels as $index => $level)
          <div class="glass-card" style="border:2px solid {{ $level['borderColor'] }};position:relative;overflow:hidden;">
            <div style="padding:16px;">
              <div style="text-align:center;margin-bottom:12px;">
                <h3 style="color:#fff;font-size:22px;margin:0;">{{ $level['title'] }}</h3>
                <span style="background:{{ $level['borderColor'] }};color:#000;border-radius:6px;padding:4px 8px;font-weight:700;display:inline-block;margin-top:8px;">مستوى {{ $index + 1 }}</span>
              </div>
              <p style="color:#cbd5e1;text-align:center;">{{ $level['description'] }}</p>
            </div>
            <div style="padding:0 16px 16px;">
              <div class="card-inner-2" style="margin-bottom:10px;">
                <div>
                  <div style="font-size:12px;color:#9ca3af;">نطاق الاستثمار</div>
                  <div style="color:#06b6d4;font-weight:600;">{{ $level['investmentRange'] }}</div>
                </div>
                <div>
                  <div style="font-size:12px;color:#9ca3af;">نطاق الأرباح</div>
                  <div style="color:#fbbf24;font-weight:600;">{{ $level['profitRange'] }}</div>
                </div>
                <div>
                  <div style="font-size:12px;color:#9ca3af;">مستوى المخاطر</div>
                  <div style="color:#fff;font-weight:600;">{{ $level['riskLevel'] }}</div>
                </div>
                
              </div>

              <!-- مثال عملي -->
              <div style="background:rgba(31,41,55,0.5);padding:12px;border-radius:10px;margin-bottom:10px;">
                <h4 style="color:#fff;font-weight:700;text-align:center;margin-bottom:8px;">مثال عملي:</h4>
                <div class="example-grid">
                  <div>
                    <div style="color:#9ca3af;">الاستثمار</div>
                    <div style="color:#06b6d4;font-weight:700;">{{ number_format($level['exampleAmount']) }}</div>
                  </div>
                  <div>
                    <div style="color:#9ca3af;">النسبة</div>
                    <div style="color:#fbbf24;font-weight:700;">{{ $level['exampleRate'] }}%</div>
                  </div>
                  <div>
                    <div style="color:#9ca3af;">الربح</div>
                    <div style="color:#22c55e;font-weight:700;">{{ number_format($level['exampleReturn']) }}</div>
                  </div>
                </div>
              </div>

              <!-- مؤشر المخاطر -->
              <div>
                <div style="text-align:center;margin-bottom:6px;">
                  <div style="font-size:12px;color:#9ca3af;">مؤشر المخاطر</div>
                  <div style="font-size:12px;color:#fff;">{{ $level['riskPercentage'] }}%</div>
                </div>
                <div style="height:8px;background:#1f2937;border-radius:6px;overflow:hidden;">
                  <div style="height:8px;background:{{ $level['borderColor'] }};width: {{ $level['riskPercentage'] }}%;"></div>
                </div>
              </div>

              <!-- الإجراءات -->
              <div style="display:flex;gap:8px;padding-top:10px;">
                @php
                  // If a redirect param is provided (e.g., ?redirect=/investment/confirm?...), forward to that URL
                  $redirect = request()->query('redirect');
                  $amount = request()->query('amount');
                  $rate = request()->query('rate');
                  if ($redirect) {
                      // build redirect with level, and preserve amount & rate if present
                      $url = $redirect . (str_contains($redirect, '?') ? '&' : '?') . 'level=' . $level['id'];
                      if ($amount) { $url .= '&amount=' . urlencode($amount); }
                      if ($rate) { $url .= '&rate=' . urlencode($rate); }
                      $href = $url;
                  } else {
                      $href = '/large-investment';
                      if ($level['id'] === 'beginner') { $href = '/small-investment?level=beginner'; }
                      elseif ($level['id'] === 'intermediate') { $href = '/small-investment?level=intermediate'; }
                  }
                @endphp
                <a href="{{ $href }}" class="btn-primary" style="flex:1;text-align:center;">ابدأ الاستثمار</a>
                <button type="button" class="btn-secondary level-more" data-level-index="{{ $index }}" data-start-href="{{ $href }}" style="text-align:center;border-color:#64748b;color:#cbd5e1;">اعرف المزيد</button>
              </div>
              
            </div>
          </div>
        @endforeach
      </div>

      {{-- @if(isset($stats))
      <div style="margin-top:24px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:28px;color:#06b6d4;font-weight:700;">{{ number_format($stats['total_users']) }}+</div>
          <div style="color:#cbd5e1;">مستثمر نشط</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:28px;color:#fbbf24;font-weight:700;">{{ number_format($stats['total_investments']) }}+</div>
          <div style="color:#cbd5e1;">استثمار مكتمل</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:28px;color:#22c55e;font-weight:700;">{{ $stats['success_rate'] }}%</div>
          <div style="color:#cbd5e1;">نسبة النجاح</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:28px;color:#a855f7;font-weight:700;">{{ number_format(($stats['total_investment_amount'] ?? 0) / 1000000, 1) }}M</div>
          <div style="color:#cbd5e1;">إجمالي الاستثمارات</div>
        </div>
      </div>
      @endif --}}
    </div>

    <!-- Level details modal -->
    <div id="levelModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;align-items:center;justify-content:center;background:rgba(2,6,23,0.6);z-index:2147483647;pointer-events:auto;"> 
      <div style="position:relative;z-index:2147483648;width:720px;max-width:95%;background:#0b1220;border-radius:10px;padding:16px;border:1px solid #334155;box-shadow:0 10px 30px rgba(2,6,23,0.6);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
          <h3 id="modalTitle" style="color:#fff;margin:0;font-size:20px;"></h3>
          <button id="modalClose" style="background:transparent;border:0;color:#94a3b8;font-size:20px;">✕</button>
        </div>
        <p id="modalDescription" style="color:#cbd5e1;margin-bottom:12px;"></p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px;">
          <div>
            <div style="font-size:12px;color:#9ca3af;">نطاق الاستثمار</div>
            <div id="modalInvestmentRange" style="color:#06b6d4;font-weight:600;"></div>
          </div>
          <div>
            <div style="font-size:12px;color:#9ca3af;">نطاق الأرباح</div>
            <div id="modalProfitRange" style="color:#fbbf24;font-weight:600;"></div>
          </div>
          <div>
            <div style="font-size:12px;color:#9ca3af;">مستوى المخاطر</div>
            <div id="modalRiskLevel" style="color:#fff;font-weight:600;"></div>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
          <!-- Make Start button expand like the main card button -->
          <a id="modalStart" href="#" class="btn-primary" style="flex:1;text-align:center;display:inline-block;padding:10px 16px;">ابدأ الاستثمار</a>
          <button id="modalCloseBtn" type="button" class="btn-secondary" style="flex:0 0 auto;padding:10px 14px;">إغلاق</button>
        </div>
      </div>
    </div>
    <script>
      (function(){
        var levels = @json($levels);
        function openModal(idx, startHref){
          var level = levels[idx];
          if(!level) return;
          document.getElementById('modalTitle').textContent = level.title || '';
          document.getElementById('modalDescription').textContent = level.description || '';
          document.getElementById('modalInvestmentRange').textContent = level.investmentRange || '';
          document.getElementById('modalProfitRange').textContent = level.profitRange || '';
          document.getElementById('modalRiskLevel').textContent = level.riskLevel || '';
          // If a startHref is provided from the button (server-computed) use it; otherwise compute fallback
          var href = startHref || (function(){
            var redirect = new URLSearchParams(window.location.search).get('redirect');
            var h = redirect ? (redirect + (redirect.indexOf('?') !== -1 ? '&' : '?') + 'level=' + level.id) : ('/large-investment');
            if(level.id === 'beginner') h = '/small-investment?level=beginner';
            if(level.id === 'intermediate') h = '/small-investment?level=intermediate';
            return h;
          })();
          document.getElementById('modalStart').setAttribute('href', href);
          var modal = document.getElementById('levelModal');
          // Move modal to document.body to avoid stacking-context issues when it's inside transformed/positioned containers
          try {
            if (modal && modal.parentNode !== document.body) document.body.appendChild(modal);
          } catch(e) { /* ignore */ }
          // show modal and prevent background scroll
          if(modal){ modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        }
        function closeModal(){ 
          var modal = document.getElementById('levelModal');
          if(modal){ modal.style.display = 'none'; }
          // restore scrolling
          try{ document.body.style.overflow = ''; }catch(e){}
        }
        document.addEventListener('click', function(e){
          // Use closest so clicking inner elements (text, icons) still finds the button
          var btn = e.target && e.target.closest ? e.target.closest('.level-more') : null;
          if(btn){
            var idx = btn.getAttribute('data-level-index');
            var startHref = btn.getAttribute('data-start-href');
            openModal(idx, startHref);
          }
        });
        // Also bind directly to buttons to ensure opening works in all rendering contexts
        try {
          var directBtns = document.querySelectorAll('.level-more');
          directBtns.forEach(function(b){
            if(b.dataset._levelBound) return; b.dataset._levelBound = '1';
            b.addEventListener('click', function(ev){
              ev.preventDefault();
              var idx = this.getAttribute('data-level-index');
              var startHref = this.getAttribute('data-start-href');
              openModal(idx, startHref);
            });
          });
        } catch(e){ /* silent */ }
        document.getElementById('modalClose').addEventListener('click', closeModal);
        document.getElementById('modalCloseBtn').addEventListener('click', closeModal);
        // close when clicking outside the content
        document.getElementById('levelModal').addEventListener('click', function(e){ if(e.target === this) closeModal(); });
        // Ensure modal Start link always navigates (in case default anchor behavior is interrupted)
        var modalStartEl = document.getElementById('modalStart');
        if(modalStartEl){
          modalStartEl.addEventListener('click', function(e){
            var href = this.getAttribute('href');
            if(href && href !== '#'){
              e.preventDefault();
              // use assign to preserve history
              window.location.assign(href);
            }
          });
        }
      })();
    </script>
  </section>
</div>
