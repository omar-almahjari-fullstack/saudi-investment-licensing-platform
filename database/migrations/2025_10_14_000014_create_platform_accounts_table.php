<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_accounts', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('name');
            $table->enum('type', ['main','commission','escrow']);
            $table->decimal('balance', 16, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_accounts');
    }
};
