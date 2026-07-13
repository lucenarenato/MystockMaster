<!-- resources/views/dashboard/usage.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard de Uso</h1>
    <div class="row">
        @foreach ($tenant->getPlanLimits() as $limit => $value)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ ucfirst($limit) }}</h5>
                        <p class="card-text">Usado: {{ $usage[$limit] ?? 0 }} / Limite: {{ $value }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
