<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ruc',
        'contact',
        'phone',
        'email',
        'status',
        'address'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relaciones
    public function items()
    {
        return $this->hasMany(Item::class, 'default_supplier_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('ruc', 'like', "%{$search}%")
              ->orWhere('contact', 'like', "%{$search}%");
        });
    }
}