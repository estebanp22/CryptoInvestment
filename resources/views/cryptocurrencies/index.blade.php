@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container">
        <a href="{{ route('cryptocurrencies.create') }}" class="btn btn-success mb-4">Agregar criptomoneda</a>
        <button id="updateBtn" class="btn btn-primary mb-4">Actualizar Precios</button>

        <form id="searchForm" class="mb-4">
            <input type="text" class="form-control" id="searchInput" placeholder="Buscar criptomoneda por nombre o símbolo...">
        </form>

        <div id="cryptocurrencies-container">
            @include('partials.cryptocurrencies', ['cryptocurrencies' => $cryptocurrencies])
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function loadCryptos() {
            const search = $('#searchInput').val().toLowerCase(); // Captura lo que el usuario escribió

            $.get('/cryptocurrencies', function(data) {
                $('#cryptocurrencies-container').html(data);
                initCharts();

                // Reaplicar el filtro después de recargar el contenido
                $('.card').each(function() {
                    const name = $(this).find('.card-title').text().toLowerCase();
                    $(this).toggle(name.includes(search));
                });
            });
        }

        function initCharts() {
            document.querySelectorAll('canvas[id^="chart-"]').forEach(canvas => {
                let ctx = canvas.getContext('2d');
                let data = JSON.parse(canvas.dataset.chartData);
                let labels = JSON.parse(canvas.dataset.chartLabels);

                let borderColor = data[0] < data[data.length - 1] ? 'rgba(0, 128, 0, 1)' : 'rgba(255, 0, 0, 1)';

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Precio histórico',
                            data: data,
                            borderColor: borderColor,
                            fill: false,
                            tension: 0.2
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: false }
                        }
                    }
                });
            });
        }

        $('#updateBtn').click(function() {
            $.get('/cryptocurrencies/update')
                .done(res => {
                    console.log('✅ Precios actualizados', res);
                    loadCryptos();
                })
                .fail(err => console.error('❌ Error actualizando precios:', err));
        });

        // Buscar criptomonedas
        $('#searchInput').on('input', function() {
            const search = $(this).val().toLowerCase();
            $('.card').each(function() {
                const name = $(this).find('.card-title').text().toLowerCase();
                $(this).toggle(name.includes(search));
            });
        });

        // Forzar la primera actualización apenas carga la página
        $.get('/cryptocurrencies/update')
            .done(() => loadCryptos())
            .fail(err => console.error('❌ Error en la actualización inicial:', err));

        // Actualizar la página cada segundo
        setInterval(loadCryptos, 1000);

        // Intervalo que actualiza los precios cada 10 segundos
        setInterval(() => {
            $.get('/cryptocurrencies/update')
                .done(res => console.log('✅ Precios actualizados', res))
                .fail(err => console.error('❌ Error actualizando precios:', err));
        }, 10000);
    </script>

@endsection
