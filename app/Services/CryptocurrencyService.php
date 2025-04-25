<?php
namespace App\Services;

use App\Models\Cryptocurrency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CryptocurrencyService
{
    public function getAllWithPrices()
    {
        return Cryptocurrency::with([
            'latestPrice',
            'prices' => function($query) {
                $query->orderBy('recorded_at', 'desc')->limit(10);
            }
        ])->get();
    }

    public function updatePrices()
    {
        $cryptocurrencies = Cryptocurrency::all();

        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY'),
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json()['data'];

            foreach ($data as $coin) {
                $cryptocurrency = Cryptocurrency::where('symbol', $coin['symbol'])->first();

                if ($cryptocurrency) {
                    // Verificar si ya existe un precio para hoy
                    $existingPrice = $cryptocurrency->prices()->where('recorded_at', now()->toDateString())->first();

                    // Solo crear un nuevo precio si no existe un registro para hoy
                    if (!$existingPrice) {
                        $quote = $coin['quote']['USD'];

                        $cryptocurrency->prices()->create([
                            'price' => $quote['price'] ?? 0,
                            'volume_24h' => $quote['volume_24h'] ?? 0,
                            'percent_change_1h' => $quote['percent_change_1h'] ?? 0,
                            'percent_change_24h' => $quote['percent_change_24h'] ?? 0,
                            'percent_change_7d' => $quote['percent_change_7d'] ?? 0,
                            'market_cap' => $quote['market_cap'] ?? 0,
                            'recorded_at' => now(),
                            'timestamp' => now(),
                        ]);
                    }
                }
            }
        }
    }


    public function getHistoricalData($crypto_id)
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY'),
        ])->get("https://pro-api.coinmarketcap.com/v1/cryptocurrency/ohlcv/historical", [
            'id' => $crypto_id,
            'time_start' => now()->subMonth()->timestamp,
            'time_end' => now()->timestamp,
            'interval' => 'daily',
        ]);

        return $response->json();
    }

    public function storeCryptocurrency($symbol)
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY'),
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/map');

        if ($response->failed()) {
            return ['error' => 'No se pudo contactar con CoinMarketCap'];
        }

        $cryptos = $response->json('data');
        $crypto = collect($cryptos)->firstWhere('symbol', $symbol);

        if (!$crypto) {
            return ['error' => 'Símbolo no encontrado en CoinMarketCap'];
        }

        $slug = Str::slug($crypto['name']);

        Cryptocurrency::create([
            'name' => $crypto['name'],
            'symbol' => $crypto['symbol'],
            'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/' . $crypto['id'] . '.png',
            'cmc_id' => $crypto['id'],
            'slug' => $slug,
        ]);

        return ['success' => true];
    }
}
