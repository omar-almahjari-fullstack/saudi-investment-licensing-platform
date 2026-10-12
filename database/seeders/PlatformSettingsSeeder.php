<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\DB;

class PlatformSettingsSeeder extends Seeder
{
    public function run()
    {
        // Avoid duplicates: delete existing rows then insert a single canonical row
        DB::table('platform_settings')->delete();

        PlatformSetting::create([
            'name' => 'عوائد الخليج للاستثمار السعودي',
            'url' => 'http://gulfreturns.infinityfreeapp.com',
            'locale' => 'en',
            'description' => 'منصة التراخيص الموحدة',
            'created_by' => 1,
        ]);
    }
}
