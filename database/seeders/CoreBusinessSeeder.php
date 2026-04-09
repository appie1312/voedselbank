<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoreBusinessSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        $categorieNamen = [
            'Aardappelen, groente, fruit',
            'Kaas, vleeswaren',
            'Zuivel, plantaardig en eieren',
            'Bakkerij en banket',
            'Frisdrank, sappen, koffie en thee',
            'Pasta, rijst en wereldkeuken',
            'Soepen, sauzen, kruiden en olie',
            'Snoep, koek, chips en chocolade',
            'Baby, verzorging en hygiëne',
        ];

        foreach ($categorieNamen as $naam) {
            DB::table('categories')->updateOrInsert(
                ['naam' => $naam],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        $wensen = [
            'Geen varkensvlees',
            'Gluten',
            "Pinda's",
            'Schaaldieren',
            'Hazelnoten',
            'Lactose',
            'Veganistisch',
            'Vegetarisch',
        ];

        foreach ($wensen as $beschrijving) {
            DB::table('wens_allergies')->updateOrInsert(
                ['beschrijving' => $beschrijving],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        $catAgfId = DB::table('categories')->where('naam', 'Aardappelen, groente, fruit')->value('id');
        $catKaasId = DB::table('categories')->where('naam', 'Kaas, vleeswaren')->value('id');
        $catZuivelId = DB::table('categories')->where('naam', 'Zuivel, plantaardig en eieren')->value('id');

        if ($catAgfId) {
            DB::table('products')->updateOrInsert(
                ['ean_nummer' => '8710400000001'],
                [
                    'productnaam' => 'Appels Elstar (1kg)',
                    'aantal_in_voorraad' => 50,
                    'categorie_id' => $catAgfId,
                    'leverancier_id' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        if ($catKaasId) {
            DB::table('products')->updateOrInsert(
                ['ean_nummer' => '8710400000002'],
                [
                    'productnaam' => 'Jong Belegen Kaas (400g)',
                    'aantal_in_voorraad' => 20,
                    'categorie_id' => $catKaasId,
                    'leverancier_id' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        if ($catZuivelId) {
            DB::table('products')->updateOrInsert(
                ['ean_nummer' => '8710400000003'],
                [
                    'productnaam' => 'Halfvolle Melk (1L)',
                    'aantal_in_voorraad' => 100,
                    'categorie_id' => $catZuivelId,
                    'leverancier_id' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
