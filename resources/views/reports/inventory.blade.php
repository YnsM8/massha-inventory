@extends('layouts.app')

@section('title', 'Reporte de Inventario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-alt me-3"></i>Reporte de Inventario</h2>
    <a href="{{ route('reports.inventory', ['export' => 'csv']) }}" class="btn btn-success">
        <i class="fas fa-download me-2"></i>Exportar CSV
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <h4 class="text-primary">{{ $totals['total_items'] }}</h4>
                <p class="text-muted">Total Items</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-success">S/. {{ number_format($totals['total_value'], 2) }}</h4>
                <p class="text-muted">Valor Total</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-warning">{{ $totals['low_stock_count'] }}</h4>
                <p class="text-muted">Stock Bajo</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-danger">{{ $totals['expired_count'] }}</h4>
                <p class="text-muted">Por Vencer</p>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Unidad</th>
                <th>Precio Unit.</th>
                <th>Valor Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->current_stock }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>S/. {{ number_format($item->unit_price, 2) }}</td>
                    <td>S/. {{ number_format($item->total_value, 2) }}</td>
                    <td><span class="badge bg-{{ $item->status_color }}">{{ $item->status_label }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection