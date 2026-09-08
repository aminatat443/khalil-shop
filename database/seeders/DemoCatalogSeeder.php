<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Données de démonstration UNIQUEMENT pour vérifier visuellement le rendu du frontend
 * en développement local. Ne pas exécuter en production — le vrai catalogue sera
 * saisi par KhalilShop via le back-office (section 41 du cahier des charges).
 *
 * Usage : php artisan db:seed --class=DemoCatalogSeeder
 */
class DemoCatalogSeeder extends Seeder
{
    private const PRODUCTS = [
        ['category' => 'femme-robes', 'name' => 'Robe longue satinée', 'price' => 35000, 'old_price' => 45000, 'is_promo' => true, 'color' => 'fef7fa'],
        ['category' => 'femme-tops', 'name' => 'Top plissé beige', 'price' => 18000, 'is_new' => true, 'color' => 'f3ede4'],
        ['category' => 'homme-chemises', 'name' => 'Chemise lin blanche', 'price' => 22000, 'is_new' => true, 'color' => 'f5f5f0'],
        ['category' => 'homme-vestes', 'name' => 'Veste tailleur noire', 'price' => 42000, 'is_featured' => true, 'color' => 'e8e8e8'],
        ['category' => 'chaussures-sneakers', 'name' => 'Sneakers blanches minimalistes', 'price' => 28000, 'is_new' => true, 'color' => 'ffffff'],
        ['category' => 'chaussures-escarpins', 'name' => 'Escarpins nude', 'price' => 32000, 'old_price' => 38000, 'is_promo' => true, 'color' => 'e7d3a0'],
        ['category' => 'accessoires-sacs-a-main', 'name' => 'Sac à main cuir camel', 'price' => 38000, 'is_featured' => true, 'color' => 'c9a27e'],
        ['category' => 'accessoires-montres', 'name' => 'Montre minimaliste dorée', 'price' => 45000, 'color' => 'd7b661'],
        ['category' => 'maison-decoration-vases', 'name' => 'Vase céramique galet', 'price' => 15000, 'is_new' => true, 'color' => 'ded6c9'],
        ['category' => 'maison-decoration-coussins', 'name' => 'Coussin lin terracotta', 'price' => 9000, 'color' => 'd77a61'],
        ['category' => 'maison-decoration-miroirs', 'name' => 'Miroir rond laiton', 'price' => 26000, 'is_featured' => true, 'color' => 'c2a457'],
        ['category' => 'maison-decoration-luminaires', 'name' => 'Lampe à poser arche', 'price' => 32000, 'old_price' => 39000, 'is_promo' => true, 'color' => '2f4f4f'],
    ];

    public function run(): void
    {
        $noir = Color::firstOrCreate(['name' => 'Noir'], ['hex_code' => '#111111']);
        $beige = Color::firstOrCreate(['name' => 'Beige'], ['hex_code' => '#E8DED2']);
        $sizeM = Size::firstOrCreate(['name' => 'M', 'type' => 'clothing']);
        $sizeL = Size::firstOrCreate(['name' => 'L', 'type' => 'clothing']);

        foreach (self::PRODUCTS as $data) {
            $category = Category::where('slug', $data['category'])->first();

            if (! $category) {
                continue; // catégorie absente (CategorySeeder pas lancé) — on saute plutôt que de planter
            }

            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'description' => 'Produit de démonstration pour la mise en page — à remplacer par le vrai catalogue KhalilShop.',
                    'price' => $data['price'],
                    'old_price' => $data['old_price'] ?? null,
                    'stock' => 10,
                    'is_new' => $data['is_new'] ?? false,
                    'is_promo' => $data['is_promo'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                    'is_active' => true,
                ]
            );

            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'sort_order' => 0],
                [
                    'url' => "https://placehold.co/800x800/{$data['color']}/1a1a1a?text=".urlencode($data['name']),
                    'alt' => $data['name'],
                ]
            );

            // Une variante taille/couleur pour les produits mode, aucune pour la déco (vendue à l'unité)
            if (! str_starts_with($data['category'], 'maison-')) {
                ProductVariant::firstOrCreate(
                    ['sku' => strtoupper(Str::slug($data['name'], '-')).'-M-NOIR'],
                    [
                        'product_id' => $product->id,
                        'color_id' => $noir->id,
                        'size_id' => $sizeM->id,
                        'stock' => 6,
                    ]
                );

                ProductVariant::firstOrCreate(
                    ['sku' => strtoupper(Str::slug($data['name'], '-')).'-L-BEIGE'],
                    [
                        'product_id' => $product->id,
                        'color_id' => $beige->id,
                        'size_id' => $sizeL->id,
                        'stock' => 4,
                    ]
                );
            }
        }
    }
}
