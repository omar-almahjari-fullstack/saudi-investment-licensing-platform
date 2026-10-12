@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">مركز رؤى السوق</li>
            </ol>
        </nav>
    </div>
</div>

<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-bottom:22px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">مركز رؤى السوق</h1>
      <p style="color:#cbd5e1;font-size:18px;">تحليلات وأخبار السوق الاستثماري السعودي</p>
    </div>
  </header>

  <style>
    /* Clamp long news summaries to 3 lines and ensure long words break */
    .news-summary {
      white-space: normal;
      /* allow breaking very long words/URLs so they don't overflow the card */
      overflow-wrap: anywhere;
      word-break: break-word; /* preferred */
      -ms-word-break: break-word;
      word-break: break-all; /* fallback to force-break extremely long words */
      max-width: 100%;
    }
    .news-summary.collapsed {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    /* Ensure news cards do not expand horizontally and stay within their column */
    #tab-news .glass-card {
      width: 100%;
      box-sizing: border-box;
      max-width: 100%;
      overflow-wrap: anywhere;
      word-break: break-word;
      -ms-word-break: break-word;
      overflow-x: hidden;
    }
    /* ensure summary wraps and expands vertically instead of horizontally */
    #tab-news .news-summary {
      white-space: normal;
      overflow-wrap: anywhere;
      word-break: break-word;
    }
    /* Responsive grids and card rules for investor dashboard (match admin behavior) */
    .mi-grid { display: grid; gap: 12px; }
    .mi-grid.indicators { grid-template-columns: repeat(4, 1fr); }
    .mi-grid.sectors { grid-template-columns: repeat(2, 1fr); }
    .mi-grid.news { grid-template-columns: 1fr; }
    .mi-grid.trends { grid-template-columns: repeat(3, 1fr); }

    .mi-card { width:100%; box-sizing:border-box; overflow-wrap:anywhere; word-break:break-word; padding:16px; }
    .mi-card .mi-summary { color:#cbd5e1; margin-bottom:10px; white-space:normal; overflow-wrap:anywhere; word-break:break-word; }

    /* Small screens: single column layout */
    @media (max-width: 640px) {
      .mi-grid.indicators { grid-template-columns: 1fr !important; }
      .mi-grid.sectors { grid-template-columns: 1fr !important; }
      .mi-grid.trends { grid-template-columns: 1fr !important; }
    }
  </style>

  @php
    // Data is provided by MarketInsightsController@index as:
    // $indicators, $sectors, $news, $trends
    // Fallback to empty collections to avoid undefined variable errors when rendering in isolation.
    $indicators = $indicators ?? collect();
    $sectors = $sectors ?? collect();
    $news = $news ?? collect();
    $trends = $trends ?? collect();
  @endphp

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">

      <!-- Market Overview -->
      <div style="margin-bottom:18px;">
        <h2 style="font-size:22px;color:#fff;margin-bottom:12px;font-weight:800;">نظرة عامة على السوق</h2>
        <div class="mi-grid indicators">
          @foreach($indicators as $idx => $ind)
            <div class="glass-card mi-card" style="text-align:center;" data-testid="market-indicator-{{ $idx }}">
              <div style="color:#9ca3af;font-size:12px;margin-bottom:6px;">{{ $ind->name }}</div>
              <div style="color:#fff;font-size:22px;font-weight:800;margin-bottom:4px;">{{ $ind->value }}</div>
              <div style="display:flex;align-items:center;justify-content:center;font-weight:700;{{ $ind->trend === 'up' ? 'color:#22c55e' : 'color:#ef4444' }};">
                @if($ind->trend === 'up')
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="margin-left:4px;"><path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                @else
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="margin-left:4px;"><path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 15.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                @endif
                {{ $ind->change }}
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Tabs -->
      <div class="glass-card" style="padding:12px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px;background:#1f2937;border-radius:10px;padding:6px;">
          <button class="tab-trigger active" data-tab="sectors" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:#22d3ee;color:#000;font-weight:700;">أفضل القطاعات أداءً</button>
          <button class="tab-trigger" data-tab="news" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:transparent;color:#cbd5e1;">الأخبار الحديثة</button>
          <button class="tab-trigger" data-tab="trends" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:transparent;color:#cbd5e1;">اتجاهات الاستثمار</button>
        </div>

        <!-- Sectors -->
        <div class="tab-content" id="tab-sectors">
          <div class="mi-grid sectors">
            @foreach($sectors as $i => $sector)
              <div class="glass-card mi-card" data-testid="sector-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ $sector->name }}</h3>
                  <span style="background:{{ $sector->color }};color:#fff;border-radius:6px;padding:4px 8px;font-size:12px;">{{ $sector->trend }}</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                  <div>
                    <div style="color:#9ca3af;font-size:12px;">الأداء</div>
                    <div style="color:#22c55e;font-size:22px;font-weight:800;">{{ $sector->performance }}</div>
                  </div>
                  <div>
                    <div style="color:#9ca3af;font-size:12px;">حجم التداول</div>
                    <div style="color:#22d3ee;font-size:18px;font-weight:700;">{{ $sector->volume }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

    <!-- News -->
    <div class="tab-content" id="tab-news" style="display:none;">
      <div class="mi-grid news">
            @foreach($news as $i => $news)
              <div class="glass-card mi-card" data-testid="news-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                  <span style="color:#22d3ee;border:1px solid #22d3ee;border-radius:8px;padding:4px 8px;font-size:12px;">{{ $news->category }}</span>
                  <div style="color:#94a3b8;font-size:12px;">{{ $news->date }} • {{ $news->read_time ?? $news->readTime ?? '' }}</div>
                </div>
                <h3 style="color:#fff;font-size:20px;font-weight:800;margin-bottom:6px;">{{ $news->title }}</h3>
                  @php
                    $full = (string) ($news->summary ?? '');
                    // Insert zero-width space every 40 non-space characters to allow browsers to break very long unbroken strings
                    $display = preg_replace('/(\S{40})(?=\S)/u', '$1&#8203;', $full);
                  @endphp
                  <p class="news-summary collapsed" data-full="{{ e($full) }}" style="color:#cbd5e1;margin-bottom:10px;">{!! $display !!}</p>
                  <button class="btn-secondary news-read-more" data-expanded="0" style="border-color:#22d3ee;color:#22d3ee;display:inline-block;">اقرأ المزيد</button>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Trends -->
        <div class="tab-content" id="tab-trends" style="display:none;">
          <div class="mi-grid trends">
            @foreach($trends as $i => $tr)
              <div class="glass-card mi-card" data-testid="trend-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ $tr->trend }}</h3>
                  <div style="color:#22c55e;font-size:22px;font-weight:800;">{{ $tr->growth }}</div>
                </div>
                <p class="mi-summary">{{ $tr->description }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
  $(function(){
    $('.tab-trigger').on('click', function(){
      var tab = $(this).data('tab');
  $('.tab-trigger').removeClass('active').css({background:'transparent', color:'#cbd5e1', border:'1px solid #b8860b'});
  $(this).addClass('active').css({background:'#22d3ee', color:'#000', border:'1px solid #b8860b'});
      $('.tab-content').hide();
      $('#tab-'+tab).show();
    });
    // Read more toggle for news summaries using CSS clamp
    $('.news-read-more').each(function(){
      var $btn = $(this);
      var $p = $btn.prev('.news-summary');
      if (!$p.length) return;
      var full = $p.data('full') || '';
      if (!full || full.length <= 160) {
        $btn.hide();
      } else {
        $p.addClass('collapsed');
        $btn.attr('data-expanded','0');
        $btn.show();
      }
    });

    $(document).on('click', '.news-read-more', function(e){
      e.preventDefault();
      var $btn = $(this);
      var $p = $btn.prev('.news-summary');
      if (!$p.length) return;
      var expanded = $btn.attr('data-expanded') === '1';
      if (!expanded) {
        $p.removeClass('collapsed');
        $btn.text('إخفاء');
        $btn.attr('data-expanded','1');
      } else {
        $p.addClass('collapsed');
        $btn.text('اقرأ المزيد');
        $btn.attr('data-expanded','0');
        // scroll card into view if needed
        try { $p[0].scrollIntoView({behavior:'smooth', block:'nearest'}); } catch(e) {}
      }
    });
  });
</script>
@endsection
