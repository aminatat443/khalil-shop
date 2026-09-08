<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Retire l'étape logistique "en_livraison" du parcours de commande (expédiée → livrée
     * directement désormais). Aucune commande n'utilisait ce statut au moment du retrait.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE orders DROP CONSTRAINT orders_status_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('recue', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE orders DROP CONSTRAINT orders_status_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('recue', 'confirmee', 'en_preparation', 'expediee', 'en_livraison', 'livree', 'annulee'))");
    }
};
