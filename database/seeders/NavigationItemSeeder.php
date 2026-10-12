<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NavigationItem;

class NavigationItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ["path" => "/", "label" => "الصفحة الرئيسية", "icon" => "🏠", 'order' => 0],
            ["path" => "/investor/dashboard", "label" => "لوحة تحكم المستثمر", "icon" => "🧾", 'order' => 1],
            ["path" => "/register-investor", "label" => "تسجيل مستثمر جديد", "icon" => "👤", 'order' => 2],
            ["path" => "/investment-levels", "label" => "مركز مستويات الاستثمار", "icon" => "📊", 'order' => 3],
            ["path" => "/small-investment", "label" => "بوابة الاستثمار الصغيرة", "icon" => "💰", 'order' => 4],
            ["path" => "/large-investment", "label" => "مركز استثماري كبير", "icon" => "🏦", 'order' => 5],
            ["path" => "/dashboard", "label" => "لوحة معلومات الاستثمار", "icon" => "📈", 'order' => 6],
            ["path" => "/documents", "label" => "تحميل المستندات", "icon" => "📄", 'order' => 7],
            ["path" => "/market-insights", "label" => "مركز رؤى السوق", "icon" => "📊", 'order' => 8],
            ["path" => "/success-stories", "label" => "قصص النجاح", "icon" => "🏆", 'order' => 9],
            ["path" => "/trust-compliance", "label" => "مركز الثقة والامتثال", "icon" => "🛡️", 'order' => 10],
        ];
        foreach ($items as $i => $data) {
            NavigationItem::updateOrCreate(['path' => $data['path']], $data + ['active' => true]);
        }
    }
}
