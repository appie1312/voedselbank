<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlantenSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Demo-gezinnen voor CRUD- en pakketflow tests.
        $gezinnen = [
            [
                'gezinsnaam' => 'Familie Jansen',
                'adres' => 'Dorpsstraat 12, Maaskantje',
                'telefoonnummer' => '06-11111111',
                'emailadres' => 'jansen@voorbeeld.nl',
                'aanwezigheidsstatus' => 'binnen_land',
                'aantal_volwassenen' => 2,
                'aantal_kinderen' => 2,
                'aantal_babys' => 0,
                'wens' => 'Geen varkensvlees',
            ],
            [
                'gezinsnaam' => 'Familie De Vries',
                'adres' => 'Molenweg 8, Maaskantje',
                'telefoonnummer' => '06-22222222',
                'emailadres' => 'devries@voorbeeld.nl',
                'aanwezigheidsstatus' => 'buiten_land',
                'aantal_volwassenen' => 1,
                'aantal_kinderen' => 3,
                'aantal_babys' => 1,
                'wens' => 'Lactose',
            ],
            [
                'gezinsnaam' => 'Familie Bakker',
                'adres' => 'Stationslaan 21, Maaskantje',
                'telefoonnummer' => '06-33333333',
                'emailadres' => 'bakker@voorbeeld.nl',
                'aanwezigheidsstatus' => 'binnen_land',
                'aantal_volwassenen' => 2,
                'aantal_kinderen' => 1,
                'aantal_babys' => 0,
                'wens' => 'Gluten',
            ],
            [
                'gezinsnaam' => 'Familie El Idrissi',
                'adres' => 'Waterweg 4, Maaskantje',
                'telefoonnummer' => '06-44444444',
                'emailadres' => 'elidrissi@voorbeeld.nl',
                'aanwezigheidsstatus' => 'afwezig',
                'aantal_volwassenen' => 2,
                'aantal_kinderen' => 2,
                'aantal_babys' => 1,
                'wens' => 'Vegetarisch',
            ],
            [
                'gezinsnaam' => 'Familie Van Dijk',
                'adres' => 'Kerkstraat 2, Maaskantje',
                'telefoonnummer' => '06-55555555',
                'emailadres' => 'vandijk@voorbeeld.nl',
                'aanwezigheidsstatus' => 'binnen_land',
                'aantal_volwassenen' => 1,
                'aantal_kinderen' => 1,
                'aantal_babys' => 0,
                'wens' => "Pinda's",
            ],
        ];

        foreach ($gezinnen as $gezin) {
            $wens = $gezin['wens'];
            unset($gezin['wens']);

            // Upsert op gezinsnaam + telefoon om dubbelen te voorkomen.
            DB::table('klanten')->updateOrInsert(
                [
                    'gezinsnaam' => $gezin['gezinsnaam'],
                    'telefoonnummer' => $gezin['telefoonnummer'],
                ],
                array_merge($gezin, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );

            $klantId = DB::table('klanten')
                ->where('gezinsnaam', $gezin['gezinsnaam'])
                ->where('telefoonnummer', $gezin['telefoonnummer'])
                ->value('id');

            $wensId = DB::table('wens_allergies')
                ->where('beschrijving', $wens)
                ->value('id');

            if ($klantId && $wensId) {
                // Optionele wens/allergie koppelen voor realistischer data.
                DB::table('klant_wens')->updateOrInsert(
                    [
                        'klant_id' => $klantId,
                        'wens_id' => $wensId,
                    ],
                    []
                );
            }
        }
    }
}
