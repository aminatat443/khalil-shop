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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('KhalilShop');
            $table->string('shop_address')->nullable();
            $table->string('shop_phone')->nullable();
            $table->string('shop_email')->nullable();
            $table->string('invoice_logo')->nullable();
            $table->string('invoice_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
