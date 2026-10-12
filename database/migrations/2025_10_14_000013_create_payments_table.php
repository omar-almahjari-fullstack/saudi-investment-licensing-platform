<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('user_id');
            $table->string('investment_id')->nullable();
            $table->string('withdrawal_request_id')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('currency')->default('USD');
            $table->enum('status', ['pending','completed','failed','refunded'])->default('pending');
            $table->string('transaction_reference')->unique();
            $table->timestamp('processed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('set null');
            $table->foreign('withdrawal_request_id')->references('id')->on('withdrawal_requests')->onDelete('set null');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');

            $table->index(['user_id','status']);
            $table->index(['investment_id']);
            $table->index(['withdrawal_request_id']);
            $table->index(['payment_method_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
