<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Investment;
use App\Models\License;

class DemoContentSeeder extends Seeder
{
    public function run()
    {
        // create some users if none exist
        if (User::count() < 3) {
            $users = [];
            for ($i = 1; $i <= 5; $i++) {
                $users[] = User::create([
                    'id' => (string) Str::uuid(),
                    'full_name' => 'مستثمر '.$i,
                    'email' => "investor{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role' => 'investor',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // create investments and licenses for those users
            foreach ($users as $idx => $u) {
                // ensure an investor row exists for this user
                $invRow = \App\Models\Investor::firstOrCreate(
                    ['user_id' => $u->id],
                    ['id' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]
                );
                Investment::create([
                    'id' => (string) Str::uuid(),
                    'investor_id' => $invRow->id,
                    'amount' => 1000 * ($idx+1),
                    'profit_rate' => 5 + $idx,
                    'expected_return' => (1000 * ($idx+1)) * (1 + (0.05 + $idx/100)),
                    'duration_months' => 12,
                    'risk_level' => 'low',
                    'status' => 'active',
                    'created_at' => now(),
                ]);

                License::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $u->id,
                    'business_name' => 'منشأة '.$idx,
                    'business_type' => 'تجاري',
                    'activity_description' => 'نشاط تجريبي',
                    'location' => 'الرياض',
                    'status' => 'approved',
                    'created_at' => now(),
                ]);
            }
        }
    }
}
