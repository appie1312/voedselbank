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

        $voorraadItems = [
            ['ean_nummer' => '8710400000001', 'hoeveelheid' => 20, 'minimum_voorraad' => 10, 'locatie' => 'Stelling A'],
            ['ean_nummer' => '8710400000002', 'hoeveelheid' => 5, 'minimum_voorraad' => 10, 'locatie' => 'Koeling 1'],
            ['ean_nummer' => '8710400000003', 'hoeveelheid' => 30, 'minimum_voorraad' => 8, 'locatie' => 'Zuivel rek'],
            ['ean_nummer' => '8710400000004', 'hoeveelheid' => 12, 'minimum_voorraad' => 6, 'locatie' => 'Broodrek'],
        ];

        foreach ($voorraadItems as $item) {
            $productId = DB::table('products')
                ->where('ean_nummer', $item['ean_nummer'])
                ->value('id');

            if (! $productId) {
                continue;
            }

            DB::table('voorraad')->updateOrInsert(
                ['product_id' => $productId],
                [
                    'hoeveelheid' => $item['hoeveelheid'],
                    'minimum_voorraad' => $item['minimum_voorraad'],
                    'locatie' => $item['locatie'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
