@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<h2><i class="fas fa-chart-bar me-3"></i>Reportes</h2>

<div class="row mt-4">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                <h5>Reporte de Inventario</h5>
                <p class="text-muted">Listado completo del inventario actual</p>
                <a href="{{ route('reports.inventory') }}" class="btn btn-primary">Ver Reporte</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                <h5>Reporte de Movimientos</h5>
                <p class="text-muted">Historial de ingresos y salidas</p>
                <a href="{{ route('reports.movements') }}" class="btn btn-success">Ver Reporte</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5>Items Críticos</h5>
                <p class="text-muted">Items con stock bajo o inactivos</p>
                <a href="{{ route('reports.critical-items') }}" class="btn btn-warning">Ver Reporte</a>
            </div>
        </div>
    </div>
</div>
@endsection