<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};
