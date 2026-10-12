<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('market_indicators', 'date')) {
            Schema::table('market_indicators', function (Blueprint $table) {
                $table->timestamp('date')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('market_indicators', 'date')) {
            Schema::table('market_indicators', function (Blueprint $table) {
                $table->dropColumn('date');
            });
        }
    }
};
