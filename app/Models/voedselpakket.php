<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voedselpakket extends Model
{
    use HasFactory;

    // Koppel het model aan de juiste tabelnaam (Laravel zoekt anders naar 'voedselpakkets')
    protected $table = 'voedselpakketten';

    // Beveiliging: Alleen deze velden mogen via een formulier ingevuld worden
    protected $fillable = [
        'klant_id',
        'datum_samenstelling',
        'datum_uitgifte',
        'is_actief',
        'opmerking',
    ];

    // Casts zorgen ervoor dat Laravel automatisch datums en booleans herkent
    protected $casts = [
        'datum_samenstelling' => 'date',
        'datum_uitgifte' => 'date',
        'is_actief' => 'boolean',
    ];


    /**
     * Een voedselpakket hoort bij één specifiek gezin (Klant).
     */
    public function klant()
    {
        return $this->belongsTo(Klant::class, 'klant_id');
    }

    /**
     * Een voedselpakket bevat meerdere Producten (Many-to-Many).
     * We gebruiken 'withPivot' om aan te geven dat we ook het 'aantal'
     * uit de tussentabel (pakket_product) willen ophalen.
     */
    public function producten()
    {
        return $this->belongsToMany(Product::class, 'pakket_product', 'pakket_id', 'product_id')
                    ->withPivot('aantal')
                    ->withTimestamps();
    }
}