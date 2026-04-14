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

    /**
     * @return array{toegevoegd: bool, bestaat_gekoppeld: bool, bestaat_al: bool, klant_bestaat: bool, allergie_id: int|null}
     */
    public static function voegToeViaStoredProcedure(int $klantId, string $beschrijving): array
    {
        $veiligeBeschrijving = trim($beschrijving);

        try {
            $resultaat = DB::select('CALL sp_allergie_toevoegen(?, ?)', [$klantId, $veiligeBeschrijving]);
        } catch (QueryException $exception) {
            // Veilige fallback als de stored procedure nog niet bestaat.
            if (! str_contains(strtolower($exception->getMessage()), 'sp_allergie_toevoegen')) {
                throw $exception;
            }

            return self::voegToeViaQuery($klantId, $veiligeBeschrijving);
        }

        $eersteRij = $resultaat[0] ?? null;

        return [
            'toegevoegd' => ((int) ($eersteRij->toegevoegd ?? 0)) === 1,
            'bestaat_gekoppeld' => ((int) ($eersteRij->bestaat_gekoppeld ?? 0)) === 1,
            'bestaat_al' => ((int) ($eersteRij->bestaat_al ?? 0)) === 1,
            'klant_bestaat' => ((int) ($eersteRij->klant_bestaat ?? 1)) === 1,
            'allergie_id' => isset($eersteRij->allergie_id) ? (int) $eersteRij->allergie_id : null,
        ];
    }

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

    /**
     * @return array{verwijderd: bool, in_gebruik: bool, allergie_bestaat: bool}
     */
    public static function verwijderViaStoredProcedure(int $allergieId): array
    {
        try {
            $resultaat = DB::select('CALL sp_allergie_verwijderen(?)', [$allergieId]);
        } catch (QueryException $exception) {
            // Veilige fallback als de stored procedure nog niet bestaat.
            if (! str_contains(strtolower($exception->getMessage()), 'sp_allergie_verwijderen')) {
                throw $exception;
            }

            return self::verwijderViaQuery($allergieId);
        }

        $eersteRij = $resultaat[0] ?? null;

        return [
            'verwijderd' => ((int) ($eersteRij->verwijderd ?? 0)) === 1,
            'in_gebruik' => ((int) ($eersteRij->in_gebruik ?? 0)) === 1,
            'allergie_bestaat' => ((int) ($eersteRij->allergie_bestaat ?? 0)) === 1,
        ];
    }

    private static function haalOverzichtViaJoinQuery(?int $klantId, string $zoekterm, int $aantalRijen): Collection
    {
        return DB::table('wens_allergies as wa')
            ->leftJoin('klant_wens as kw', 'kw.wens_id', '=', 'wa.id')
            ->leftJoin('klanten as k', 'k.id', '=', 'kw.klant_id')
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

    /**
     * @return array{toegevoegd: bool, bestaat_gekoppeld: bool, bestaat_al: bool, klant_bestaat: bool, allergie_id: int|null}
     */
    private static function voegToeViaQuery(int $klantId, string $beschrijving): array
    {
        $veiligeBeschrijving = trim($beschrijving);
        $klantBestaat = DB::table('klanten')->where('id', $klantId)->exists();

        if (! $klantBestaat) {
            return [
                'toegevoegd' => false,
                'bestaat_gekoppeld' => false,
                'bestaat_al' => false,
                'klant_bestaat' => false,
                'allergie_id' => null,
            ];
        }

        $bestaandeAllergie = DB::table('wens_allergies')
            ->whereRaw('LOWER(beschrijving) = LOWER(?)', [$veiligeBeschrijving])
            ->first(['id']);

        $bestaatAl = true;

        if (! $bestaandeAllergie) {
            $nieuwAllergieId = (int) DB::table('wens_allergies')->insertGetId([
                'beschrijving' => $veiligeBeschrijving,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $allergieId = $nieuwAllergieId;
            $bestaatAl = false;
        } else {
            $allergieId = (int) $bestaandeAllergie->id;
        }

        $bestaatGekoppeld = DB::table('klant_wens')
            ->where('klant_id', $klantId)
            ->where('wens_id', $allergieId)
            ->exists();

        if ($bestaatGekoppeld) {
            return [
                'toegevoegd' => false,
                'bestaat_gekoppeld' => true,
                'bestaat_al' => $bestaatAl,
                'klant_bestaat' => true,
                'allergie_id' => $allergieId,
            ];
        }

        DB::table('klant_wens')->insert([
            'klant_id' => $klantId,
            'wens_id' => $allergieId,
        ]);

        return [
            'toegevoegd' => true,
            'bestaat_gekoppeld' => false,
            'bestaat_al' => $bestaatAl,
            'klant_bestaat' => true,
            'allergie_id' => $allergieId,
        ];
    }

    /**
     * @return array{verwijderd: bool, in_gebruik: bool, allergie_bestaat: bool}
     */
    private static function verwijderViaQuery(int $allergieId): array
    {
        $allergieBestaat = DB::table('wens_allergies')
            ->where('id', $allergieId)
            ->exists();

        if (! $allergieBestaat) {
            return [
                'verwijderd' => false,
                'in_gebruik' => false,
                'allergie_bestaat' => false,
            ];
        }

        $inGebruik = DB::table('klant_wens')
            ->where('wens_id', $allergieId)
            ->exists();

        if ($inGebruik) {
            return [
                'verwijderd' => false,
                'in_gebruik' => true,
                'allergie_bestaat' => true,
            ];
        }

        DB::table('wens_allergies')
            ->where('id', $allergieId)
            ->delete();

        return [
            'verwijderd' => true,
            'in_gebruik' => false,
            'allergie_bestaat' => true,
        ];
    }
}
