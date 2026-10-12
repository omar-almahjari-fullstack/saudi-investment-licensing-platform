<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class TcMetricsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $metrics = [
            ['key' => 'security_rate', 'label' => 'معدل الأمان', 'value' => '96%', 'icon' => '🛡️'],
            ['key' => 'licenses_count', 'label' => 'تراخيص رسمية', 'value' => '5202+', 'icon' => '📜'],
            ['key' => 'certificates_count', 'label' => 'شهادات اعتماد', 'value' => '3524+', 'icon' => '🏆'],
            ['key' => 'compliance_rate', 'label' => 'معدل الامتثال', 'value' => '98%', 'icon' => '✅'],
        ];

        foreach ($metrics as $m) {
            DB::table('tc_metrics')->updateOrInsert(
                ['key' => $m['key']],
                [
                    'id' => (string) Str::uuid(),
                    'key' => $m['key'],
                    'label' => $m['label'],
                    'value' => $m['value'],
                    'icon' => $m['icon'],
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
