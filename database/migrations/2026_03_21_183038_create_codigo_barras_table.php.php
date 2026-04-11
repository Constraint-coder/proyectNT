<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codigo_barras', function (Blueprint $table) {
            $table->id();
            $table->string('codigoBarra', 191)->unique();
            $table->foreignId('productoId')->constrained('productos')->cascadeOnUpdate();
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('codigo_barras');
    }
};