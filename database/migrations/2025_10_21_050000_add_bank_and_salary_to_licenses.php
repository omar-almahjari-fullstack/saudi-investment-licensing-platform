<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('licenses')) {
            return;
        }

        Schema::table('licenses', function (Blueprint $table) {
            if (! Schema::hasColumn('licenses', 'bank_statement')) {
                $table->string('bank_statement')->nullable()->after('activity_description');
            }
            if (! Schema::hasColumn('licenses', 'salary_certificate')) {
                $table->string('salary_certificate')->nullable()->after('bank_statement');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('licenses')) {
            return;
        }

        Schema::table('licenses', function (Blueprint $table) {
            if (Schema::hasColumn('licenses', 'salary_certificate')) {
                $table->dropColumn('salary_certificate');
            }
            if (Schema::hasColumn('licenses', 'bank_statement')) {
                $table->dropColumn('bank_statement');
            }
        });
    }
};
