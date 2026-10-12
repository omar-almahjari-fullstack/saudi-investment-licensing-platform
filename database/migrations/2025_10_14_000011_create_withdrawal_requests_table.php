<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('investment_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('pending'); // pending, approved, rejected, paid
            $table->text('reason')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('set null');
            $table->index(['user_id','investment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
