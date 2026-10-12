/* Lightweight script to populate legacy Trust & Compliance admin panels
   - Fetches tc_regulations, tc_security_features, tc_risk_levels, tc_certificates, tc_audit_reports and tc_metrics
   - Renders simple vertical cards into the existing #tc-*-list containers
   - Provides a minimal 'Add' button for metrics and basic edit/delete via prompt
*/
(function(){
    if (typeof jQuery === 'undefined') return console.warn('admin-tc-legacy: jQuery not available');
    var $ = jQuery;

    // initial marker for debugging in browser console
    try{ console.log('admin-tc-legacy: loaded'); }catch(e){}

    function csrf(){ return $('meta[name="csrf-token"]').attr('content') || ''; }

    function apiGet(path, cb){ $.getJSON(path).done(function(d){ cb(null,d); }).fail(function(xhr){ try{ console.warn('apiGet fail', path, xhr.status, xhr.statusText, xhr.responseText); }catch(e){} cb(xhr); }); }
    function apiPost(path,payload,cb){ $.ajax({url:path,method:'POST',data:JSON.stringify(payload),contentType:'application/json',headers:{'X-CSRF-TOKEN':csrf()},success:function(d){cb(null,d);},error:function(xhr){ try{ console.warn('apiPost fail', path, xhr.status, xhr.statusText, xhr.responseText); }catch(e){} cb(xhr);} }); }
    function apiPut(path,payload,cb){ $.ajax({url:path,method:'PUT',data:JSON.stringify(payload),contentType:'application/json',headers:{'X-CSRF-TOKEN':csrf()},success:function(d){cb(null,d);},error:function(xhr){ try{ console.warn('apiPut fail', path, xhr.status, xhr.statusText, xhr.responseText); }catch(e){} cb(xhr);} }); }
    function apiDelete(path,cb){ $.ajax({url:path,method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()},success:function(d){cb(null,d);},error:function(xhr){ try{ console.warn('apiDelete fail', path, xhr.status, xhr.statusText, xhr.responseText); }catch(e){} cb(xhr);} }); }

    function renderList(container, rows, renderer){ var $c = $(container); if (!$c.length) return; if(!rows || rows.length===0){ $c.html('<div class="text-gray-400">لا توجد سجلات.</div>'); return; } var html=''; rows.forEach(function(r){ try{ html += '<div class="p-3 bg-gray-900/40 border border-gray-700 rounded mb-2">'+(renderer?renderer(r):JSON.stringify(r))+'</div>'; }catch(e){ html += '<div class="p-3 bg-gray-900/40 border border-gray-700 rounded mb-2">خطأ في عرض السجل</div>'; } }); $c.html(html); }

    // Fetch all TC resources and populate window.__PAGES so blade's renderTrustComplianceAdmin() can use them
    function fetchAllTC(cb){
        var regs = $.getJSON('/api/admin/tc/regulations');
        var secs = $.getJSON('/api/admin/tc/security-features');
        var risks = $.getJSON('/api/admin/tc/risk-levels');
        var certs = $.getJSON('/api/admin/tc/certificates');
        var audits = $.getJSON('/api/admin/tc/audit-reports');
    $.when(regs, secs, risks, certs, audits).done(function(rRes, sRes, riRes, cRes, aRes){
            try{
                window.__PAGES = window.__PAGES || {};
                window.__PAGES.trust_compliance = {
                    regulations: (rRes && rRes[0]) || [],
                    security_features: (sRes && sRes[0]) || [],
                    risk_levels: (riRes && riRes[0]) || [],
                    certifications: (cRes && cRes[0]) || [],
                    audit_reports: (aRes && aRes[0]) || []
                };
                // render via existing blade functions if available, otherwise render simple lists directly
                if (typeof renderTrustComplianceAdmin === 'function') {
                    renderTrustComplianceAdmin();
                } else {
                    // safe fallback: render basic cards into each container
                    var _regs = (window.__PAGES.trust_compliance && window.__PAGES.trust_compliance.regulations) || [];
                    renderList('#tc-reg-list', _regs, function(r){
                        var title = r.title || r.name || r.level || ('#' + (r.id || ''));
                        var sub = r.license || r.description || r.type || '';
                        var out = '<div class="font-semibold text-white">' + title + '</div>';
                        if (sub) out += '<div class="text-sm text-gray-400">' + sub + '</div>';
                        return out;
                    });

                    var _secs = (window.__PAGES.trust_compliance && window.__PAGES.trust_compliance.security_features) || [];
                    renderList('#tc-sec-list', _secs, function(r){
                        var title = r.title || r.name || ('#' + (r.id || ''));
                        var desc = r.description || '';
                        var out = '<div class="font-semibold text-white">' + title + '</div>';
                        if (desc) out += '<div class="text-sm text-gray-400">' + desc + '</div>';
                        return out;
                    });

                    var _risks = (window.__PAGES.trust_compliance && window.__PAGES.trust_compliance.risk_levels) || [];
                    renderList('#tc-risk-list', _risks, function(r){
                        var lbl = r.label || r.level || ('#' + (r.id || ''));
                        var pct = (r.percentage !== undefined && r.percentage !== null) ? ('<div class="text-sm text-gray-400">النسبة: ' + r.percentage + '</div>') : '';
                        return '<div class="font-semibold text-white">' + lbl + '</div>' + pct;
                    });

                    var _certs = (window.__PAGES.trust_compliance && window.__PAGES.trust_compliance.certifications) || [];
                    renderList('#tc-cert-list', _certs, function(r){
                        var name = r.name || ('#' + (r.id || ''));
                        var issuer = r.issuer || '';
                        var out = '<div class="font-semibold text-white">' + name + '</div>';
                        if (issuer) out += '<div class="text-sm text-gray-400">' + issuer + '</div>';
                        return out;
                    });

                    var _audits = (window.__PAGES.trust_compliance && window.__PAGES.trust_compliance.audit_reports) || [];
                    renderList('#tc-audit-list', _audits, function(r){
                        var title = r.title || ('#' + (r.id || ''));
                        var date = r.audit_date || r.date || '';
                        var out = '<div class="font-semibold text-white">' + title + '</div>';
                        if (date) out += '<div class="text-sm text-gray-400">' + date + '</div>';
                        return out;
                    });
                }
                // metrics removed from legacy renderer
                if (typeof cb === 'function') cb(null, window.__PAGES.trust_compliance);
            }catch(e){ if (typeof cb === 'function') cb(e); }
        }).fail(function(xhr){ try{ console.warn('fetchAllTC failed', xhr && xhr.status); }catch(e){} if (typeof cb === 'function') cb(xhr || new Error('fetch failed')); });
    }

    // loadMetrics removed from legacy script

    function reloadAll(){ fetchAllTC(); }

    $(function(){ try{ reloadAll();
        $(document).on('click', '.tab-trigger', function(){ try{ var t = $(this).data('tab'); if(t==='page-trust' || t==='tab-page-trust') setTimeout(reloadAll,80); }catch(e){} });
        $('#mobile-tabs').on('change', function(){ if($(this).val()==='page-trust') setTimeout(reloadAll,80); });

        // ensure we try another reload shortly after load to handle cases where the trust tab becomes visible later
        setTimeout(function(){ try{ reloadAll(); }catch(e){} }, 300);

    // tc-metrics add injection and click handler removed

    }catch(e){ console.warn('admin-tc-legacy init error', e); } });

})();
