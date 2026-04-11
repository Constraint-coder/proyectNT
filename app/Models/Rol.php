<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;    

class Rol extends Model
{
    //crear model
protected $fillable = ['id', 'nombre', 'estado'];
public $timestamps = true;

function users()
{
    return $this->hasMany(User::class);
}
}