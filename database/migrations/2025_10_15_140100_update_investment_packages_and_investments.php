<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update investment_packages schema
        Schema::table('investment_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('investment_packages', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('investment_packages', 'duration_months')) {
                $table->integer('duration_months')->nullable()->default(12)->after('profit_rate');
            }
            if (!Schema::hasColumn('investment_packages', 'description')) {
                $table->text('description')->nullable()->after('duration_months');
            }
        });

        // Update investments schema
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('user_id');
            }
            if (Schema::hasColumn('investments', 'investment_type')) {
                $table->dropColumn('investment_type');
            }
            if (Schema::hasColumn('investments', 'duration_months')) {
                // ensure default is 12
                $table->integer('duration_months')->default(12)->change();
            } else {
                $table->integer('duration_months')->default(12)->after('expected_return');
            }
            // ensure status size and default
            if (Schema::hasColumn('investments', 'status')) {
                $table->string('status', 50)->default('pending')->change();
            }
            // Add FK for package
            if (!Schema::hasColumn('investments', 'package_id')) {
                // already handled above
            }
        });

        // Add FK constraints separately to avoid issues when column exists without constraint
        if (Schema::hasColumn('investments', 'package_id')) {
            // Check if a foreign key already exists on package_id
            $exists = false;
            try {
                $dbName = config('database.connections.'.config('database.default').'.database');
                $rows = \Illuminate\Support\Facades\DB::select(
                    'SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                    [$dbName, 'investments', 'package_id']
                );
                $exists = count($rows) > 0;
            } catch (\Throwable $e) {
                $exists = false;
            }
            if (!$exists) {
                Schema::table('investments', function (Blueprint $table) {
                    $table->foreign('package_id')->references('id')->on('investment_packages')->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            // remove package FK and column
            try { $table->dropForeign(['package_id']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('investments', 'package_id')) {
                $table->dropColumn('package_id');
            }
            if (!Schema::hasColumn('investments', 'investment_type')) {
                $table->string('investment_type')->after('amount');
            }
            // leave duration default as-is
        });

        Schema::table('investment_packages', function (Blueprint $table) {
            if (Schema::hasColumn('investment_packages', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('investment_packages', 'duration_months')) {
                $table->dropColumn('duration_months');
            }
            if (Schema::hasColumn('investment_packages', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
