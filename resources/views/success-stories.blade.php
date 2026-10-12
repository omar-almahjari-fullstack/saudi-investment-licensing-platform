@extends('layouts.app')

@section('content')


<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">قصص النجاح</li>
            </ol>
        </nav>
    </div>
</div>

<div style="margin-right:260px;">
  <div class="digital-background"></div>
   

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    
    <div style="padding:0 16px;">
     
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">معرض قصص النجاح</h1>
      <p style="color:#cbd5e1;font-size:18px;margin-bottom:12px;">قصص ملهمة من مستثمرينا الناجحين</p>
      {{-- <button class="btn-primary" id="share-success" style="display:inline-flex;align-items:center;gap:8px;">
        <svg width="18" height="18" viewBox="0 0 20 20" fill="#000"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        شارك نجاحك
      </button> --}}
    </div>
  </header>

  <section style="position:relative;z-index:10;">
    <div style="padding:0 16px;">
      <!-- Top Featured Stories -->
      <h2 style="font-size:24px;color:#fff;margin:8px 0 12px;text-align:center;font-weight:800;">🌟 أبرز قصص النجاح المميزة</h2>
    
      <!-- Statistics Summary -->
    </div>
  </section>
  
</div>

@php
  // Prepare collections from paginator passed by controller
  $all = collect($stories->items());
  $topFeatured = $all->filter(fn($s) => isset($s->is_top) && $s->is_top);
  $featured = $all->filter(fn($s) => isset($s->featured) && $s->featured);
  $others = $all;
  $total = $all->count();
  // average profit percent (strip %)
  $avg = 0;
  if ($total) {
    $sum = $all->reduce(function($carry, $item){
      $p = 0;
      if (isset($item->profit)) {
        $p = floatval(str_replace('%','', $item->profit));
      } elseif (isset($item->profitRate)) {
        $p = floatval(str_replace('%','', $item->profitRate));
      }
      return $carry + $p;
    }, 0);
    $avg = round($sum / $total);
  }
@endphp

<!-- Server-side render of stories -->
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin:24px;" id="top-featured">
  @foreach($topFeatured as $s)
    <div class="investment-card" style="position:relative;border:4px solid; border-image: linear-gradient(90deg,#fbbf24,#f97316) 1;padding:0;"
    
         data-name="{{ e($s->name) }}"
         data-avatar="{{ e($s->avatar) }}"
         data-location="{{ e($s->location) }}"
         data-amount="{{ e($s->amount ?? '') }}"
         data-returns="{{ e($s->returns ?? '') }}"
         data-profit="{{ e($s->profit ?? '') }}"
         data-story="{{ e($s->story ?? '') }}"
         data-testimonial="{{ e($s->testimonial ?? '') }}">
      <span style="position:absolute;top:10px;right:10px;background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;padding:4px 8px;border-radius:8px;font-weight:800;">⭐ قصة مميزة</span>
      <div style="padding:16px;text-align:center;">
        <div style="width:80px;height:80px;margin:0 auto 8px;background:linear-gradient(90deg,#fbbf24,#f97316);border-radius:50%;display:flex;align-items:center;justify-content:center;">
          <span style="font-size:22px;font-weight:800;color:#000;">{{ mb_substr($s->name,0,1) }}</span>
        </div>
        <div style="color:#fff;font-size:20px;font-weight:800">{{ $s->name }}</div>
        <div style="color:#94a3b8">{{ $s->location }}</div>
      </div>
  <div style="padding:0 16px 16px 16px;">
        <div class="a_card" style="grid-template-columns:repeat(3,1fr);gap:8px;text-align:center;margin-bottom:10px;">
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الاستثمار</div><div style="color:#06b6d4;font-weight:800">{{ $s->amount }}</div></div>
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الأرباح</div><div style="color:#22c55e;font-weight:800">{{ $s->returns }}</div></div>
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">العائد</div><div style="color:#fbbf24;font-weight:800">{{ $s->profit }}</div></div>
        </div>
        <div style="background:linear-gradient(90deg,rgba(31,41,55,.5),rgba(55,65,81,.5));padding:12px;border-radius:10px;">
          <p style="color:#e5e7eb;font-style:italic;font-size:16px">"{{ $s->testimonial }}"</p>
        </div>
      </div>
      <div style="padding:0 16px 16px 16px;">
        <button class="btn-secondary btn-read-full" style="width:100%;border-color:#22d3ee;color:#22d3ee">اقرأ القصة كاملة</button>
      </div>
    </div>
  @endforeach
