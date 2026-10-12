<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure investors has nationality column
        Schema::table('investors', function (Blueprint $table) {
            if (!Schema::hasColumn('investors', 'nationality')) {
                $table->string('nationality')->nullable()->after('identity_image');
            }
        });

        // Migrate data from users.nationality to investors.nationality when possible
        if (Schema::hasColumn('users', 'nationality')) {
            DB::statement('UPDATE investors i JOIN users u ON i.user_id = u.id SET i.nationality = COALESCE(i.nationality, u.nationality)');
            // Drop from users
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nationality');
            });
        }
    }

    public function down(): void
    {
        // Add back nationality to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('email');
            }
        });
        // Optionally copy back
        DB::statement('UPDATE users u JOIN investors i ON i.user_id = u.id SET u.nationality = COALESCE(u.nationality, i.nationality)');
    }
};
