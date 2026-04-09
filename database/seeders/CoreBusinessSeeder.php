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

    }
}
