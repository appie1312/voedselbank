<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VoorraadSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('voorraad')) {
            return;
        }

        $now = now();
        // Voor elk product een basisvoorraadregel aanmaken.
        $producten = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.categorie_id')
            ->select([
                'p.id',
                'p.aantal_in_voorraad',
                'c.naam as categorie_naam',
            ])
            ->get();

        foreach ($producten as $product) {
            // Simpele defaults: genoeg om te testen, maar met realistische minima.
            $hoeveelheid = max(5, min(80, (int) $product->aantal_in_voorraad));
            $minimumVoorraad = max(3, (int) ceil($hoeveelheid * 0.25));

            $locatie = match ($product->categorie_naam) {
                'Zuivel, plantaardig en eieren' => 'Koeling 1',
                'Bakkerij en banket' => 'Broodrek',
                'Baby, verzorging en hygiëne' => 'Stelling C',
                'Kaas, vleeswaren' => 'Koeling 2',
                default => 'Stelling A',
            };

            DB::table('voorraad')->updateOrInsert(
                ['product_id' => (int) $product->id],
                [
                    'hoeveelheid' => $hoeveelheid,
                    'minimum_voorraad' => $minimumVoorraad,
                    'locatie' => $locatie,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
