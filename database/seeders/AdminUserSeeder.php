<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // create a known admin user if not exists
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'admin123');
        $user = User::where('email', $email)->first();
        if (!$user) {
            User::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'full_name' => 'Administrator',
                'username' => 'admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]);
        } else {
            // update to ensure predictable local credentials
            $user->username = $user->username ?: 'admin';
            $user->role = 'admin';
            $user->is_active = true;
            $user->password = Hash::make($password);
            $user->save();
        }
    }
}
