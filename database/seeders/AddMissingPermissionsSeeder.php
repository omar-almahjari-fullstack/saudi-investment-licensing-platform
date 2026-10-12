<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class AddMissingPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            ['key' => 'manage_contact_info', 'label' => 'إدارة معلومات الاتصال', 'category' => 'settings'],
            ['key' => 'view_contact_info', 'label' => 'عرض معلومات الاتصال', 'category' => 'settings'],

            ['key' => 'view_payment_reminders', 'label' => 'عرض تذكيرات الدفع', 'category' => 'payments'],
            ['key' => 'manage_payment_reminders', 'label' => 'إدارة تذكيرات الدفع', 'category' => 'payments'],
            ['key' => 'process_payments', 'label' => 'معالجة الدفعات', 'category' => 'payments'],

            ['key' => 'complete_investment', 'label' => 'تعيين الاستثمار كمكتمل', 'category' => 'investments'],
            ['key' => 'reject_investment', 'label' => 'رفض الاستثمار', 'category' => 'investments'],

            ['key' => 'view_bank_accounts', 'label' => 'عرض الحسابات البنكية', 'category' => 'bank'],
            ['key' => 'manage_bank_accounts', 'label' => 'إدارة الحسابات البنكية', 'category' => 'bank'],

            ['key' => 'view_platform_accounts', 'label' => 'عرض حسابات المنصة', 'category' => 'finance'],
            ['key' => 'manage_platform_accounts', 'label' => 'إدارة حسابات المنصة', 'category' => 'finance'],
        ];

        foreach ($perms as $p) {
            Permission::updateOrCreate(['key' => $p['key']], $p);
        }

        // Assign all new permissions to admin by default
        foreach (collect($perms)->pluck('key')->all() as $k) {
            RolePermission::updateOrCreate(['role' => 'admin', 'permission_key' => $k], ['role' => 'admin', 'permission_key' => $k]);
        }

        // Assign safe view-only permissions to staff
        $staffViews = ['view_contact_info', 'view_payment_reminders', 'view_bank_accounts', 'view_platform_accounts'];
        foreach ($staffViews as $k) {
            RolePermission::updateOrCreate(['role' => 'staff', 'permission_key' => $k], ['role' => 'staff', 'permission_key' => $k]);
        }
    }
}
