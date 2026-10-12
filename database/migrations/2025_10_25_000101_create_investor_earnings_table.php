<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('investor_earnings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('investment_id');
            $table->string('investor_id');
            $table->integer('month_number');
            $table->decimal('profit_amount', 15, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('investor_earnings');
    }
};
