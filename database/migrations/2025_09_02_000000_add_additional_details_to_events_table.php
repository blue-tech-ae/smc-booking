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
        Schema::table('events', function (Blueprint $table) {
            $table->json('setup_details')->nullable()->after('security_note');
            $table->json('gift_details')->nullable()->after('setup_details');
            $table->json('floral_details')->nullable()->after('gift_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['setup_details', 'gift_details', 'floral_details']);
        });
    }
};
