<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfume extends Model
{
    protected $fillable = [
        'nombre', 
        'marca', 
        'descripcion', 
        'volumen', 
        'precio', 
        'genero', 
        'stock',
        'imagen_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stock' => 'integer',
        'precio' => 'integer',
        'volumen' => 'integer',
    ];
}