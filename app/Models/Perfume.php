<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfume extends Model
{
    protected $fillable = ['nombre', 'marca', 'descripcion', 'volumen', 'precio', 'genero', 'stock'];
}
