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
        'aanwezigheidsstatus',
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
            trim((string) $attributes['aanwezigheidsstatus']),
            (int) $attributes['aantal_volwassenen'],
            (int) $attributes['aantal_kinderen'],
            (int) $attributes['aantal_babys'],
        ];

        try {
            $resultaat = DB::select(
                'CALL sp_klant_toevoegen(?, ?, ?, ?, ?, ?, ?, ?)',
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

    public static function haalKlantVoorWijzigen(int $klantId): ?object
    {
        return DB::table('klanten as k')
            ->leftJoin('voedselpakketten as vp', 'vp.klant_id', '=', 'k.id')
            ->leftJoin('klant_wens as kw', 'kw.klant_id', '=', 'k.id')
            ->leftJoin('wens_allergies as wa', 'wa.id', '=', 'kw.wens_id')
            ->where('k.id', $klantId)
            ->groupBy([
                'k.id',
                'k.gezinsnaam',
                'k.adres',
                'k.telefoonnummer',
                'k.emailadres',
                'k.aanwezigheidsstatus',
                'k.aantal_volwassenen',
                'k.aantal_kinderen',
                'k.aantal_babys',
            ])
            ->select([
                'k.id',
                'k.gezinsnaam',
                'k.adres',
                'k.telefoonnummer',
                'k.emailadres',
                'k.aanwezigheidsstatus',
                'k.aantal_volwassenen',
                'k.aantal_kinderen',
                'k.aantal_babys',
                DB::raw('COUNT(DISTINCT vp.id) AS totaal_voedselpakketten'),
                DB::raw("GROUP_CONCAT(DISTINCT wa.beschrijving ORDER BY wa.beschrijving SEPARATOR ', ') AS wensen_allergieen"),
            ])
            ->first();
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array{gewijzigd: bool, bestaat_email_al: bool, klant_bestaat: bool}
     */
    public static function wijzigViaStoredProcedure(int $klantId, array $attributes): array
    {
        $payload = [
            $klantId,
            trim((string) $attributes['gezinsnaam']),
            trim((string) $attributes['adres']),
            trim((string) $attributes['telefoonnummer']),
            ($attributes['emailadres'] ?? null) === null ? null : trim((string) $attributes['emailadres']),
            trim((string) $attributes['aanwezigheidsstatus']),
            (int) $attributes['aantal_volwassenen'],
            (int) $attributes['aantal_kinderen'],
            (int) $attributes['aantal_babys'],
        ];

        try {
            $resultaat = DB::select(
                'CALL sp_klant_wijzigen(?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $payload
            );
        } catch (QueryException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'sp_klant_wijzigen')) {
                throw $exception;
            }

            return self::wijzigViaQuery($klantId, $attributes);
        }

        $eersteRij = $resultaat[0] ?? null;

        return [
            'gewijzigd' => ((int) ($eersteRij->gewijzigd ?? 0)) === 1,
            'bestaat_email_al' => ((int) ($eersteRij->bestaat_email_al ?? $eersteRij->bestaat_al ?? 0)) === 1,
            'klant_bestaat' => ((int) ($eersteRij->klant_bestaat ?? 0)) === 1,
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

    /**
     * @return array{verwijderd: bool, aanwezig: bool, klant_bestaat: bool}
     */
    public static function verwijderViaStoredProcedure(int $klantId): array
    {
        try {
            $resultaat = DB::select('CALL sp_klant_verwijderen(?)', [$klantId]);
        } catch (QueryException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'sp_klant_verwijderen')) {
                throw $exception;
            }

            return self::verwijderViaQuery($klantId);
        }

        $eersteRij = $resultaat[0] ?? null;

        return [
            'verwijderd' => ((int) ($eersteRij->verwijderd ?? 0)) === 1,
            'aanwezig' => ((int) ($eersteRij->aanwezig ?? 0)) === 1,
            'klant_bestaat' => ((int) ($eersteRij->klant_bestaat ?? 0)) === 1,
        ];
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
                'k.aanwezigheidsstatus',
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
                'k.aanwezigheidsstatus',
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
            'aanwezigheidsstatus' => trim((string) $attributes['aanwezigheidsstatus']),
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

    /**
     * @param array<string, mixed> $attributes
     * @return array{gewijzigd: bool, bestaat_email_al: bool, klant_bestaat: bool}
     */
    private static function wijzigViaQuery(int $klantId, array $attributes): array
    {
        $klantBestaat = DB::table('klanten')->where('id', $klantId)->exists();

        if (! $klantBestaat) {
            return [
                'gewijzigd' => false,
                'bestaat_email_al' => false,
                'klant_bestaat' => false,
            ];
        }

        $emailadres = trim((string) ($attributes['emailadres'] ?? ''));
        $emailBestaatAl = false;

        if ($emailadres !== '') {
            $emailBestaatAl = DB::table('klanten')
                ->where('id', '!=', $klantId)
                ->where('emailadres', $emailadres)
                ->exists();
        }

        if ($emailBestaatAl) {
            return [
                'gewijzigd' => false,
                'bestaat_email_al' => true,
                'klant_bestaat' => true,
            ];
        }

        DB::table('klanten')
            ->where('id', $klantId)
            ->update([
                'gezinsnaam' => trim((string) $attributes['gezinsnaam']),
                'adres' => trim((string) $attributes['adres']),
                'telefoonnummer' => trim((string) $attributes['telefoonnummer']),
                'emailadres' => $emailadres === '' ? null : $emailadres,
                'aanwezigheidsstatus' => trim((string) $attributes['aanwezigheidsstatus']),
                'aantal_volwassenen' => (int) $attributes['aantal_volwassenen'],
                'aantal_kinderen' => (int) $attributes['aantal_kinderen'],
                'aantal_babys' => (int) $attributes['aantal_babys'],
                'updated_at' => now(),
            ]);

        return [
            'gewijzigd' => true,
            'bestaat_email_al' => false,
            'klant_bestaat' => true,
        ];
    }

    /**
     * @return array{verwijderd: bool, aanwezig: bool, klant_bestaat: bool}
     */
    private static function verwijderViaQuery(int $klantId): array
    {
        $klant = DB::table('klanten')
            ->where('id', $klantId)
            ->select(['id', 'aanwezigheidsstatus'])
            ->first();

        if (! $klant) {
            return [
                'verwijderd' => false,
                'aanwezig' => false,
                'klant_bestaat' => false,
            ];
        }

        if ($klant->aanwezigheidsstatus === 'binnen_land') {
            return [
                'verwijderd' => false,
                'aanwezig' => true,
                'klant_bestaat' => true,
            ];
        }

        DB::transaction(function () use ($klantId): void {
            // Eerst pakketregels verwijderen, daarna pakketten en klant.
            DB::statement(
                'DELETE pp FROM pakket_product pp
                 INNER JOIN voedselpakketten vp ON vp.id = pp.pakket_id
                 WHERE vp.klant_id = ?',
                [$klantId]
            );

            DB::table('voedselpakketten')->where('klant_id', $klantId)->delete();
            DB::table('klant_wens')->where('klant_id', $klantId)->delete();
            DB::table('klanten')->where('id', $klantId)->delete();
        });

        return [
            'verwijderd' => true,
            'aanwezig' => false,
            'klant_bestaat' => true,
        ];
    }
}
