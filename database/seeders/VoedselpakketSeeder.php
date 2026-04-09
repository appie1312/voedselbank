<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoedselpakketSeeder extends Seeder
{
    public function run(): void
    {
        // Koppel voorbeeldpakketten aan bestaande gezinnen.
        $klantIds = DB::table('klanten')->orderBy('id')->limit(3)->pluck('id');

        if ($klantIds->isEmpty()) {
            return;
        }

        $now = now();

        $voorbeelden = [
            [
                'klant_id' => (int) $klantIds[0],
                'datum_samenstelling' => now()->subDays(2)->toDateString(),
                'datum_uitgifte' => now()->subDay()->toDateString(),
            ],
            [
                'klant_id' => (int) ($klantIds[1] ?? $klantIds[0]),
                'datum_samenstelling' => now()->subDay()->toDateString(),
                'datum_uitgifte' => null,
            ],
            [
                'klant_id' => (int) ($klantIds[2] ?? $klantIds[0]),
                'datum_samenstelling' => now()->toDateString(),
                'datum_uitgifte' => null,
            ],
        ];

        foreach ($voorbeelden as $pakket) {
            // Voorkom dubbele demo-pakketten op dezelfde dag per gezin.
            $bestaatAl = DB::table('voedselpakketten')
                ->where('klant_id', $pakket['klant_id'])
                ->whereDate('datum_samenstelling', $pakket['datum_samenstelling'])
                ->exists();

            if ($bestaatAl) {
                continue;
            }

            DB::table('voedselpakketten')->insert([
                'klant_id' => $pakket['klant_id'],
                'datum_samenstelling' => $pakket['datum_samenstelling'],
                'datum_uitgifte' => $pakket['datum_uitgifte'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
