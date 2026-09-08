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
        // Politique de retour définie dans docs/SPEC.md §2.1 (7 jours, non prévue au cahier des charges original)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('reason'); // taille, défaut, ne correspond pas à la description, autre
            $table->text('description')->nullable();
            $table->enum('status', ['demandee', 'acceptee', 'refusee', 'article_recu', 'remboursee'])->default('demandee');
            $table->enum('refund_method', ['credit', 'moyen_original'])->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
