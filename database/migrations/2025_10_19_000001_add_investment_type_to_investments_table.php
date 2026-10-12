<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvestmentTypeToInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('investments')) {
            Schema::table('investments', function (Blueprint $table) {
                // store 'large' or 'small'
                if (!Schema::hasColumn('investments', 'investment_type')) {
                    $table->string('investment_type')->nullable()->default('small')->after('amount');
                }
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
        if (Schema::hasTable('investments')) {
            Schema::table('investments', function (Blueprint $table) {
                if (Schema::hasColumn('investments', 'investment_type')) {
                    $table->dropColumn('investment_type');
                }
            });
        }
    }
}
