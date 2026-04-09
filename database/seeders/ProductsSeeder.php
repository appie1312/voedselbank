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

        // Brede productset voor samenstellen en testscenario's.
        $producten = [
            ['ean_nummer' => '8710400000001', 'productnaam' => 'Appels Elstar (1kg)', 'aantal_in_voorraad' => 50, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000002', 'productnaam' => 'Jong Belegen Kaas (400g)', 'aantal_in_voorraad' => 20, 'categorie' => 'Kaas, vleeswaren'],
            ['ean_nummer' => '8710400000003', 'productnaam' => 'Halfvolle Melk (1L)', 'aantal_in_voorraad' => 100, 'categorie' => 'Zuivel, plantaardig en eieren'],
            ['ean_nummer' => '8710400000004', 'productnaam' => 'Volkoren Brood', 'aantal_in_voorraad' => 35, 'categorie' => 'Bakkerij en banket'],
            ['ean_nummer' => '8710400000005', 'productnaam' => 'Tomatensoep Blik (400ml)', 'aantal_in_voorraad' => 42, 'categorie' => 'Soepen, sauzen, kruiden en olie'],
            ['ean_nummer' => '8710400000006', 'productnaam' => 'Spaghetti (500g)', 'aantal_in_voorraad' => 60, 'categorie' => 'Pasta, rijst en wereldkeuken'],
            ['ean_nummer' => '8710400000007', 'productnaam' => 'Bananen (1kg)', 'aantal_in_voorraad' => 45, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000008', 'productnaam' => 'Wortelen (1kg)', 'aantal_in_voorraad' => 40, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000009', 'productnaam' => 'Aardappelen Vastkokend (2kg)', 'aantal_in_voorraad' => 55, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000010', 'productnaam' => 'Komkommer', 'aantal_in_voorraad' => 30, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000011', 'productnaam' => 'Paprika Mix (3 stuks)', 'aantal_in_voorraad' => 32, 'categorie' => 'Aardappelen, groente, fruit'],
            ['ean_nummer' => '8710400000012', 'productnaam' => 'Kipfilet (500g)', 'aantal_in_voorraad' => 26, 'categorie' => 'Kaas, vleeswaren'],
            ['ean_nummer' => '8710400000013', 'productnaam' => 'Rundergehakt (500g)', 'aantal_in_voorraad' => 22, 'categorie' => 'Kaas, vleeswaren'],
            ['ean_nummer' => '8710400000014', 'productnaam' => 'Kalkoenplakjes (150g)', 'aantal_in_voorraad' => 28, 'categorie' => 'Kaas, vleeswaren'],
            ['ean_nummer' => '8710400000015', 'productnaam' => 'Eieren Vrije Uitloop (10 stuks)', 'aantal_in_voorraad' => 70, 'categorie' => 'Zuivel, plantaardig en eieren'],
            ['ean_nummer' => '8710400000016', 'productnaam' => 'Yoghurt Naturel (1L)', 'aantal_in_voorraad' => 36, 'categorie' => 'Zuivel, plantaardig en eieren'],
            ['ean_nummer' => '8710400000017', 'productnaam' => 'Plantaardige Drink Haver (1L)', 'aantal_in_voorraad' => 38, 'categorie' => 'Zuivel, plantaardig en eieren'],
            ['ean_nummer' => '8710400000018', 'productnaam' => 'Wit Brood Heel', 'aantal_in_voorraad' => 30, 'categorie' => 'Bakkerij en banket'],
            ['ean_nummer' => '8710400000019', 'productnaam' => 'Krentenbollen (6 stuks)', 'aantal_in_voorraad' => 25, 'categorie' => 'Bakkerij en banket'],
            ['ean_nummer' => '8710400000020', 'productnaam' => 'Beschuit Volkoren (13 stuks)', 'aantal_in_voorraad' => 34, 'categorie' => 'Bakkerij en banket'],
            ['ean_nummer' => '8710400000021', 'productnaam' => 'Sinaasappelsap (1L)', 'aantal_in_voorraad' => 44, 'categorie' => 'Frisdrank, sappen, koffie en thee'],
            ['ean_nummer' => '8710400000022', 'productnaam' => 'Koffie Filtermaling (500g)', 'aantal_in_voorraad' => 20, 'categorie' => 'Frisdrank, sappen, koffie en thee'],
            ['ean_nummer' => '8710400000023', 'productnaam' => 'Thee Zwart (20 zakjes)', 'aantal_in_voorraad' => 40, 'categorie' => 'Frisdrank, sappen, koffie en thee'],
            ['ean_nummer' => '8710400000024', 'productnaam' => 'Rijst Witte Korrel (1kg)', 'aantal_in_voorraad' => 48, 'categorie' => 'Pasta, rijst en wereldkeuken'],
            ['ean_nummer' => '8710400000025', 'productnaam' => 'Penne (500g)', 'aantal_in_voorraad' => 52, 'categorie' => 'Pasta, rijst en wereldkeuken'],
            ['ean_nummer' => '8710400000026', 'productnaam' => 'Couscous (500g)', 'aantal_in_voorraad' => 29, 'categorie' => 'Pasta, rijst en wereldkeuken'],
            ['ean_nummer' => '8710400000027', 'productnaam' => 'Pastasaus Basilicum (490g)', 'aantal_in_voorraad' => 46, 'categorie' => 'Soepen, sauzen, kruiden en olie'],
            ['ean_nummer' => '8710400000028', 'productnaam' => 'Zonnebloemolie (1L)', 'aantal_in_voorraad' => 27, 'categorie' => 'Soepen, sauzen, kruiden en olie'],
            ['ean_nummer' => '8710400000029', 'productnaam' => 'Kruidenmix Italiaanse Stijl (25g)', 'aantal_in_voorraad' => 33, 'categorie' => 'Soepen, sauzen, kruiden en olie'],
            ['ean_nummer' => '8710400000030', 'productnaam' => 'Pindakaas (350g)', 'aantal_in_voorraad' => 31, 'categorie' => 'Soepen, sauzen, kruiden en olie'],
            ['ean_nummer' => '8710400000031', 'productnaam' => 'Ontbijtkoek (450g)', 'aantal_in_voorraad' => 24, 'categorie' => 'Snoep, koek, chips en chocolade'],
            ['ean_nummer' => '8710400000032', 'productnaam' => 'Crackers Naturel (250g)', 'aantal_in_voorraad' => 35, 'categorie' => 'Snoep, koek, chips en chocolade'],
            ['ean_nummer' => '8710400000033', 'productnaam' => 'Pure Chocolade Reep (100g)', 'aantal_in_voorraad' => 28, 'categorie' => 'Snoep, koek, chips en chocolade'],
            ['ean_nummer' => '8710400000034', 'productnaam' => 'Babydoekjes (72 stuks)', 'aantal_in_voorraad' => 26, 'categorie' => 'Baby, verzorging en hygiëne'],
            ['ean_nummer' => '8710400000035', 'productnaam' => 'Luiers Maat 4 (34 stuks)', 'aantal_in_voorraad' => 18, 'categorie' => 'Baby, verzorging en hygiëne'],
            ['ean_nummer' => '8710400000036', 'productnaam' => 'Tandpasta Family (75ml)', 'aantal_in_voorraad' => 39, 'categorie' => 'Baby, verzorging en hygiëne'],
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
                // Houd leverancier-product koppeling synchroon met products.
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
