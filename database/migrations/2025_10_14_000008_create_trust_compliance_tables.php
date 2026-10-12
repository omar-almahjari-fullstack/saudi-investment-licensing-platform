<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tc_regulations', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('tc_security_features', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tc_risk_levels', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('label');
            $table->unsignedTinyInteger('percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('tc_certificates', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tc_audit_reports', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->date('date')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tc_audit_reports');
        Schema::dropIfExists('tc_certificates');
        Schema::dropIfExists('tc_risk_levels');
        Schema::dropIfExists('tc_security_features');
        Schema::dropIfExists('tc_regulations');
    }
};
