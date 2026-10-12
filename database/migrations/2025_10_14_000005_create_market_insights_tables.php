<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Indicators
        Schema::create('market_indicators', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('name');
            $table->string('value');
            $table->string('change')->nullable();
            $table->string('trend')->nullable(); // up/down/flat
            $table->timestamps();
        });

        // Sectors
        Schema::create('market_sectors', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('performance')->nullable();
            $table->string('volume')->nullable();
            $table->string('trend')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // News
        Schema::create('market_news', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->date('date')->nullable();
            $table->string('category')->nullable();
            $table->string('read_time')->nullable();
            $table->timestamps();
        });

        // Trends
        Schema::create('market_trends', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('trend');
            $table->string('growth')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_trends');
        Schema::dropIfExists('market_news');
        Schema::dropIfExists('market_sectors');
        Schema::dropIfExists('market_indicators');
    }
};
