<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    public static function overzichtLijst(): Collection
    {
        return DB::table('users as u')
            ->join('user_profiles as p', 'p.user_id', '=', 'u.id')
            ->orderBy('u.name')
            ->select([
                'u.id',
                'u.name',
                'u.email',
                'u.role',
                'u.created_at',
                'p.telefoon',
                'p.afdeling',
            ])
            ->get();
    }

    public static function profielVanGebruiker(int $userId): ?object
    {
        return DB::table('users as u')
            ->join('user_profiles as p', 'p.user_id', '=', 'u.id')
            ->where('u.id', $userId)
            ->select([
                'u.id',
                'u.name',
                'u.email',
                'u.role',
                'p.telefoon',
                'p.adres',
                'p.afdeling',
                'p.beschikbaarheid',
                'p.verantwoordelijkheden',
                'p.bio',
            ])
            ->first();
    }

    public static function voegToeMetProfiel(array $attributes): object
    {
        return DB::transaction(function () use ($attributes): object {
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'role' => $attributes['role'],
                'password' => $attributes['password'],
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'adres' => $attributes['adres'] ?? null,
                'telefoon' => $attributes['telefoon'] ?? null,
                'afdeling' => $attributes['afdeling'] ?? null,
                'beschikbaarheid' => $attributes['beschikbaarheid'] ?? null,
                'verantwoordelijkheden' => $attributes['verantwoordelijkheden'] ?? null,
                'bio' => $attributes['bio'] ?? null,
            ]);

            // Expliciete INNER JOIN tussen users en user_profiles.
            return DB::table('users as u')
                ->join('user_profiles as p', 'p.user_id', '=', 'u.id')
                ->where('u.id', $user->id)
                ->select([
                    'u.id',
                    'u.name',
                    'u.email',
                    'u.role',
                    'u.created_at',
                    'p.telefoon',
                    'p.afdeling',
                ])
                ->firstOrFail();
        });
    }
}
