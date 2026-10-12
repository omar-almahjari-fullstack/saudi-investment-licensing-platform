<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIdentityColumnsFromInvestors extends Migration
{
    /**
     * Run the migrations.
     * Drops identity_image, identity_image_front, identity_image_back from investors if they exist.
     * This operation is destructive: data in those columns will be lost. Ensure you have a backup.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('investors')) {
            return;
        }

        // Drop only existing columns to avoid SQL errors on older/newer schemas
        $columnsToDrop = [];
        if (Schema::hasColumn('investors', 'identity_image')) {
            $columnsToDrop[] = 'identity_image';
        }
        if (Schema::hasColumn('investors', 'identity_image_front')) {
            $columnsToDrop[] = 'identity_image_front';
        }
        if (Schema::hasColumn('investors', 'identity_image_back')) {
            $columnsToDrop[] = 'identity_image_back';
        }

        if (count($columnsToDrop) === 0) {
            return;
        }

        Schema::table('investors', function (Blueprint $table) use ($columnsToDrop) {
            // Some DB drivers allow dropping multiple columns at once; Laravel will handle the SQL generation.
            $table->dropColumn($columnsToDrop);
        });
    }

    /**
     * Reverse the migrations.
     * Re-adds the columns as nullable strings (data will not be restored by this migration).
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('investors')) {
            return;
        }

        Schema::table('investors', function (Blueprint $table) {
            if (!Schema::hasColumn('investors', 'identity_image')) {
                $table->string('identity_image')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('investors', 'identity_image_front')) {
                $table->string('identity_image_front')->nullable()->after('identity_image');
            }
            if (!Schema::hasColumn('investors', 'identity_image_back')) {
                $table->string('identity_image_back')->nullable()->after('identity_image_front');
            }
        });
    }
}
