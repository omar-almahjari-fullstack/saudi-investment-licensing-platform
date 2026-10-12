<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlatformAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $accounts = [
            ['name' => 'Main Account', 'type' => 'main', 'balance' => 0.00, 'currency' => 'USD', 'is_active' => 1],
            ['name' => 'Commission Account', 'type' => 'commission', 'balance' => 0.00, 'currency' => 'USD', 'is_active' => 1],
            ['name' => 'Escrow Account', 'type' => 'escrow', 'balance' => 0.00, 'currency' => 'USD', 'is_active' => 1],
        ];
        foreach ($accounts as $acc) {
            $exists = DB::table('platform_accounts')->where('name',$acc['name'])->where('type',$acc['type'])->exists();
            if (!$exists) {
                DB::table('platform_accounts')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'balance' => $acc['balance'],
                    'currency' => $acc['currency'],
                    'is_active' => $acc['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
