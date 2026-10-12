<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            if (!Schema::hasColumn('investors', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('investors', 'email')) {
                $table->string('email')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('investors', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('investors', 'national_id')) {
                $table->string('national_id')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('investors', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('investors', 'identity_image')) {
                $table->string('identity_image')->nullable()->after('bank_account');
            }
            if (!Schema::hasColumn('investors', 'investment_size')) {
                $table->string('investment_size')->nullable()->after('investment_duration'); // small | large
            }
            if (!Schema::hasColumn('investors', 'notes')) {
                $table->text('notes')->nullable()->after('investment_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            foreach (['notes','investment_size','identity_image','bank_account','national_id','phone','email','full_name'] as $col) {
                if (Schema::hasColumn('investors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
