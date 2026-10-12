<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNationalIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only add if missing to be safe on existing installations
        if (! Schema::hasColumn('users', 'national_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('national_id', 50)->nullable()->after('email')->comment('National ID stored once per user');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'national_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('national_id');
            });
        }
    }
}
