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
        Schema::create('event_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->onDelete('cascade');

            // Catering
            $table->boolean('catering_required')->default(false);
            $table->unsignedInteger('catering_people')->nullable();
            $table->string('dietary_requirements')->nullable();
            $table->text('catering_notes')->nullable();

            // Photography
            $table->boolean('photography_required')->default(false);
            $table->string('photography_type')->nullable();

            // Security
            $table->boolean('security_required')->default(false);
            $table->unsignedInteger('security_guards')->nullable();
            $table->text('risk_assessment')->nullable();
            $table->text('security_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_services');
    }
};
