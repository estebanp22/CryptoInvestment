@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Agregar nueva criptomoneda</h1>

        <form action="{{ route('cryptocurrencies.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label for="symbol" class="form-label">Símbolo de la criptomoneda (ej. BTC, ETH)</label>
                <input type="text" class="form-control" id="symbol" name="symbol" required placeholder="Ingresa el símbolo de la criptomoneda">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Agregar criptomoneda</button>
                <a href="{{ route('cryptocurrencies.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
