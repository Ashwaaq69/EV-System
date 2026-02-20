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
        Schema::create('meter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('charging_sessions')->onDelete('cascade');
            $table->foreignId('charge_point_id')->constrained('charge_points')->onDelete('cascade');
            $table->decimal('value', 10, 4); // energy in kWh
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_values');
    }
};
