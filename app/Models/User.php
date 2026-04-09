<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_DIRECTIE = 'directie';
    public const ROLE_MAGAZIJN_MEDEWERKER = 'magazijn_medewerker';
    public const ROLE_VRIJWILLIGER = 'vrijwilliger';

    public const ROLES = [
        self::ROLE_DIRECTIE,
        self::ROLE_MAGAZIJN_MEDEWERKER,
        self::ROLE_VRIJWILLIGER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function isDirectie(): bool
    {
        return $this->role === self::ROLE_DIRECTIE;
    }

    public function isMagazijnMedewerker(): bool
    {
        return $this->role === self::ROLE_MAGAZIJN_MEDEWERKER;
    }

    public function isVrijwilliger(): bool
    {
        return $this->role === self::ROLE_VRIJWILLIGER;
    }
}
