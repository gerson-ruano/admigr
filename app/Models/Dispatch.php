<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'seller_id',
        'total_amount',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
        //'items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }

            /*if (empty($order->user_id)) {
                $order->user_id = auth()->id(); // Asignar el ID del usuario autenticado
            }*/
        });

        static::saved(function ($order) {
            $order->updateTotalAmount();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    public function updateTotalAmount()
    {
        $total = $this->items()->sum('subtotal');

        if ($this->total_amount !== $total) {
            $this->forceFill(['total_amount' => $total])->saveQuietly();
            //$this->total_amount = $total;
            //$this->saveQuietly();
        }
    }

    // Método para generar automáticamente un número de orden
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getAvailableStatuses(): array
    {
        return match ($this->status) {
            'pending' => [
                'pending' => 'Pendiente',
                'processing' => 'Procesando',
                'completed' => 'Completado',
                'cancelled' => 'Cancelado',
            ],
            'processing' => [
                'processing' => 'Procesando',
                'completed' => 'Completado',
                'cancelled' => 'Cancelado',
            ],
            'completed', 'cancelled' => [
                $this->status => ucfirst($this->status),
            ],
            default => [
                'pending' => 'Pendiente',
                'processing' => 'Procesando',
                'completed' => 'Completado',
                'cancelled' => 'Cancelado',
            ]
        };
    }

}
