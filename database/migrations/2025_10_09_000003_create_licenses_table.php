<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('business_name');
            $table->string('business_type');
            $table->text('activity_description')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};
