<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'identity_image')) {
                $table->string('identity_image')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'identity_image_front')) {
                $table->string('identity_image_front')->nullable()->after('identity_image');
            }
            if (! Schema::hasColumn('users', 'identity_image_back')) {
                $table->string('identity_image_back')->nullable()->after('identity_image_front');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'identity_image_back')) {
                $table->dropColumn('identity_image_back');
            }
            if (Schema::hasColumn('users', 'identity_image_front')) {
                $table->dropColumn('identity_image_front');
            }
            if (Schema::hasColumn('users', 'identity_image')) {
                $table->dropColumn('identity_image');
            }
        });
    }
};
