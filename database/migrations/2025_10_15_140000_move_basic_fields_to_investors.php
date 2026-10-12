<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'national_id')) {
                $table->dropColumn('national_id');
            }
            if (Schema::hasColumn('users', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('users', 'identity_image')) {
                $table->dropColumn('identity_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('users', 'identity_image')) {
                $table->string('identity_image')->nullable()->after('bank_account');
            }
        });
    }
};
