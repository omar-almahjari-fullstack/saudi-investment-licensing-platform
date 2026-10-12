<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('market_news', function (Blueprint $table) {
            if (!Schema::hasColumn('market_news', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('read_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('market_news', function (Blueprint $table) {
            if (Schema::hasColumn('market_news', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
