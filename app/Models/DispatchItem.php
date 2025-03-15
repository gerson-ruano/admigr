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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!isset($item->subtotal) && isset($item->quantity, $item->unit_price)) {
                $item->subtotal = $item->quantity * $item->unit_price;
            }
        });

        static::updating(function ($item) {
            if ($item->isDirty('quantity') || $item->isDirty('unit_price')) {
                $item->subtotal = $item->quantity * $item->unit_price;
            }
        });

        static::saved(function ($item) {
            if ($item->dispatch) { // Verifica que la relación no sea null
                $item->dispatch->updateTotalAmount();
            }
        });

        static::deleted(function ($item) {
            if ($item->dispatch) { // Verifica que la relación no sea null
                $item->dispatch->updateTotalAmount();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
