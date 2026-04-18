<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;    
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Model
{
     use HasFactory;
    protected $table = 'rols';
    //crear model
protected $fillable = ['id', 'nombre', 'estado'];
public $timestamps = true;

function users()
{
    return $this->hasMany(User::class);
}
}