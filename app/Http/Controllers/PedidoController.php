<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetallePedidoResource;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    // GET /api/pedidos
    public function index()
    {
        return PedidoResource::collection(
            Pedido::with('cliente')->orderBy('id')->get()
        );
    }

    // POST /api/pedidos -> 201
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => ['required', 'integer', 'exists:clientes,id'],
            'fecha_pedido' => ['required', 'date'],
            'estado'       => ['sometimes', Rule::in(Pedido::ESTADOS)],
            'total'        => ['sometimes', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $pedido = Pedido::create($data);

        return new PedidoResource($pedido->load('cliente'));
    }

    // GET /api/pedidos/{pedido}  (incluye cliente y productos)
    public function show(Pedido $pedido)
    {
        return new PedidoResource($pedido->load(['cliente', 'productos']));
    }

    // PUT|PATCH /api/pedidos/{pedido}
    public function update(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'cliente_id'   => ['sometimes', 'required', 'integer', 'exists:clientes,id'],
            'fecha_pedido' => ['sometimes', 'required', 'date'],
            'estado'       => ['sometimes', 'required', Rule::in(Pedido::ESTADOS)],
            'total'        => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $pedido->update($data);

        return new PedidoResource($pedido->load(['cliente', 'productos']));
    }

    // DELETE /api/pedidos/{pedido}
    public function destroy(Pedido $pedido): JsonResponse
    {
        $pedido->delete(); // el detalle se borra por cascada

        return response()->json(['message' => 'Pedido eliminado correctamente.'], 200);
    }

    // GET /api/pedidos/{pedido}/productos
    public function productos(Pedido $pedido)
    {
        return DetallePedidoResource::collection($pedido->productos()->get());
    }
}