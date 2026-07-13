<!-- resources/views/settings/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Configurações</h1>
    <form action="{{ route('settings.update', ['setting' => $setting->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_logo" class="form-label">Logo da Empresa</label>
            <input type="file" name="company_logo" id="company_logo" accept="image/*" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Configurações</button>
    </form>
</div>
@endsection
