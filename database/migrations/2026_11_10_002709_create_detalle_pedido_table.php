<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_pedido', function (Blueprint $table) {
            $table->foreignId('pedido_id')
                  ->constrained('pedidos')
                  ->cascadeOnDelete();
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->restrictOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);

            // Clave primaria compuesta: un producto solo una vez por pedido
            $table->primary(['pedido_id', 'producto_id']);
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido');
    }
};