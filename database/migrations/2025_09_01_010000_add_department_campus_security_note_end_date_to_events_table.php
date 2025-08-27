<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Campus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('department_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->enum('campus', [
                Campus::DAVISSON_STREET->value,
                Campus::DALTON_ROAD->value,
                Campus::SGC->value,
            ])->after('location_id');
            $table->text('security_note')->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id','campus','security_note']);
        });
    }
};

