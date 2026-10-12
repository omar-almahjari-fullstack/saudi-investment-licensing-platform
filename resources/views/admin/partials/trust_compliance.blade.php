@php
    // Minimal admin controls for Trust & Compliance. This partial renders inner content only
    // and should be included inside the existing #tab-page-trust tab panel to avoid duplicate IDs.
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-gray-900/30 border border-gray-800 rounded p-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-white font-semibold">التقارير والتدقيق (tc_audit_reports)</h4>
            <button id="tc-audits-add" class="px-3 py-1 bg-emerald-600 text-white rounded text-sm">إضافة تقرير</button>
        </div>
        <div id="tc-audits-table" class="overflow-auto text-sm text-gray-200"></div>
    </div>

    <div class="bg-gray-900/30 border border-gray-800 rounded p-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-white font-semibold">الشهادات (tc_certificates)</h4>
            <button id="tc-certificates-add" class="px-3 py-1 bg-emerald-600 text-white rounded text-sm">إضافة شهادة</button>
        </div>
        <div id="tc-certificates-table" class="overflow-auto text-sm text-gray-200"></div>
    </div>

    

    <div class="bg-gray-900/30 border border-gray-800 rounded p-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-white font-semibold">التشريعات (tc_regulations)</h4>
            <button id="tc-regulations-add" class="px-3 py-1 bg-emerald-600 text-white rounded text-sm">إضافة تشريع</button>
        </div>
        <div id="tc-regulations-table" class="overflow-auto text-sm text-gray-200"></div>
    </div>

    <div class="bg-gray-900/30 border border-gray-800 rounded p-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-white font-semibold">مستويات المخاطر (tc_risk_levels)</h4>
            <button id="tc-risk-levels-add" class="px-3 py-1 bg-emerald-600 text-white rounded text-sm">إضافة مستوى</button>
        </div>
        <div id="tc-risk-levels-table" class="overflow-auto text-sm text-gray-200"></div>
    </div>

    <div class="bg-gray-900/30 border border-gray-800 rounded p-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-white font-semibold">ميزات الأمان (tc_security_features)</h4>
            <button id="tc-security-add" class="px-3 py-1 bg-emerald-600 text-white rounded text-sm">إضافة ميزة</button>
        </div>
        <div id="tc-security-table" class="overflow-auto text-sm text-gray-200"></div>
    </div>
</div>

<script src="{{ asset('js/admin-trust-compliance.js') }}?v=1"></script>
