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
Schema::create('roles', function (Blueprint $table) {
    $table->id();

    // requerido por Spatie
    $table->string('name');

    // requerido por Spatie
    $table->string('guard_name')->default('api');

    // opcional tuyo
    $table->boolean('estado')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rols');
    }
};
