<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('permission_key');
            $table->timestamps();
            $table->unique(['user_id','permission_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_permissions');
    }
};
