<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('conversation_id');
            $table->string('user_id');
            $table->unsignedInteger('unread_count')->default(0);
            $table->string('last_read_message_id')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id','user_id']);
            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};
