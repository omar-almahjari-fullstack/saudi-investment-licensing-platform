<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add investor_id (nullable first), add FK, and backfill from investors.user_id
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'investor_id')) {
                $table->string('investor_id')->nullable()->after('id');
            }
        });

        // Backfill investor_id from investors table using existing user_id linkage
        try {
            DB::statement('UPDATE investments i JOIN investors inv ON inv.user_id = i.user_id SET i.investor_id = inv.id');
        } catch (\Throwable $e) {
            // ignore backfill issues; caller can handle manually
        }

        // Add FK constraint if not already present
        try {
            $dbName = config('database.connections.'.config('database.default').'.database');
            $rows = DB::select(
                'SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$dbName, 'investments', 'investor_id']
            );
            $hasFk = count($rows) > 0;
        } catch (\Throwable $e) { $hasFk = false; }
        if (!$hasFk) {
            Schema::table('investments', function (Blueprint $table) {
                $table->foreign('investor_id')->references('id')->on('investors')->onDelete('cascade');
            });
        }

        // 2) Make investor_id NOT NULL via raw SQL (avoids requiring doctrine/dbal)
        try { DB::statement('ALTER TABLE investments MODIFY investor_id varchar(255) NOT NULL'); } catch (\Throwable $e) {}

        // 3) Ensure package_id exists and FK is set (SET NULL on delete)
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('investor_id');
            }
        });
        try {
            $dbName = config('database.connections.'.config('database.default').'.database');
            $rows = DB::select(
                'SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$dbName, 'investments', 'package_id']
            );
            $hasFk = count($rows) > 0;
        } catch (\Throwable $e) { $hasFk = false; }
        if (!$hasFk && Schema::hasColumn('investments', 'package_id')) {
            Schema::table('investments', function (Blueprint $table) {
                $table->foreign('package_id')->references('id')->on('investment_packages')->onDelete('set null');
            });
        }

        // 4) Drop user_id FK and column if present
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'user_id')) {
                try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            }
        });
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });

        // 5) Drop investment_type column to match target schema
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'investment_type')) {
                $table->dropColumn('investment_type');
            }
        });

        // 6) Ensure column types/defaults roughly match target (best-effort w/o DBAL)
        try { DB::statement('ALTER TABLE investments MODIFY amount DECIMAL(14,2) NOT NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE investments MODIFY profit_rate DECIMAL(5,2) NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE investments MODIFY expected_return DECIMAL(14,2) NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE investments MODIFY duration_months INT NOT NULL DEFAULT 12'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE investments MODIFY risk_level VARCHAR(255) NULL'); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE investments MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'"); } catch (\Throwable $e) {}
        // notes already text nullable typically; skip altering timestamps defaults for portability
    }

    public function down(): void
    {
        // Recreate user_id and drop investor_id to revert
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'user_id')) {
                $table->string('user_id')->nullable()->after('id');
            }
        });
        // Backfill user_id from investors again
        try {
            DB::statement('UPDATE investments i JOIN investors inv ON inv.id = i.investor_id SET i.user_id = inv.user_id');
        } catch (\Throwable $e) {}

        // Drop FK and column investor_id
        Schema::table('investments', function (Blueprint $table) {
            try { $table->dropForeign(['investor_id']); } catch (\Throwable $e) {}
        });
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'investor_id')) {
                $table->dropColumn('investor_id');
            }
        });

        // Optionally add back investment_type
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'investment_type')) {
                $table->string('investment_type')->nullable()->after('amount');
            }
        });

        // Make user_id NOT NULL and FK
        try { DB::statement('ALTER TABLE investments MODIFY user_id varchar(255) NOT NULL'); } catch (\Throwable $e) {}
        Schema::table('investments', function (Blueprint $table) {
            try { $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); } catch (\Throwable $e) {}
        });
    }
};
