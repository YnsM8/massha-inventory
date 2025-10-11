@extends('layouts.app')

@section('title', 'Reporte de Movimientos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-line me-3"></i>Reporte de Movimientos</h2>
    <a href="{{ route('reports.movements', ['export' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
        <i class="fas fa-download me-2"></i>Exportar CSV
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.movements') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <h4 class="text-primary">{{ $stats['total_movements'] }}</h4>
                <p class="text-muted">Total Movimientos</p>
            </div>
            <div class="col-md-4">
                <h4 class="text-success">{{ number_format($stats['total_incoming'], 2) }}</h4>
                <p class="text-muted">Total Ingresos</p>
            </div>
            <div class="col-md-4">
                <h4 class="text-warning">{{ number_format($stats['total_outgoing'], 2) }}</h4>
                <p class="text-muted">Total Salidas</p>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Item</th>
                <th>Cantidad</th>
                <th>Proveedor/Motivo</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $movement->type === 'incoming' ? 'success' : 'warning' }}">
                            {{ $movement->type_description }}
                        </span>
                    </td>
                    <td>{{ $movement->item->name }}</td>
                    <td>{{ $movement->quantity }} {{ $movement->item->unit }}</td>
                    <td>{{ $movement->supplier ? $movement->supplier->name : $movement->reason_description }}</td>
                    <td>{{ $movement->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection