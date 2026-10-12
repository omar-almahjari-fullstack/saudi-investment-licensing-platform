<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_infos', 'updated_by_uuid')) {
                $table->string('updated_by_uuid', 64)->nullable()->after('updated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_infos', function (Blueprint $table) {
            if (Schema::hasColumn('contact_infos', 'updated_by_uuid')) {
                $table->dropColumn('updated_by_uuid');
            }
        });
    }
};
