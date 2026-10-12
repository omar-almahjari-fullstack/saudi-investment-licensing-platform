<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $seed = [
            ['name' => 'Credit Card', 'description' => 'Visa/Mastercard/Mada', 'is_active' => 1],
            ['name' => 'Apple Pay', 'description' => 'Apple Pay', 'is_active' => 1],
            ['name' => 'Bank Transfer', 'description' => 'Manual IBAN transfer with receipt', 'is_active' => 1],
        ];
        foreach ($seed as $row) {
            $exists = DB::table('payment_methods')->where('name', $row['name'])->exists();
            if (!$exists) {
                DB::table('payment_methods')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'is_active' => $row['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
