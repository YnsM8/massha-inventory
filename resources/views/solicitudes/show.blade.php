@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt"></i> Detalle de Solicitud</h2>
            <p class="text-muted">{{ $solicitud->codigo_solicitud }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            
            @if($solicitud->estado == 'pendiente')
                <form action="{{ route('solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar esta solicitud? Se descontará el stock automáticamente.')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                </form>
                
                <form action="{{ route('solicitudes.rechazar', $solicitud->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar esta solicitud?')">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Información General -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Evento</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Código:</th>
                            <td><strong>{{ $solicitud->codigo_solicitud }}</strong></td>
                        </tr>
                        <tr>
                            <th>Evento:</th>
                            <td>{{ $solicitud->evento }}</td>
                        </tr>
                        <tr>
                            <th>Fecha del Evento:</th>
                            <td>{{ $solicitud->fecha_evento ? $solicitud->fecha_evento->format('d/m/Y') : 'Sin fecha' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $solicitud->estado_color }} fs-6">
                                    {{ $solicitud->estado_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Observaciones:</th>
                            <td>{{ $solicitud->observaciones ?? 'Sin observaciones' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Gestión -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Información de Gestión</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Código:</th>
                            <td><strong>{{ $solicitud->codigo_solicitud ?? 'Sin código' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Evento:</th>
                            <td>{{ $solicitud->evento ?? 'Sin nombre' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha del Evento:</th>
                            <td>
                                @if($solicitud->fecha_evento)
                                    {{ $solicitud->fecha_evento->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $solicitud->estado_color ?? 'secondary' }} fs-6">
                                    {{ $solicitud->estado_label ?? 'Desconocido' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Observaciones:</th>
                            <td>{{ $solicitud->observaciones ?? 'Sin observaciones' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Solicitados -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Items Solicitados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Cantidad Solicitada</th>
                            <th>Stock Disponible (al momento)</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitud->items as $index => $solicitudItem)
                            <tr class="{{ !$solicitudItem->stock_suficiente ? 'table-warning' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $solicitudItem->item->name }}</strong><br>
                                    <small class="text-muted">{{ $solicitudItem->item->category }}</small>
                                </td>
                                <td>
                                    {{ number_format($solicitudItem->cantidad_solicitada, 2) }} 
                                    {{ $solicitudItem->item->unit }}
                                </td>
                                <td>
                                    {{ number_format($solicitudItem->cantidad_disponible, 2) }} 
                                    {{ $solicitudItem->item->unit }}
                                </td>
                                <td>
                                    @if($solicitudItem->stock_suficiente)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Stock Suficiente
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation-triangle"></i> Stock Insuficiente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($solicitud->items->where('stock_suficiente', false)->count() > 0)
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Atención:</strong> Algunos items tienen stock insuficiente. 
                    @if($solicitud->estado == 'pendiente')
                        La solicitud puede ser aprobada solo si el stock actual es suficiente al momento de la aprobación.
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Resumen -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h3 class="text-primary">{{ $solicitud->items->count() }}</h3>
                    <p class="text-muted mb-0">Items Solicitados</p>
                </div>
                <div class="col-md-4">
                    <h3 class="text-success">{{ $solicitud->items->where('stock_suficiente', true)->count() }}</h3>
                    <p class="text-muted mb-0">Con Stock Suficiente</p>
                </div>
                <div class="col-md-4">
                    <h3 class="text-warning">{{ $solicitud->items->where('stock_suficiente', false)->count() }}</h3>
                    <p class="text-muted mb-0">Con Stock Insuficiente</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection