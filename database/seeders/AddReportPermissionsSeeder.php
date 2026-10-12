<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class AddReportPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            ['key' => 'view_reports', 'label' => 'عرض التقارير', 'category' => 'reports'],
            ['key' => 'manage_reports', 'label' => 'إدارة التقارير', 'category' => 'reports'],
        ];

        foreach ($perms as $p) {
            Permission::updateOrCreate(['key' => $p['key']], $p);
        }

        // Assign to admin
        foreach (collect($perms)->pluck('key')->all() as $k) {
            RolePermission::updateOrCreate(['role' => 'admin', 'permission_key' => $k], ['role' => 'admin', 'permission_key' => $k]);
        }

        // Give staff view access to reports by default
        RolePermission::updateOrCreate(['role' => 'staff', 'permission_key' => 'view_reports'], ['role' => 'staff', 'permission_key' => 'view_reports']);
    }
}
