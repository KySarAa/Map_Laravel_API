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

            // Informations minimalistes
            $table->boolean('is_weed')->comment('1 pour mauvaise herbe, 0 pour plante');
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
