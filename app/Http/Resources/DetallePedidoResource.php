<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetallePedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cantidad = $this->pivot->cantidad;
        $precio   = (float) $this->pivot->precio_unitario;

        return [
            'pedido_id'       => $this->pivot->pedido_id,
            'producto_id'     => $this->id,
            'nombre'          => $this->nombre,
            'cantidad'        => $cantidad,
            'precio_unitario' => $precio,
            'subtotal'        => round($cantidad * $precio, 2),
        ];
    }
}