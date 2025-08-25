<?php

use App\Enums\Campus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->enum('campus', [
                Campus::DAVISSON_STREET->value,
                Campus::DALTON_ROAD->value,
                Campus::SGC->value,
            ])->after('name');
            $table->text('description')->nullable()->after('campus');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['campus', 'description']);
        });
    }
};
