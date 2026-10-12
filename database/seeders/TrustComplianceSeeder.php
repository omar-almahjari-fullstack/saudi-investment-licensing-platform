<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrustComplianceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        if (!DB::table('tc_regulations')->exists()) {
            DB::table('tc_regulations')->insert([
                'id' => (string) Str::uuid(),
                'title' => 'Saudi Central Bank (SAMA) Regulation',
                'content' => 'الامتثال للوائح مؤسسة النقد.',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        if (!DB::table('tc_security_features')->exists()) {
            foreach ([
                ['title'=>'تشفير البيانات','description'=>'حماية كاملة أثناء النقل والتخزين'],
                ['title'=>'مراجعات أمنية دورية','description'=>'اختبارات اختراق وتقييمات أمنية'],
            ] as $row) {
                DB::table('tc_security_features')->insert($row + [
                    'id' => (string) Str::uuid(),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
        if (!DB::table('tc_risk_levels')->exists()) {
            foreach ([
                ['label'=>'منخفض','percentage'=>10],
                ['label'=>'متوسط','percentage'=>50],
                ['label'=>'مرتفع','percentage'=>80],
            ] as $row) {
                DB::table('tc_risk_levels')->insert($row + [
                    'id' => (string) Str::uuid(),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
        if (!DB::table('tc_certificates')->exists()) {
            DB::table('tc_certificates')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'ISO 27001',
                'issuer' => 'International Organization for Standardization',
                'issued_at' => now()->subYear()->toDateString(),
                'expires_at' => now()->addYear()->toDateString(),
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        if (!DB::table('tc_audit_reports')->exists()) {
            DB::table('tc_audit_reports')->insert([
                'id' => (string) Str::uuid(),
                'title' => 'تقرير تدقيق أمني ربع سنوي',
                'date' => now()->subMonths(2)->toDateString(),
                'summary' => 'نتائج إيجابية وتحسينات مقترحة طفيفة.',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
