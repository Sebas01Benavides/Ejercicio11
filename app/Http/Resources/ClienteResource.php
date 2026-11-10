<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'apellido' => $this->apellido,
            'correo'   => $this->correo,
            'telefono' => $this->telefono,
            'pedidos'  => PedidoResource::collection($this->whenLoaded('pedidos')),
        ];
    }
}