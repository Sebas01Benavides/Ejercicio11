<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetallePedidoResource;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DetallePedidoController extends Controller
{
    // GET /api/pedidos/{pedido}/detalles
    public function index(Pedido $pedido)
    {
        return DetallePedidoResource::collection($pedido->productos()->get());
    }

    // POST /api/pedidos/{pedido}/detalles -> 201
    public function store(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'producto_id' => [
                'required', 'integer', 'exists:productos,id',
                // combinación pedido-producto no duplicada
                Rule::unique('detalle_pedido', 'producto_id')->where('pedido_id', $pedido->id),
            ],
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['sometimes', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        // Si no se envía precio_unitario, se toma el precio actual del producto
        $precio = $data['precio_unitario'] ?? Producto::findOrFail($data['producto_id'])->precio;

        $pedido->productos()->attach($data['producto_id'], [
            'cantidad'        => $data['cantidad'],
            'precio_unitario' => $precio,
        ]);

        $pedido->recalcularTotal();

        return (new DetallePedidoResource($this->detalleDe($pedido, $data['producto_id'])))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/pedidos/{pedido}/detalles/{producto}
    public function show(Pedido $pedido, Producto $producto)
    {
        return new DetallePedidoResource($this->detalleDe($pedido, $producto->id));
    }

    // PUT|PATCH /api/pedidos/{pedido}/detalles/{producto}
    public function update(Request $request, Pedido $pedido, Producto $producto)
    {
        $this->detalleDe($pedido, $producto->id); // 404 si no pertenece al pedido

        $data = $request->validate([
            'cantidad'        => ['required_without:precio_unitario', 'integer', 'min:1'],
            'precio_unitario' => ['required_without:cantidad', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $pedido->productos()->updateExistingPivot($producto->id, $data);
        $pedido->recalcularTotal();

        return new DetallePedidoResource($this->detalleDe($pedido, $producto->id));
    }

    // DELETE /api/pedidos/{pedido}/detalles/{producto}
    public function destroy(Pedido $pedido, Producto $producto): JsonResponse
    {
        $this->detalleDe($pedido, $producto->id); // 404 si no pertenece al pedido

        $pedido->productos()->detach($producto->id);
        $pedido->recalcularTotal();

        return response()->json(['message' => 'Producto retirado del pedido.'], 200);
    }

    /** Devuelve el producto con su pivot o aborta con 404 si no pertenece al pedido. */
    private function detalleDe(Pedido $pedido, int $productoId): Producto
    {
        $detalle = $pedido->productos()->where('productos.id', $productoId)->first();

        abort_if(is_null($detalle), 404, 'El producto no pertenece a este pedido.');

        return $detalle;
    }
}