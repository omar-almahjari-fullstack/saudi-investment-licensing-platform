(function(){
    // Simple admin CRUD UI for Trust & Compliance tables.
    // Uses jQuery AJAX and prompt/confirm dialogs to keep implementation minimal.
    try{ console.info('[admin-trust-compliance] script loaded'); } catch(e){}

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    function apiGet(path, cb) {
        $.getJSON(path).done(function(data){ cb(null, data); }).fail(function(xhr){
            try{ console.warn('[admin-trust-compliance] GET', path, 'failed', xhr.status, xhr.statusText, xhr.responseText); }catch(e){}
            cb(xhr);
        });
    }

    function apiPost(path, payload, cb) {
        $.ajax({ url: path, method: 'POST', data: JSON.stringify(payload), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrf()}, success: function(d){ cb(null,d); }, error: function(xhr){ try{ console.warn('[admin-trust-compliance] POST', path, 'failed', xhr.status, xhr.responseText); }catch(e){}; cb(xhr); } });
    }
    function apiPut(path, payload, cb) {
        $.ajax({ url: path, method: 'PUT', data: JSON.stringify(payload), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrf()}, success: function(d){ cb(null,d); }, error: function(xhr){ try{ console.warn('[admin-trust-compliance] PUT', path, 'failed', xhr.status, xhr.responseText); }catch(e){}; cb(xhr); } });
    }
    function apiDelete(path, cb) {
        $.ajax({ url: path, method: 'DELETE', headers: {'X-CSRF-TOKEN': csrf()}, success: function(d){ cb(null,d); }, error: function(xhr){ try{ console.warn('[admin-trust-compliance] DELETE', path, 'failed', xhr.status, xhr.responseText); }catch(e){}; cb(xhr); } });
    }

    function renderTable(containerSelector, columns, rows, actions) {
        var $c = $(containerSelector);
        var html = '<table class="w-full text-sm table-auto"><thead><tr>';
        columns.forEach(function(col){ html += '<th class="text-left text-gray-300 p-2">'+col.label+'</th>'; });
        html += '<th class="text-right text-gray-300 p-2">الإجراءات</th>';
        html += '</tr></thead><tbody>';
        if (!rows || rows.length === 0) {
            html += '<tr><td class="p-3 text-gray-400" colspan="'+(columns.length+1)+'">لا توجد سجلات.</td></tr>';
        } else {
            rows.forEach(function(r){
                html += '<tr class="border-t border-gray-800">';
                columns.forEach(function(col){
                    var v = r[col.key] === null || typeof r[col.key] === 'undefined' ? '' : r[col.key];
                    html += '<td class="p-2 align-top text-gray-200">' + (typeof v === 'object' ? JSON.stringify(v) : v) + '</td>';
                });
                // actions
                var btns = '';
                if (actions && actions.edit) btns += '<button class="tc-btn-edit mr-2 px-2 py-1 bg-blue-600 text-white rounded" data-id="'+r.id+'">تعديل</button>';
                if (actions && actions.del) btns += '<button class="tc-btn-delete px-2 py-1 bg-red-600 text-white rounded" data-id="'+r.id+'">حذف</button>';
                html += '<td class="p-2 text-right">'+btns+'</td>';
                html += '</tr>';
            });
        }
        html += '</tbody></table>';
        $c.html(html);
    }

    // Render compact vertical list cards used in the main tab layout (right side panels)
    function renderList(containerSelector, rows, mapFn) {
        var $c = $(containerSelector);
        if (!$c || $c.length === 0) return;
        if (!rows || rows.length === 0) {
            $c.html('<div class="text-gray-400">لا توجد سجلات.</div>');
            return;
        }
        var html = '';
        rows.forEach(function(r){
            try{
                var content = (typeof mapFn === 'function') ? mapFn(r) : JSON.stringify(r);
                html += '<div class="p-3 bg-gray-900/40 border border-gray-700 rounded mb-2">' + content + '</div>';
            }catch(e){ html += '<div class="p-3 bg-gray-900/40 border border-gray-700 rounded mb-2">خطأ في عرض السجل</div>'; }
        });
        $c.html(html);
    }

    // Loaders for each table
    function loadAudits(){ apiGet('/api/admin/tc/audit-reports', function(err,data){ if(err){ console.warn(err); $('#tc-audits-table').html('<div class="text-red-400 p-2">خطأ في جلب البيانات</div>'); return; } renderTable('#tc-audits-table', [
            {key:'id', label:'#'}, {key:'title', label:'العنوان'}, {key:'date', label:'التاريخ'}, {key:'audit_date', label:'تاريخ التدقيق'}, {key:'type', label:'النوع'}, {key:'status', label:'الحالة'}
        ], data, {edit:true, del:true});
        try{ renderList('#tc-audit-list', data, function(r){ var title = r.title || ('تقرير #' + (r.id||'')); var date = r.audit_date || r.date || ''; var sub = date ? ('<div class="text-sm text-gray-400">'+date+'</div>') : ''; return '<div class="font-semibold text-white">'+title+'</div>'+sub; }); }catch(e){}
    }); }
    function loadCertificates(){ apiGet('/api/admin/tc/certificates', function(err,data){ if(err){ console.warn(err); $('#tc-certificates-table').html('<div class="text-red-400 p-2">خطأ في جلب البيانات</div>'); return; } renderTable('#tc-certificates-table', [
            {key:'id', label:'#'}, {key:'name', label:'الاسم'}, {key:'issuer', label:'الجهة'}, {key:'issued_at', label:'تاريخ الإصدار'}, {key:'expires_at', label:'تاريخ الانتهاء'}, {key:'status', label:'الحالة'}
        ], data, {edit:true, del:true});
        try{ renderList('#tc-cert-list', data, function(r){ var name = r.name || ('شهادة #' + (r.id||'')); var issuer = r.issuer ? ('<div class="text-sm text-gray-400">الجهة: '+r.issuer+'</div>') : ''; return '<div class="font-semibold text-white">'+name+'</div>'+issuer; }); }catch(e){}
    }); }
    // metrics removed: loadMetrics() no longer used
    function loadRegulations(){ apiGet('/api/admin/tc/regulations', function(err,data){ if(err){ console.warn(err); $('#tc-regulations-table').html('<div class="text-red-400 p-2">خطأ في جلب البيانات</div>'); return; } renderTable('#tc-regulations-table', [
            {key:'id', label:'#'}, {key:'title', label:'العنوان'}, {key:'license', label:'ترخيص'}, {key:'valid_until', label:'صالح حتى'}, {key:'status', label:'الحالة'}
        ], data, {edit:true, del:true});
        // Also render the compact right-side list used in the tab layout
        try{ renderList('#tc-reg-list', data, function(r){
            var title = r.title || r.name || ('#'+r.id);
            var sub = r.license ? ('<div class="text-sm text-gray-400">' + r.license + '</div>') : '';
            return '<div class="font-semibold text-white">' + title + '</div>' + sub;
        }); }catch(e){}
    }); }
    function loadRiskLevels(){ apiGet('/api/admin/tc/risk-levels', function(err,data){ if(err){ console.warn(err); $('#tc-risk-levels-table').html('<div class="text-red-400 p-2">خطأ في جلب البيانات</div>'); return; } renderTable('#tc-risk-levels-table', [
            {key:'id', label:'#'}, {key:'level', label:'مستوى'}, {key:'label', label:'التسمية'}, {key:'percentage', label:'النسبة'}, {key:'color', label:'اللون'}
        ], data, {edit:true, del:true});
        try{ renderList('#tc-risk-list', data, function(r){ var lbl = r.label || r.level || ('#'+r.id); var pct = r.percentage ? ('<div class="text-sm text-gray-400">نسبة: '+r.percentage+'</div>') : ''; return '<div class="font-semibold text-white">'+lbl+'</div>'+pct; }); }catch(e){}
    }); }
    function loadSecurity(){ apiGet('/api/admin/tc/security-features', function(err,data){ if(err){ console.warn(err); $('#tc-security-table').html('<div class="text-red-400 p-2">خطأ في جلب البيانات</div>'); return; } renderTable('#tc-security-table', [
            {key:'id', label:'#'}, {key:'title', label:'العنوان'}, {key:'description', label:'الوصف'}, {key:'level', label:'المستوى'}, {key:'icon', label:'أيقونة'}
        ], data, {edit:true, del:true});
        try{ renderList('#tc-sec-list', data, function(r){ var t = r.title || r.name || ('#'+r.id); var d = r.description ? ('<div class="text-sm text-gray-400">'+r.description+'</div>') : ''; return '<div class="font-semibold text-white">'+t+'</div>'+d; }); }catch(e){}
    }); }

    // when loading certificates and audits also fill the right-side lists
    // (certificates list)
    var _orig_loadCertificates = loadCertificates;
    // wrap to also render compact list
    (function(){
        // noop: already defined above; ensure certificate list renders into #tc-cert-list
    })();

    // Wire actions (delegated)
    $(function(){
        try{
            // initial load
            loadAudits(); loadCertificates(); loadRegulations(); loadRiskLevels(); loadSecurity();

            // Also reload when the admin tabs switch to the Trust tab to ensure hidden-tab timing doesn't hide data
            function reloadAll(){ loadAudits(); loadCertificates(); loadRegulations(); loadRiskLevels(); loadSecurity(); }
            // Listen for sidebar tab clicks
            $(document).on('click', '.tab-trigger', function(){ try{ var tab = $(this).data('tab'); if(tab === 'page-trust' || tab === 'page-trust' ) { setTimeout(reloadAll, 80); } }catch(e){} });
            // Mobile select change
            $('#mobile-tabs').on('change', function(){ try{ if ($(this).val() === 'page-trust') setTimeout(reloadAll, 80); }catch(e){} });

            // add buttons (simple prompts to keep minimal)
            $('#tc-audits-add').on('click', function(){
                var title = prompt('عنوان التقرير'); if (!title) return;
                var payload = { title: title };
                apiPost('/api/admin/tc/audit-reports', payload, function(err,res){ if(err){ alert('تعذر الإنشاء'); } else { loadAudits(); } });
            });

            $('#tc-certificates-add').on('click', function(){
                var name = prompt('اسم الشهادة'); if (!name) return;
                var payload = { name: name };
                apiPost('/api/admin/tc/certificates', payload, function(err,res){ if(err){ alert('تعذر الإنشاء'); } else { loadCertificates(); } });
            });

            // tc-metrics add handler removed

            $('#tc-regulations-add').on('click', function(){
                var title = prompt('عنوان التشريع'); if(!title) return; var payload = { title: title };
                apiPost('/api/admin/tc/regulations', payload, function(err,res){ if(err){ alert('تعذر الإنشاء'); } else { loadRegulations(); } });
            });

            $('#tc-risk-levels-add').on('click', function(){
                var label = prompt('تسمية مستوى المخاطر (مثال: منخفض)'); if(!label) return; var payload = { label: label };
                apiPost('/api/admin/tc/risk-levels', payload, function(err,res){ if(err){ alert('تعذر الإنشاء'); } else { loadRiskLevels(); } });
            });

            $('#tc-security-add').on('click', function(){
                var title = prompt('عنوان ميزة الأمان'); if(!title) return; var payload = { title: title };
                apiPost('/api/admin/tc/security-features', payload, function(err,res){ if(err){ alert('تعذر الإنشاء'); } else { loadSecurity(); } });
            });

            // delegated edit/delete
            $(document).on('click', '.tc-btn-edit', function(){
                var $btn = $(this); var id = $btn.data('id');
                var $row = $btn.closest('tr');
                // For simplicity, fetch the full record from each endpoint to prefill: try each endpoint path for id
                // We'll attempt audit -> certificates -> metrics -> regulations -> risk-levels -> security
                var tried = 0;
                var endpoints = [
                    '/api/admin/tc/audit-reports', '/api/admin/tc/certificates', '/api/admin/tc/regulations', '/api/admin/tc/risk-levels', '/api/admin/tc/security-features'
                ];
                function tryNext(){
                    if (tried >= endpoints.length) { alert('غير قادر على إيجاد السجل'); return; }
                    var url = endpoints[tried] + '/' + encodeURIComponent(id);
                    // many endpoints don't implement GET /{id} individually; fall back to list and find
                    $.getJSON(endpoints[tried]).done(function(list){
                        var found = list.find(function(x){ return (''+x.id) === (''+id); });
                        if (!found) { tried++; tryNext(); return; }
                        // prompt editable fields generically: show JSON, allow editing full JSON
                        var edited = prompt('تعديل (عدل JSON للصف)', JSON.stringify(found, null, 2));
                        if (!edited) return;
                        try { var payload = JSON.parse(edited); } catch(e){ alert('JSON غير صالح'); return; }
                        apiPut(endpoints[tried] + '/' + encodeURIComponent(id), payload, function(err,res){ if(err){ alert('تعذر التحديث'); } else { loadAudits(); loadCertificates(); loadMetrics(); loadRegulations(); loadRiskLevels(); loadSecurity(); } });
                    }).fail(function(){ tried++; tryNext(); });
                }
                tryNext();
            });

            $(document).on('click', '.tc-btn-delete', function(){
                var id = $(this).data('id');
                if (!confirm('هل تريد الحذف نهائياً؟')) return;
                // try each delete endpoint until success
                var endpoints = ['/api/admin/tc/audit-reports','/api/admin/tc/certificates','/api/admin/tc/regulations','/api/admin/tc/risk-levels','/api/admin/tc/security-features'];
                var i = 0;
                function tryDel(){
                    if (i >= endpoints.length) { alert('تعذر الحذف'); return; }
                    apiDelete(endpoints[i] + '/' + encodeURIComponent(id), function(err,res){ if(!err){ loadAudits(); loadCertificates(); loadMetrics(); loadRegulations(); loadRiskLevels(); loadSecurity(); } else { i++; tryDel(); } });
                }
                tryDel();
            });

        } catch(e){ console.warn('admin-trust-compliance error', e); }
    });

})();
