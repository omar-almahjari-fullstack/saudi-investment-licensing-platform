<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            ['key'=>'view_dashboard','label'=>'عرض لوحة التحكم','category'=>'general'],
            ['key'=>'view_analytics','label'=>'عرض التحليلات','category'=>'general'],
            ['key'=>'view_users','label'=>'عرض المستخدمين','category'=>'users'],
            ['key'=>'manage_users','label'=>'إدارة المستخدمين','category'=>'users'],
            ['key'=>'manage_roles_permissions','label'=>'إدارة صلاحيات الأدوار','category'=>'permissions'],
            ['key'=>'manage_user_permissions','label'=>'تعيين صلاحيات للمستخدم','category'=>'permissions'],
            ['key'=>'view_investors','label'=>'عرض المستثمرين','category'=>'investors'],
            ['key'=>'manage_investors','label'=>'إدارة المستثمرين','category'=>'investors'],
            ['key'=>'view_investments','label'=>'عرض الاستثمارات','category'=>'investments'],
            ['key'=>'manage_investments','label'=>'إدارة الاستثمارات','category'=>'investments'],
            ['key'=>'view_licenses','label'=>'عرض التراخيص','category'=>'licenses'],
            ['key'=>'manage_licenses','label'=>'إدارة التراخيص','category'=>'licenses'],
            ['key'=>'view_license_requests','label'=>'عرض طلبات التراخيص','category'=>'license_requests'],
            ['key'=>'manage_license_requests','label'=>'إدارة طلبات التراخيص','category'=>'license_requests'],
            ['key'=>'view_documents','label'=>'عرض المستندات','category'=>'documents'],
            ['key'=>'manage_documents','label'=>'إدارة المستندات','category'=>'documents'],
            ['key'=>'view_chat','label'=>'عرض الدردشة','category'=>'chat'],
            ['key'=>'manage_chat','label'=>'إدارة الدردشة','category'=>'chat'],
            ['key'=>'view_settings','label'=>'عرض الإعدادات','category'=>'settings'],
            ['key'=>'manage_settings','label'=>'إدارة الإعدادات','category'=>'settings'],
            ['key'=>'manage_branding','label'=>'إدارة الهوية/العلامة','category'=>'branding'],
            ['key'=>'manage_page_market','label'=>'إدارة صفحة رؤى السوق','category'=>'pages'],
            ['key'=>'manage_page_success','label'=>'إدارة صفحة قصص النجاح','category'=>'pages'],
            ['key'=>'manage_page_trust','label'=>'إدارة صفحة الثقة والامتثال','category'=>'pages'],
            ['key'=>'view_logs','label'=>'عرض السجلات','category'=>'system'],
            ['key'=>'view_backups','label'=>'عرض النسخ الاحتياطية','category'=>'system'],
            ['key'=>'manage_backups','label'=>'إدارة النسخ الاحتياطية','category'=>'system'],
            ['key'=>'manage_navigation','label'=>'إدارة القائمة','category'=>'content'],
            ['key'=>'manage_packages','label'=>'إدارة الحزم','category'=>'content'],
            ['key'=>'view_investor_portal','label'=>'الوصول لمنصة المستثمر','category'=>'investor'],
        ];

        foreach ($perms as $p) {
            Permission::updateOrCreate(['key'=>$p['key']], $p);
        }

        // Assign defaults: admin gets all
        $allKeys = collect($perms)->pluck('key')->all();
        foreach ($allKeys as $k) {
            RolePermission::updateOrCreate(['role'=>'admin','permission_key'=>$k], ['role'=>'admin','permission_key'=>$k]);
        }
        // staff gets a safe subset
        $staff = [
            'view_dashboard','view_analytics',
            'view_users',
            'view_investors',
            'view_investments',
            'view_licenses','view_license_requests',
            'view_documents',
            'view_chat',
            'view_settings',
            'view_logs','view_backups'
        ];
        foreach ($staff as $k) {
            RolePermission::updateOrCreate(['role'=>'staff','permission_key'=>$k], ['role'=>'staff','permission_key'=>$k]);
        }
        // investor gets portal access only
        RolePermission::updateOrCreate(['role'=>'investor','permission_key'=>'view_investor_portal'], ['role'=>'investor','permission_key'=>'view_investor_portal']);
    }
}
