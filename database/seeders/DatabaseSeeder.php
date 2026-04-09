<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CoreBusinessSeeder::class);

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
