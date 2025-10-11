<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->orderBy('name')->paginate(15)->appends($request->query());

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ruc' => 'required|string|size:11|unique:suppliers,ruc',
            'contact' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        // Limpiar RUC
        $validated['ruc'] = preg_replace('/[^0-9]/', '', $validated['ruc']);

        try {
            $supplier = Supplier::create($validated);
            return redirect()->route('suppliers.index')->with('success', 'Proveedor creado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el proveedor: ' . $e->getMessage());
        }
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['items', 'movements.item']);
        
        $stats = [
            'items_supplied' => $supplier->items()->count(),
            'total_movements' => $supplier->movements()->count(),
            'last_movement' => $supplier->movements()->latest('movement_date')->first(),
        ];

        return view('suppliers.show', compact('supplier', 'stats'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ruc' => 'required|string|size:11|unique:suppliers,ruc,' . $supplier->id,
            'contact' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        // Limpiar RUC
        $validated['ruc'] = preg_replace('/[^0-9]/', '', $validated['ruc']);

        try {
            $supplier->update($validated);
            return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Supplier $supplier)
    {
        try {
            $supplier->status = $supplier->status === 'active' ? 'inactive' : 'active';
            $supplier->save();

            $message = $supplier->status === 'active' ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'message' => "Proveedor {$message} exitosamente",
                'status' => $supplier->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            if ($supplier->items()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un proveedor que tiene items asociados'
                ], 400);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
}