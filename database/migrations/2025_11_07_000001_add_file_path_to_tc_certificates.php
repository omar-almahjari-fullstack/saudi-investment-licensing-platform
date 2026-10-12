<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tc_certificates')) return;
        Schema::table('tc_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('tc_certificates', 'file_path')) {
                $table->string('file_path')->nullable()->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tc_certificates')) return;
        Schema::table('tc_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('tc_certificates', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};
