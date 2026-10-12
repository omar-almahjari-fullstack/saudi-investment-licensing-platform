<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('success_stories', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('location')->nullable();
            $table->string('amount')->nullable();
            $table->string('returns')->nullable();
            $table->string('duration')->nullable();
            $table->string('profit')->nullable();
            $table->string('category')->default('small'); // small/large
            $table->boolean('featured')->default(false);
            $table->boolean('is_top')->default(false);
            $table->text('story')->nullable();
            $table->text('testimonial')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('success_stories');
    }
};
