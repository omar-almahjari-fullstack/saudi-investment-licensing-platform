<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investor_profiles', function (Blueprint $table) {
            $table->string('id')->primary(); // keep string UUID style consistent
            $table->string('user_id');
            $table->string('nationality')->nullable();
            $table->string('city')->nullable();
            $table->string('investor_type')->nullable(); // individual | institutional
            $table->string('expected_amount')->nullable(); // e.g., ranges like 1000-10000
            $table->string('investment_duration')->nullable(); // e.g., 6months, 1year
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_profiles');
    }
};
