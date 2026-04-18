<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('total', 10, 2);
            $table->boolean('estado')->default(1);

            // FK hacia users (quien realiza la venta)
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
