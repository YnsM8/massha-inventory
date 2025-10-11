<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('supplier');

        // Filtros
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $items = $query->paginate(15)->appends($request->query());

        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = ['Carnes', 'Verduras', 'Lácteos', 'Especias', 'Bebidas', 'Otros'];
        $units = ['kg' => 'Kilogramos', 'g' => 'Gramos', 'l' => 'Litros', 'ml' => 'Mililitros', 'unid' => 'Unidades', 'paq' => 'Paquetes'];

        return view('inventory.index', compact('items', 'suppliers', 'categories', 'units'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = ['Carnes', 'Verduras', 'Lácteos', 'Especias', 'Bebidas', 'Otros'];
        $units = ['kg', 'g', 'l', 'ml', 'unid', 'paq'];

        return view('inventory.create', compact('suppliers', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:100',
            'category' => 'required|in:Carnes,Verduras,Lácteos,Especias,Bebidas,Otros',
            'min_stock' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,g,l,ml,unid,paq',
            'unit_price' => 'nullable|numeric|min:0',
            'default_supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $item = Item::create($validated);
            return redirect()->route('inventory.index')->with('success', 'Item creado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el item: ' . $e->getMessage());
        }
    }

    public function show(Item $item)
    {
        $item->load(['supplier', 'movements.user']);
        
        $recentMovements = $item->movements()
            ->with(['supplier', 'user'])
            ->orderBy('movement_date', 'desc')
            ->limit(20)
            ->get();

        $stats = [
            'total_incoming' => $item->movements()->incoming()->sum('quantity'),
            'total_outgoing' => $item->movements()->outgoing()->sum('quantity'),
            'movements_count' => $item->movements()->count(),
            'days_since_last_movement' => $item->movements()->latest('movement_date')->first()
                ? $item->movements()->latest('movement_date')->first()->movement_date->diffInDays(now())
                : 0,
        ];

        return view('inventory.show', compact('item', 'recentMovements', 'stats'));
    }

    public function edit(Item $item)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = ['Carnes', 'Verduras', 'Lácteos', 'Especias', 'Bebidas', 'Otros'];
        $units = ['kg', 'g', 'l', 'ml', 'unid', 'paq'];

        return view('inventory.edit', compact('item', 'suppliers', 'categories', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,  // <-- DEBE TENER ESTO
            'name' => 'required|string|max:100',
            'category' => 'required|in:Carnes,Verduras,Lácteos,Especias,Bebidas,Otros',
            'min_stock' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,g,l,ml,unid,paq',
            'unit_price' => 'nullable|numeric|min:0',
            'default_supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $item->update($validated);
            return redirect()->route('inventory.index')->with('success', 'Item actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el item: ' . $e->getMessage());
        }
    }

    public function destroy(Item $item)
    {

        try {
        if ($item->movements()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un item que tiene movimientos registrados');
        }

        $item->delete();
        
        return redirect()->route('inventory.index')->with('success', 'Item eliminado exitosamente');
        
        } catch (\Exception $e) {
        return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }

        //     Log::info('=== DESTROY EJECUTADO ===', [
        //     'item_id' => $item->id,
        //     'name' => $item->name,
        //     'method' => request()->method()
        // ]);
        
        // try {
        //     $movementsCount = $item->movements()->count();
        //     Log::info('Movimientos encontrados', ['count' => $movementsCount]);
            
        //     if ($movementsCount > 0) {
        //         Log::warning('ITEM TIENE MOVIMIENTOS - NO SE BORRA');
        //         return back()->with('error', 'No se puede eliminar: tiene ' . $movementsCount . ' movimientos');
        //     }

        //     Log::info('EJECUTANDO DELETE...');
        //     $item->delete();
        //     Log::info('DELETE EJECUTADO EXITOSAMENTE');
            
        //     return redirect()->route('inventory.index')->with('success', 'Item eliminado');
            
        // } catch (\Exception $e) {
        //     Log::error('ERROR AL BORRAR', ['error' => $e->getMessage()]);
        //     return back()->with('error', 'Error: ' . $e->getMessage());
        // }

    //     try {
    //     if ($item->movements()->count() > 0) {
    //         return back()->with('error', 'No se puede eliminar un item que tiene movimientos registrados');
    //     }

    //     $item->delete();
    //     return redirect()->route('inventory.index')->with('success', 'Item eliminado exitosamente');

    // } catch (\Exception $e) {
    //     return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
    // }


        // try {
        //     if ($item->movements()->count() > 0) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'No se puede eliminar un item que tiene movimientos registrados'
        //         ], 400);
        //     }

        //     $item->delete();

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Item eliminado exitosamente'
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Error al eliminar el item: ' . $e->getMessage()
        //     ], 500);
        // }
    }
}