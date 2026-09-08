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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ex: KH-000123
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // nullable = commande invité
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot des informations client/livraison au moment de la commande (étapes 1 et 2 du checkout)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->string('delivery_region');
            $table->string('delivery_city');
            $table->string('delivery_quartier')->nullable();
            $table->string('delivery_address');
            $table->text('delivery_instructions')->nullable();

            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('delivery_fee')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');

            $table->enum('payment_method', ['wave', 'orange_money', 'carte', 'cod']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('status', [
                'recue', 'confirmee', 'en_preparation', 'expediee', 'en_livraison', 'livree', 'annulee',
            ])->default('recue');

            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
