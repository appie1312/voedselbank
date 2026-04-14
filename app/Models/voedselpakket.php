<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Voedselpakket extends Model
{
    use HasFactory;

    protected $table = 'voedselpakketten';

    protected $fillable = [
        'klant_id',
        'datum_samenstelling',
        'datum_uitgifte',
    ];

    protected $casts = [
        'datum_samenstelling' => 'date',
        'datum_uitgifte' => 'date',
    ];


    /**
     * Een voedselpakket hoort bij één specifiek gezin (Klant).
     */
    public function klant()
    {
        return $this->belongsTo(Klant::class, 'klant_id');
    }

    public function producten()
    {
        // Producten in pakket inclusief gekozen aantal.
        return $this->belongsToMany(Product::class, 'pakket_product', 'pakket_id', 'product_id')
                    ->withPivot('aantal');
    }

    public static function haalOverzichtViaStoredProcedure(?string $zoekterm, int $aantalRijen, ?string $eetwens = null): Collection
    {
        $veiligeZoekterm = trim((string) $zoekterm);
        $veiligeEetwens = trim((string) $eetwens);
        $begrensdAantalRijen = min(max($aantalRijen, 1), 25);

        try {
            $resultaten = DB::select('CALL sp_voedselpakketten_overzicht(?, ?, ?)', [
                $veiligeZoekterm === '' ? null : $veiligeZoekterm,
                $begrensdAantalRijen,
                $veiligeEetwens === '' ? null : $veiligeEetwens,
            ]);
        } catch (QueryException $exception) {
            $message = strtolower($exception->getMessage());
            $sqliteFallback = DB::getDriverName() !== 'mysql' && str_contains($message, 'call');
            $procedureNietBeschikbaar = str_contains($message, 'sp_voedselpakketten_overzicht');

            if (! $sqliteFallback && ! $procedureNietBeschikbaar) {
                throw $exception;
            }

            // Fallback voor omgevingen zonder SP.
            return self::haalOverzichtViaJoinQuery($veiligeZoekterm, $begrensdAantalRijen, $veiligeEetwens);
        }

        return collect($resultaten);
    }

    public static function haalVoorWijzigenViaJoin(int $pakketId): ?object
    {
        return DB::table('voedselpakketten as vp')
            ->join('klanten as k', 'k.id', '=', 'vp.klant_id')
            ->where('vp.id', $pakketId)
            ->select([
                'vp.id',
                'vp.klant_id',
                'vp.datum_samenstelling',
                'vp.datum_uitgifte',
                'k.gezinsnaam',
                'k.aanwezigheidsstatus',
            ])
            ->first();
    }

    /**
     * @param array{klant_id:int, opgehaald:bool} $attributes
     */
    public static function wijzigViaQuery(int $pakketId, array $attributes): bool
    {
        $pakketBestaat = DB::table('voedselpakketten')->where('id', $pakketId)->exists();

        if (! $pakketBestaat) {
            return false;
        }

        DB::table('voedselpakketten')
            ->where('id', $pakketId)
            ->update([
                'klant_id' => $attributes['klant_id'],
                'datum_uitgifte' => $attributes['opgehaald'] ? now()->toDateString() : null,
                'updated_at' => now(),
            ]);

        return true;
    }

    public static function voegToeViaQuery(int $klantId): int
    {
        return (int) DB::table('voedselpakketten')->insertGetId([
            'klant_id' => $klantId,
            'datum_samenstelling' => now()->toDateString(),
            'datum_uitgifte' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function verwijderViaQuery(int $pakketId): bool
    {
        $bestaat = DB::table('voedselpakketten')->where('id', $pakketId)->exists();

        if (! $bestaat) {
            return false;
        }

        DB::transaction(function () use ($pakketId): void {
            // Eerst pivot opruimen, daarna pakket.
            DB::table('pakket_product')->where('pakket_id', $pakketId)->delete();
            DB::table('voedselpakketten')->where('id', $pakketId)->delete();
        });

        return true;
    }

    private static function haalOverzichtViaJoinQuery(string $zoekterm, int $aantalRijen, string $eetwens): Collection
    {
        return DB::table('voedselpakketten as vp')
            ->join('klanten as k', 'k.id', '=', 'vp.klant_id')
            ->when($eetwens !== '', function ($query) use ($eetwens): void {
                $query
                    ->join('klant_wens as kw', 'kw.klant_id', '=', 'k.id')
                    ->join('wens_allergies as wa', 'wa.id', '=', 'kw.wens_id')
                    ->where('wa.beschrijving', $eetwens);
            })
            ->when($zoekterm !== '', function ($query) use ($zoekterm): void {
                $query->where(function ($subQuery) use ($zoekterm): void {
                    $subQuery->where('k.gezinsnaam', 'like', '%' . $zoekterm . '%');

                    if (is_numeric($zoekterm)) {
                        $subQuery->orWhere('vp.id', (int) $zoekterm);
                    }
                });
            })
            ->orderByDesc('vp.datum_samenstelling')
            ->orderByDesc('vp.id')
            ->limit($aantalRijen)
            ->select([
                'vp.id',
                'vp.klant_id',
                'k.gezinsnaam',
                'vp.datum_samenstelling',
                'vp.datum_uitgifte',
            ])
            ->distinct()
            ->get();
    }
}
