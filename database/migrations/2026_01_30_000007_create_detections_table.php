<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->foreignId('point_trajet_id')->constrained('path_points')->onDelete('cascade');

            // IA Data
            $table->string('class_ia'); // ex: 'weed', 'crop_anomaly'
            $table->decimal('confidence', 5, 2); // Score de confiance

            // Action de pulvérisation
            $table->decimal('applied_quantity', 8, 4)->default(0); // Quantité appliquée

            // Lien vers l'image capture
            $table->string('photo_path')->nullable();

            // Position au moment de la détection (pour redondance/vitesse)
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
