<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('investment_packages', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('profit_rate');
            }
        });

        // Ensure existing rows are marked active
        if (Schema::hasColumn('investment_packages', 'is_active')) {
            \Illuminate\Support\Facades\DB::table('investment_packages')
                ->whereNull('is_active')
                ->update(['is_active' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            if (Schema::hasColumn('investment_packages', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
