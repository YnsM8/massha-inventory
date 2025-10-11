<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Movement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function inventory(Request $request)
    {
        $query = Item::with('supplier');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $items = $query->orderBy('name')->get();

        $totals = [
            'total_items' => $items->count(),
            'total_value' => $items->sum('total_value'),
            'low_stock_count' => $items->where('status', 'low')->count(),
            'expired_count' => $items->where('status', 'expired')->count(),
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportInventoryCSV($items, $totals);
        }

        return view('reports.inventory', compact('items', 'totals'));
    }

    public function movements(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $movements = Movement::with(['item', 'supplier', 'user'])
            ->byDateRange($startDate, $endDate)
            ->orderBy('movement_date', 'desc')
            ->get();

        $stats = [
            'total_movements' => $movements->count(),
            'total_incoming' => $movements->where('type', 'incoming')->sum('quantity'),
            'total_outgoing' => $movements->where('type', 'outgoing')->sum('quantity'),
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportMovementsCSV($movements, $stats, $startDate, $endDate);
        }

        return view('reports.movements', compact('movements', 'stats', 'startDate', 'endDate'));
    }

    public function criticalItems(Request $request)
    {
        $lowStockItems = Item::with('supplier')
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->get();

        $inactiveItems = Item::whereDoesntHave('movements', function($query) {
            $query->where('movement_date', '>=', now()->subDays(60));
        })->orderBy('name')->get();

        if ($request->get('export') === 'csv') {
            return $this->exportCriticalItemsCSV($lowStockItems, $inactiveItems);
        }

        return view('reports.critical-items', compact('lowStockItems', 'inactiveItems'));
    }

    private function exportInventoryCSV($items, $totals)
    {
        $filename = 'inventario_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($items, $totals) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['Código', 'Nombre', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Unidad', 'Estado', 'Precio Unitario', 'Valor Total', 'Proveedor']);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->code,
                    $item->name,
                    $item->category,
                    $item->current_stock,
                    $item->min_stock,
                    $item->unit,
                    $item->status_label,
                    number_format($item->unit_price, 2),
                    number_format($item->total_value, 2),
                    $item->supplier ? $item->supplier->name : 'Sin proveedor'
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['RESUMEN']);
            fputcsv($handle, ['Total Items:', $totals['total_items']]);
            fputcsv($handle, ['Valor Total Inventario:', number_format($totals['total_value'], 2)]);

            fclose($handle);
        }, 200, $headers);
    }

    private function exportMovementsCSV($movements, $stats, $startDate, $endDate)
    {
        $filename = 'movimientos_' . $startDate . '_to_' . $endDate . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($movements, $stats) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['Fecha', 'Tipo', 'Item', 'Cantidad', 'Proveedor', 'Motivo', 'Referencia', 'Usuario']);

            foreach ($movements as $movement) {
                fputcsv($handle, [
                    $movement->movement_date->format('Y-m-d H:i'),
                    $movement->type_description,
                    $movement->item->name,
                    $movement->quantity,
                    $movement->supplier ? $movement->supplier->name : '-',
                    $movement->reason_description,
                    $movement->reference ?? '-',
                    $movement->user->name
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function exportCriticalItemsCSV($lowStockItems, $inactiveItems)
    {
        $filename = 'items_criticos_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($lowStockItems, $inactiveItems) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['ITEMS CON STOCK BAJO']);
            fputcsv($handle, ['Código', 'Nombre', 'Stock Actual', 'Stock Mínimo', 'Diferencia']);
            
            foreach ($lowStockItems as $item) {
                fputcsv($handle, [
                    $item->code,
                    $item->name,
                    $item->current_stock,
                    $item->min_stock,
                    $item->current_stock - $item->min_stock
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}