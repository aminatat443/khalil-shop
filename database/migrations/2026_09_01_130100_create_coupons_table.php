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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: KHALIL20
            $table->enum('type', ['percentage', 'fixed']);
            $table->unsignedInteger('value'); // % ou montant FCFA selon le type
            $table->unsignedInteger('min_amount')->nullable(); // montant minimum de commande
            $table->unsignedInteger('usage_limit')->nullable(); // nombre total d'utilisations autorisées
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
