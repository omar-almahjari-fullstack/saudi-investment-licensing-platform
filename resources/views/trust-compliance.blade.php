@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">مركز الثقة والامتثال</li>
            </ol>
        </nav>
    </div>
</div>

<div style="margin-left:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">مركز الثقة والامتثال</h1>
      <p style="color:#cbd5e1;font-size:18px;">الشفافية والأمان في جميع عملياتنا الاستثمارية</p>
    </div>
  </header>

  {{-- Data comes from controller: $regulations, $security, $risks, $certs, $audits --}}

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <!-- Trust Indicators (from DB) -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
        @php
          $sec = data_get($indicators, 'security_rate', ['icon'=>'🛡️','value'=>'96%','label'=>'معدل الأمان']);
          $lic = data_get($indicators, 'licenses_count', ['icon'=>'📜','value'=>'5202+','label'=>'تراخيص رسمية']);
          $cert = data_get($indicators, 'certificates_count', ['icon'=>'🏆','value'=>'3524+','label'=>'شهادات اعتماد']);
          $comp = data_get($indicators, 'compliance_rate', ['icon'=>'✅','value'=>'98%','label'=>'معدل الامتثال']);
        @endphp

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($sec,'icon') }}</div>
          <div style="font-size:24px;color:#22c55e;font-weight:800;">{{ data_get($sec,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($sec,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($lic,'icon') }}</div>
          <div style="font-size:24px;color:#22d3ee;font-weight:800;">{{ data_get($lic,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($lic,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($cert,'icon') }}</div>
          <div style="font-size:24px;color:#fbbf24;font-weight:800;">{{ data_get($cert,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($cert,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($comp,'icon') }}</div>
          <div style="font-size:24px;color:#22c55e;font-weight:800;">{{ data_get($comp,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($comp,'label') }}</div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="glass-card" style="padding:12px;">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px;background:#1f2937;border-radius:10px;padding:6px;">
          <button class="tc-trigger active" data-tab="reg" style="padding:8px;border-radius:8px;border:0;background:#22d3ee;color:#000;font-weight:700;">اللوائح والتراخيص</button>
          <button class="tc-trigger" data-tab="sec" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الأمان والحماية</button>
          <button class="tc-trigger" data-tab="trans" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الشفافية</button>
          <button class="tc-trigger" data-tab="cert" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الشهادات والاعتمادات</button>
        </div>

        <!-- Regulations -->
        <div class="tc-content" id="tc-reg">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            @foreach($regulations as $i => $r)
              <div class="investment-card" style="padding:12px;" data-testid="regulation-card-{{ $i }}">
                <div style="display:flex;align-items:center;margin-bottom:8px;gap:8px;">
                  <div style="font-size:24px;">{{ data_get($r, 'icon', '') }}</div>
                  <div style="color:#fff;font-weight:700;">{{ data_get($r, 'title', '') }}</div>
                </div>
                <div style="color:#94a3b8;font-size:12px;margin-bottom:8px;">{{ data_get($r, 'name_en', '') }}</div>
                <div style="color:#cbd5e1;font-size:12px;margin-bottom:8px;">{{ data_get($r, 'license', '') }}</div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                  <span style="background:#22c55e;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;">{{ data_get($r, 'status', '') }}</span>
                  <span style="color:#94a3b8;font-size:12px;">حتى: {{ data_get($r, 'valid_until', '') }}</span>
                </div>
                @php
                  // Prefer stored file_path (from DB). If it's an absolute URL use it directly, otherwise generate storage URL.
                  $fileUrl = null;
                  if (!empty($r->file_path)) {
                      if (preg_match('/^https?:\/\//i', $r->file_path)) { $fileUrl = $r->file_path; }
                      else { $fileUrl = \Illuminate\Support\Facades\Storage::url($r->file_path); }
                  }
                @endphp
                @if(!empty($fileUrl))
                  <button type="button" class="btn-secondary tc-open-cert" hidden data-url="{{ $fileUrl }}" style="width:100%;border-color:#22d3ee;color:#22d3ee;">عرض الشهادة</button>
                    @else
                  <span style="color:#f87171;font-size:12px;"></span>
                    @endif
              </div>
            @endforeach
          </div>
        </div>

        <!-- Security -->
        <div class="tc-content" id="tc-sec" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:12px;">
            @foreach($security as $i => $f)
              <div class="glass-card" style="padding:16px;" data-testid="security-feature-{{ $i }}">
                <div style="display:flex;gap:12px;align-items:flex-start;">
                  <div style="font-size:28px;">{{ data_get($f, 'icon', '') }}</div>
                  <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                      <h3 style="color:#fff;font-weight:800;">{{ data_get($f, 'title', '') }}</h3>
                      <span style="background:#22c55e;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;">{{ data_get($f, 'level', '') }}</span>
                    </div>
                    <p style="color:#cbd5e1;">{{ data_get($f, 'description', '') }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Risk Management -->
          <div class="glass-card" style="padding:16px;">
            <h3 style="color:#fff;margin-bottom:10px;">إدارة المخاطر</h3>
            <div style="display:grid;gap:10px;">
              @foreach($risks as $rl)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:rgba(31,41,55,.5);border-radius:10px;">
                  <div style="display:flex;gap:10px;align-items:center;">
                    <div style="width:16px;height:16px;border-radius:50%;background:{{ data_get($rl, 'color', '#000') }};"></div>
                    <div>
                      <div style="color:#fff;font-weight:700;">مخاطر {{ data_get($rl, 'level', '') }}</div>
                      <div style="color:#94a3b8;font-size:12px;">{{ data_get($rl, 'description', '') }}</div>
                    <script>
                      // Open certificate links in a new tab while preserving original button styling
                      document.addEventListener('click', function(e){
                        var t = e.target;
                        if(!t) return;
                        if(t.classList && t.classList.contains('tc-open-cert')){
                          var url = t.getAttribute('data-url');
                          if(url){ window.open(url, '_blank'); }
                        }
                      }, false);
                    </script>

                    </div>
                  </div>
                  <div style="color:#22d3ee;font-weight:800;">{{ data_get($rl, 'percentage_text') ?: data_get($rl, 'percentage', '') }}</div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Transparency -->
        <div class="tc-content" id="tc-trans" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div class="glass-card" style="padding:16px;">
              <h3 style="color:#fff;margin-bottom:8px;">الشفافية المالية</h3>
              <div style="display:grid;gap:8px;">
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">إجمالي الأصول المدارة</span>
                  <span style="color:#22d3ee;font-weight:800;">2.5 مليار ريال</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">عدد المستثمرين النشطين</span>
                  <span style="color:#22d3ee;font-weight:800;">15,000+ مستثمر</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">متوسط العوائد السنوية</span>
                  <span style="color:#22c55e;font-weight:800;">38.5%</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">معدل نجاح الاستثمارات</span>
                  <span style="color:#22c55e;font-weight:800;">98.2%</span>
                </div>
              </div>
            </div>

            <div class="glass-card" style="padding:16px;">
              <h3 style="color:#fff;margin-bottom:8px;">تقارير المراجعة</h3>
              <div style="display:grid;gap:8px;">
                @foreach($audits as $r)
                  <div style="padding:10px;background:rgba(31,41,55,.5);border-radius:10px;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                      <div style="color:#fff;font-weight:600;font-size:14px;">{{ data_get($r, 'title', '') }}</div>
                      <div style="color:#94a3b8;font-size:12px;">{{ data_get($r, 'date', '') }} • {{ data_get($r, 'file_size', '') }}</div>
                    </div>

                    @if($r->file_path)
                        <a href="storage/{{ $r->file_path }}" target="_blank" class="btn-primary" style="display:inline-block;margin-top:8px;">عرض التقرير</a>
                    @else
                      <span style="color:#f87171;font-size:12px;"></span>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <!-- Certifications -->
        <div class="tc-content" id="tc-cert" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            @foreach($certs as $i => $c)
              <div class="investment-card" style="padding:12px;" data-testid="certification-card-{{ $i }}">
                <div style="text-align:center;margin-bottom:8px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ data_get($c, 'name', '') }}</h3>
                  <div style="color:#94a3b8;font-size:12px;">{{ data_get($c, 'description', '') }}</div>
                </div>
                <div style="text-align:center;margin-bottom:8px;">
                  <div style="font-size:40px;">🏆</div>
                  <span style="background:#22c55e;color:#fff;border-radius:8px;padding:2px 8px;font-size:12px;">{{ data_get($c, 'status', '') }}</span>
                </div>
                <div style="display:grid;gap:6px;font-size:12px;color:#cbd5e1;">
                  <div style="display:flex;justify-content:space-between;"><span>تاريخ الإصدار:</span><span>{{ data_get($c, 'issued_at', '') }}</span></div>
                  <div style="display:flex;justify-content:space-between;"><span>ساري حتى:</span><span>{{ data_get($c, 'expires_at', '') }}</span></div>
                </div>
                @php
                  $fileUrl = null;
                  if (!empty($c->file_path)) {
                      if (preg_match('/^https?:\/\//i', $c->file_path)) { $fileUrl = $c->file_path; }
                      else { $fileUrl = \Illuminate\Support\Facades\Storage::url($c->file_path); }
                  }
                @endphp
             @if($fileUrl)
                  <button type="button" class="btn-secondary tc-open-cert" data-url="{{ $fileUrl }}" style="width:100%;margin-top:8px;border-color:#22d3ee;color:#22d3ee;">عرض الشهادة</button>
                @else
                  <span style="color:#f87171;font-size:12px;"></span>
                @endif 
            
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Contact Compliance -->
      <div class="glass-card" style="padding:20px;margin-top:18px;text-align:center;">
        <h3 style="color:#fff;font-size:22px;margin-bottom:8px;font-weight:800;">تواصل مع فريق الامتثال</h3>
        <p style="color:#cbd5e1;margin-bottom:12px;">لديك استفسارات حول الامتثال أو الأمان؟ تواصل مع فريقنا المتخصص</p>
        @php
          $waRaw = isset($site_contact) ? ($site_contact['whatsapp'] ?? $site_contact['phone'] ?? '') : '';
          $waNumber = preg_replace('/[^0-9]/', '', $waRaw ?: '');
        @endphp
        <div style="display:flex;gap:10px;justify-content:center;">
          <button class="btn-primary" type="button" onclick="(function(){
              var msg = 'مرحباً، لدي استفسار حول الامتثال.';
              try{
                if(window.openWhatsAppHidden && typeof window.openWhatsAppHidden === 'function'){
                  window.openWhatsAppHidden(msg);
                  return;
                }
              }catch(e){}
              // Fallback: open wa.me directly with sanitized site contact
              var url = 'https://wa.me/{{ $waNumber }}?text=' + encodeURIComponent(msg);
              window.open(url, '_blank', 'noopener');
          })()">اتصل بفريق الامتثال</button>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(function(){
    $('.tc-trigger').on('click', function(){
      var tab = $(this).data('tab');
      $('.tc-trigger').removeClass('active').css({background:'transparent', color:'#cbd5e1', border:'1px solid #334155'});
      $(this).addClass('active').css({background:'#22d3ee', color:'#000', border:'0'});
      $('.tc-content').hide();
      $('#tc-'+tab).show();
    });
  });
</script>
@endsection
