@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Escolha um Plano</h1>
    <form action="{{ route('subscription.checkout') }}" method="POST">
        @csrf
        <div class="row">
            @foreach ($plans as $plan)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $plan['name'] }}</h5>
                            <p class="card-text">R$ {{ number_format($plan['price'], 2, ',', '.') }} / mês</p>
                            <ul class="list-group list-group-flush">
                                @foreach ($plan['features'] as $feature)
                                    <li class="list-group-item">{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <button type="submit" name="plan_id" value="{{ $plan['id'] }}" class="btn btn-primary">Selecionar Plano</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
@endsection
