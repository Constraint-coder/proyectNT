<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'estado'];

    public function codigoBarras()
    {
        return $this->hasMany(CodigoBarra::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}