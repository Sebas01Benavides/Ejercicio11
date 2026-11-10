<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    // GET /api/clientes  (opcional: ?con_pedidos=1)
    public function index(Request $request)
    {
        $query = Cliente::orderBy('id');

        if ($request->boolean('con_pedidos')) {
            $query->with('pedidos');
        }

        return ClienteResource::collection($query->get());
    }

    // POST /api/clientes -> 201
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'correo'   => ['required', 'email', 'max:150', 'unique:clientes,correo'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ]);

        return new ClienteResource(Cliente::create($data)); // 201 automático
    }

    // GET /api/clientes/{cliente}
    public function show(Cliente $cliente)
    {
        return new ClienteResource($cliente->load('pedidos'));
    }

    // PUT|PATCH /api/clientes/{cliente}
    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'   => ['sometimes', 'required', 'string', 'max:100'],
            'apellido' => ['sometimes', 'required', 'string', 'max:100'],
            'correo'   => ['sometimes', 'required', 'email', 'max:150',
                           Rule::unique('clientes', 'correo')->ignore($cliente->id)],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ]);

        $cliente->update($data);

        return new ClienteResource($cliente);
    }

    // DELETE /api/clientes/{cliente}
    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente.'], 200);
    }
}