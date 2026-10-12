@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">تحميل المستندات</li>
            </ol>
        </nav>
    </div>
</div>

<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">تحميل المستندات</h1>
      <p style="color:#cbd5e1;font-size:18px;margin-bottom:16px;">قم برفع المستندات المطلوبة لاستكمال عملية الاستثمار</p>

      <!-- Upload Progress Summary -->
      <div class="glass-card" style="padding:16px;margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
          <h3 style="color:#fff;margin:0;font-size:18px;font-weight:700;">تقدم الرفع</h3>
          <span id="summary-badge" style="background:#22d3ee;color:#000;border-radius:8px;padding:4px 10px;font-weight:700;">0/0 مكتمل</span>
        </div>
        <div style="height:12px;background:#1f2937;border-radius:8px;overflow:hidden;">
          <div id="summary-progress" style="height:12px;background:#22d3ee;width:0%"></div>
        </div>
        <p id="summary-text" style="color:#9ca3af;font-size:12px;margin-top:8px;">0 من 0 مستندات تم رفعها</p>
      </div>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <!-- Required Documents -->
      <div style="margin-bottom:18px;">
        <h2 style="color:#fff;font-size:22px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="#ef4444"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          المستندات المطلوبة
        </h2>
        <div id="required-list" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"></div>
      </div>

      <!-- Optional Documents -->
      <div>
        <h2 style="color:#fff;font-size:22px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="#60a5fa"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
          المستندات الاختيارية
        </h2>
        <div id="optional-list" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"></div>
      </div>

      <!-- Submit Button -->
      <div style="text-align:center;margin-top:18px;">
        <button id="submit-documents" class="btn-primary" style="font-size:16px;padding:12px 32px;" disabled>إرسال المستندات للمراجعة</button>
        <p style="color:#9ca3af;font-size:12px;margin-top:6px;">يجب رفع جميع المستندات المطلوبة قبل الإرسال</p>
      </div>
  
      <!-- Submission success modal -->
      <div id="submit-success-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#0b1220;padding:20px;border-radius:12px;max-width:420px;margin:0 auto;color:#fff;text-align:center;">
          <h3 style="margin-top:0;color:#22d3ee">تم الإرسال</h3>
          <p id="submit-success-text" style="color:#cbd5e1">تم إرسال المستندات بنجاح. سيتم المراجعة خلال 24 ساعة.</p>
          <div style="display:flex;gap:8px;justify-content:center;margin-top:16px;">
            <a id="return-home" class="btn-primary" href="/" style="padding:10px 16px;">العودة إلى الصفحة الرئيسية</a>
            <button id="close-success-modal" class="btn-secondary" style="padding:10px 16px;border-color:#64748b;color:#cbd5e1;">إغلاق</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(function(){
    var requiredDocuments = [
      { id:'identity_front', name:'صورة الهوية (الوجه الأمامي)', description:'صورة واضحة للوجه الأمامي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' },
      { id:'identity_back',  name:'صورة الهوية (الوجه الخلفي)', description:'صورة واضحة للوجه الخلفي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' },
      { id:'bank_statement',  name:'كشف حساب بنكي', description:'كشف حساب بنكي حديث (آخر 3 أشهر)', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '10 MB', icon: '🏦' },
      { id:'salary_certificate', name:'شهادة راتب', description:'شهادة راتب أو إثبات دخل من جهة العمل', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '5 MB', icon: '💼' }
    ];
    var optionalDocuments = [
      { id:'additional_income', name:'إثبات دخل إضافي', description:'وثائق تثبت مصادر دخل إضافية (اختياري)', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '10 MB', icon: '💰' },
      { id:'investment_experience', name:'خبرة استثمارية سابقة', description:'شهادات أو وثائق تثبت الخبرة الاستثمارية (اختياري)', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '10 MB', icon: '📊' }
    ];

    var uploadedFiles = {};   // {id: {name,size,type,uploadDate,status}}
    var uploadProgress = {};  // {id: percent}

    function renderList($container, docs, isRequired){
      $container.empty();
      docs.forEach(function(doc){
        var card = $('<div/>').addClass('glass-card').css({padding:'14px'}).attr('data-doc', doc.id);
        var header = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var title = $('<div/>').css({color:'#fff',fontWeight:'700'}).html('<span style="font-size:20px;margin-left:8px;">'+doc.icon+'</span>'+doc.name);
        var badge = isRequired ? $('<span/>').text('مطلوب').css({background:'#ef4444',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}) : null;
        header.append(title); if(badge) header.append(badge);

        var desc = $('<p/>').text(doc.description).css({color:'#cbd5e1',fontSize:'12px',margin:'6px 0'});
        var meta = $('<div/>').css({display:'flex',gap:'12px',color:'#9ca3af',fontSize:'12px'}).append(
          $('<span/>').text('الأنواع المقبولة: '+doc.acceptedTypes),
          $('<span/>').text('الحد الأقصى: '+doc.maxSize)
        );

        var body = $('<div/>').attr('data-body', doc.id);
        var inputId = 'file_'+doc.id;
        var input = $('<input/>').attr({type:'file',id:inputId,accept:doc.acceptedTypes.replace(/\s/g,'')}).css({display:'none'});
        var choose = $('<label/>').attr('for',inputId).css({display:'flex',alignItems:'center',justifyContent:'center',width:'100%',padding:'24px',border:'2px dashed #4b5563',borderRadius:'10px',cursor:'pointer'}).hover(function(){ $(this).css('borderColor','#22d3ee'); }, function(){ $(this).css('borderColor','#4b5563'); });
        choose.append($('<div/>').css({textAlign:'center'}).append(
          $('<svg/>').attr({width:48,height:48,viewBox:'0 0 24 24'}).css({display:'block',margin:'0 auto 8px',color:'#9ca3af'}).html('<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>'),
          $('<div/>').css({color:'#cbd5e1',fontWeight:600}).text('اختيار ملف'),
          $('<p/>').css({color:'#9ca3af',fontSize:'12px',marginTop:'4px'}).text('اسحب وأفلت الملف هنا أو انقر للاختيار')
        ));

        var progressWrap = $('<div/>').attr('data-progress', doc.id).css({display:'none'});
        var progTop = $('<div/>').css({display:'flex',justifyContent:'space-between',fontSize:'12px',color:'#cbd5e1',margin:'6px 0'}).append(
          $('<span/>').text('تقدم الرفع'),
          $('<span/>').attr('data-prog-text', doc.id).css({color:'#22d3ee'}).text('0%')
        );
        var progBar = $('<div/>').css({height:'8px',background:'#1f2937',borderRadius:'6px',overflow:'hidden'}).append(
          $('<div/>').attr('data-prog-bar', doc.id).css({height:'8px',background:'#22d3ee',width:'0%'})
        );
        progressWrap.append(progTop, progBar);

        var fileInfoWrap = $('<div/>').attr('data-info', doc.id).css({display:'none',background:'#1f2937',padding:'12px',borderRadius:'10px'});
        var fileHeader = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var fileTitle = $('<div/>');
        var fileName = $('<h4/>').css({color:'#fff',margin:'0 0 4px 0'});
        var fileMeta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'});
        var statusBadge = $('<span/>').attr('data-status', doc.id).css({background:'#22c55e',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}).text('مكتمل');
        fileHeader.append(fileTitle.append(fileName, fileMeta), statusBadge);
        var actions = $('<div/>').css({display:'flex',gap:'8px'});
        var viewBtn = $('<button/>').text('عرض الملف').addClass('btn-secondary').css({borderColor:'#22d3ee',color:'#22d3ee'}).on('click', function(){
          var f = uploadedFiles[doc.id]; if(!f) return; var url = f.url || (f.file ? URL.createObjectURL(f.file) : null); if(!url) return; window.open(url, '_blank');
        });
        var removeBtn = $('<button/>').text('إزالة الملف').addClass('btn-secondary').css({borderColor:'#f87171',color:'#f87171'}).on('click', function(){ removeFile(doc.id); });
        actions.append(viewBtn, removeBtn);
        fileInfoWrap.append(fileHeader, actions);

        body.append(input, choose, progressWrap, fileInfoWrap);
        card.append(header, desc, meta, body);
        $container.append(card);

        input.on('change', function(e){ handleFileUpload(doc.id, e.target.files[0]); });
      });
    }

    function handleFileUpload(documentId, file){
      if(!file) return;
      uploadProgress[documentId] = 0;
      var $progWrap = $('[data-progress="'+documentId+'"]');
      var $progText = $('[data-prog-text="'+documentId+'"]');
      var $progBar  = $('[data-prog-bar="'+documentId+'"]');
      var $infoWrap = $('[data-info="'+documentId+'"]');
      var $body     = $('[data-body="'+documentId+'"]');

      $progWrap.show();

      var interval = setInterval(function(){
        var curr = uploadProgress[documentId] || 0;
        var next = curr + 10;
        if(next >= 100){
          next = 100;
          clearInterval(interval);
          // When simulation completes, upload file to server
          var fd = new FormData();
          var userId = window.APP_USER_ID || $('meta[name="user-id"]').attr('content') || '';
          // include the document "type" (the field id) so the server can record which
          // of the six document slots this file belongs to (e.g. identity_front)
          // send user_id only when present; prefer server session id otherwise
          if (userId) { fd.append('user_id', String(userId)); }
          fd.append('type', documentId);
          fd.append('file', file);
          var token = $('meta[name="csrf-token"]').attr('content');
          $.ajax({
            url: '/documents/upload',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': token },
            success: function(res){
              var sizeMB = (file.size / 1024 / 1024).toFixed(2) + ' MB';
              uploadedFiles[documentId] = {
                file: file,
                name: file.name,
                size: sizeMB,
                type: file.type,
                uploadDate: new Date().toLocaleDateString('ar-SA'),
                status: 'completed',
                url: res.url,
                path: res.path,
                id: res.id
              };
              fillFileInfo(documentId);
              $progWrap.hide();
            },
            error: function(xhr){
              var msg = 'فشل رفع الملف';
              try {
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr && xhr.responseText) {
                  // try parse fallback
                  var j = JSON.parse(xhr.responseText || '{}'); if (j.message) msg = j.message;
                }
              } catch(e) { /* ignore parse errors */ }
              alert(msg);
              console.error('Upload error', xhr);
              $progWrap.hide();
            }
          });
        }
        uploadProgress[documentId] = next;
        $progText.text(next + '%');
        $progBar.css('width', next + '%');
        updateSummary();
      }, 200);
    }

    function removeFile(documentId){
      delete uploadedFiles[documentId];
      delete uploadProgress[documentId];
      var $body = $('[data-body="'+documentId+'"]');
      $body.find('[data-info]')  .hide();
      $body.find('[data-progress]').hide();
      $body.find('label[for="file_'+documentId+'"]').show();
      $body.find('input[type=file]')[0].value = '';
      updateSummary();
    }

    function fillFileInfo(documentId){
      var info = uploadedFiles[documentId];
      if(!info) return;
      var $body = $('[data-body="'+documentId+'"]');
      $body.find('label[for="file_'+documentId+'"]').hide();
      var $infoWrap = $body.find('[data-info]');
      $infoWrap.find('h4').text(info.name);
      $infoWrap.find('div').eq(2); // placeholder no-op
      var $meta = $infoWrap.find('div').eq(0).find('div').eq(1);
      // If structure changes, fallback fill directly:
      $infoWrap.find('h4').next('div').remove();
      var meta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'}).append(
        $('<span/>').text('حجم الملف: ' + info.size),
        $('<span/>').text('تاريخ الرفع: ' + info.uploadDate)
      );
      $infoWrap.find('h4').after(meta);
      $infoWrap.show();
    }

    function updateSummary(){
      var completedUploads = Object.keys(uploadedFiles).length;
      var totalDocs = requiredDocuments.length + optionalDocuments.length;
      var percent = totalDocs ? Math.round((completedUploads / totalDocs) * 100) : 0;
      $('#summary-badge').text(completedUploads + '/' + totalDocs + ' مكتمل');
      $('#summary-progress').css('width', percent + '%');
      $('#summary-text').text(completedUploads + ' من ' + totalDocs + ' مستندات تم رفعها');

      var requiredOk = requiredDocuments.every(function(d){ return !!uploadedFiles[d.id]; });
      $('#submit-documents').prop('disabled', !requiredOk);
    }

    // Handle submit button click: POST to /documents/submit and show Arabic success/failure
    $('#submit-documents').on('click', function(){
      var requiredOk = requiredDocuments.every(function(d){ return !!uploadedFiles[d.id]; });
      if (!requiredOk) {
        alert('يجب رفع جميع المستندات المطلوبة قبل الإرسال');
        return;
      }
      var token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
        url: '/documents/submit',
        method: 'POST',
        data: {},
        headers: { 'X-CSRF-TOKEN': token },
        success: function(res){
          if (res && res.ok) {
            var msg = res.message || 'تم الإرسال. سيتم المراجعة خلال 24 ساعة.';
            // disable button to prevent duplicate submissions
            $('#submit-documents').prop('disabled', true).text('تم الإرسال');
            // show modal with return-home button
            $('#submit-success-text').text(msg);
            $('#submit-success-modal').css('display','flex');
            // also navigate to dashboard documents tab after a short delay (keeps modal visible briefly)
            setTimeout(function(){ window.location.href = '/investor/dashboard?tab=documents'; }, 2000);
          } else {
            alert(res.message || 'حدث خطأ');
          }
        },
        error: function(xhr){
          var msg = 'فشل الإرسال';
          try { var j = xhr.responseJSON || JSON.parse(xhr.responseText || '{}'); if (j.message) msg = j.message; } catch(e) {}
          // show modal with error message (reuse modal UI)
          $('#submit-success-text').text(msg);
          $('#submit-success-modal').css('display','flex');
        }
      });
    });

    // Modal close handler
    $('#close-success-modal').on('click', function(){ $('#submit-success-modal').hide(); });

    // Initial Render
    renderList($('#required-list'), requiredDocuments, true);
    renderList($('#optional-list'), optionalDocuments, false);
    updateSummary();
  });
</script>
@endsection
