<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\DetalleVenta;

class Lote extends Model
{
  protected $table = 'lotes';

    protected $fillable = [
        'fechaIngreso',
        'numeroLote',
        'precioCompra',
        'precioVenta',
        'cantidadDisponible',
        'cantidadInicial',
        'estado',
        'productoId',
    ];

    // Relación: Lote pertenece a Producto
    public function productos()
    {
        return $this->belongsTo(Producto::class, 'productoId');
    }
     public function detalleventas(){
        return $this->hasMany(DetalleVenta::class);
    }


}
