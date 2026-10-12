<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Investor;
use App\Models\Investment;

/**
 * Seeder تجريبي لإنشاء استثمار لمستثمر "نيسان" بهدف اختبار تدفق الأرباح والإشعارات.
 *
 * الخطوات:
 * 1) البحث عن المستثمر بالاسم "نيسان" أو البريد الذي يحتوي nisan.
 *    - إذا لم يوجد، يتم إنشاء مستخدم + مستثمر تجريبيين وربطهما معًا.
 * 2) إنشاء استثمار جديد مرتبط بالمستثمر:
 *    - status = pending أولاً ثم التبديل إلى approved لتحفيز الـ Observer.
 *    - duration_months = 3
 *    - profit_rate = 10 (10%)
 *    - amount = 10000
 *    - expected_return = 1000
 *    - approved_at = الآن - 20 دقيقة (اختبار سريع)
 * 3) ملاحظات الاختبار السريع:
 *    - لتسريع الجدولة، عيّن في .env: EARNINGS_SIM_MINUTES_PER_MONTH=10
 *      بهذا سيعتبر النظام كل "شهر" = 10 دقائق عند تشغيل الأمر earnings:process-monthly.
 *
 * تشغيل يدوي:
 * php artisan db:seed --class=InvestmentTestSeeder
 */
class InvestmentTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1) احضر المستثمر "نيسان" بالاسم أو البريد
        $investor = Investor::where('full_name', 'LIKE', 'نيسان%')
            ->orWhere('email', 'LIKE', '%nisan%')
            ->first();

        // إذا لم يوجد، أنشئ مستخدم + مستثمر تجريبيين
        if (!$investor) {
            $userId = (string) Str::uuid();
            $email = 'nisan@example.com';

            // أنشئ مستخدمًا بسيطًا مع بريد للوصول إلى إشعارات البريد
            $user = User::create([
                'id' => $userId,
                'full_name' => 'نيسان',
                'username' => 'nisan',
                'email' => $email,
                'password' => Hash::make('Password123!'),
                'role' => 'investor',
                'is_active' => 1,
            ]);

            $investor = Investor::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'full_name' => 'نيسان',
                'email' => $email,
                'phone' => '0512345678',
                'national_id' => '1000000000',
                'marketing_opt_in' => 1,
            ]);
        } else {
            // إن كان investor موجودًا لكن بلا user_id، اربطه بمستخدم عبر البريد أو أنشئ مستخدمًا جديدًا
            if (empty($investor->user_id)) {
                $user = null;
                if (!empty($investor->email)) {
                    $user = User::where('email', $investor->email)->first();
                }
                if (!$user) {
                    $user = User::create([
                        'id' => (string) Str::uuid(),
                        'full_name' => $investor->full_name ?: 'نيسان',
                        'username' => Str::slug($investor->full_name ?: 'nisan') . '-' . substr(Str::uuid(), 0, 8),
                        'email' => $investor->email ?: ('nisan+' . substr(Str::uuid(), 0, 8) . '@example.com'),
                        'password' => Hash::make('Password123!'),
                        'role' => 'investor',
                        'is_active' => 1,
                    ]);
                }
                $investor->user_id = $user->id;
                $investor->save();
            }
        }

        // 2) أنشئ استثمارًا جديدًا مرتبطًا بالمستثمر (بشكل آمن دون حذف أي بيانات سابقة)
        $amount = 20000.0;
        $rate = 10.0; // 10%
        $duration = 3; // أشهر
        $expected = round($amount * ($rate / 100.0), 2); // 1000.00

        $now = Carbon::now();
        $approvedAt = (clone $now)->subMinutes(20);

        // أنشئ استثمارًا بالحالة pending أولًا
        $inv = new Investment([
            'id' => (string) Str::uuid(),
            'investor_id' => $investor->id,
            'package_id' => null, // أو ضع قيمة مناسبة إن لزم
            'amount' => $amount,
            'profit_rate' => $rate,
            'expected_return' => $expected,
            'duration_months' => $duration,
            'risk_level' => null,
            'notes' => 'Test seeder investment for Nissan',
            'status' => 'pending',
            'created_at' => $now->toDateTimeString(),
        ]);
        $inv->save();

        // التبديل إلى approved + ختم approved_at لتحفيز الـ Observer على إنشاء شهر 1 وإرسال إشعار
        $inv->approved_at = $approvedAt->toDateTimeString();
        $inv->status = 'approved';
        $inv->save();

        // تعليمات للمطور: لتسريع الاختبار، ضع في .env:
        // EARNINGS_SIM_MINUTES_PER_MONTH=10
        // ثم شغّل:
        // php artisan earnings:process-monthly

        $this->command->info('InvestmentTestSeeder: تم إعداد مستثمر "نيسان" واستثمار approved لمدة 3 أشهر.');
        $this->command->info('نصيحة: فعّل EARNINGS_SIM_MINUTES_PER_MONTH=10 لاختبار توليد الأرباح كل 10 دقائق.');
    }
}
