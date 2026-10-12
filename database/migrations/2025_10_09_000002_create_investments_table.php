<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->decimal('amount', 14, 2);
            $table->string('investment_type');
            $table->decimal('profit_rate', 5, 2)->nullable();
            $table->decimal('expected_return', 14,2)->nullable();
            $table->integer('duration_months')->default(1);
            $table->string('risk_level')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('investments');
    }
};
