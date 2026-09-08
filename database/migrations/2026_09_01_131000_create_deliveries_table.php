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
        // Zones de livraison administrables (section 35) : Dakar, Thiès, autres régions...
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('zone'); // ex: Dakar, Thiès
            $table->unsignedInteger('fee'); // coût en FCFA
            $table->string('estimated_days')->nullable(); // ex: "1-2 jours"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
