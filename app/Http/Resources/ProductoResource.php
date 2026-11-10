<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,
            'precio' => (float) $this->precio,
            // Solo aparece cuando el producto viene desde la relación con un pedido
            'detalle' => $this->whenPivotLoaded('detalle_pedido', fn () => [
                'cantidad'        => $this->pivot->cantidad,
                'precio_unitario' => (float) $this->pivot->precio_unitario,
            ]),
        ];
    }
}