@extends('layouts.app')

@section('content')
    <div class="container">
        <button id="updateBtn" class="btn btn-primary mb-4">Actualizar Precios</button>
        <div id="cryptocurrencies-container"></div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // Actualización periódica cada 1 minuto (60000 milisegundos)
            setInterval(function() {
                $.get('/cryptocurrencies', function(data) {
                    $('#cryptocurrencies-container').html(data);
                });
            }, 60000); // 1 minuto

            $('#updateBtn').click(function() {
                $.get('/cryptocurrencies', function(data) {
                    $('#cryptocurrencies-container').html(data);
                });
            });
        </script>





        <h1 class="my-4">Criptomonedas</h1>

        <!-- Tarjetas de criptomonedas -->
        <div class="row">
            @foreach($cryptocurrencies as $crypto)
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">{{ $crypto->name }} ({{ $crypto->symbol }})</h4>
                        </div>
                        <div class="card-body">
                            @if($crypto->latestPrice) <!-- Verifica si latestPrice existe -->
                            <p><strong>Price:</strong> ${{ number_format($crypto->latestPrice->price, 2) }}</p>
                            <p><strong>24h Change:</strong> {{ number_format($crypto->latestPrice->percent_change_24h, 4) }}%</p>
                            <p><strong>Volume:</strong> ${{ number_format($crypto->latestPrice->volume_24h, 2) }}</p>
                            <p><strong>Last Update:</strong> {{ $crypto->latestPrice->recorded_at->diffForHumans() }}</p>
                            @else
                                <p>No data available</p> <!-- Muestra un mensaje si no hay datos -->
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Gráfico de precios -->
        <canvas id="cryptoChart" width="400" height="200"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            var ctx = document.getElementById('cryptoChart').getContext('2d');
            var cryptoChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($cryptocurrencies->pluck('name')), // Nombres de criptos como etiquetas
                    datasets: [{
                        label: 'Precio',
                        data: @json($cryptocurrencies->pluck('latestPrice.price')), // Precios actuales
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        </script>
    </div>
@endsection
