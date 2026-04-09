<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeverancierSeeder extends Seeder
{
    public function run(): void
    {
        $leveranciers = [
            [
                'bedrijfsnaam' => 'SuperFood BV',
                'adres' => 'Straat 12, Utrecht',
                'contactpersoon_naam' => 'Jan de Vries',
                'contactpersoon_email' => 'info@superfood.nl',
                'telefoonnummer' => '030-1234567',
                'volgende_levering' => null,
            ],
            [
                'bedrijfsnaam' => 'Groente & Fruit NV',
                'adres' => 'Marktplein 5, Breukelen',
                'contactpersoon_naam' => 'Sanne Bakker',
                'contactpersoon_email' => 'contact@grof.nl',
                'telefoonnummer' => '0346-765432',
                'volgende_levering' => null,
            ],
            [
                'bedrijfsnaam' => 'Bakkerij De Zoete',
                'adres' => 'Bakkerstraat 1, Utrecht',
                'contactpersoon_naam' => 'Pieter de Zoete',
                'contactpersoon_email' => 'bakker@dezoete.nl',
                'telefoonnummer' => '030-9876543',
                'volgende_levering' => null,
            ],
        ];

        foreach ($leveranciers as $leverancier) {
            DB::table('leveranciers')->insert([
                'bedrijfsnaam' => $leverancier['bedrijfsnaam'],
                'adres' => $leverancier['adres'],
                'contactpersoon_naam' => $leverancier['contactpersoon_naam'],
                'contactpersoon_email' => $leverancier['contactpersoon_email'],
                'telefoonnummer' => $leverancier['telefoonnummer'],
                'volgende_levering' => $leverancier['volgende_levering'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}