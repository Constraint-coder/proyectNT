<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoBarra extends Model
{
    protected $fillable = ['codigoBarra', 'productoId', 'estado'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productoId');
    }
}