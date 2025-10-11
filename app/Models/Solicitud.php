<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'codigo_solicitud',
        'evento',
        'fecha_evento',
        'estado',
        'observaciones',
        'user_id',
        'aprobado_por',
        'fecha_aprobacion'
    ];

    protected $casts = [
        'fecha_evento' => 'date',
        'fecha_aprobacion' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function items()
    {
        return $this->hasMany(SolicitudItem::class);
    }

    // Métodos
    public function aprobar($userId)
    {
        $this->estado = 'aprobada';
        $this->aprobado_por = $userId;
        $this->fecha_aprobacion = now();
        $this->save();

        // Descontar stock
        foreach ($this->items as $solicitudItem) {
            $item = $solicitudItem->item;
            $item->removeStock(
                $solicitudItem->cantidad_solicitada,
                $userId,
                'production',
                "Solicitud: {$this->codigo_solicitud} - {$this->evento}"
            );
        }
    }

    public function rechazar()
    {
        $this->estado = 'rechazada';
        $this->save();
    }

    // Accessors
    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            default => 'Desconocido'
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'aprobada' => 'success',
            'rechazada' => 'danger',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }
}