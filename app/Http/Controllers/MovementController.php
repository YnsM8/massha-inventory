<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $query = Movement::with(['item', 'supplier', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $movements = $query->orderBy('movement_date', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $items = Item::orderBy('name')->get(['id', 'name', 'code']);
        $suppliers = Supplier::active()->orderBy('name')->get(['id', 'name']);

        return view('movements.index', compact('movements', 'items', 'suppliers'));
    }

    public function incoming(Request $request)
    {
        $movements = Movement::with(['item', 'supplier', 'user'])
            ->incoming()
            ->orderBy('movement_date', 'desc')
            ->paginate(15);

        $items = Item::orderBy('name')->get(['id', 'name', 'code']);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('movements.incoming', compact('movements', 'items', 'suppliers'));
    }

    public function outgoing(Request $request)
    {
        $movements = Movement::with(['item', 'user'])
            ->outgoing()
            ->orderBy('movement_date', 'desc')
            ->paginate(15);

        $items = Item::orderBy('name')->get(['id', 'name', 'code', 'current_stock', 'unit']);

        return view('movements.outgoing', compact('movements', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:incoming,outgoing',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reason' => 'nullable|in:purchase,event,production,waste,expiry,adjustment,return,transfer',
            'reference' => 'nullable|string|max:100',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($request->item_id);
            
            // Validaciones específicas
            if ($request->type === 'incoming' && !$request->supplier_id) {
                return back()->withErrors(['supplier_id' => 'El proveedor es obligatorio para ingresos.'])->withInput();
            }

            if ($request->type === 'outgoing' && $item->current_stock < $request->quantity) {
                return back()->withErrors(['quantity' => "Stock insuficiente. Stock actual: {$item->current_stock} {$item->unit}"])->withInput();
            }
            
            if ($request->type === 'incoming') {
                $item->addStock(
                    $request->quantity,
                    auth()->id(),
                    $request->supplier_id,
                    $request->unit_price,
                    $request->reference,
                    $request->batch_number,
                    $request->expiry_date
                );
            } else {
                $item->removeStock(
                    $request->quantity,
                    auth()->id(),
                    $request->reason,
                    $request->reference
                );
            }

            DB::commit();

            $route = $request->type === 'incoming' ? 'movements.incoming' : 'movements.outgoing';
            return redirect()->route($route)->with('success', 'Movimiento registrado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar el movimiento: ' . $e->getMessage());
        }
    }

    public function show(Movement $movement)
    {
        $movement->load(['item', 'supplier', 'user']);
        return view('movements.show', compact('movement'));
    }

    public function destroy(Movement $movement)
    {
        try {
            DB::beginTransaction();

            // Revertir el movimiento
            if ($movement->type === 'incoming') {
                $movement->item->current_stock -= $movement->quantity;
            } else {
                $movement->item->current_stock += $movement->quantity;
            }
            
            $movement->item->updateStatus();
            $movement->item->save();
            $movement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento eliminado y stock revertido exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el movimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}