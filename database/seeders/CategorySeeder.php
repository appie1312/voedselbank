<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['naam' => 'Aardappelen, groente, fruit'],
            ['naam' => 'Kaas, vleeswaren'],
            ['naam' => 'Zuivel, plantaardig en eieren'],
            ['naam' => 'Bakkerij en banket'],
            ['naam' => 'Frisdrank, sappen, koffie en thee'],
            ['naam' => 'Pasta, rijst en wereldkeuken'],
            ['naam' => 'Soepen, sauzen, kruiden en olie'],
            ['naam' => 'Snoep, koek, chips en chocolade'],
            ['naam' => 'Baby, verzorging en hygiëne'],
        ]);
    }
}
