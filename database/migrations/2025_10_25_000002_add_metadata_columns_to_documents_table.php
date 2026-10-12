<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Add commonly useful metadata columns for the documents page
        \Illuminate\Support\Facades\DB::statement(<<<'SQL'
            ALTER TABLE `documents`
            ADD COLUMN `size_bytes` BIGINT UNSIGNED NULL AFTER `path`,
            ADD COLUMN `mime_type` VARCHAR(191) NULL AFTER `size_bytes`,
            ADD COLUMN `status` VARCHAR(50) NULL AFTER `mime_type`,
            ADD COLUMN `uploaded_at` TIMESTAMP NULL AFTER `status`;
        SQL
        );
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['size_bytes','mime_type','status','uploaded_at']);
        });
    }
};
