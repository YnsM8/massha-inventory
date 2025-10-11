@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-user"></i> Detalle del Usuario</h2>
            <p class="text-muted">Información completa del usuario</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Personal</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div style="width: 120px; height: 120px; margin: 0 auto; background: linear-gradient(135deg, var(--naranja), var(--naranja-hover)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 3rem; color: white;"></i>
                        </div>
                        <h3 class="mt-3 mb-0">{{ $user->name }}</h3>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">ID:</th>
                            <td><strong>#{{ $user->id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Rol:</th>
                            <td>
                                <span class="badge bg-{{ $user->role_color }} fs-6">
                                    {{ $user->role_name }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Permisos del Rol -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Permisos del Rol</h5>
                </div>
                <div class="card-body">
                    @if($user->role === 'admin')
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-crown"></i> Administrador - Acceso Total</h6>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Gestionar inventario completo</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Registrar ingresos y salidas</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Gestionar proveedores</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Crear y aprobar solicitudes</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Generar reportes</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Gestionar usuarios</li>
                        </ul>
                    @elseif($user->role === 'produccion')
                        <div class="alert alert-primary">
                            <h6><i class="fas fa-utensils"></i> Producción (Cocina)</h6>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Consultar stock disponible</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Crear solicitudes de insumos</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Ver historial de solicitudes</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> No puede aprobar solicitudes</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> No puede gestionar usuarios</li>
                        </ul>
                    @elseif($user->role === 'ventas')
                        <div class="alert alert-success">
                            <h6><i class="fas fa-handshake"></i> Ventas</h6>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Consultar disponibilidad de insumos</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Ver stock para cotizaciones</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> No puede crear solicitudes</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> No puede registrar movimientos</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> Solo lectura del inventario</li>
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-chart-line"></i> Gerencia</h6>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Ver dashboard y reportes</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Consultar movimientos</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Ver indicadores del sistema</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> Solo acceso de lectura</li>
                            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> No puede modificar datos</li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cogs"></i> Acciones</h5>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar Usuario
                </a>
                
                @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user->id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Usuario
                        </button>
                    </form>
                @else
                    <button class="btn btn-danger" disabled title="No puedes eliminarte a ti mismo">
                        <i class="fas fa-ban"></i> No puedes eliminarte
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection