<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            // أضف فهرسًا/مرجعًا للPermission عند الإمكان
            if (!Schema::hasColumn('role_permissions', 'permission_key')) {
                $table->string('permission_key');
            }
            // لا يوجد مفتاح خارجي للدور (role) لأنه نص حر، لكن يمكن ربط permission_key بجدول permissions.key
            try {
                $table->foreign('permission_key')->references('key')->on('permissions')->cascadeOnDelete();
            } catch (\Throwable $e) {
                // تجاهل في حال كان المفتاح موجودًا مسبقًا
            }
        });

        Schema::table('user_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_permissions', 'user_id')) {
                $table->string('user_id');
            }
            if (!Schema::hasColumn('user_permissions', 'permission_key')) {
                $table->string('permission_key');
            }
            try {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {}
            try {
                $table->foreign('permission_key')->references('key')->on('permissions')->cascadeOnDelete();
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            try { $table->dropForeign(['permission_key']); } catch (\Throwable $e) {}
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['permission_key']); } catch (\Throwable $e) {}
        });
    }
};
