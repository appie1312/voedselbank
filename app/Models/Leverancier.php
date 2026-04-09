<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Leverancier extends Model
{
    protected $table = 'leveranciers';

    protected $fillable = [
        'bedrijfsnaam', 'adres', 'contactpersoon_naam', 'contactpersoon_email', 'telefoonnummer', 'volgende_levering'
    ];

    public static function getAllMetProducten(): Collection
    {
        $leveranciers = collect(DB::select('CALL sp_get_all_leveranciers()'));
        $heeftPivotTabel = Schema::hasTable('leverancier_products');
        $heeftLeverancierKolom = Schema::hasColumn('products', 'leverancier_id');

        // Producten toevoegen (optioneel)
        foreach ($leveranciers as $leverancier) {
            $query = DB::table('products')->select('products.*');

            if ($heeftPivotTabel) {
                $query
                    ->join('leverancier_products', 'products.id', '=', 'leverancier_products.product_id')
                    ->where('leverancier_products.leverancier_id', $leverancier->id);
            } elseif ($heeftLeverancierKolom) {
                $query->where('products.leverancier_id', $leverancier->id);
            } else {
                $leverancier->producten = collect();
                continue;
            }

            $leverancier->producten = collect($query->get());
        }

        return $leveranciers;
    }
}
