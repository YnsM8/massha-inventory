<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'item_id',
        'quantity',
        'unit_price',
        'supplier_id',
        'reason',
        'reference',
        'batch_number',
        'expiry_date',
        'notes',
        'user_id',
        'movement_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'expiry_date' => 'date',
        'movement_date' => 'datetime',
    ];

    // Relaciones
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('movement_date', '>=', now()->subDays($days));
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getTypeDescriptionAttribute()
    {
        return $this->type === 'incoming' ? 'Ingreso' : 'Salida';
    }

    public function getReasonDescriptionAttribute()
    {
        return match($this->reason) {
            'purchase' => 'Compra',
            'event' => 'Evento',
            'production' => 'Producción',
            'waste' => 'Merma',
            'expiry' => 'Vencimiento',
            'adjustment' => 'Ajuste',
            'return' => 'Devolución',
            'transfer' => 'Transferencia',
            default => 'Otro'
        };
    }

    public function getTotalValueAttribute()
    {
        return $this->quantity * ($this->unit_price ?? 0);
    }
}