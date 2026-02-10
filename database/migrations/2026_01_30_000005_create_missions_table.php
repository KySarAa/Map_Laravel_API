<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->date('date_mission');
            $table->enum('statut', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('operator_id')->nullable()->constrained('users');

            // Paramètres métier
            $table->string('culture')->nullable(); // Type de culture
            $table->decimal('target_dose', 8, 2)->nullable(); // Dose cible (L/ha)
            $table->decimal('target_speed', 5, 2)->nullable(); // Vitesse cible (km/h)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
