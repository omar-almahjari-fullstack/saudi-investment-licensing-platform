<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('investment_id');
            $table->string('user_id')->nullable(); // optional actor/owner, investment links to user
            $table->string('type'); // credit | debit
            $table->decimal('amount', 14, 2);
            $table->text('note')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['investment_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
