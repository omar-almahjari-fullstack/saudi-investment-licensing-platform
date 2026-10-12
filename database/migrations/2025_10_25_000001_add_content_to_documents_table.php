<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Use a direct ALTER statement to create a LONGBLOB column so larger files
        // (up to several hundred MB / GB depending on MySQL settings) can be stored.
        // Using Schema::table()->binary() maps to BLOB (64KB) which is usually too small.
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `documents` ADD COLUMN `content` LONGBLOB NULL AFTER `path`");
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
