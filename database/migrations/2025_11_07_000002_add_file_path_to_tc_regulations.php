<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tc_regulations')) return;
        Schema::table('tc_regulations', function (Blueprint $table) {
            if (!Schema::hasColumn('tc_regulations', 'file_path')) {
                $table->string('file_path')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('tc_regulations')) return;
        Schema::table('tc_regulations', function (Blueprint $table) {
            if (Schema::hasColumn('tc_regulations', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};
