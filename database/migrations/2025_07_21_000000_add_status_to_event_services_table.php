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
        Schema::table('event_services', function (Blueprint $table) {
            if (!Schema::hasColumn('event_services', 'status')) {
                $table->enum('status', ['pending','accepted','rejected'])->default('pending')->after('assigned_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_services', function (Blueprint $table) {
            if (Schema::hasColumn('event_services', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
