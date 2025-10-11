@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-clipboard-list"></i> Solicitudes de Insumos</h2>
            <p class="text-muted">Gestión de solicitudes para eventos</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('solicitudes.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de solicitudes -->
    <div class="card">
        <div class="card-body">
            @if($solicitudes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Evento</th>
                                <th>Fecha Evento</th>
                                <th>Estado</th>
                                <th>Solicitado por</th>
                                <th>Fecha Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td><strong>{{ $solicitud->codigo_solicitud }}</strong></td>
                                    <td>{{ $solicitud->evento }}</td>
                                    <td>{{ $solicitud->fecha_evento->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $solicitud->estado_color }}">
                                            {{ $solicitud->estado_label }}
                                        </span>
                                    </td>
                                    <td>{{ $solicitud->user->name }}</td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('solicitudes.show', $solicitud->id) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($solicitud->estado == 'pendiente')
                                            <form action="{{ route('solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Aprobar" onclick="return confirm('¿Aprobar esta solicitud? Se descontará el stock.')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('solicitudes.rechazar', $solicitud->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Rechazar" onclick="return confirm('¿Rechazar esta solicitud?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $solicitudes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay solicitudes registradas</p>
                    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Primera Solicitud
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection