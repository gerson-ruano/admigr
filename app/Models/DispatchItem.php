<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            // Calcular el subtotal automáticamente si no está definido
            if (!isset($item->subtotal) && isset($item->quantity) && isset($item->unit_price)) {
                $item->subtotal = $item->quantity * $item->unit_price;
            }
        });
        
        static::updating(function ($item) {
            // Actualizar el subtotal si cambia la cantidad o el precio
            if ($item->isDirty('quantity') || $item->isDirty('unit_price')) {
                $item->subtotal = $item->quantity * $item->unit_price;
            }
        });
    }
}
