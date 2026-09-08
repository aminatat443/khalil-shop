<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Zones et tarifs de livraison (section 35 du cahier des charges) — organisés par
     * distance depuis Malika, où se trouve la boutique, avec un découpage plus fin à
     * l'intérieur de la région de Dakar (communes de plus en plus loin de Malika). Seules
     * les zones de la région de Dakar ont un tarif fixe (livraison locale prévisible) ; au-delà,
     * le tarif dépend trop des horaires/trajets disponibles pour être fixé à l'avance —
     * fee = 0 sert de repère "à discuter sur WhatsApp" (voir affichage conditionnel dans
     * checkout/index.blade.php et AiAssistantService).
     */
    private const ZONES = [
        ['zone' => 'Malika', 'fee' => 1000, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Keur Massar', 'fee' => 1200, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Pikine', 'fee' => 1500, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Guédiawaye', 'fee' => 1500, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Dakar', 'fee' => 2000, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Rufisque', 'fee' => 2000, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Almadies', 'fee' => 2500, 'estimated_days' => 'Moins de 24h (jour ouvrable suivant)'],
        ['zone' => 'Thiès et autres régions', 'fee' => 0, 'estimated_days' => 'Tarif et délai à discuter sur WhatsApp'],
    ];

    public function run(): void
    {
        // Anciennes zones fusionnées en une seule "Thiès et autres régions" ci-dessus.
        Delivery::whereIn('zone', ['Thiès', 'Autres régions'])->delete();

        foreach (self::ZONES as $zone) {
            Delivery::updateOrCreate(
                ['zone' => $zone['zone']],
                ['fee' => $zone['fee'], 'estimated_days' => $zone['estimated_days'], 'is_active' => true]
            );
        }
    }
}
