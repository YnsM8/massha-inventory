<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',
        'message',
        'is_read',
        'alert_date'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'alert_date' => 'datetime',
    ];

    // Relaciones
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}