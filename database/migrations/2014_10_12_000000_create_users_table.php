<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // للحسابات المحلية فقط

            $table->string('azure_id')->nullable()->unique(); // لمستخدمي Microsoft SSO
            $table->string('avatar')->nullable(); // صورة المستخدم من SSO (اختياري)

            $table->boolean('is_active')->default(true); // لتعطيل الحساب من قبل الإدارة

            $table->rememberToken(); // للواجهات أو تسجيل الدخول المستمر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
