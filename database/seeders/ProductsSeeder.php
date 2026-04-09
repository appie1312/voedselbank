<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductsSeeder extends Seeder
{
    /**
     * Seed the products table.
     */
    public function run(): void
    {
        $now = now();

        $categorieen = DB::table('categories')->pluck('id', 'naam');
        $leverancierIds = DB::table('leveranciers')->orderBy('id')->pluck('id')->values();
        $heeftPivotTabel = Schema::hasTable('leverancier_products');

        $producten = [
            [
                'ean_nummer' => '8710400000001',
                'productnaam' => 'Appels Elstar (1kg)',
                'aantal_in_voorraad' => 50,
                'categorie' => 'Aardappelen, groente, fruit',
            ],
            [
                'ean_nummer' => '8710400000002',
                'productnaam' => 'Jong Belegen Kaas (400g)',
                'aantal_in_voorraad' => 20,
                'categorie' => 'Kaas, vleeswaren',
            ],
            [
                'ean_nummer' => '8710400000003',
                'productnaam' => 'Halfvolle Melk (1L)',
                'aantal_in_voorraad' => 100,
                'categorie' => 'Zuivel, plantaardig en eieren',
            ],
            [
                'ean_nummer' => '8710400000004',
                'productnaam' => 'Volkoren Brood',
                'aantal_in_voorraad' => 35,
                'categorie' => 'Bakkerij en banket',
            ],
            [
                'ean_nummer' => '8710400000005',
                'productnaam' => 'Tomatensoep Blik (400ml)',
                'aantal_in_voorraad' => 42,
                'categorie' => 'Soepen, sauzen, kruiden en olie',
            ],
            [
                'ean_nummer' => '8710400000006',
                'productnaam' => 'Spaghetti (500g)',
                'aantal_in_voorraad' => 60,
                'categorie' => 'Pasta, rijst en wereldkeuken',
            ],
        ];

        foreach ($producten as $index => $product) {
            $categorieId = $categorieen[$product['categorie']] ?? null;
            $leverancierId = $leverancierIds->count() > 0
                ? $leverancierIds[$index % $leverancierIds->count()]
                : null;

            if (! $categorieId) {
                continue;
            }

            DB::table('products')->updateOrInsert(
                ['ean_nummer' => $product['ean_nummer']],
                [
                    'productnaam' => $product['productnaam'],
                    'aantal_in_voorraad' => $product['aantal_in_voorraad'],
                    'categorie_id' => $categorieId,
                    'leverancier_id' => $leverancierId,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            if ($heeftPivotTabel && $leverancierId) {
                $productId = DB::table('products')
                    ->where('ean_nummer', $product['ean_nummer'])
                    ->value('id');

                if ($productId) {
                    DB::table('leverancier_products')->updateOrInsert(
                        [
                            'leverancier_id' => $leverancierId,
                            'product_id' => $productId,
                        ],
                        [
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );
                }
            }
        }
    }
}
