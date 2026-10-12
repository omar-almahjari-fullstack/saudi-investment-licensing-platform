<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('success_stories')) return;
        Schema::table('success_stories', function (Blueprint $table) {
            if (!Schema::hasColumn('success_stories', 'investment_amount')) {
                $table->string('investment_amount')->nullable()->after('location');
            }
            if (!Schema::hasColumn('success_stories', 'profit_rate')) {
                $table->string('profit_rate')->nullable()->after('profit');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('success_stories')) return;
        Schema::table('success_stories', function (Blueprint $table) {
            if (Schema::hasColumn('success_stories', 'investment_amount')) {
                $table->dropColumn('investment_amount');
            }
            if (Schema::hasColumn('success_stories', 'profit_rate')) {
                $table->dropColumn('profit_rate');
            }
        });
    }
};
