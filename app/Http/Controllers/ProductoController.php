<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return ProductoResource::collection(Producto::orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        return new ProductoResource(Producto::create($data)); // 201
    }

    public function show(Producto $producto)
    {
        return new ProductoResource($producto);
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $producto->update($data);

        return new ProductoResource($producto);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        if ($producto->pedidos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: el producto está incluido en pedidos.',
            ], 409);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente.'], 200);
    }
}