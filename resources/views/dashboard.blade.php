@extends('layouts.app')

@section('title', 'Dashboard - MASSHA\'S CATERING')

@section('content')
<h2><i class="fas fa-tachometer-alt me-3"></i>Dashboard</h2>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #27ae60);">
            <div class="card-body text-center">
                <h3>{{ $stats['total_items'] ?? 0 }}</h3>
                <p>Items Totales</p>
                <i class="fas fa-boxes fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
            <div class="card-body text-center">
                <h3>{{ $stats['low_stock_items'] ?? 0 }}</h3>
                <p>Stock Bajo</p>
                <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
            <div class="card-body text-center">
                <h3>{{ $stats['expired_items'] ?? 0 }}</h3>
                <p>Por Vencer</p>
                <i class="fas fa-clock fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
            <div class="card-body text-center">
                <h3>{{ $stats['total_suppliers'] ?? 0 }}</h3>
                <p>Proveedores</p>
                <i class="fas fa-truck fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Alertas de Stock</h5>
            </div>
            <div class="card-body">
                @if($lowStockItems->count() > 0)
                    @foreach($lowStockItems as $item)
                        <div class="alert alert-warning mb-2">
                            <strong>{{ $item->name }}</strong> - Stock: {{ $item->current_stock }} {{ $item->unit }} (Mínimo: {{ $item->min_stock }})
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-success">No hay alertas de stock</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-history me-2"></i>Movimientos Recientes</h5>
            </div>
            <div class="card-body">
                @if($recentMovements->count() > 0)
                    @foreach($recentMovements as $movement)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge {{ $movement->type === 'incoming' ? 'bg-success' : 'bg-warning' }} me-2">
                                <i class="fas {{ $movement->type === 'incoming' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </span>
                            <div>
                                <strong>{{ $movement->item->name }}</strong> - {{ $movement->quantity }} {{ $movement->item->unit }}
                                <small class="text-muted d-block">{{ $movement->movement_date->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No hay movimientos recientes</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($topItems->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5><i class="fas fa-trophy me-2"></i>Items Más Utilizados (Últimos 30 días)</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Categoría</th>
                            <th>Total Usado</th>
                            <th>Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category }}</td>
                                <td>{{ $item->total_used }} {{ $item->unit }}</td>
                                <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endsection