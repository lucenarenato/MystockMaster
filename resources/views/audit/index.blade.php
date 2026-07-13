<!-- resources/views/audit/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard de Auditoria</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ação</th>
                <th>Modelo</th>
                <th>Descrição</th>
                <th>Criado por</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr>
                    <td>{{ $activity->id }}</td>
                    <td>{{ $activity->description }}</td>
                    <td>{{ $activity->subject_type }}</td>
                    <td>{{ optional($activity->properties['attributes'])->toJson() ?? '' }}</td>
                    <td>{{ optional($activity->causer)->name ?? 'Anônimo' }}</td>
                    <td>{{ $activity->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
