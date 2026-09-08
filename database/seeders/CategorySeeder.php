<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Arborescence officielle du catalogue (cahier des charges, section 2).
     */
    private const TREE = [
        'Femme' => ['Robes', 'Ensembles', 'Tops', 'Chemisiers', 'T-shirts', 'Pantalons', 'Jeans', 'Jupes', 'Vestes', 'Manteaux', 'Pulls', 'Tenues tendance'],
        'Homme' => ['Chemises', 'T-shirts', 'Polos', 'Pantalons', 'Jeans', 'Shorts', 'Ensembles', 'Vestes', 'Pulls', 'Sweats', 'Tenues casual', 'Tenues élégantes'],
        'Chaussures' => ['Sneakers', 'Baskets', 'Sandales', 'Escarpins', 'Mocassins', 'Bottes', 'Chaussures homme', 'Chaussures femme', 'Chaussures casual', 'Chaussures élégantes'],
        'Accessoires' => ['Sacs', 'Sacs à main', 'Sacs à dos', 'Portefeuilles', 'Ceintures', 'Lunettes', 'Bijoux', 'Montres', 'Casquettes', 'Chapeaux', 'Foulards'],
        'Maison & Décoration' => ['Décoration murale', 'Vases', 'Miroirs', 'Bougies', 'Luminaires', 'Coussins', 'Tapis', 'Objets décoratifs', 'Rangement', 'Accessoires de table', 'Décoration chambre', 'Décoration salon'],
    ];

    public function run(): void
    {
        $sortOrder = 0;

        foreach (self::TREE as $name => $children) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true, 'sort_order' => $sortOrder++]
            );

            $childOrder = 0;

            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name.'-'.$childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'is_active' => true,
                        'sort_order' => $childOrder++,
                    ]
                );
            }
        }
    }
}
