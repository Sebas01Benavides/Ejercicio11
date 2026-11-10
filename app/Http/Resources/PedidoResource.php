<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'fecha_pedido' => $this->fecha_pedido?->format('Y-m-d'),
            'estado'       => $this->estado,
            'total'        => (float) $this->total,
            'cliente'      => new ClienteResource($this->whenLoaded('cliente')),
            'productos'    => DetallePedidoResource::collection($this->whenLoaded('productos')),
        ];
    }
}