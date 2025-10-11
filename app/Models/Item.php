<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'current_stock',
        'min_stock',
        'unit',
        'status',
        'unit_price',
        'default_supplier_id',
        'description'
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    // Boot method para observers
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->updateStatus();
        });
    }

    // Relaciones
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    // Métodos de negocio
    public function updateStatus()
    {
        if ($this->current_stock <= 0) {
            $this->status = 'expired';
        } elseif ($this->current_stock <= $this->min_stock) {
            $this->status = 'low';
        } else {
            $this->status = 'normal';
        }
    }

    public function addStock($quantity, $userId, $supplierId = null, $unitPrice = null, $reference = null, $batchNumber = null, $expiryDate = null)
    {
        $this->current_stock += $quantity;
        $this->save();

        return $this->movements()->create([
            'type' => 'incoming',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'supplier_id' => $supplierId,
            'reason' => 'purchase',
            'reference' => $reference,
            'batch_number' => $batchNumber,
            'expiry_date' => $expiryDate,
            'user_id' => $userId,
        ]);
    }

    public function removeStock($quantity, $userId, $reason = 'event', $reference = null)
    {
        if ($this->current_stock < $quantity) {
            throw new \Exception('Stock insuficiente');
        }

        $this->current_stock -= $quantity;
        $this->save();

        return $this->movements()->create([
            'type' => 'outgoing',
            'quantity' => $quantity,
            'reason' => $reason,
            'reference' => $reference,
            'user_id' => $userId,
        ]);
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_stock');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'normal' => 'Normal',
            'low' => 'Stock Bajo',
            'expired' => 'Por Vencer',
            default => 'Desconocido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'normal' => 'success',
            'low' => 'warning',
            'expired' => 'danger',
            default => 'secondary'
        };
    }

    public function getTotalValueAttribute()
    {
        return $this->current_stock * $this->unit_price;
    }
}