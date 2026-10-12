<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seed admin user
        $this->call([
            AdminUserSeeder::class,
            DemoContentSeeder::class,
            PermissionsSeeder::class,
            PaymentMethodsSeeder::class,
            PlatformAccountsSeeder::class,
            MarketInsightsSeeder::class,
            SuccessStoriesSeeder::class,
            TrustComplianceSeeder::class,
            PaymentsDemoSeeder::class,
            InvestmentPackagesSeeder::class,
        ]);
    }
}
