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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // wave, orange_money, cinetpay, paytech, cod...
            $table->string('transaction_id')->nullable(); // référence renvoyée par le prestataire
            $table->unsignedInteger('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->text('raw_response')->nullable(); // payload webhook brut, pour debug/audit
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
