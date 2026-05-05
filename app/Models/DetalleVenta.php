<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $fillable = ['nombreProducto', 'cantidad', 'precioUnitario', 'subtotal', 'ventaId', 'productoId','loteId'];

    // Un detalle pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ventaId');
    }

    // Un detalle pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productoId');
    }
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'loteId');
    }
}