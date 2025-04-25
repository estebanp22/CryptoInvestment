<?php
namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use App\Services\CryptocurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CryptocurrencyController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptocurrencyService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function index(Request $request)
    {
        $cryptocurrencies = $this->cryptoService->getAllWithPrices();

        if ($request->ajax()) {
            return view('partials.cryptocurrencies', compact('cryptocurrencies'));
        }

        return view('cryptocurrencies.index', compact('cryptocurrencies'));
    }

    public function updatePrices()
    {
        // Llama al servicio para actualizar los precios
        $this->cryptoService->updatePrices();

        // Responde con un mensaje en JSON para indicar que los precios se actualizaron
        return response()->json(['message' => 'Prices updated']);
    }



    public function showHistory($crypto_id)
    {
        $historicalData = $this->cryptoService->getHistoricalData($crypto_id);
        return view('cryptocurrencies.history', compact('historicalData'));
    }

    public function create()
    {
        return view('cryptocurrencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|max:10',
        ]);

        $symbol = strtoupper($request->symbol);
        $result = $this->cryptoService->storeCryptocurrency($symbol);

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']]);
        }

        return redirect()->route('cryptocurrencies.index');
    }

    public function destroy(Cryptocurrency $cryptocurrency)
    {
        $cryptocurrency->delete();
        return redirect()->route('cryptocurrencies.index');
    }
}

