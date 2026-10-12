<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tc_metrics', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->string('icon')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tc_metrics');
    }
};
