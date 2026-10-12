<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('tc_risk_levels', 'level')) {
            Schema::table('tc_risk_levels', function (Blueprint $table) {
                $table->string('level')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tc_risk_levels', 'level')) {
            Schema::table('tc_risk_levels', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
};
