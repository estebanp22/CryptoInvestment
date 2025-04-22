<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptocurrencyPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'cryptocurrency_id',
        'price',
        'volume_24h',
        'percent_change_1h',  // Agrega este campo
        'percent_change_24h',
        'percent_change_7d',  // También agregamos este campo si lo necesitas
        'market_cap',  // Si lo necesitas
        'recorded_at',
        'timestamp'
    ];
    // Relación inversa: un precio pertenece a una criptomoneda
    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
}
