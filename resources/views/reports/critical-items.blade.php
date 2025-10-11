@extends('layouts.app')

@section('title', 'Items Críticos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-exclamation-triangle me-3"></i>Items Críticos</h2>
    <a href="{{ route('reports.critical-items', ['export' => 'csv']) }}" class="btn btn-success">
        <i class="fas fa-download me-2"></i>Exportar CSV
    </a>
</div>

<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Items con Stock Bajo</h5>
    </div>
    <div class="card-body">
        @if($lowStockItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-danger fw-bold">{{ $item->current_stock }} {{ $item->unit }}</td>
                                <td>{{ $item->min_stock }} {{ $item->unit }}</td>
                                <td class="text-danger">{{ $item->current_stock - $item->min_stock }} {{ $item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success">No hay items con stock bajo</div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-secondary text-white">
        <h5><i class="fas fa-clock me-2"></i>Items Sin Movimiento (Últimos 60 días)</h5>
    </div>
    <div class="card-body">
        @if($inactiveItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inactiveItems as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category }}</td>
                                <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success">Todos los items tienen movimientos recientes</div>
        @endif
    </div>
</div>
@endsection