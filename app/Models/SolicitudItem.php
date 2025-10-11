<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudItem extends Model
{
    use HasFactory;

    protected $table = 'solicitud_items';

    protected $fillable = [
        'solicitud_id',
        'item_id',
        'cantidad_solicitada',
        'cantidad_disponible',
        'stock_suficiente'
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:2',
        'cantidad_disponible' => 'decimal:2',
        'stock_suficiente' => 'boolean',
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}