</div>

<style>
  .a_card{
    display:grid;
    }
    
  
  
  @media (max-width: 468px) {
  .a_card{
    display: flex;
        display: flex;
    flex-wrap: wrap; /* ✅ تسمح للعناصر بالنزول للسطر الثاني */
    justify-content: center; /* اختياري - لترتيبهم بشكل جميل */
    }
    
    .a_card_it{
        min-width: 120px;
    }
    
  
  }
</style>

<div id="featured-stories" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:24px;">
  @foreach($featured as $s)
  <div class="investment-card" style="position:relative;border:3px solid;border-image: linear-gradient(90deg,#06b6d4,#3b82f6) 1;padding:0;overflow:hidden;"
         data-name="{{ e($s->name) }}"
         data-avatar="{{ e($s->avatar) }}"
         data-location="{{ e($s->location) }}"
         data-amount="{{ e($s->amount ?? '') }}"
         data-returns="{{ e($s->returns ?? '') }}"
         data-profit="{{ e($s->profit ?? '') }}"
         data-story="{{ e($s->story ?? '') }}"
         data-testimonial="{{ e($s->testimonial ?? '') }}">
      <span style="position:absolute;top:10px;right:10px;background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;padding:4px 8px;border-radius:8px;font-size:12px;font-weight:800;display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#000"/></svg>
        <span>مميز</span>
      </span>
      <div style="padding:14px;text-align:center;">
        <div style="width:72px;height:72px;margin:0 auto 8px;background:linear-gradient(90deg,#06b6d4,#3b82f6);border-radius:50%;display:flex;align-items:center;justify-content:center;">
          <span style="font-size:20px;font-weight:800;color:#fff;">{{ mb_substr($s->name,0,1) }}</span>
        </div>
        <div style="color:#fff;font-size:18px;font-weight:800">{{ $s->name }}</div>
        <div style="color:#94a3b8;font-size:13px">{{ $s->location }}</div>
      </div>
      <div style="padding:0 14px 14px 14px;">
        <div class="a_card" style="grid-template-columns:repeat(3,1fr);gap:8px;text-align:center;margin-bottom:10px;">
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الاستثمار</div><div style="color:#06b6d4;font-weight:800">{{ $s->amount }}</div></div>
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الأرباح</div><div style="color:#22c55e;font-weight:800">{{ $s->returns }}</div></div>
          <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">العائد</div><div style="color:#fbbf24;font-weight:800">{{ $s->profit }}</div></div>
        </div>
        <div style="background:linear-gradient(90deg,rgba(15,23,42,.6),rgba(55,65,81,.4));padding:12px;border-radius:10px;margin-bottom:12px;">
          <p style="color:#e5e7eb;font-style:italic;font-size:15px;">"{{ $s->testimonial }}"</p>
        </div>
        <div style="text-align:center;"><button class="btn-secondary btn-read-full" style="width:100%;border-color:#22d3ee;color:#22d3ee">اقرأ القصة كاملة</button></div>
      </div>
    </div>
  @endforeach
</div>

