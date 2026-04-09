<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(LeverancierSeeder::class);
        $this->call(CoreBusinessSeeder::class);
        DB::table('voorraad')->delete();
        DB::table('products')->delete();
        DB::table('categories')->delete();

        DB::table('categories')->insert([
            [
                'id' => 1,
                'naam' => 'Aardappelen, groente, fruit',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 2,
                'naam' => 'Kaas, vleeswaren',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 3,
                'naam' => 'Zuivel, plantaardig en eieren',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 4,
                'naam' => 'Bakkerij en banket',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 5,
                'naam' => 'Frisdrank, sappen, koffie en thee',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 6,
                'naam' => 'Pasta, rijst en wereldkeuken',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 7,
                'naam' => 'Soepen, sauzen, kruiden en olie',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 8,
                'naam' => 'Snoep, koek, chips en chocolade',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 9,
                'naam' => 'Baby, verzorging en hygiëne',
                'beschrijving' => null,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
        ]);

        DB::table('products')->insert([
            [
                'id' => 1,
                'categorie_id' => 1,
                'naam' => 'Appels',
                'beschrijving' => 'Rode appels',
                'prijs' => 2.49,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 2,
                'categorie_id' => 3,
                'naam' => 'Melk',
                'beschrijving' => 'Halfvolle melk',
                'prijs' => 1.39,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 3,
                'categorie_id' => 4,
                'naam' => 'Brood',
                'beschrijving' => 'Volkoren brood',
                'prijs' => 2.19,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'id' => 4,
                'categorie_id' => 8,
                'naam' => 'Chips',
                'beschrijving' => 'Paprika chips',
                'prijs' => 1.89,
                'is_actief' => 1,
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
        ]);

        DB::table('voorraad')->insert([
            [
                'product_id' => 1,
                'hoeveelheid' => 20,
                'minimum_voorraad' => 10,
                'locatie' => 'Stelling A',
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'product_id' => 2,
                'hoeveelheid' => 5,
                'minimum_voorraad' => 10,
                'locatie' => 'Koeling 1',
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'product_id' => 3,
                'hoeveelheid' => 0,
                'minimum_voorraad' => 5,
                'locatie' => 'Broodrek',
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
            [
                'product_id' => 4,
                'hoeveelheid' => 30,
                'minimum_voorraad' => 8,
                'locatie' => 'Snackschap',
                'datum_aangemaakt' => now(),
                'datum_gewijzigd' => now(),
            ],
        ]);

        $gebruikers = [
            [
                'name' => 'Directie Demo',
                'email' => 'directie@voedselbank.local',
                'role' => User::ROLE_DIRECTIE,
                'afdeling' => 'Bestuur',
                'verantwoordelijkheden' => 'Operationeel beleid en planning',
            ],
            [
                'name' => 'Magazijn Demo',
                'email' => 'magazijn@voedselbank.local',
                'role' => User::ROLE_MAGAZIJN_MEDEWERKER,
                'afdeling' => 'Magazijn A',
                'verantwoordelijkheden' => null,
            ],
            [
                'name' => 'Vrijwilliger Demo',
                'email' => 'vrijwilliger@voedselbank.local',
                'role' => User::ROLE_VRIJWILLIGER,
                'afdeling' => null,
                'verantwoordelijkheden' => null,
            ],
        ];

        foreach ($gebruikers as $gebruiker) {
            $user = User::updateOrCreate(
                ['email' => $gebruiker['email']],
                [
                    'name' => $gebruiker['name'],
                    'role' => $gebruiker['role'],
                    'password' => Hash::make('Wachtwoord123!'),
                    'email_verified_at' => now(),
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'telefoon' => '06-12345678',
                    'adres' => 'Maaskantje 1',
                    'afdeling' => $gebruiker['afdeling'],
                    'beschikbaarheid' => 'Maandag en woensdag',
                    'verantwoordelijkheden' => $gebruiker['verantwoordelijkheden'],
                    'bio' => 'Demo account voor de applicatie.',
                ]
            );
        }
    }
}
