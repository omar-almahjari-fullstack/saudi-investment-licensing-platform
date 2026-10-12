<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('investment_transactions', 'user_id')) {
                // make user_id not nullable if possible
                try {
                    $table->string('user_id')->nullable(false)->change();
                } catch (\Throwable $e) {
                    // if there are existing nulls or platform limitations, skip strict change
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('investment_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('investment_transactions', 'user_id')) {
                try {
                    $table->string('user_id')->nullable()->change();
                } catch (\Throwable $e) {}
            }
        });
    }
};
