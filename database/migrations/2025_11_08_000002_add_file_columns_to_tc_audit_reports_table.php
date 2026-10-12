<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tc_audit_reports', function (Blueprint $table) {
            // add file metadata columns if they don't already exist
            if (!Schema::hasColumn('tc_audit_reports', 'file_path')) {
                $table->string('file_path')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tc_audit_reports', 'file_name')) {
                $table->string('file_name')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('tc_audit_reports', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('tc_audit_reports', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('mime_type');
            }
        });
    }

    public function down()
    {
        Schema::table('tc_audit_reports', function (Blueprint $table) {
            // drop if exist (down may fail on older MySQL versions if columns absent; it's acceptable)
            if (Schema::hasColumn('tc_audit_reports', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('tc_audit_reports', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
            if (Schema::hasColumn('tc_audit_reports', 'file_name')) {
                $table->dropColumn('file_name');
            }
            if (Schema::hasColumn('tc_audit_reports', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};
