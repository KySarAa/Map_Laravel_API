<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('path_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');

            // Géolocalisation haute précision (RTK)
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 8, 2)->nullable();

            // Métriques de rampe
            $table->decimal('speed', 5, 2)->nullable(); // Vitesse réelle
            $table->decimal('pressure', 5, 2)->nullable(); // Pression des buses

            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('path_points');
    }
};
