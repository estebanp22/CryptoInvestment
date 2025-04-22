<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptocurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptos = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'cmc_id' => 1,
                'slug' => 'bitcoin',
                'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/1.png'
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'cmc_id' => 1027,
                'slug' => 'ethereum',
                'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/1027.png'
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'cmc_id' => 2010,
                'slug' => 'cardano',
                'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/2010.png'
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'cmc_id' => 5426,
                'slug' => 'solana',
                'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/5426.png'
            ],
            [
                'name' => 'Ripple',
                'symbol' => 'XRP',
                'cmc_id' => 52,
                'slug' => 'ripple',
                'logo_url' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/52.png'
            ],
        ];




        foreach ($cryptos as $crypto) {
            Cryptocurrency::create($crypto);
        }
    }
}
