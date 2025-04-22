<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use App\Models\CryptocurrencyPrice;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class CryptocurrencyController extends Controller
{
    public function index()
    {
        $cryptocurrencies = Cryptocurrency::with('latestPrice')->get();

        // Asegurándote de convertir recorded_at a Carbon
        foreach ($cryptocurrencies as $crypto) {
            if ($crypto->latestPrice) {
                $crypto->latestPrice->recorded_at = Carbon::parse($crypto->latestPrice->recorded_at);
            }
        }

        return view('cryptocurrencies.index', compact('cryptocurrencies'));
    }

    public function updatePrices()
    {
        // Obtener las criptomonedas
        $cryptocurrencies = Cryptocurrency::all();

        // Acceder a la API de CoinMarketCap para obtener precios
        $apiKey = '15999931-4232-44bb-91a9-1d271f17fcfd';  // Sustituir con tu clave API
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json()['data'];

            foreach ($data as $coin) {
                $cryptocurrency = Cryptocurrency::where('symbol', $coin['symbol'])->first();

                if ($cryptocurrency) {
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

        return redirect()->route('cryptocurrencies.index');
    }
}
