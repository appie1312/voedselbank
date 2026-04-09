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

    /**
     * @param array<string, mixed> $attributes
     * @return array{toegevoegd: bool, bestaat_al: bool, klant_id: int|null}
     */
    public static function voegToeViaStoredProcedure(array $attributes): array
    {
        $payload = [
            trim((string) $attributes['gezinsnaam']),
            trim((string) $attributes['adres']),
            trim((string) $attributes['telefoonnummer']),
            ($attributes['emailadres'] ?? null) === null ? null : trim((string) $attributes['emailadres']),
            (int) $attributes['aantal_volwassenen'],
            (int) $attributes['aantal_kinderen'],
            (int) $attributes['aantal_babys'],
        ];

        try {
            $resultaat = DB::select(
                'CALL sp_klant_toevoegen(?, ?, ?, ?, ?, ?, ?)',
                $payload
            );
        } catch (QueryException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'sp_klant_toevoegen')) {
                throw $exception;
            }

            return self::voegToeViaQuery($attributes);
        }

        $eersteRij = $resultaat[0] ?? null;

        return [
            'toegevoegd' => ((int) ($eersteRij->toegevoegd ?? 0)) === 1,
            'bestaat_al' => ((int) ($eersteRij->bestaat_al ?? 0)) === 1,
            'klant_id' => isset($eersteRij->klant_id) ? (int) $eersteRij->klant_id : null,
        ];
    }

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

    /**
     * @param array<string, mixed> $attributes
     * @return array{toegevoegd: bool, bestaat_al: bool, klant_id: int|null}
     */
    private static function voegToeViaQuery(array $attributes): array
    {
        $emailadres = trim((string) ($attributes['emailadres'] ?? ''));

        $bestaatAl = DB::table('klanten')
            ->where(function ($query) use ($attributes, $emailadres): void {
                if ($emailadres !== '') {
                    $query->orWhere('emailadres', $emailadres);
                }

                $query->orWhere(function ($subQuery) use ($attributes): void {
                    $subQuery
                        ->where('gezinsnaam', trim((string) $attributes['gezinsnaam']))
                        ->where('adres', trim((string) $attributes['adres']))
                        ->where('telefoonnummer', trim((string) $attributes['telefoonnummer']));
                });
            })
            ->exists();

        if ($bestaatAl) {
            return [
                'toegevoegd' => false,
                'bestaat_al' => true,
                'klant_id' => null,
            ];
        }

        $klantId = (int) DB::table('klanten')->insertGetId([
            'gezinsnaam' => trim((string) $attributes['gezinsnaam']),
            'adres' => trim((string) $attributes['adres']),
            'telefoonnummer' => trim((string) $attributes['telefoonnummer']),
            'emailadres' => $emailadres === '' ? null : $emailadres,
            'aantal_volwassenen' => (int) $attributes['aantal_volwassenen'],
            'aantal_kinderen' => (int) $attributes['aantal_kinderen'],
            'aantal_babys' => (int) $attributes['aantal_babys'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'toegevoegd' => true,
            'bestaat_al' => false,
            'klant_id' => $klantId,
        ];
    }
}
