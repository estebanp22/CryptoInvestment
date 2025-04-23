<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use App\Models\CryptocurrencyPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CryptocurrencyController extends Controller
{

    public function index(Request $request)
    {
        $cryptocurrencies = Cryptocurrency::with([
            'latestPrice',
            'prices' => function($query) {
                $query->orderBy('recorded_at', 'desc')->limit(10);
            }
        ])->get();

        if ($request->ajax()) {
            return view('partials.cryptocurrencies', compact('cryptocurrencies'));
        }

        return view('cryptocurrencies.index', compact('cryptocurrencies'));
    }





    public function updatePrices()
    {
        // Obtener las criptomonedas
        $cryptocurrencies = Cryptocurrency::all();

        // Acceder a la API de CoinMarketCap para obtener precios
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

        return redirect()->route('cryptocurrencies.index');
    }

    // Nuevo método para mostrar el historial de precios de una criptomoneda
    public function showHistory($crypto_id)
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY'),
        ])->get("https://pro-api.coinmarketcap.com/v1/cryptocurrency/ohlcv/historical", [
            'id' => $crypto_id,
            'time_start' => now()->subMonth()->timestamp, // Hace un mes
            'time_end' => now()->timestamp, // Hasta la fecha actual
            'interval' => 'daily', // Intervalo diario
        ]);

        $historicalData = $response->json();

        return view('cryptocurrencies.history', compact('historicalData'));
    }



    /**
     * Show the form for creating a new cryptocurrency.
     *
     *
     */
    public function create(): \Illuminate\View\View
    {

        return view('cryptocurrencies.create');
    }

    /**
     * Store a newly created cryptocurrency in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $apiKey = '15999931-4232-44bb-91a9-1d271f17fcfd';  // Sustituir con tu clave API

        $request->validate([
            'symbol' => 'required|string|max:10',
        ]);

        $symbol = strtoupper($request->symbol);

        // Realizar la solicitud a la API de CoinMarketCap
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY'),
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/map');

        // Verificar si la solicitud falló
        if ($response->failed()) {
            return back()->withErrors(['error' => 'No se pudo contactar con CoinMarketCap']);
        }

        // Obtener los datos de criptomonedas de la respuesta
        $cryptos = $response->json('data');
        $crypto = collect($cryptos)->firstWhere('symbol', $symbol);

        // Verificar si no se encuentra la criptomoneda
        if (!$crypto) {
            return back()->withErrors(['symbol' => 'Símbolo no encontrado en CoinMarketCap']);
        }

        // Generar el slug basado en el nombre de la criptomoneda
        $slug = Str::slug($crypto['name']);

        // Crear la criptomoneda en la base de datos
        Cryptocurrency::create([
            'name' => $crypto['name'],
            'symbol' => $crypto['symbol'],
            'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/' . $crypto['id'] . '.png',
            'cmc_id' => $crypto['id'],
            'slug' => $slug, // Aquí se asigna el slug
        ]);

        return redirect()->route('cryptocurrencies.index');
    }


    /**
     * Remove the specified cryptocurrency from storage.
     *
     * @param \App\Models\Cryptocurrency $cryptocurrency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cryptocurrency $cryptocurrency)
    {
        $cryptocurrency->delete();
        return redirect()->route('cryptocurrencies.index');
    }
}
