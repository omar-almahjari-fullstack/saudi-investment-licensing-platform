<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('tc_audit_reports', 'audit_date')) {
            Schema::table('tc_audit_reports', function (Blueprint $table) {
                $table->timestamp('audit_date')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tc_audit_reports', 'audit_date')) {
            Schema::table('tc_audit_reports', function (Blueprint $table) {
                $table->dropColumn('audit_date');
            });
        }
    }
};
