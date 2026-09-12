<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permet d'enregistrer une commande prise en boutique (section "commande sur place") :
     * paiement en espèces sur place, et un indicateur pour la distinguer des commandes en
     * ligne dans les listes/rapports.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE orders DROP CONSTRAINT orders_payment_method_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check CHECK (payment_method IN ('wave', 'orange_money', 'carte', 'cod', 'especes'))");

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_in_store')->default(false)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_in_store');
        });

        DB::statement('ALTER TABLE orders DROP CONSTRAINT orders_payment_method_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check CHECK (payment_method IN ('wave', 'orange_money', 'carte', 'cod'))");
    }
};
