<?php


namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::with(['user', 'items.item']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        return view('solicitudes.create', compact('items'));
    }

    public function store(Request $request)
    {
            $validated = $request->validate([
            'evento' => 'required|string|max:200',
            'fecha_evento' => 'required|date|after:today',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Generar código único
            $codigoSolicitud = 'SOL-' . date('Ymd') . '-' . str_pad(Solicitud::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            // Crear solicitud
            $userId = auth()->id();
            $solicitud = Solicitud::create([
                'codigo_solicitud' => $codigoSolicitud,
                'evento' => $request->evento,
                'fecha_evento' => $request->fecha_evento,
                'observaciones' => $request->observaciones,
                'user_id' => $userId,
                'estado' => 'pendiente',
            ]);

            // Agregar items y verificar stock
            $stockInsuficiente = false;
            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['item_id']);
                $cantidadSolicitada = $itemData['cantidad'];
                $stockDisponible = $item->current_stock;
                $suficiente = $stockDisponible >= $cantidadSolicitada;

                if (!$suficiente) {
                    $stockInsuficiente = true;
                }

                SolicitudItem::create([
                    'solicitud_id' => $solicitud->id,
                    'item_id' => $item->id,
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'cantidad_disponible' => $stockDisponible,
                    'stock_suficiente' => $suficiente,
                ]);
            }

            DB::commit();

            if ($stockInsuficiente) {
                return redirect()->route('solicitudes.show', $solicitud->id)  
                    ->with('warning', 'Solicitud creada, pero algunos items tienen stock insuficiente');
            }

            return redirect()->route('solicitudes.index')
                ->with('success', "Solicitud {$codigoSolicitud} creada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear solicitud: ' . $e->getMessage());
        }
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['items.item', 'user', 'aprobador']);
        return view('solicitudes.show', compact('solicitud'));
    }

    public function aprobar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes');
        }

        try {
            DB::beginTransaction();

            // Verificar stock actual antes de aprobar
            foreach ($solicitud->items as $solicitudItem) {
                if ($solicitudItem->item->current_stock < $solicitudItem->cantidad_solicitada) {
                    DB::rollBack();
                    return back()->with('error', "Stock insuficiente para {$solicitudItem->item->name}");
                }
            }

            $userId = auth()->id();
            $solicitud->aprobar($userId);

            DB::commit();

            return redirect()->route('solicitudes.index')
                ->with('success', 'Solicitud aprobada y stock descontado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    public function rechazar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes');
        }

        $solicitud->rechazar();

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud rechazada');
    }
}