<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_transactions', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('platform_account_id');
            $table->string('payment_id')->nullable();
            $table->string('withdrawal_request_id')->nullable();
            $table->string('investment_transaction_id')->nullable();
            $table->enum('type', ['credit','debit']);
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_before', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();

            $table->foreign('platform_account_id')->references('id')->on('platform_accounts')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->foreign('withdrawal_request_id')->references('id')->on('withdrawal_requests')->onDelete('set null');
            $table->foreign('investment_transaction_id')->references('id')->on('investment_transactions')->onDelete('set null');

            $table->index(['platform_account_id','transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_transactions');
    }
};
