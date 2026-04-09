<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model voor de tabel 'voorraad'.
 * Dit model haalt voorraadgegevens op uit de database.
 */
class Voorraad extends Model
{
    // Laravel gebruikt standaard de meervoudsvorm,
    // maar omdat jouw tabel precies 'voorraad' heet zetten we dit expliciet.
    protected $table = 'voorraad';

    // Welke velden ingevuld mogen worden
    protected $fillable = [
        'product_id',
        'hoeveelheid',
        'minimum_voorraad',
        'locatie',
    ];

    /**
     * Relatie:
     * Elke voorraadregel hoort bij 1 product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Bepaal de status van de voorraad.
     * - Leeg als hoeveelheid 0 is
     * - Aanvullen als hoeveelheid <= minimum voorraad
     * - Voldoende in andere gevallen
     */
    public function getVoorraadStatusAttribute()
    {
        if ($this->hoeveelheid <= 0) {
            return 'Leeg';
        }

        if (!is_null($this->minimum_voorraad) && $this->hoeveelheid <= $this->minimum_voorraad) {
            return 'Aanvullen';
        }

        return 'Voldoende';
    }
}
