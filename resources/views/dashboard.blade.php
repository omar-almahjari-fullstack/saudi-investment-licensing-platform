@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@extends('layouts.app')

@section('content')
<div style="margin-left:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Login Required State -->
  <div id="state-login-required" style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div class="glass-card" style="max-width:480px;margin:0 auto;padding:24px;text-align:center;">
      <h2 style="color:#fff;font-size:22px;margin-bottom:8px;font-weight:800;">تسجيل الدخول مطلوب</h2>
      <p style="color:#cbd5e1;margin-bottom:16px;">يرجى إنشاء استثمار أو طلب ترخيص أولاً للوصول للوحة المعلومات</p>
      <div style="display:grid;gap:10px;">
        <a href="/investment/check" class="btn-primary" style="display:block;">إنشاء استثمار</a>
        <a href="/license" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">طلب ترخيص</a>
        <a href="/" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
      </div>
    </div>
  </div>

  <!-- Dashboard Content (Visible when user exists) -->
  <div id="state-dashboard" style="display:none;">
    <!-- Header -->
    <header style="position:relative;z-index:10;padding-top:8px;padding-bottom:12px;">
      <div style="padding:0 16px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
          <a href="/" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;margin-bottom:8px;">← العودة للرئيسية</a>
          <div>
            <h1 style="font-size:32px;color:#fff;margin:6px 0;" class="glow-text">لوحة المعلومات</h1>
            <p id="greeting" style="color:#cbd5e1;">مرحباً</p>
          </div>
        </div>
        <div style="display:flex;gap:8px;">
          <a href="/investment/check" class="btn-primary">استثمار جديد</a>
          <a href="/license" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">طلب جديد</a>
        </div>
      </div>
    </header>

    <div style="position:relative;z-index:10;padding:0 16px 24px;max-width:1200px;margin:0 auto;">
      <!-- Summary Cards -->
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div id="sum-investments-count" style="font-size:28px;color:#22d3ee;font-weight:800;">0</div>
          <div style="color:#cbd5e1;">إجمالي الاستثمارات</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div id="sum-investments-amount" style="font-size:28px;color:#fbbf24;font-weight:800;">0</div>
          <div style="color:#cbd5e1;">إجمالي المبالغ المستثمرة</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div id="sum-applications-count" style="font-size:28px;color:#22c55e;font-weight:800;">0</div>
          <div style="color:#cbd5e1;">طلبات التراخيص</div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="glass-card" style="padding:12px;">
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:12px;background:#1f2937;border-radius:10px;padding:6px;">
          <button class="dash-trigger active" data-tab="inv" style="padding:10px;border-radius:8px;border:0;background:#22d3ee;color:#000;font-weight:700;">استثماراتي</button>
          <button class="dash-trigger" data-tab="apps" style="padding:10px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">طلباتي</button>
        </div>

        <!-- Investments Tab -->
        <div class="dash-content" id="dash-inv">
          <div id="inv-list" style="display:grid;gap:12px;"></div>
          <div id="inv-empty" class="glass-card" style="padding:24px;text-align:center;display:none;">
            <div style="width:64px;height:64px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#334155;">
              <svg width="28" height="28" viewBox="0 0 20 20" fill="#94a3b8"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
            </div>
            <h3 style="color:#fff;margin-bottom:8px;">لا توجد استثمارات بعد</h3>
            <a href="/investment/check" class="btn-primary" style="display:inline-block;">ابدأ أول استثمار</a>
          </div>
        </div>

        <!-- Applications Tab -->
        <div class="dash-content" id="dash-apps" style="display:none;">
          <div id="apps-list" style="display:grid;gap:12px;"></div>
          <div id="apps-empty" class="glass-card" style="padding:24px;text-align:center;display:none;">
            <div style="width:64px;height:64px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#334155;">
              <svg width="28" height="28" viewBox="0 0 20 20" fill="#94a3b8"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm4 0a1 1 0 100 2h.01a1 1 0 100-2h-.01z" clip-rule="evenodd"/></svg>
            </div>
            <h3 style="color:#fff;margin-bottom:8px;">لا توجد طلبات بعد</h3>
            <a href="/license" class="btn-primary" style="display:inline-block;">تقديم أول طلب ترخيص</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // محتوى الجافاسكريبت كما في كودك القديم...
  // (لم يتم التعديل عليه هنا للحفاظ على وظائف الصفحة والتوافق)
  $(function(){
    // If the client already has local investment data (used by this UI),
    // clicking any "create investment" link should take them to the
    // investment levels center instead of the server login check. This
    // handles cases where the UI is driven by localStorage/session but
    // the server-side session isn't present.
    $(document).on('click', 'a[href="/investment/check"]', function(e){
      try{
        var invs = JSON.parse(localStorage.getItem('sg_investments') || '[]');
        if(Array.isArray(invs) && invs.length > 0){
          e.preventDefault();
          window.location.href = '/investment-levels';
          return false;
        }
      }catch(err){ /* ignore parse errors and let the link work normally */ }
      // otherwise allow default navigation to /investment/check which will
      // perform server-side verification and redirection.
    });
    // Helper formatting
    function fmt(n){ return (n||0).toLocaleString('en-US'); }
    function badge(status){
      var key = (status || '').toString().toLowerCase();
      var cfgMap = {
        pending:   {bg:'#f59e0b', text:'قيد المراجعة'},
        active:    {bg:'#22c55e', text:'نشط'},
        completed: {bg:'#3b82f6', text:'مكتمل'},
        approved:  {bg:'#22c55e', text:'موافق عليه'},
        // Investor-facing requirement: rejected should appear as pending/under review
        rejected:  {bg:'#f59e0b', text:'قيد المراجعة'}
      };
      var cfg = cfgMap[key] || {bg:'#64748b', text: status };
      return $('<span/>').css({background:cfg.bg,color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}).text(cfg.text);
    }
    function formatDate(s){
      try { var d = new Date(s); return d.toLocaleDateString('ar-SA', {year:'numeric',month:'short',day:'numeric'}); } catch(e){ return s||''; }
    }

    // Simulate user presence from localStorage (if available)
    // Expected structure: localStorage.setItem('sg_user', JSON.stringify({ id:'...', full_name:'...' }))
    var user = null;
    try { user = JSON.parse(localStorage.getItem('sg_user')||'null'); } catch(e) { user = null; }

    if(user){
      $('#state-login-required').hide();
      $('#state-dashboard').show();
      $('#greeting').text('مرحباً، ' + (user.full_name||''));
    } else {
      $('#state-login-required').show();
      $('#state-dashboard').hide();
      return; // No further rendering needed
    }

    // Load user data if available in localStorage; else keep empty arrays
    // Expected structures (optional):
    // localStorage.setItem('sg_investments', JSON.stringify([{id:'...', amount:10000, profit_rate:33, expected_return:3300, status:'active', created_at:'2025-10-01'}]))
    // localStorage.setItem('sg_applications', JSON.stringify([{id:'...', business_name:'...', business_type:'...', status:'approved', created_at:'2025-10-03'}]))
    var investments = []; var applications = [];
    try { investments = JSON.parse(localStorage.getItem('sg_investments')||'[]'); } catch(e){}
    try { applications = JSON.parse(localStorage.getItem('sg_applications')||'[]'); } catch(e){}

    // Summary
    $('#sum-investments-count').text(investments.length);
    var totalAmt = investments.reduce(function(sum, inv){ return sum + (parseFloat(inv.amount)||0); }, 0);
    $('#sum-investments-amount').text(fmt(totalAmt));
    $('#sum-applications-count').text(applications.length);

    // Render investments list
    function renderInvestments(){
      var $list = $('#inv-list').empty();
      if(!investments.length){ $('#inv-empty').show(); return; } else { $('#inv-empty').hide(); }
      investments.forEach(function(inv){
        // map investment_type to Arabic label
        function typeLabel(t){
          if(!t) return 'غير محدد';
          if(String(t).toLowerCase() === 'large') return 'استثمار كبير';
          if(String(t).toLowerCase() === 'small') return 'استثمار صغير';
          return t;
        }

        var card = $('<div/>').addClass('investment-card').append(
          $('<div/>').css({padding:'16px'}).append(
            $('<div/>').css({display:'grid',gridTemplateColumns:'repeat(7,1fr)',gap:'10px',alignItems:'center'}).append(
              cell('المبلغ', fmt(inv.amount)+' ريال', '#06b6d4', true),
              cell('نسبة الربح', (inv.profit_rate||0)+'%', '#fbbf24', true),
              cell('العائد المتوقع', fmt(inv.expected_return||0)+' ريال', '#22c55e', true),
              cell('نوع الاستثمار', typeLabel(inv.investment_type), '#a78bfa', false),
              (function(){
                // Payment + Details column: show Details button and Pay button or Paid label
                var $wrap = $('<div/>');
                var paid = inv.payment && Object.keys(inv.payment).length>0;
                var details = $('<a/>').addClass('btn-secondary').attr('href','/investment/'+inv.id).css({display:'inline-block',padding:'6px 8px',borderRadius:'8px',border:'1px solid #334155',color:'#cbd5e1',textDecoration:'none'}).text('تفاصيل');
                var $inner = $('<div/>').css({display:'flex',gap:'8px',alignItems:'center'});
                if(paid){
                  $inner.append(details);
                  $inner.append($('<div/>').append(badge('completed')));
                } else {
                  var $btn = $('<button/>').addClass('btn-primary pay-btn').css({padding:'8px 10px',borderRadius:'8px'}).text('ادفع الآن');
                  $btn.attr('data-id', inv.id);
                  $inner.append(details).append($btn);
                }
                $wrap.append($inner);
                return $wrap;
              })(),
              $('<div/>').append(
                $('<div/>').css({color:'#9ca3af',fontSize:'12px'}).text('الحالة'),
                $('<div/>').css({marginTop:'6px'}).append(badge(inv.status||''))
              ),
              cell('تاريخ الإنشاء', formatDate(inv.created_at), '#fff', false)
            )
          )
        );
        $list.append(card);
      });
    }

    // Payment modal HTML (insert once)
    var paymentModalHtml = '\
    <div id="payment-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;padding:24px;">\
      <div style="max-width:900px;margin:40px auto;background:#0b1220;border-radius:12px;padding:18px;">\
        <button id="payment-close" class="btn-secondary" style="float:right;border-color:#334155;color:#cbd5e1;">إغلاق</button>\
        <h2 style="font-size:20px;color:#fff;margin:6px 0 12px;">الدفع</h2>\
        <div id="pm-body">Loading...</div>\
      </div>\
    </div>';
    $('body').append(paymentModalHtml);
    // helper: render bank instructions into the modal based on fetched bank object
    function renderBankInstructionsInModal(bank){
      try{
        var $block = $('#pm-bank-modal');
        var $container = $block.find('#pm-bank-instructions');
        var $ul = $block.find('#bank-instructions-ul');
        if(!$container || !$container.length) return;

        // When API returns an object, it may be an active bank account or settings with 'instructions'
        var instructions = undefined;
        if(bank && typeof bank === 'object'){
          if(Object.prototype.hasOwnProperty.call(bank, 'instructions')) instructions = bank.instructions;
          else if(Object.prototype.hasOwnProperty.call(bank, 'bank') && bank.bank && Object.prototype.hasOwnProperty.call(bank.bank, 'instructions')) instructions = bank.bank.instructions;
        }

        // If instructions property exists and is an empty string -> hide entirely
        if(typeof instructions !== 'undefined' && instructions === ''){
          $container.hide();
          return;
        }

        // Determine lines to render
        var lines = [];
        // Show instructions only when admin provided a non-empty string. Otherwise hide the container.
        if (typeof instructions !== 'undefined' && instructions !== null && String(instructions).trim() !== ''){
          lines = String(instructions).split(/\r?\n/).map(function(s){ return s.trim(); }).filter(function(s){ return s.length>0; });
          if(!lines || !lines.length){ $container.hide(); return; }
          $ul.empty();
          lines.forEach(function(line){ $ul.append($('<li/>').html(line)); });
          $container.show();
        } else {
          $container.hide();
        }
      }catch(e){ console.warn('renderBankInstructionsInModal error', e); }
    }

    // Fetch bank settings and render into modal (safe fallback)
    function fetchAndRenderBankInstructions(){
      $.getJSON('/api/bank-settings').done(function(resp){
        // also populate IBAN in modal if provided
        try{
          if(resp && resp.iban){
            var fmt = function(iban){ try{ return String(iban||'').replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' ');}catch(e){ return iban||''; } };
            $('#bank-iban-modal').text(fmt(resp.iban));
          }
        }catch(e){}
        // resp may be an object with instructions or an active bank account
        renderBankInstructionsInModal(resp);
      }).fail(function(){
        // on fail, render defaults (pass undefined)
        renderBankInstructionsInModal(undefined);
      });
    }

    // Toast container for non-blocking notifications
    var toastHtml = '\
      <div id="app-toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:10000;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none;"></div>';
    $('body').append(toastHtml);

    function showToast(type, msg, timeout){
      timeout = timeout || 7000;
      var bg = type === 'success' ? '#16a34a' : (type === 'error' ? '#dc2626' : '#0ea5e9');
      var $t = $('<div/>').css({background:bg,color:'#fff',padding:'10px 14px',borderRadius:'8px',boxShadow:'0 6px 18px rgba(2,6,23,0.6)',maxWidth:'90%',pointerEvents:'auto'}).text(msg);
      $('#app-toast').append($t);
      setTimeout(function(){ $t.fadeOut(300,function(){ $t.remove(); }); }, timeout);
    }

    // Open payment modal and populate
    $(document).on('click', '.pay-btn', function(){
      var id = $(this).attr('data-id');
      var inv = investments.find(function(x){ return String(x.id) === String(id); });
  if(!inv) return showToast('error', 'الاستثمار غير موجود');
      // Build modal body copied from investment.blade.php payment step
      var body = $('<div/>').append(
        $('<div/>').addClass('glass-card').css({padding:'12px',marginBottom:'12px'}).append(
          $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'center'}).append(
            $('<div/>').css({color:'#9ca3af',fontSize:'12px'}).text('المبلغ المستحق'),
            $('<div/>').attr('id','pay-amount-modal').css({color:'#06b6d4',fontWeight:700}).text(fmt(inv.amount)+' ريال')
          ),
          $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'center',marginTop:'6px'}).append(
            $('<div/>').css({color:'#9ca3af',fontSize:'12px'}).text('العائد المتوقع'),
            $('<div/>').attr('id','pay-return-modal').css({color:'#22c55e',fontWeight:700}).text(fmt(inv.expected_return||0)+' ريال')
          )
        ),
        $('<div/>').attr('id','pm-methods').css({display:'flex',gap:'8px',flexWrap:'wrap',marginBottom:'12px'}).append(
          $('<button/>').addClass('pm-btn').attr('data-method','bank').css({flex:1,border:'1px solid #4b5563',background:'#111827',color:'#fff',padding:'10px',borderRadius:'8px'}).text('تحويل بنكي')
        ),
        $('<div/>').attr('id','pm-bank-modal').addClass('glass-card').css({padding:'12px',display:'none',color:'#cbd5e1',fontSize:'14px',marginTop:'12px'}).append(
          $('<div/>').css({display:'grid',gap:'10px'}).append(
            $('<div/>').css({display:'grid',gap:'8px'}).append(
              $('<div/>').css({fontWeight:700,color:'#fff'}).text('تفاصيل التحويل البنكي'),
              $('<div/>').css({display:'grid',gridTemplateColumns:'1fr 1fr',gap:'10px'}).append(
                $('<div/>').addClass('glass-card').css({padding:'10px'}).append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('اسم الحساب'),$('<div/>').css({color:'#e5e7eb',fontWeight:600}).text('شركة الاستثمار القابضة')),
                $('<div/>').addClass('glass-card').css({padding:'10px'}).append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('البنك'),$('<div/>').css({color:'#e5e7eb',fontWeight:600}).text('مصرف الراجحي')),
                $('<div/>').addClass('glass-card').css({padding:'10px'}).append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('رقم الحساب'),$('<div/>').css({color:'#e5e7eb',fontWeight:600}).text('1234567890123')),
                $('<div/>').addClass('glass-card').css({padding:'10px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'8px'}).append(
                  $('<div/>').append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('الآيبان (IBAN)'),$('<div/>').attr('id','bank-iban-modal').css({color:'#06b6d4',fontWeight:700}).text('SA12 3456 7890 1234 5678 9012')),
                  $('<button/>').attr('type','button').addClass('btn-secondary copy-btn').attr('data-copy-target','#bank-iban-modal').css({borderColor:'#64748b',color:'#cbd5e1',whiteSpace:'nowrap'}).text('نسخ')
                ),
                $('<div/>').addClass('glass-card').css({padding:'10px'}).append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('SWIFT / BIC'),$('<div/>').css({color:'#e5e7eb',fontWeight:600}).text('RJHISARI')),
                $('<div/>').addClass('glass-card').css({padding:'10px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'8px'}).append(
                  $('<div/>').append($('<div/>').css({fontSize:'12px',color:'#9ca3af'}).text('رمز مرجع العملية'),$('<div/>').attr('id','bank-ref-modal').css({color:'#fbbf24',fontWeight:700}).text('INV-XXXXXX')),
                  $('<button/>').attr('type','button').addClass('btn-secondary copy-btn').attr('data-copy-target','#bank-ref-modal').css({borderColor:'#64748b',color:'#cbd5e1',whiteSpace:'nowrap'}).text('نسخ')
                )
              )
            ),
            // Bank instructions container (will be filled dynamically from /api/bank-settings)
            $('<div/>').addClass('glass-card').css({padding:'10px'}).attr('id','pm-bank-instructions').append(
              $('<div/>').css({fontSize:'13px',color:'#9ca3af'}).text('التعليمات'),
              // ul is intentionally empty here; JS will populate and hide/show the whole container
              $('<ul/>').attr('id','bank-instructions-ul').css({margin:'8px 0 0 0',paddingRight:'18px',listStyle:'disc',color:'#cbd5e1'})
            )
          )
        ),
        $('<div/>').addClass('glass-card').css({padding:'10px',display:'grid',gap:'10px'}).append(
          $('<div/>').css({display:'grid',gridTemplateColumns:'1fr 1fr',gap:'10px'}).append(
            $('<div/>').append($('<label/>').css({display:'block',color:'#cbd5e1',fontSize:'13px',marginBottom:'6px'}).text('اسم المحوِّل'),$('<input/>').attr('id','bank-sender-name-modal').attr('placeholder','الاسم كما في الحساب').css({width:'100%',background:'#1f2937',border:'1px solid #4b5563',color:'#fff',padding:'10px',borderRadius:'8px'})),
            $('<div/>').append($('<label/>').css({display:'block',color:'#cbd5e1',fontSize:'13px',marginBottom:'6px'}).text('تاريخ ووقت التحويل'),$('<input/>').attr('id','bank-date-modal').attr('type','datetime-local').css({width:'100%',background:'#1f2937',border:'1px solid #4b5563',color:'#fff',padding:'10px',borderRadius:'8px'}))
          ),
          $('<div/>').append($('<label/>').css({display:'block',color:'#cbd5e1',fontSize:'13px',marginBottom:'6px'}).text('ملاحظة (اختياري)'),$('<input/>').attr('id','bank-note-modal').attr('placeholder','أي تفاصيل إضافية').css({width:'100%',background:'#1f2937',border:'1px solid #4b5563',color:'#fff',padding:'10px',borderRadius:'8px'})),
          $('<div/>').append($('<label/>').css({display:'block',color:'#cbd5e1',fontSize:'13px',marginBottom:'6px'}).text('إرفاق إيصال التحويل (صورة أو PDF)'),$('<input/>').attr('id','bank-receipt-modal').attr('type','file').attr('accept','image/*,.pdf').css({width:'100%',background:'#1f2937',border:'1px solid #4b5563',color:'#fff',padding:'10px',borderRadius:'8px'}),$('<div/>').attr('id','bank-receipt-name-modal').css({fontSize:'12px',color:'#9ca3af',marginTop:'6px'}),$('<div/>').attr('id','bank-error-modal').css({display:'none',color:'#ffb4b4',fontSize:'12px',marginTop:'6px'}))
        ),
        $('<div/>').css({display:'flex',gap:'10px',marginTop:'16px'}).append($('<button/>').attr('id','payment-back-modal').addClass('btn-secondary').css({borderColor:'#64748b',color:'#cbd5e1'}).text('رجوع'),$('<button/>').attr('id','pay-later-modal').addClass('btn-secondary').css({borderColor:'#94a3b8',color:'#cbd5e1'}).text('تحويل لاحقاً'),$('<button/>').attr('id','pay-now-modal').addClass('btn-primary').css({flex:1}).text('تاكيد التحويل'))
      );

      $('#pm-body').empty().append(body);

      // set initial bank-ref unique value
      function genRef(){ return 'TX-'+Math.random().toString(36).substr(2,9).toUpperCase(); }
    $('#bank-ref-modal').text(genRef());

    $('#payment-modal').show();
    // Fetch current bank settings and render instructions (will hide if admin set empty string)
    fetchAndRenderBankInstructions();

      // handlers inside modal
      $('#pm-methods .pm-btn').on('click', function(){
        var m = $(this).data('method');
        if(m === 'bank'){
          $('#pm-bank-modal').show();
        }
      });

      // copy buttons
      $(document).on('click', '.copy-btn', function(){
        var tgt = $($(this).data('copy-target'));
        if(!tgt || !tgt.length) return;
        var txt = tgt.text();
        if((tgt.attr('id')||'').toLowerCase().indexOf('iban') !== -1){ txt = txt.replace(/\s+/g,''); }
        navigator.clipboard && navigator.clipboard.writeText(txt).then(function(){ /* copied */ }, function(){ showToast('error', 'نسخ فشل'); });
      });

      // file name display and simple validation (10MB)
      $('#bank-receipt-modal').on('change', function(){
        var f = this.files && this.files[0];
        if(!f){ $('#bank-receipt-name-modal').text(''); return; }
        if(f.size > 10240*1024){ $('#bank-error-modal').show().text('حجم الإيصال يجب أن لا يتجاوز 10 ميجابايت'); return; }
        $('#bank-error-modal').hide(); $('#bank-receipt-name-modal').text(f.name);
      });

      // close - also clear modal inputs
      function clearPaymentModal(){
        $('#bank-sender-name-modal').val('');
        $('#bank-date-modal').val('');
        $('#bank-note-modal').val('');
        $('#bank-receipt-modal').val('');
        $('#bank-receipt-name-modal').text('');
        $('#bank-error-modal').hide().text('');
        // regenerate a fresh ref for next time
        $('#bank-ref-modal').text(genRef());
      }

      $('#payment-close, #payment-back-modal, #pay-later-modal').off('click').on('click', function(){
        $('#payment-modal').hide();
        clearPaymentModal();
      });

      // submit payment
      $('#pay-now-modal').off('click').on('click', function(){
        // prevent submission if there is a visible file error
        if($('#bank-error-modal').is(':visible')){
          showToast('error', 'يرجى تصحيح مشاكل الإيصال قبل الإرسال');
          return;
        }

        var fd = new FormData();
        fd.append('investment_id', inv.id);
        fd.append('amount', inv.amount);
        fd.append('payment_method', 'bank');
        fd.append('bank_sender_name', $('#bank-sender-name-modal').val()||'');
        fd.append('bank_date', $('#bank-date-modal').val()||'');
        fd.append('note', $('#bank-note-modal').val()||'');

        // ensure we don't submit placeholder INV-XXXXXX; generate a fresh reference if needed
        var tx = ($('#bank-ref-modal').text()||'').trim();
        if(!tx || /^INV-?/i.test(tx)) tx = genRef();
        fd.append('transaction_reference', tx);

        var fileEl = $('#bank-receipt-modal')[0];
        if(fileEl && fileEl.files && fileEl.files[0]){
          var f = fileEl.files[0];
          // double-check size before appending
          if(f.size > 10240*1024){
            $('#bank-error-modal').show().text('حجم الإيصال يجب أن لا يتجاوز 10 ميجابايت');
            showToast('error', 'حجم الإيصال يجب أن لا يتجاوز 10 ميجابايت');
            return;
          }
          fd.append('receipt', f);
        }

        // show loading
        $('#pay-now-modal').prop('disabled', true).text('جاري الارسال...');

        // send via AJAX to server endpoint (use same route as investment.blade.php pay)
        $.ajax({
          url: '/investment/pay',
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function(res){
            $('#pay-now-modal').prop('disabled', false).text('تاكيد التحويل');
            if(res && res.success){
              // mark local investment as paid
              inv.payment = res.payment || { transaction_reference: tx };
              // persist to localStorage if used
              try { localStorage.setItem('sg_investments', JSON.stringify(investments)); } catch(e){}
              renderInvestments();
              // hide and clear modal inputs for next use
              $('#payment-modal').hide();
              clearPaymentModal();
              showToast('success', 'تم ارسال بيانات الدفع بنجاح، سيتم التأكيد خلال 24 ساعة');
            } else {
              showToast('error', (res && res.message) || 'حدث خطأ أثناء ارسال بيانات الدفع');
            }
          },
          error: function(xhr){
            $('#pay-now-modal').prop('disabled', false).text('تاكيد التحويل');
            var msg = 'فشل الاتصال بالخادم';
            try { var j = xhr.responseJSON; if(j && j.message) msg = j.message; } catch(e){}
            showToast('error', msg);
          }
        });
      });
    });

    // modal close via top-level click
    $(document).on('click', '#payment-close', function(){ $('#payment-modal').hide(); });

    function cell(label, value, color, bold){
      var vStyle = {color:color, fontWeight:bold?800:600, fontSize:'18px'};
      return $('<div/>').append(
        $('<div/>').css({color:'#9ca3af',fontSize:'12px'}).text(label),
        $('<div/>').css(vStyle).text(value)
      );
    }

    // Render applications list
    function renderApplications(){
      var $list = $('#apps-list').empty();
      if(!applications.length){ $('#apps-empty').show(); return; } else { $('#apps-empty').hide(); }
      applications.forEach(function(app){
        var card = $('<div/>').addClass('investment-card').append(
          $('<div/>').css({padding:'16px'}).append(
            $('<div/>').css({display:'grid',gridTemplateColumns:'repeat(4,1fr)',gap:'10px',alignItems:'center'}).append(
              cell('رقم الطلب', (String(app.id||'').slice(0,8).toUpperCase()), '#06b6d4', true),
              cell('اسم النشاط', app.business_name||'', '#fff', false),
              cell('نوع النشاط', app.business_type||'', '#fff', false),
              $('<div/>').append(
                $('<div/>').css({color:'#9ca3af',fontSize:'12px'}).text('الحالة'),
                $('<div/>').css({marginTop:'6px'}).append(badge(app.status||'')),
                $('<div/>').css({color:'#94a3b8',fontSize:'12px',marginTop:'6px'}).text(formatDate(app.created_at))
              )
            )
          )
        );
        $list.append(card);
      });
    }

    renderInvestments();
    renderApplications();

    // Tabs toggle
    $('.dash-trigger').on('click', function(){
      var tab = $(this).data('tab');
      $('.dash-trigger').removeClass('active').css({background:'transparent', color:'#cbd5e1', border:'1px solid #334155'});
      $(this).addClass('active').css({background:'#22d3ee', color:'#000', border:'0'});
      $('.dash-content').hide();
      $('#dash-'+tab).show();
    });
  });
</script>
@endsection