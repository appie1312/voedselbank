<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Allergie extends Model
{
    protected $table = 'wens_allergies';

    protected $fillable = [
        'beschrijving',
    ];

    public static function haalOverzichtViaStoredProcedure(?int $klantId, ?string $zoekterm, int $aantalRijen): Collection
    {
        $veiligeKlantId = $klantId !== null && $klantId > 0 ? $klantId : null;
        $veiligeZoekterm = trim((string) $zoekterm);
        $begrensdAantalRijen = min(max($aantalRijen, 1), 50);

        try {
            $resultaten = DB::select('CALL sp_allergieen_overzicht(?, ?, ?)', [
                $veiligeKlantId,
                $veiligeZoekterm === '' ? null : $veiligeZoekterm,
                $begrensdAantalRijen,
            ]);
        } catch (QueryException $exception) {
            // Veilige fallback als de stored procedure nog niet bestaat.
            if (! str_contains(strtolower($exception->getMessage()), 'sp_allergieen_overzicht')) {
                throw $exception;
            }

            return self::haalOverzichtViaJoinQuery($veiligeKlantId, $veiligeZoekterm, $begrensdAantalRijen);
        }

        return collect($resultaten);
    }

    private static function haalOverzichtViaJoinQuery(?int $klantId, string $zoekterm, int $aantalRijen): Collection
    {
        return DB::table('klant_wens as kw')
            ->join('wens_allergies as wa', 'wa.id', '=', 'kw.wens_id')
            ->join('klanten as k', 'k.id', '=', 'kw.klant_id')
            ->when($klantId !== null, function ($query) use ($klantId): void {
                $query->where('kw.klant_id', $klantId);
            })
            ->when($zoekterm !== '', function ($query) use ($zoekterm): void {
                $query->where(function ($subQuery) use ($zoekterm): void {
                    $subQuery->where('wa.beschrijving', 'like', '%'.$zoekterm.'%')
                        ->orWhere('k.gezinsnaam', 'like', '%'.$zoekterm.'%');
                });
            })
            ->select([
                'kw.klant_id as klant_id',
                'k.gezinsnaam',
                'wa.id as allergie_id',
                'wa.beschrijving as allergie_beschrijving',
            ])
            ->orderBy('k.gezinsnaam')
            ->orderBy('wa.beschrijving')
            ->limit($aantalRijen)
            ->get();
    }
}
