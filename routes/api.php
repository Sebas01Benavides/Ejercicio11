<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('pedidos', PedidoController::class);

// Consultar los productos de un pedido desde PedidoController
Route::get('pedidos/{pedido}/productos', [PedidoController::class, 'productos'])
    ->name('pedidos.productos');

// Rutas anidadas: detalle del pedido (identifica pedido Y producto)
Route::apiResource('pedidos.detalles', DetallePedidoController::class)
    ->parameters(['detalles' => 'producto']);