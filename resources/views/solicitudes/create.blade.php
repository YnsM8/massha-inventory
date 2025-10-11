@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-plus-circle"></i> Nueva Solicitud de Insumos</h2>
            <p class="text-muted">Completa el formulario para solicitar insumos para un evento</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('solicitudes.store') }}" method="POST" id="solicitudForm">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Evento</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="evento" class="form-label">Nombre del Evento <span class="text-danger">*</span></label>
                        <input type="text" 
                            class="form-control @error('evento') is-invalid @enderror" 
                            id="evento" 
                            name="evento" 
                            value="{{ old('evento') }}"
                            placeholder="Ej: Boda García, Cumpleaños Empresa, etc."
                            required>
                        @error('evento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_evento" class="form-label">Fecha del Evento <span class="text-danger">*</span></label>
                        <input type="date" 
                            class="form-control @error('fecha_evento') is-invalid @enderror" 
                            id="fecha_evento" 
                            name="fecha_evento" 
                            value="{{ old('fecha_evento') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            required>
                        @error('fecha_evento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                id="observaciones" 
                                name="observaciones" 
                                rows="3"
                                placeholder="Incluye detalles especiales, restricciones alimentarias, etc.">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Items Solicitados</h5>
                <button type="button" class="btn btn-light btn-sm" onclick="agregarItem()">
                    <i class="fas fa-plus"></i> Agregar Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <!-- Los items se agregarán aquí dinámicamente -->
                </div>
                
                <div class="alert alert-info mt-3" id="noItemsAlert">
                    <i class="fas fa-info-circle"></i> Haz clic en "Agregar Item" para comenzar a solicitar insumos
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Crear Solicitud
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
let itemCount = 0;

// Lista de items disponibles
const itemsDisponibles = @json($items);

function agregarItem() {
    itemCount++;
    
    const container = document.getElementById('itemsContainer');
    const noItemsAlert = document.getElementById('noItemsAlert');
    
    if (noItemsAlert) {
        noItemsAlert.style.display = 'none';
    }
    
    const itemRow = document.createElement('div');
    itemRow.className = 'row mb-3 item-row border-bottom pb-3';
    itemRow.id = `item-${itemCount}`;
    
    let optionsHtml = '<option value="">Seleccionar item...</option>';
    itemsDisponibles.forEach(item => {
        optionsHtml += `<option value="${item.id}" data-stock="${item.current_stock}" data-unit="${item.unit}">
            ${item.name} (Stock: ${item.current_stock} ${item.unit})
        </option>`;
    });
    
    itemRow.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Item <span class="text-danger">*</span></label>
            <select class="form-select" name="items[${itemCount}][item_id]" onchange="actualizarStockInfo(${itemCount})" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Cantidad <span class="text-danger">*</span></label>
            <input type="number" 
                class="form-control" 
                name="items[${itemCount}][cantidad]" 
                step="0.01" 
                min="0.01" 
                placeholder="0.00"
                onchange="verificarStock(${itemCount})"
                required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Stock Disponible</label>
            <input type="text" class="form-control" id="stock-info-${itemCount}" readonly disabled>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger" onclick="eliminarItem(${itemCount})" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(itemRow);
    actualizarBotonSubmit();
}

function eliminarItem(id) {
    const itemRow = document.getElementById(`item-${id}`);
    if (itemRow) {
        itemRow.remove();
    }
    
    const container = document.getElementById('itemsContainer');
    if (container.children.length === 0) {
        document.getElementById('noItemsAlert').style.display = 'block';
    }
    
    actualizarBotonSubmit();
}

function actualizarStockInfo(id) {
    const select = document.querySelector(`#item-${id} select`);
    const stockInfo = document.getElementById(`stock-info-${id}`);
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const stock = selectedOption.dataset.stock;
        const unit = selectedOption.dataset.unit;
        stockInfo.value = `${stock} ${unit}`;
    } else {
        stockInfo.value = '';
    }
}

function verificarStock(id) {
    const select = document.querySelector(`#item-${id} select`);
    const cantidadInput = document.querySelector(`#item-${id} input[type="number"]`);
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value && cantidadInput.value) {
        const stock = parseFloat(selectedOption.dataset.stock);
        const cantidad = parseFloat(cantidadInput.value);
        
        if (cantidad > stock) {
            cantidadInput.classList.add('is-invalid');
            alert(`⚠️ La cantidad solicitada (${cantidad}) supera el stock disponible (${stock}). La solicitud quedará pendiente de aprobación con stock insuficiente.`);
        } else {
            cantidadInput.classList.remove('is-invalid');
        }
    }
}

function actualizarBotonSubmit() {
    const submitBtn = document.getElementById('submitBtn');
    const container = document.getElementById('itemsContainer');
    
    if (container.children.length === 0) {
        submitBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
    }
}

// Agregar un item por defecto al cargar
document.addEventListener('DOMContentLoaded', function() {
    agregarItem();
});
</script>
@endsection