<div style="display:flex;flex-direction:column;gap:12px;">
  <div style="display:flex;justify-content:space-between;align-items:center;margin:12px;">
    <h2 style="font-size:20px;color:#fff;font-weight:800">جميع القصص</h2>
    <div style="display:flex;align-items:center;gap:8px;">
      <span style="color:#9ca3af;font-size:14px;">تصفية حسب:</span>
      <button class="filter-btn btn-primary" data-filter="all">جميع الفئات</button>
      <button class="filter-btn btn-secondary" data-filter="small" style="border-color:#64748b;color:#cbd5e1;">الاستثمارات الصغيرة</button>
      <button class="filter-btn btn-secondary" data-filter="large" style="border-color:#64748b;color:#cbd5e1;">الاستثمارات الكبيرة</button>
    </div>
  </div>

  <div id="all-stories" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
    @foreach($others as $s)
      <div class="investment-card story-item" data-category="{{ $s->category }}" style="padding:16px;margin:7px 14px;"
           data-name="{{ e($s->name) }}"
           data-avatar="{{ e($s->avatar) }}"
           data-location="{{ e($s->location) }}"
           data-amount="{{ e($s->amount ?? '') }}"
           data-returns="{{ e($s->returns ?? '') }}"
           data-profit="{{ e($s->profit ?? '') }}"
           data-story="{{ e($s->story ?? '') }}"
           data-testimonial="{{ e($s->testimonial ?? '') }}">
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:10px;justify-content: center;">
          <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(90deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;max-width: 23%;height: 71px;">{{ $s->avatar }}</div>
          <div>
            <div style="color:#fff;font-weight:800">{{ $s->name }}</div>
            <div style="color:#94a3b8;font-size:12px">{{ $s->location }}</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;font-size:12px;margin-bottom:10px">
          <div><div style="color:#9ca3af">الاستثمار</div><div style="color:#06b6d4;font-weight:700">{{ $s->amount }}</div></div>
          <div><div style="color:#9ca3af">العوائد</div><div style="color:#22c55e;font-weight:700">{{ $s->returns }}</div></div>
        </div>
        <p style="color:#e5e7eb;font-size:14px">{{ 
          
          (strlen($s->story) > 120) ? mb_substr($s->story,0,120).'...' : $s->story
        }}</p>
        <button class="btn-secondary btn-read-full" style="width:100%;margin-top:8px;border-color:#64748b;color:#cbd5e1">اقرأ القصة كاملة</button>
      </div>
    @endforeach
  </div>

  <div id="stories-stats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:24px;">
    <div class="glass-card" style="text-align:center;padding:16px"><div style="font-size:28px;font-weight:800;color:#06b6d4">{{ $total }}</div><div style="color:#cbd5e1">قصة نجاح</div></div>
    <div class="glass-card" style="text-align:center;padding:16px"><div style="font-size:28px;font-weight:800;color:#22c55e">98%</div><div style="color:#cbd5e1">معدل الرضا</div></div>
    <div class="glass-card" style="text-align:center;padding:16px"><div style="font-size:28px;font-weight:800;color:#fbbf24">{{ $avg }}%</div><div style="color:#cbd5e1">متوسط العوائد</div></div>
    <div class="glass-card" style="text-align:center;padding:16px"><div style="font-size:28px;font-weight:800;color:#a855f7">30</div><div style="color:#cbd5e1">يوم متوسط العوائد</div></div>
  </div>
</div>

  <!-- Modal: Read Full Story -->
  <div id="ss-full-modal" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);">
    <div style="background:#0f172a;border:1px solid #334155;border-radius:8px;max-width:820px;margin:40px auto;padding:18px;color:#e6eef8;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
          <div id="ss-full-name" style="font-size:20px;font-weight:800;color:#fff"></div>
          <div id="ss-full-meta" style="color:#94a3b8;font-size:13px;margin-top:4px"></div>
        </div>
        <div>
          <button id="ss-full-close" style="background:transparent;border:0;color:#94a3b8;font-size:20px;">✕</button>
        </div>
      </div>
      <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:12px;">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(90deg,#06b6d4,#3b82f6);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:24px" id="ss-full-avatar">A</div>
        <div style="flex:1;">
          <div style="display:flex;gap:12px;margin-bottom:8px;">
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">الاستثمار</div>
              <div id="ss-full-amount" style="color:#06b6d4;font-weight:800"></div>
            </div>
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">العوائد</div>
              <div id="ss-full-returns" style="color:#22c55e;font-weight:800"></div>
            </div>
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">العائد</div>
              <div id="ss-full-profit" style="color:#fbbf24;font-weight:800"></div>
            </div>
          </div>
          <div id="ss-full-story" style="color:#e5e7eb;line-height:1.6;margin-bottom:10px"></div>
          <div id="ss-full-testimonial" style="background:linear-gradient(90deg,rgba(31,41,55,.5),rgba(55,65,81,.5));padding:12px;border-radius:8px;color:#cfe6ff;font-style:italic"></div>
        </div>
      </div>
      <div style="text-align:right;"><button id="ss-full-close-2" style="background:#2563eb;color:#fff;border:0;padding:8px 12px;border-radius:6px">إغلاق</button></div>
    </div>
  </div>

