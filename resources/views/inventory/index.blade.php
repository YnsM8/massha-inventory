@extends('layouts.app')

@section('title', 'Inventario - MASSHA\'S CATERING')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-boxes me-3"></i>Gestión de Inventario</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="fas fa-plus me-2"></i>Nuevo Item
    </button>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" class="form-control" id="searchInventory" placeholder="Buscar items..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <select class="form-control" id="categoryFilter">
            <option value="">Todas las categorías</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="statusFilter">
            <option value="">Todos los estados</option>
            <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>Stock Normal</option>
            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stock Bajo</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-secondary w-100" onclick="window.location.href='{{ route('inventory.index') }}'">
            <i class="fas fa-times me-2"></i>Limpiar
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Unidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->code }}</td>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->category }}</td>
                    <td class="{{ $item->current_stock <= $item->min_stock ? 'text-danger fw-bold' : '' }}">
                        {{ $item->current_stock }}
                    </td>
                    <td>{{ $item->min_stock }}</td>
                    <td>{{ $item->unit }}</td>
                    <td><span class="badge bg-{{ $item->status_color }}">{{ $item->status_label }}</span></td>
                    <td>
                        {{-- <button class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">
                            <i class="fas fa-trash"></i>
                        </button> --}}


                        {{-- <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form> --}}

                        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $items->links() }}

<!-- Modal Agregar Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código *</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría *</label>
                        <select class="form-control" name="category" required>
                            <option value="">Seleccionar</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control" name="min_stock" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unidad *</label>
                                <select class="form-control" name="unit" required>
                                    <option value="">Seleccionar</option>
                                    @foreach($units as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Unitario</label>
                        <input type="number" class="form-control" name="unit_price" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <select class="form-control" name="default_supplier_id">
                            <option value="">Seleccionar</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// function deleteItem(id) {
//     if (confirm('¿Está seguro de eliminar este item?')) {
//         fetch(`/inventory/${id}`, {
//             method: 'DELETE',
//             headers: {
//                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
//                 'Accept': 'application/json'
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 showAlert('success', data.message);
//                 setTimeout(() => location.reload(), 1500);
//             } else {
//                 showAlert('danger', data.message);
//             }
//         })
//         .catch(error => {
//             showAlert('danger', 'Error al eliminar el item');
//             console.error('Error:', error);
//         });
//     }
// }

// Filtros
document.getElementById('searchInventory').addEventListener('input', applyFilters);
document.getElementById('categoryFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInventory').value;
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (category) params.append('category', category);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("inventory.index") }}?' + params.toString();
}
</script>
@endsection