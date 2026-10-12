<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		if (! Schema::hasTable('investor_accounts')) {
			Schema::create('investor_accounts', function (Blueprint $table) {
				$table->id();
				$table->string('user_id')->nullable();
				$table->string('account_number')->nullable();
				$table->string('bank_name')->nullable();
				$table->timestamps();
			});
		}
	}

	public function down()
	{
		Schema::dropIfExists('investor_accounts');
	}
};

