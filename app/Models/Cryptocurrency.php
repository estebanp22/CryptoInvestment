<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cryptocurrency extends Model
{
  use HasFactory;

    protected $fillable = [
        'name', 'symbol', 'logo_url', 'cmc_id', 'slug',
    ];

    public function prices(): HasMany
    {
    return $this->hasMany(CryptocurrencyPrice::class);
}

    public function latestPrice()
    {
        return $this->hasOne(CryptocurrencyPrice::class)->latest();
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'favorites');
}
}
