@extends('layouts.app')

@section('title', 'Salidas de Mercadería')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-arrow-up me-3"></i>Salidas de Mercadería</h2>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addOutgoingModal">
        <i class="fas fa-plus me-2"></i>Nueva Salida
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Item</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Referencia</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $movement->item->name }}</strong><br><small>{{ $movement->item->code }}</small></td>
                    <td>{{ $movement->quantity }} {{ $movement->item->unit }}</td>
                    <td><span class="badge bg-info">{{ $movement->reason_description }}</span></td>
                    <td>{{ $movement->reference ?: '-' }}</td>
                    <td>{{ $movement->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $movements->links() }}

<!-- Modal Nueva Salida -->
<div class="modal fade" id="addOutgoingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Nueva Salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('movements.store') }}" method="POST" id="outgoingForm">
                @csrf
                <input type="hidden" name="type" value="outgoing">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item *</label>
                        <select class="form-control" name="item_id" id="itemSelect" required onchange="updateStock()">
                            <option value="">Seleccionar item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-stock="{{ $item->current_stock }}" data-unit="{{ $item->unit }}">
                                    {{ $item->name }} (Stock: {{ $item->current_stock }} {{ $item->unit }})
                                </option>
                            @endforeach
                        </select>
                        <small id="stockInfo" class="text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad *</label>
                        <input type="number" class="form-control" name="quantity" id="quantityInput" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo *</label>
                        <select class="form-control" name="reason" required>
                            <option value="">Seleccionar motivo</option>
                            <option value="event">Uso en Evento</option>
                            <option value="production">Producción</option>
                            <option value="waste">Merma</option>
                            <option value="expiry">Vencimiento</option>
                            <option value="adjustment">Ajuste</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia (Evento/Solicitud)</label>
                        <input type="text" class="form-control" name="reference" placeholder="Ej: Matrimonio González">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Registrar Salida</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStock() {
    const select = document.getElementById('itemSelect');
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stockInfo');
    
    if (option.value) {
        const stock = option.dataset.stock;
        const unit = option.dataset.unit;
        stockInfo.textContent = `Stock disponible: ${stock} ${unit}`;
        document.getElementById('quantityInput').max = stock;
    }
}

document.getElementById('outgoingForm').addEventListener('submit', function(e) {
    const select = document.getElementById('itemSelect');
    const quantity = parseFloat(document.getElementById('quantityInput').value);
    const stock = parseFloat(select.options[select.selectedIndex].dataset.stock);
    
    if (quantity > stock) {
        e.preventDefault();
        showAlert('danger', 'La cantidad excede el stock disponible');
    }
});
</script>
@endsection