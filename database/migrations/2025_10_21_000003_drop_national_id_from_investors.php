<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // This migration will drop the investors.national_id column.
        // DO NOT RUN until you have verified all references use users.national_id and you have DB backups.
        if (Schema::hasColumn('investors', 'national_id')) {
            Schema::table('investors', function (Blueprint $table) {
                $table->dropColumn('national_id');
            });
        }
    }

    public function down()
    {
        if (! Schema::hasColumn('investors', 'national_id')) {
            Schema::table('investors', function (Blueprint $table) {
                $table->string('national_id')->nullable()->after('phone');
            });
        }
    }
};
