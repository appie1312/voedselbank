<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Leverancier extends Model
{
    protected $table = 'leveranciers';

    protected $fillable = [
        'bedrijfsnaam', 'adres', 'contactpersoon_naam', 'contactpersoon_email', 'telefoonnummer', 'volgende_levering'
    ];

    public static function getAllMetProducten(): Collection
    {
        $leveranciers = collect(DB::select('CALL sp_get_all_leveranciers()'));

        // Producten toevoegen (optioneel)
        foreach ($leveranciers as $leverancier) {
            $leverancier->producten = collect(DB::table('products')
                ->join('leverancier_products', 'products.id', '=', 'leverancier_products.product_id')
                ->where('leverancier_products.leverancier_id', $leverancier->id)
                ->select('products.*')
                ->get()
            );
        }

        return $leveranciers;
    }
}