<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class TrustComplianceArraysSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Regulations
        $regulations = [
            [ 'name' => 'هيئة السوق المالية', 'nameEn' => 'Capital Market Authority (CMA)', 'license' => 'ترخيص رقم: 37-07073', 'status' => 'ساري المفعول', 'validUntil' => '2026-12-31', 'icon' => '🏛️' ],
            [ 'name' => 'مؤسسة النقد العربي السعودي', 'nameEn' => 'Saudi Central Bank (SAMA)', 'license' => 'ترخيص رقم: INV-2024-001', 'status' => 'ساري المفعول', 'validUntil' => '2026-06-30', 'icon' => '🏦' ],
            [ 'name' => 'وزارة التجارة', 'nameEn' => 'Ministry of Commerce', 'license' => 'سجل تجاري: 1010567891', 'status' => 'ساري المفعول', 'validUntil' => '2026-03-15', 'icon' => '🏢' ],
        ];

        foreach ($regulations as $r) {
            $match = ['title' => $r['name']];
            $values = [
                'id' => (string) Str::uuid(),
                'title' => $r['name'],
                'name_en' => $r['nameEn'] ?? null,
                'license' => $r['license'] ?? null,
                'status' => $r['status'] ?? null,
                'valid_until' => !empty($r['validUntil']) ? Carbon::parse($r['validUntil'])->toDateString() : null,
                'icon' => $r['icon'] ?? null,
                'content' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('tc_regulations')->updateOrInsert($match, $values);
        }

        // Security features
        $securityFeatures = [
            [ 'title' => 'تشفير البيانات', 'description' => 'تشفير AES-256 لجميع البيانات المالية والشخصية', 'level' => 'متقدم', 'icon' => '🔒' ],
            [ 'title' => 'حماية متعددة العوامل', 'description' => 'نظام حماية متعدد المراحل للوصول للحسابات', 'level' => 'عالي', 'icon' => '🛡️' ],
            [ 'title' => 'مراقبة العمليات 24/7', 'description' => 'مراقبة مستمرة لجميع العمليات المالية', 'level' => 'مستمر', 'icon' => '👁️' ],
            [ 'title' => 'النسخ الاحتياطي', 'description' => 'نسخ احتياطية متعددة وآمنة للبيانات', 'level' => 'يومي', 'icon' => '💾' ],
        ];

        foreach ($securityFeatures as $s) {
            $match = ['title' => $s['title']];
            $values = [
                'id' => (string) Str::uuid(),
                'title' => $s['title'],
                'description' => $s['description'] ?? null,
                'level' => $s['level'] ?? null,
                'icon' => $s['icon'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('tc_security_features')->updateOrInsert($match, $values);
        }

        // Risk levels
        $riskLevels = [
            [ 'level' => 'منخفض', 'percentage' => '15-25%', 'description' => 'استثمارات آمنة مع عوائد مضمونة', 'color' => '#22c55e' ],
            [ 'level' => 'متوسط', 'percentage' => '25-35%', 'description' => 'استثمارات متوازنة مع مخاطر محسوبة', 'color' => '#f59e0b' ],
            [ 'level' => 'عالي', 'percentage' => '35-50%', 'description' => 'استثمارات عالية العائد مع إدارة مخاطر', 'color' => '#f97316' ],
        ];

        foreach ($riskLevels as $rl) {
            $label = $rl['level'];
            $pct = 0;
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $rl['percentage'], $m)) {
                $pct = round((intval($m[1]) + intval($m[2])) / 2);
            } elseif (preg_match('/(\d+)/', $rl['percentage'], $m)) {
                $pct = intval($m[1]);
            }

            $match = ['label' => $label];
            $values = [
                'id' => (string) Str::uuid(),
                'label' => $label,
                'level' => $label,
                'percentage' => $pct,
                'percentage_text' => $rl['percentage'] ?? null,
                'color' => $rl['color'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('tc_risk_levels')->updateOrInsert($match, $values);
        }

        // Certificates
        $certifications = [
            [ 'name' => 'ISO 27001', 'description' => 'شهادة أمان المعلومات الدولية', 'issueDate' => '2024-01-15', 'validUntil' => '2027-01-15', 'status' => 'ساري' ],
            [ 'name' => 'SOC 2 Type II', 'description' => 'شهادة الضوابط الأمنية للخدمات', 'issueDate' => '2024-03-20', 'validUntil' => '2025-03-20', 'status' => 'ساري' ],
            [ 'name' => 'PCI DSS', 'description' => 'معايير أمان بيانات البطاقات', 'issueDate' => '2024-06-10', 'validUntil' => '2025-06-10', 'status' => 'ساري' ],
        ];

        foreach ($certifications as $c) {
            $match = ['name' => $c['name']];
            $values = [
                'id' => (string) Str::uuid(),
                'name' => $c['name'],
                'description' => $c['description'] ?? null,
                'issuer' => $c['issuer'] ?? $c['description'] ?? null,
                'issued_at' => !empty($c['issueDate']) ? Carbon::parse($c['issueDate'])->toDateString() : null,
                'expires_at' => !empty($c['validUntil']) ? Carbon::parse($c['validUntil'])->toDateString() : null,
                'status' => $c['status'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('tc_certificates')->updateOrInsert($match, $values);
        }

        // Audit reports
        $auditReports = [
            [ 'title' => 'تقرير المراجعة السنوية 2024', 'date' => '2024-12-31', 'type' => 'مراجعة شاملة', 'status' => 'مكتمل', 'fileSize' => '2.4 MB' ],
            [ 'title' => 'تقرير الربع الثالث 2024', 'date' => '2024-09-30', 'type' => 'مراجعة ربع سنوية', 'status' => 'مكتمل', 'fileSize' => '1.8 MB' ],
            [ 'title' => 'تقرير الأداء المالي Q2 2024', 'date' => '2024-06-30', 'type' => 'تقرير أداء', 'status' => 'مكتمل', 'fileSize' => '1.5 MB' ],
        ];

        foreach ($auditReports as $a) {
            $match = ['title' => $a['title']];
            $values = [
                'id' => (string) Str::uuid(),
                'title' => $a['title'],
                'date' => !empty($a['date']) ? Carbon::parse($a['date'])->toDateString() : null,
                'type' => $a['type'] ?? null,
                'status' => $a['status'] ?? null,
                'file_size' => $a['fileSize'] ?? null,
                'summary' => null,
                'audit_date' => !empty($a['date']) ? Carbon::parse($a['date'])->toDateTimeString() : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('tc_audit_reports')->updateOrInsert($match, $values);
        }
    }
}
