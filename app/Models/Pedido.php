<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pedido extends Model
{
    public const ESTADOS = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];

    protected $table = 'pedidos';

    protected $fillable = ['cliente_id', 'fecha_pedido', 'estado', 'total'];

    protected $attributes = [
        'estado' => 'pendiente',
        'total'  => 0,
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'total'        => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'detalle_pedido', 'pedido_id', 'producto_id')
                    ->withPivot('cantidad', 'precio_unitario');
    }

    /** Recalcula el total sumando cantidad * precio_unitario del detalle. */
    public function recalcularTotal(): void
    {
        $total = $this->productos()->get()
            ->sum(fn ($p) => $p->pivot->cantidad * $p->pivot->precio_unitario);

        $this->update(['total' => round($total, 2)]);
    }
}