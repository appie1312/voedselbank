<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Klant extends Model
{
    protected $table = 'klanten';

    protected $fillable = [
        'gezinsnaam',
        'adres',
        'telefoonnummer',
        'emailadres',
        'aantal_volwassenen',
        'aantal_kinderen',
        'aantal_babys',
    ];

    public static function haalOverzichtViaStoredProcedure(?string $zoekterm, int $aantalRijen): Collection
    {
        $veiligeZoekterm = trim((string) $zoekterm);
        $begrensdAantalRijen = min(max($aantalRijen, 1), 25);

        try {
            $resultaten = DB::select('CALL sp_klanten_overzicht(?, ?)', [
                $veiligeZoekterm === '' ? null : $veiligeZoekterm,
                $begrensdAantalRijen,
            ]);
        } catch (QueryException $exception) {
            // Veilige fallback als de stored procedure nog niet is aangemaakt.
            if (! str_contains(strtolower($exception->getMessage()), 'sp_klanten_overzicht')) {
                throw $exception;
            }

            return self::haalOverzichtViaJoinQuery($veiligeZoekterm, $begrensdAantalRijen);
        }

        return collect($resultaten);
    }

    private static function haalOverzichtViaJoinQuery(string $zoekterm, int $aantalRijen): Collection
    {
        return DB::table('klanten as k')
            ->leftJoin('voedselpakketten as vp', 'vp.klant_id', '=', 'k.id')
            ->leftJoin('klant_wens as kw', 'kw.klant_id', '=', 'k.id')
            ->leftJoin('wens_allergies as wa', 'wa.id', '=', 'kw.wens_id')
            ->when($zoekterm !== '', function ($query) use ($zoekterm): void {
                $query->where(function ($subQuery) use ($zoekterm): void {
                    $subQuery->where('k.gezinsnaam', 'like', '%'.$zoekterm.'%')
                        ->orWhere('k.adres', 'like', '%'.$zoekterm.'%')
                        ->orWhere('k.emailadres', 'like', '%'.$zoekterm.'%')
                        ->orWhere('k.telefoonnummer', 'like', '%'.$zoekterm.'%');
                });
            })
            ->groupBy([
                'k.id',
                'k.gezinsnaam',
                'k.adres',
                'k.telefoonnummer',
                'k.emailadres',
                'k.aantal_volwassenen',
                'k.aantal_kinderen',
                'k.aantal_babys',
            ])
            ->orderBy('k.gezinsnaam')
            ->limit($aantalRijen)
            ->select([
                'k.id',
                'k.gezinsnaam',
                'k.adres',
                'k.telefoonnummer',
                'k.emailadres',
                'k.aantal_volwassenen',
                'k.aantal_kinderen',
                'k.aantal_babys',
                DB::raw('COUNT(DISTINCT vp.id) AS totaal_voedselpakketten'),
                DB::raw("GROUP_CONCAT(DISTINCT wa.beschrijving ORDER BY wa.beschrijving SEPARATOR ', ') AS wensen_allergieen"),
            ])
            ->get();
    }
}
