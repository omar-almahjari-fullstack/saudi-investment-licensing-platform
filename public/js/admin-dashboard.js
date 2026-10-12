(function(){
    // admin-dashboard.js
    // Reads initial server data from window.__SERVER_DATA and falls back to calling /api/* endpoints.

    var apiPrefix = '/api';
    var server = window.__SERVER_DATA || {};
    var csrf = window.__CSRF_TOKEN || '';

    // Global defensive handler: log and ensure loading overlay is hidden if an uncaught error occurs
    (function(){
        try {
            window.addEventListener('error', function(e){
                try { console.error('Admin global error:', e.error || e.message || e); } catch(ignore){}
                try { if (window.jQuery) $('#loading-overlay').hide(); } catch(ignore){}
            });
        } catch(ignore){}
    })();

    function setAdminName(name){
        if (window.jQuery) $('#admin-fullname').text(name || 'مدير النظام');
    }

    function statusBadgeText(status){
        var map = { pending: 'معلق', active: 'نشط', approved: 'موافق عليه', rejected: 'مرفوض', completed: 'مكتمل' };
        return map[status] || status;
    }

    function applyStats(stats){
        if (!stats) return;
        $('#stat-users').text(stats.total_users || 0);
        $('#stat-investments').text(stats.total_investments || 0);
        $('#stat-licenses').text(stats.total_licenses || 0);
        $('#stat-amount').text((stats.total_investment_amount_formatted || '0') + ' ريال');
    }

    function renderUsers(users){
        var $list = $('#users-list').empty();
        if (!Array.isArray(users)) return;
        $('#users-count').text(users.length + ' مستخدم');
        users.forEach(function(u){
            var $card = $('<div/>').addClass('glass-card').css({padding:'10px',display:'flex',justifyContent:'space-between',alignItems:'center'});
            $card.append('<div><div style="font-weight:700;color:#fff">'+u.full_name+'</div><div style="color:#9ca3af;font-size:13px">'+(u.email||'')+' • '+(u.phone||'')+'</div></div>');
            var $right = $('<div/>').css({display:'flex',gap:'8px',alignItems:'center'});
            var $badge = $('<div/>').css({padding:'6px 8px',borderRadius:'8px',background:u.is_active? '#16a34a':'#ef4444',color:'#fff',fontWeight:700}).text(u.is_active? 'نشط':'غير نشط');
            $right.append($badge);
            $right.append($('<button/>').addClass('btn-secondary').text('عرض').css({borderColor:'#22d3ee',color:'#22d3ee'}).on('click',function(){ alert('عرض: '+u.full_name); }));
            $card.append($right);
            $list.append($card);
        });
    }

    function renderInvestments(items){
        var $list = $('#investments-list').empty();
        if (!Array.isArray(items)) return;
        $('#investments-count').text(items.length + ' استثمار');
        items.forEach(function(it){
            var $card = $('<div/>').addClass('glass-card').css({padding:'10px',display:'flex',justifyContent:'space-between',alignItems:'center'});
            var amountText = (it.amount ? (Number(it.amount).toLocaleString() + ' ريال') : '-');
            var statusColor = (it.status === 'pending')? '#f59e0b' : (it.status === 'active' ? '#16a34a' : '#64748b');
            $card.append('<div><div style="font-weight:700;color:#fff">'+amountText+'</div><div style="color:#9ca3af;font-size:13px">نوع: '+(it.investment_type || '-')+' • ربح: '+(it.profit_rate||'-')+'%</div></div>');
            var $right = $('<div/>').css({display:'flex',gap:'8px',alignItems:'center'});
            var $badge = $('<div/>').css({padding:'6px 8px',borderRadius:'8px',background:statusColor,color:'#000',fontWeight:700}).text(statusBadgeText(it.status) || '-');
            $right.append($badge);
            // Only show a single, namespaced "دفع" (pay) button for pending investments.
            // Uses unique data attributes and event namespace to avoid conflicts with other handlers.
            if (it.status === 'pending') {
                var $payBtn = $('<button/>')
                    .addClass('btn-primary')
                    .attr('data-__adm_pay_btn', '1')
                    .attr('data-__adm_pay_id', it.id)
                    .text('دفع')
                    .css({borderColor: '#10b981', color: '#fff'});
                $right.append($payBtn);
            }
            $card.append($right);
            $list.append($card);
        });
    }

    function renderLicenses(items){
        var $list = $('#licenses-list').empty();
        if (!Array.isArray(items)) return;
        $('#licenses-count').text(items.length + ' ترخيص');
        items.forEach(function(it){
            var $card = $('<div/>').addClass('glass-card').css({padding:'10px',display:'flex',justifyContent:'space-between',alignItems:'center'});
            var infoHtml = '<div style="display:flex;gap:10px;align-items:center;">';
            // thumbnails container
            if (it.id_front_url || it.id_back_url) {
                infoHtml += '<div style="display:flex;flex-direction:column;gap:6px;align-items:center;">';
                if (it.id_front_url) {
                    infoHtml += '<img src="'+it.id_front_url+'" alt="front" style="width:56px;height:40px;object-fit:cover;border-radius:6px;border:1px solid rgba(255,255,255,0.06)"/>';
                }
                if (it.id_back_url) {
                    infoHtml += '<img src="'+it.id_back_url+'" alt="back" style="width:56px;height:40px;object-fit:cover;border-radius:6px;border:1px solid rgba(255,255,255,0.06)"/>';
                }
                infoHtml += '</div>';
            }
            infoHtml += '<div><div style="font-weight:700;color:#fff">'+(it.business_name||'-')+'</div><div style="color:#9ca3af;font-size:13px">'+(it.business_type||'-')+' • '+(it.location||'-')+'</div></div>';
            infoHtml += '</div>';
            $card.append(infoHtml);
            var $right = $('<div/>').css({display:'flex',gap:'8px',alignItems:'center'});
            var statusColor = (it.status === 'pending')? '#f59e0b' : (it.status === 'approved' ? '#16a34a' : (it.status === 'rejected' ? '#ef4444' : '#64748b'));
            var $badge = $('<div/>').css({padding:'6px 8px',borderRadius:'8px',background:statusColor,color:'#000',fontWeight:700}).text(statusBadgeText(it.status) || '-');
            $right.append($badge);
            // Approval / rejection UI removed per admin request to avoid conflicts.
            // Previously: if(it.status === 'pending') { ... }
            // To re-enable, insert buttons that call changeLicenseStatus(id, status).
            $card.append($right);
            $list.append($card);
        });
    }

    function fetchStats(){
        $('#loading-overlay').show();
        return $.get(apiPrefix + '/stats').done(function(data){
            applyStats(data);
        }).fail(function(){ console.error('Failed to fetch stats'); }).always(function(){ $('#loading-overlay').hide(); });
    }

    function fetchUsers(){
        return $.get(apiPrefix + '/users').done(function(users){ renderUsers(users); }).fail(function(){ console.error('Failed to fetch users'); });
    }

    function fetchInvestments(){
        return $.get(apiPrefix + '/investments').done(function(items){ renderInvestments(items); }).fail(function(){ console.error('Failed to fetch investments'); });
    }

    function fetchLicenses(){
        return $.get(apiPrefix + '/business-licenses').done(function(items){ renderLicenses(items); }).fail(function(){ console.error('Failed to fetch licenses'); });
    }

    function changeInvestmentStatus(id, status){
        return $.ajax({ url: apiPrefix + '/admin/investments/' + id + '/status', method: 'PUT', data: { status: status, _token: csrf } })
        .done(function(){ fetchAll(); })
        .fail(function(){ alert('فشل تحديث الحالة'); });
    }

    // --- Namespaced pay helper (unique variables/events to avoid collisions) ---
    var __admPay_csrf = csrf; // unique local alias
    function __admPay_invoke(id){
        if (!id) return $.Deferred().reject();
        return $.ajax({ url: apiPrefix + '/admin/investments/' + id + '/status', method: 'PUT', data: { status: 'active', _token: __admPay_csrf } })
            .done(function(){ fetchAll(); })
            .fail(function(){ alert('فشل تنفيذ الدفع'); });
    }

    function changeLicenseStatus(id, status){
        return $.ajax({ url: apiPrefix + '/admin/licenses/' + id + '/status', method: 'PUT', data: { status: status, _token: csrf } })
        .done(function(){ fetchAll(); })
        .fail(function(){ alert('فشل تحديث الحالة'); });
    }

    function fetchAll(){
        // prefer remote fetch for fresh data but render server-provided first
        fetchStats();
        fetchUsers();
        fetchInvestments();
        fetchLicenses();
    }

    function initTabs(){
        $(document).on('click', '.tab-btn', function(){
            var tab = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-panel').addClass('hidden');
            $('#tab-'+tab).removeClass('hidden');
        });
    }

    // Initialize on DOM ready
    $(function(){
        // defensive: ensure loading overlay hidden on init so UI doesn't stay blocked
        try { $('#loading-overlay').hide(); } catch(ignore){}
        // fallback: force-hide after a short timeout in case something else shows it
        setTimeout(function(){ try { $('#loading-overlay').hide(); } catch(ignore){} }, 3000);
        console.log('[admin-dashboard] init');
        setAdminName((window.Laravel && window.Laravel.user && window.Laravel.user.full_name) || (server && server.users && server.users.length ? null : null) || $('#admin-fullname').text());

        // Render server bootstrapped data if present
        if(server.stats) applyStats(server.stats);
        if(server.users) renderUsers(server.users);
        if(server.investments) renderInvestments(server.investments);
        if(server.licenses) renderLicenses(server.licenses);

        initTabs();

        // Bind a single delegated handler for the admin pay button using a namespaced event
        try {
            // Centralized pay handlers temporarily disabled; Blade fallback handles modal open/confirm.
        } catch(ignore){}

        // After rendering server-provided data, fetch fresh copies (guarded)
        try {
            fetchAll();
        } catch (e) {
            console.error('[admin-dashboard] fetchAll error', e);
            try { $('#loading-overlay').hide(); } catch(ignore){}
        }
    });

})();
