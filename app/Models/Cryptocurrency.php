<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
  use HasFactory;

    protected $fillable = ['symbol', 'name'];

    // Relación uno a muchos (cryptocurrency tiene muchos precios)
    public function prices()
{
    return $this->hasMany(CryptocurrencyPrice::class);
}

    public function latestPrice()
    {
        return $this->hasOne(CryptocurrencyPrice::class)->latest();
    }

    // Relación muchos a muchos con usuarios (favoritos)
    public function users()
{
    return $this->belongsToMany(User::class, 'favorites');
}
}
