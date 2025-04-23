<div class="row">
    @foreach($cryptocurrencies as $crypto)
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">{{ $crypto->name }} ({{ $crypto->symbol }})</h4>
                </div>
                <div class="card-body">
                    @if($crypto->latestPrice)
                        <p><strong>Precio:</strong> ${{ number_format($crypto->latestPrice->price, 2) }}</p>
                        <p><strong>Cambio 24h:</strong> {{ number_format($crypto->latestPrice->percent_change_24h, 4) }}%</p>
                        <p><strong>Volumen:</strong> ${{ number_format($crypto->latestPrice->volume_24h, 2) }}</p>
                        <p><strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($crypto->latestPrice->recorded_at)->diffForHumans() }}</p>

                        <canvas
                            id="chart-{{ $crypto->id }}"
                            height="200"
                            data-chart-data='@json($crypto->prices->pluck("price")->reverse()->values())'
                            data-chart-labels='@json($crypto->prices->pluck("recorded_at")->map(fn($d) => \Carbon\Carbon::parse($d)->format("H:i d/m"))->reverse()->values())'>
                        </canvas>

                        <form action="{{ route('cryptocurrencies.destroy', $crypto->id) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    @else
                        <p>No hay datos disponibles</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
