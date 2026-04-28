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
        if (Schema::hasColumn('detections', 'class_ia')) {
            Schema::table('detections', function (Blueprint $table) {
                $table->dropColumn('class_ia');
            });
        }
        if (Schema::hasColumn('detections', 'confidence')) {
            Schema::table('detections', function (Blueprint $table) {
                $table->dropColumn(['confidence', 'applied_quantity', 'photo_path']);
            });
        }
        
        if (!Schema::hasColumn('detections', 'is_weed')) {
            Schema::table('detections', function (Blueprint $table) {
                $table->boolean('is_weed')->default(0)->comment('1 pour mauvaise herbe, 0 pour plante');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detections', function (Blueprint $table) {
            $table->dropColumn('is_weed');
            $table->string('class_ia')->nullable();
        });
    }
};
