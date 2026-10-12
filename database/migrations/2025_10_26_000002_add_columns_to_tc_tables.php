<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // tc_regulations: add name_en, license, status, valid_until, icon
        if (!Schema::hasColumn('tc_regulations', 'name_en')) {
            Schema::table('tc_regulations', function (Blueprint $table) {
                $table->string('name_en')->nullable()->after('title');
            });
        }
        if (!Schema::hasColumn('tc_regulations', 'license')) {
            Schema::table('tc_regulations', function (Blueprint $table) {
                $table->string('license')->nullable()->after('name_en');
            });
        }
        if (!Schema::hasColumn('tc_regulations', 'status')) {
            Schema::table('tc_regulations', function (Blueprint $table) {
                $table->string('status')->nullable()->after('license');
            });
        }
        if (!Schema::hasColumn('tc_regulations', 'valid_until')) {
            Schema::table('tc_regulations', function (Blueprint $table) {
                $table->date('valid_until')->nullable()->after('status');
            });
        }
        if (!Schema::hasColumn('tc_regulations', 'icon')) {
            Schema::table('tc_regulations', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('valid_until');
            });
        }

        // tc_security_features: add level, icon
        if (!Schema::hasColumn('tc_security_features', 'level')) {
            Schema::table('tc_security_features', function (Blueprint $table) {
                $table->string('level')->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('tc_security_features', 'icon')) {
            Schema::table('tc_security_features', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('level');
            });
        }

        // tc_risk_levels: add color and percentage_text (preserve original range string)
        if (!Schema::hasColumn('tc_risk_levels', 'color')) {
            Schema::table('tc_risk_levels', function (Blueprint $table) {
                $table->string('color')->nullable()->after('percentage');
            });
        }
        if (!Schema::hasColumn('tc_risk_levels', 'percentage_text')) {
            Schema::table('tc_risk_levels', function (Blueprint $table) {
                $table->string('percentage_text')->nullable()->after('color');
            });
        }

        // tc_certificates: add description, status
        if (!Schema::hasColumn('tc_certificates', 'description')) {
            Schema::table('tc_certificates', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
        if (!Schema::hasColumn('tc_certificates', 'status')) {
            Schema::table('tc_certificates', function (Blueprint $table) {
                $table->string('status')->nullable()->after('expires_at');
            });
        }

        // tc_audit_reports: add type, status, file_size, audit_date already exists in later migration but keep checks
        if (!Schema::hasColumn('tc_audit_reports', 'type')) {
            Schema::table('tc_audit_reports', function (Blueprint $table) {
                $table->string('type')->nullable()->after('date');
            });
        }
        if (!Schema::hasColumn('tc_audit_reports', 'status')) {
            Schema::table('tc_audit_reports', function (Blueprint $table) {
                $table->string('status')->nullable()->after('type');
            });
        }
        if (!Schema::hasColumn('tc_audit_reports', 'file_size')) {
            Schema::table('tc_audit_reports', function (Blueprint $table) {
                $table->string('file_size')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tc_regulations', function (Blueprint $table) {
            if (Schema::hasColumn('tc_regulations', 'name_en')) $table->dropColumn('name_en');
            if (Schema::hasColumn('tc_regulations', 'license')) $table->dropColumn('license');
            if (Schema::hasColumn('tc_regulations', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('tc_regulations', 'valid_until')) $table->dropColumn('valid_until');
            if (Schema::hasColumn('tc_regulations', 'icon')) $table->dropColumn('icon');
        });

        Schema::table('tc_security_features', function (Blueprint $table) {
            if (Schema::hasColumn('tc_security_features', 'level')) $table->dropColumn('level');
            if (Schema::hasColumn('tc_security_features', 'icon')) $table->dropColumn('icon');
        });

        Schema::table('tc_risk_levels', function (Blueprint $table) {
            if (Schema::hasColumn('tc_risk_levels', 'color')) $table->dropColumn('color');
            if (Schema::hasColumn('tc_risk_levels', 'percentage_text')) $table->dropColumn('percentage_text');
        });

        Schema::table('tc_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('tc_certificates', 'description')) $table->dropColumn('description');
            if (Schema::hasColumn('tc_certificates', 'status')) $table->dropColumn('status');
        });

        Schema::table('tc_audit_reports', function (Blueprint $table) {
            if (Schema::hasColumn('tc_audit_reports', 'type')) $table->dropColumn('type');
            if (Schema::hasColumn('tc_audit_reports', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('tc_audit_reports', 'file_size')) $table->dropColumn('file_size');
        });
    }
};
