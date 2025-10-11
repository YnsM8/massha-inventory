<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Movement;
use App\Models\StockAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas principales
        $stats = [
            'total_items' => Item::count(),
            'low_stock_items' => Item::lowStock()->count(),
            'expired_items' => Item::where('status', 'expired')->count(),
            'total_suppliers' => Supplier::active()->count(),
            'total_movements_today' => Movement::whereDate('movement_date', today())->count(),
            'total_inventory_value' => Item::sum(DB::raw('current_stock * unit_price')),
        ];

        // Items con stock bajo
        $lowStockItems = Item::with('supplier')
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();

        // Movimientos recientes
        $recentMovements = Movement::with(['item', 'supplier', 'user'])
            ->orderBy('movement_date', 'desc')
            ->limit(10)
            ->get();

        // Alertas de stock no leídas
        $stockAlerts = StockAlert::with('item')
            ->unread()
            ->orderBy('alert_date', 'desc')
            ->limit(5)
            ->get();

        // Top 5 items más utilizados (últimos 30 días)
        $topItems = Item::select(
                'items.id',
                'items.name', 
                'items.category',
                'items.current_stock',
                'items.unit',
                DB::raw('SUM(movements.quantity) as total_used')
            )
            ->join('movements', 'items.id', '=', 'movements.item_id')
            ->where('movements.type', 'outgoing')
            ->where('movements.movement_date', '>=', now()->subDays(30))
            ->groupBy('items.id', 'items.name', 'items.category', 'items.current_stock', 'items.unit')
            ->orderBy('total_used', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'lowStockItems', 
            'recentMovements', 
            'stockAlerts', 
            'topItems'
        ));
    }

    public function getStats()
    {
        return response()->json([
            'total_items' => Item::count(),
            'low_stock_items' => Item::lowStock()->count(),
            'expired_items' => Item::where('status', 'expired')->count(),
            'total_suppliers' => Supplier::active()->count(),
        ]);
    }
}