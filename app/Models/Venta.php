<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['fecha', 'total', 'estado', 'userId'];

    // Una venta pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Una venta tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'ventaId');
    }
}
