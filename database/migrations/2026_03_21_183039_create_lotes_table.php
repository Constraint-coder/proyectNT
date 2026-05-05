<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fechaIngreso');
            $table->string('numeroLote');
            $table->decimal('precioCompra', 10, 2);
            $table->decimal('precioVenta', 10, 2);
            $table->integer('cantidadDisponible');
            $table->integer('cantidadInicial');
            $table->boolean('estado')->default(1); 
            $table->unsignedBigInteger('productoId');
            $table->foreign('productoId')->references('id')->on('productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
