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
            if (!Schema::hasColumn('event_services', 'service_type')) {
                $table->enum('service_type', ['catering', 'photography', 'security'])->after('event_id');
            }
            if (!Schema::hasColumn('event_services', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('service_type')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_services', function (Blueprint $table) {
            if (Schema::hasColumn('event_services', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }
            if (Schema::hasColumn('event_services', 'service_type')) {
                $table->dropColumn('service_type');
            }
        });
    }
};
