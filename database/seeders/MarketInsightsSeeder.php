<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketInsightsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // helper: update or insert with uuid if insert needed
        $upsert = function(string $table, string $keyColumn, $keyValue, array $data) use ($now) {
            $updated = DB::table($table)->where($keyColumn, $keyValue)->update(array_merge($data, ['updated_at' => $now]));
            if (!$updated) {
                DB::table($table)->insert(array_merge([$keyColumn => $keyValue], $data, ['id' => (string) Str::uuid(), 'created_at' => $now, 'updated_at' => $now]));
            }
        };

        // Indicators (original array)
        $marketIndicators = [
            [ 'name' => 'مؤشر السوق العام', 'value' => '12,450.67', 'change' => '+2.45%', 'trend' => 'up' ],
            [ 'name' => 'مؤشر الطاقة',      'value' => '8,890.23',  'change' => '+4.12%', 'trend' => 'up' ],
            [ 'name' => 'مؤشر البنوك',      'value' => '5,670.89',  'change' => '-1.23%', 'trend' => 'down' ],
            [ 'name' => 'مؤشر العقارات',    'value' => '3,245.45',  'change' => '+0.87%', 'trend' => 'up' ],
        ];
        foreach ($marketIndicators as $i => $ind) {
            $upsert('market_indicators', 'name', $ind['name'], ['value' => $ind['value'], 'change' => $ind['change'], 'trend' => $ind['trend']]);
        }

        // Sectors
        $topSectors = [
            [ 'name' => 'التقنية والاتصالات', 'performance' => '+8.5%', 'volume' => '2.4B ريال', 'trend' => 'صاعد بقوة', 'color' => '#22c55e' ],
            [ 'name' => 'الطاقة المتجددة',    'performance' => '+6.2%', 'volume' => '1.8B ريال', 'trend' => 'نمو مستدام', 'color' => '#3b82f6' ],
            [ 'name' => 'الرعاية الصحية',     'performance' => '+4.7%', 'volume' => '1.2B ريال', 'trend' => 'أداء قوي',  'color' => '#06b6d4' ],
            [ 'name' => 'التجارة الإلكترونية','performance' => '+3.9%', 'volume' => '950M ريال', 'trend' => 'نمو متسارع', 'color' => '#f59e0b' ],
        ];
        foreach ($topSectors as $i => $sector) {
            $upsert('market_sectors', 'name', $sector['name'], ['performance' => $sector['performance'], 'volume' => $sector['volume'], 'trend' => $sector['trend'], 'color' => $sector['color']]);
        }

        // News
        $recentNews = [
            [ 'title' => 'صندوق الاستثمارات العامة يعلن عن استثمارات جديدة بقيمة 50 مليار ريال', 'summary' => 'يهدف الصندوق إلى تنويع الاستثمارات في القطاعات الناشئة والتقنيات المتطورة', 'date' => '2025-10-06', 'category' => 'استثمارات حكومية', 'read_time' => '3 دقائق' ],
            [ 'title' => 'ارتفع مؤشر السوق السعودي إلى مستويات قياسية جديدة', 'summary' => 'حقق المؤشر العام للسوق السعودي مكاسب قوية مدفوعة بأداء قطاعي الطاقة والتقنية', 'date' => '2025-10-05', 'category' => 'أسواق مالية', 'read_time' => '2 دقائق' ],
            [ 'title' => 'قطاع العقارات يشهد نمواً مستداماً مع مشاريع رؤية 2030', 'summary' => 'تواصل مشاريع نيوم والقدية دفع النمو في قطاع العقارات والإنشاءات', 'date' => '2025-10-04', 'category' => 'عقارات', 'read_time' => '4 دقائق' ],
        ];
        foreach ($recentNews as $i => $n) {
            $upsert('market_news', 'title', $n['title'], ['summary' => $n['summary'], 'date' => $n['date'], 'category' => $n['category'], 'read_time' => $n['read_time']]);
        }

        // Trends
        $investmentTrends = [
            [ 'trend' => 'الاستثمار في التقنيات الناشئة', 'growth' => '+15.3%', 'description' => 'زيادة الاهتمام بالذكاء الاصطناعي والبلوك تشين' ],
            [ 'trend' => 'الاستثمار المستدام والأخضر',    'growth' => '+12.7%', 'description' => 'نمو الاستثمارات في الطاقة المتجددة والتقنيات النظيفة' ],
            [ 'trend' => 'الاستثمار في قطاع الترفيه',     'growth' => '+9.8%',  'description' => 'تطوير صناعة الترفيه كجزء من رؤية السعودية 2030' ],
        ];
        foreach ($investmentTrends as $i => $t) {
            $upsert('market_trends', 'trend', $t['trend'], ['growth' => $t['growth'], 'description' => $t['description']]);
        }
    }
}
