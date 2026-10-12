@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">مركز الاستثمار الكبير</li>
            </ol>
        </nav>
    </div>
</div>
<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  @php
    $title = 'مركز الاستثمار الكبير';
    $subtitle = 'استثمارات احترافية تبدأ من 50,000 ريال سعودي';
    $tiers = [
      ['range' => '50,000 - 74,999 ريال',  'rate' => '48%', 'example' => '50,000 ريال → 22,500 ريال ربح'],
      ['range' => '75,000 - 99,999 ريال',  'rate' => '49%', 'example' => '75,000 ريال → 34,500 ريال ربح'],
      ['range' => '100,000 - 199,999 ريال','rate' => '50%', 'example' => '100,000 ريال → 47,000 ريال ربح'],
      ['range' => '200,000 - 499,999 ريال','rate' => '51%', 'example' => '200,000 ريال → 96,000 ريال ربح'],
      ['range' => '500,000+ ريال',         'rate' => '52%', 'example' => '500,000 ريال → 250,000 ريال ربح'],
    ];
  @endphp

  @php
    // Resolve server-side flags for JS
    $srvUser = auth()->user();
    if (!$srvUser && session()->has('user_id')) { $srvUser = \App\Models\User::find(session('user_id')); }
    $srvHasInvestor = false;
    $srvHasInvestments = false;
    if ($srvUser) {
      $inv = \App\Models\Investor::where('user_id', $srvUser->id)->first();
      if ($inv) {
        $srvHasInvestor = true;
        $srvHasInvestments = \App\Models\Investment::where('investor_id', $inv->id)->exists();
      }
    }
  @endphp

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">{{ $title }}</h1>
      <p style="color:#cbd5e1;font-size:18px;margin-bottom:18px;">{{ $subtitle }}</p>

      <style>
        /* Strongly enforce two items per row on ALL screen sizes so five items become 3 rows (2+2+1).
           Use !important to avoid project-level rules that collapse the grid on small screens. */
        .two-per-row {
          display: grid !important;
          grid-template-columns: repeat(2, 1fr) !important;
          gap: 10px !important;
          margin-bottom: 16px !important;
          align-items: stretch !important;
        }
        /* Center the final single item when the count is odd (e.g. 5 items -> last item centered).
           This spans the last item across both columns and narrows it for a pleasant centered card. */
        .two-per-row > :nth-child(odd):last-child {
          grid-column: 1 / -1 !important;
          justify-self: center !important;
          width: 60% !important;
          max-width: 480px !important;
        }
      </style>

      <div class="two-per-row">
        @foreach($tiers as $t)
          <div class="glass-card" style="text-align:center;padding:12px;">
            <div style="color:#22d3ee;font-weight:700;">{{ $t['rate'] }}</div>
            <div style="color:#9ca3af;font-size:12px;">{{ $t['range'] }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </header>

  <!-- Content -->
  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:1100px;margin:0 auto;">
      <!-- Calculator -->
      <div class="glass-card" style="padding:16px;margin-bottom:16px;max-width:760px;margin-left:auto;margin-right:auto;">
        <h3 style="color:#fff;text-align:center;font-size:20px;margin-bottom:12px;">حاسبة الاستثمار</h3>
        <div style="max-width:460px;margin:0 auto;">
          <label for="large-amount" style="display:block;color:#cbd5e1;margin-bottom:6px;font-size:16px;">أدخل مبلغ الاستثمار</label>
          <div style="position:relative;">
            <input id="large-amount" type="number" min="50000" placeholder="50000"
                   class="form-control"
                   style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:14px 48px 14px 16px;border-radius:10px;font-size:18px;" />
            <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">ريال</span>
          </div>
          <div id="calc-error" style="color:#f87171;font-size:13px;margin-top:6px;display:none;"></div>
          <button id="btn-calc" class="btn-primary" style="width:100%;margin-top:12px;font-size:16px;padding:12px 0;">حساب العوائد</button>
        </div>
      </div>

      <!-- Calculation Results -->
      <div id="calc-result" class="glass-card" style="display:none;border:2px solid #fbbf24;margin-bottom:16px;">
        <div style="padding:16px;display:flex;justify-content:space-between;align-items:center;">
          <h4 style="color:#fff;font-size:18px;margin:0;">نتائج الحساب</h4>
          <span style="background:#fbbf24;color:#000;border-radius:8px;padding:4px 8px;font-weight:700;font-size:12px;">محسوبة</span>
        </div>
  <!-- Arrange results in two columns (two items per row) like the tiers -->
  <div style="padding:0 16px 16px 16px;display:grid;grid-template-columns:repeat(2,1fr) !important;gap:12px !important;">
          <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
            <span style="color:#9ca3af;">مبلغ الاستثمار:</span>
            <span id="res-amount" style="color:#22d3ee;font-weight:700;"></span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
            <span style="color:#9ca3af;">نسبة الربح:</span>
            <span id="res-rate" style="color:#fbbf24;font-weight:700;"></span>
          </div>
        
          <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(22,163,74,.2);border-radius:10px;">
            <span style="color:#9ca3af;">الربح المتوقع:</span>
            <span id="res-profit" style="color:#22c55e;font-weight:700;"></span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(30,64,175,.2);border-radius:10px;">
            <span style="color:#9ca3af;">إجمالي العائد:</span>
            <span id="res-total" style="color:#60a5fa;font-weight:700;"></span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
            <span style="color:#9ca3af;">مستوى المخاطر:</span>
            <span style="color:#22c55e;font-weight:700;">مضمون</span>
          </div>
        </div>
        <div style="text-align:center;padding:0 16px 16px 16px;">
        
                    <a id="btn-submit-invest" href="#" class="btn-primary" style="font-size:18px;padding:12px 36px;display:inline-block;">🚀 رفع طلب الاستثمار</a>

          <p style="color:#9ca3af;font-size:12px;margin-top:6px;">سيتم توجيهك لملء البيانات المطلوبة</p>
        </div>
      </div>

      <!-- Tiers details -->
      <div class="glass-card" style="padding:16px;">
        <h3 style="color:#fff;text-align:center;margin-bottom:12px;">شرائح الاستثمار والعوائد</h3>
        <div class="two-per-row">
          @foreach($tiers as $t)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:rgba(31,41,55,.5);border-radius:10px;">
              <div>
                <div style="color:#fff;font-weight:700;">{{ $t['range'] }}</div>
                <div style="color:#9ca3af;font-size:12px;">{{ $t['example'] }}</div>
              </div>
              <div style="color:#fbbf24;font-weight:800;font-size:22px;">{{ $t['rate'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(function(){
    function formatNumber(n){ return (n||0).toLocaleString('en-US'); }
    function rateFor(amount){
      // Match the tiers shown above
      if(amount >= 500000) return 52; // 500k+
      if(amount >= 200000) return 51; // 200k - 499,999
      if(amount >= 100000) return 50; // 100k - 199,999
      if(amount >= 75000)  return 49; // 75k  - 99,999
      return 48; // 50k - 74,999
    }

    function computeAndShow(amt){
      var hasInvestor = {{ $srvHasInvestor ? 'true' : 'false' }};
      var pr = rateFor(amt);
      var profit = Math.round(amt * pr / 100);
      var total  = amt + profit;
      $('#res-amount').text(formatNumber(amt)+' ريال');
      $('#res-rate').text(pr+'%');
      $('#res-profit').text('+'+formatNumber(profit)+' ريال');
      $('#res-total').text(formatNumber(total)+' ريال');
      $('#calc-result').show();

      var baseConfirm = '/investment/confirm?amount=' + encodeURIComponent(Math.round(amt)) + '&rate=' + encodeURIComponent(pr) + '&isLargeA=true&level=advanced';
      var confirmUrl = hasInvestor ? baseConfirm : '{{ route('register.investor') }}';
      $('#btn-submit-invest').attr('href', confirmUrl);
    }

    // Ensure click always performs the investor profile check and navigates accordingly
    $('#btn-submit-invest').on('click', function(e){
      e.preventDefault();
      var val = $('#large-amount').val();
      var amt = parseFloat(val);
      if(!val || isNaN(amt) || amt < 50000){
        $('#calc-error').text('يرجى إدخال مبلغ صحيح لا يقل عن 50,000 ريال').show();
        $('#calc-result').hide();
        return;
      }
      var pr = rateFor(amt);
      var baseConfirm = '/investment/confirm?amount=' + encodeURIComponent(Math.round(amt)) + '&rate=' + encodeURIComponent(pr) + '&isLargeA=true&level=advanced';
      var hasInvestor = {{ $srvHasInvestor ? 'true' : 'false' }};
      var target = hasInvestor ? baseConfirm : '{{ route('register.investor') }}';
      window.location.assign(target);
    });

      $('#btn-calc').on('click', function(){
      var val = $('#large-amount').val();
      var amt = parseFloat(val);
      if(!val || isNaN(amt)){
        $('#calc-error').text('يرجى إدخال مبلغ صحيح').show();
        $('#calc-result').hide();
        return;
      }
      if(amt < 50000){
        $('#calc-error').text('⚠️الحد الأدنى للاستثمار الكبير هو 50,000 ريال سعودي. للمبالغ الأصغر يرجى استخدام بوابة الاستثمار الصغيرة').show();
        $('#calc-result').hide();
        return;
      }
      $('#calc-error').hide();
      computeAndShow(amt);
    });

    $('#large-amount').on('input', function(){
      var v = parseFloat($(this).val());
      if(!isNaN(v) && v >= 50000){
        $('#calc-error').hide();
        computeAndShow(Math.round(v));
      } else {
        $('#calc-error').text('الحد الأدنى: 50,000 ريال').show();
        $('#calc-result').hide();
        // point to register as safe default when invalid
        $('#btn-submit-invest').attr('href', '{{ route('register.investor') }}');
      }
    });
  });
</script>
@endsection
