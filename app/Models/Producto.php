<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = ['nombre', 'precio'];

    protected $casts = ['precio' => 'decimal:2'];

    public function pedidos(): BelongsToMany
    {
        return $this->belongsToMany(Pedido::class, 'detalle_pedido', 'producto_id', 'pedido_id')
                    ->withPivot('cantidad', 'precio_unitario');
    }
}