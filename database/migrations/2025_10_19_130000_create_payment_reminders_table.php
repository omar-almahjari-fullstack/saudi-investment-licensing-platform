<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('investment_id')->index();
            $table->string('admin_id')->nullable()->index();
            $table->text('note')->nullable();
            // store an explicit requested amount (nullable) so reminders can display exact value
            $table->decimal('amount', 14, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_reminders');
    }
};
