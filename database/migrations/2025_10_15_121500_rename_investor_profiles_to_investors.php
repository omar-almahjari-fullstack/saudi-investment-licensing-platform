<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('investor_profiles') && !Schema::hasTable('investors')) {
            Schema::rename('investor_profiles', 'investors');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('investors') && !Schema::hasTable('investor_profiles')) {
            Schema::rename('investors', 'investor_profiles');
        }
    }
};