<script>
  // simple client-side filter for categories
  (function(){
    function setActive(btn){ document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('btn-primary') || b.classList.add('btn-secondary'));
    btn.classList.remove('btn-secondary'); btn.classList.add('btn-primary'); }
    document.querySelectorAll('.filter-btn').forEach(function(b){
      b.addEventListener('click', function(){
        var key = this.dataset.filter;
        setActive(this);
        document.querySelectorAll('.story-item').forEach(function(it){
          if(key==='all' || it.dataset.category===key) it.style.display = '';
          else it.style.display = 'none';
        });
      });
    });
    // Read full story modal logic
    function escapeHtml(unsafe) {
      return (unsafe || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function toArabicIndicDigits(str) {
      if (!str) return str || '';
      const map = { '0':'٠','1':'١','2':'٢','3':'٣','4':'٤','5':'٥','6':'٦','7':'٧','8':'٨','9':'٩' };
      return str.toString().replace(/[0-9]/g, function(d){ return map[d]; });
    }

    function formatAmountForDisplay(amount) {
      if (!amount) return '-';
      // keep full string but convert latin digits to Arabic-Indic for consistency
      return toArabicIndicDigits(amount);
    }

    function enhanceStoryDisplay(data) {
      // Name
      document.getElementById('ss-full-name').textContent = data.name || '-';
      // Avatar initials
      var avatarEl = document.getElementById('ss-full-avatar');
      avatarEl.textContent = (data.avatar || '').toString().substring(0,2) || (data.name || '-').toString().substring(0,1);
      // Meta (location · category)
      var meta = '';
      if (data.location) meta += escapeHtml(data.location);
      if (data.category) meta += (meta ? ' · ' : '') + escapeHtml(data.category);
      document.getElementById('ss-full-meta').textContent = meta;

      // Amounts (formatted)
      document.getElementById('ss-full-amount').innerHTML = '<span>' + formatAmountForDisplay(data.amount || '-') + '</span>';
      document.getElementById('ss-full-returns').innerHTML = '<span>' + formatAmountForDisplay(data.returns || '-') + '</span>';
      document.getElementById('ss-full-profit').innerHTML = '<span>' + escapeHtml(data.profit || '-') + '</span>';

      // Story: preserve paragraphs, escape HTML
      var storyHtml = '';
      if (data.story) {
        var parts = data.story.split(/\r?\n\r?\n/); // split by blank line for paragraphs
        storyHtml = parts.map(function(p){ return '<p>' + escapeHtml(p.trim()).replace(/\n/g, '<br>') + '</p>'; }).join('');
      }
      document.getElementById('ss-full-story').innerHTML = storyHtml;

      // Testimonial
      document.getElementById('ss-full-testimonial').innerHTML = data.testimonial ? ('"' + escapeHtml(data.testimonial) + '"') : '';
    }

    function openFullStoryModal(data) {
      enhanceStoryDisplay(data);
      document.getElementById('ss-full-modal').style.display = 'flex';
    }

    function closeFullStoryModal() {
      document.getElementById('ss-full-modal').style.display = 'none';
    }

    document.addEventListener('click', function(e){
      var btn = e.target.closest && e.target.closest('.btn-read-full');
      if (btn) {
        var card = btn.closest('.investment-card');
        if (!card) return;
        var data = {
          name: card.dataset.name || '',
          avatar: card.dataset.avatar || '',
          location: card.dataset.location || '',
          amount: card.dataset.amount || '',
          returns: card.dataset.returns || '',
          profit: card.dataset.profit || '',
          story: card.dataset.story || '',
          testimonial: card.dataset.testimonial || '',
          category: card.dataset.category || ''
        };
        openFullStoryModal(data);
      }
    });

    document.getElementById('ss-full-close').addEventListener('click', closeFullStoryModal);
    document.getElementById('ss-full-close-2').addEventListener('click', closeFullStoryModal);
    // close modal on overlay click
    document.getElementById('ss-full-modal').addEventListener('click', function(e){ if (e.target === this) closeFullStoryModal(); });
  })();
</script>
@endsection
