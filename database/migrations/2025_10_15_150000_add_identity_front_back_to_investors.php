<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            if (!Schema::hasColumn('investors', 'identity_image_front')) {
                $table->string('identity_image_front')->nullable()->after('identity_image');
            }
            if (!Schema::hasColumn('investors', 'identity_image_back')) {
                $table->string('identity_image_back')->nullable()->after('identity_image_front');
            }
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            if (Schema::hasColumn('investors', 'identity_image_back')) {
                $table->dropColumn('identity_image_back');
            }
            if (Schema::hasColumn('investors', 'identity_image_front')) {
                $table->dropColumn('identity_image_front');
            }
        });
    }
};
