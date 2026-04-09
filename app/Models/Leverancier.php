<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Leverancier extends Model
{
    protected $table = 'leveranciers';

    protected $fillable = [
        'naam', 'adres', 'telefoon', 'email', 'is_actief',
    ];

    protected $casts = [
        'is_actief'        => 'boolean',
        'datum_aangemaakt' => 'datetime',
        'datum_gewijzigd'  => 'datetime',
    ];

    public static function getAllMetProducten(): Collection
    {
        try {
            return collect(DB::select('CALL sp_get_all_leveranciers()'));
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public static function naamBestaatAl(string $naam): bool
    {
        try {
            $resultaat = DB::select('CALL sp_check_leverancier_naam(?)', [trim($naam)]);
            return !empty($resultaat);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public static function aanmakenViaSP(
        string  $naam,
        ?string $adres    = null,
        ?string $telefoon = null,
        ?string $email    = null,
    ): array {
        try {
            $uitvoer = DB::select(
                'CALL sp_create_leverancier(?, ?, ?, ?)',
                [$naam, $adres ?? '', $telefoon ?? '', $email ?? '']
            );
            $nieuwId = $uitvoer[0]->nieuw_id ?? null;
            $fout    = $uitvoer[0]->fout ?? null;
            return ['nieuw_id' => $nieuwId, 'fout' => $fout];
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function producten(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'leverancier_products', 'leverancier_id', 'product_id');
    }

    public function contacten(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'leverancier_contact', 'leverancier_id', 'contact_id');
    }
}