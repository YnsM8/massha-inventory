@extends('layouts.app')

@section('title', 'Ingresos de Mercadería')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-arrow-down me-3"></i>Ingresos de Mercadería</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addIncomingModal">
        <i class="fas fa-plus me-2"></i>Nuevo Ingreso
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Item</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Proveedor</th>
                <th>Lote</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $movement->item->name }}</strong><br><small>{{ $movement->item->code }}</small></td>
                    <td>{{ $movement->quantity }} {{ $movement->item->unit }}</td>
                    <td>S/. {{ number_format($movement->unit_price, 2) }}</td>
                    <td>{{ $movement->supplier ? $movement->supplier->name : '-' }}</td>
                    <td>{{ $movement->batch_number ?: '-' }}</td>
                    <td>{{ $movement->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $movements->links() }}

<!-- Modal Nuevo Ingreso -->
<div class="modal fade" id="addIncomingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Ingreso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('movements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="incoming">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item *</label>
                        <select class="form-control" name="item_id" required>
                            <option value="">Seleccionar item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" name="quantity" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Precio Unitario</label>
                                <input type="number" class="form-control" name="unit_price" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proveedor *</label>
                        <select class="form-control" name="supplier_id" required>
                            <option value="">Seleccionar proveedor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número de Lote</label>
                                <input type="text" class="form-control" name="batch_number" placeholder="L2024001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Vencimiento</label>
                                <input type="date" class="form-control" name="expiry_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia (OC)</label>
                        <input type="text" class="form-control" name="reference" placeholder="OC-2024-001">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection