<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->decimal('precioUnitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            // FK hacia ventas
            $table->bigInteger('ventaId')->unsigned();
            $table->foreign('ventaId')->references('id')->on('ventas')->onDelete('cascade');

            // FK hacia productos
            $table->bigInteger('productoId')->unsigned();
            $table->foreign('productoId')->references('id')->on('productos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Primero se elimina la tabla hija, luego la padre
        Schema::dropIfExists('detalle_ventas');
    }
};
