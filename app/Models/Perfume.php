<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfume extends Model
{
    protected $fillable = [
        'nombre', 
        'marca', 
        'descripcion', 
        'genero', 
        'imagen_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Relación con las variantes
     */
    public function variantes()
    {
        return $this->hasMany(PerfumeVariante::class);
    }

    /**
     * Accesor para obtener el precio mínimo de las variantes
     */
    public function getPrecioMinimoAttribute()
    {
        return $this->variantes()->min('precio') ?? 0;
    }

    /**
     * Accesor para obtener el precio máximo de las variantes
     */
    public function getPrecioMaximoAttribute()
    {
        return $this->variantes()->max('precio') ?? 0;
    }

    /**
     * Accesor para verificar si hay stock disponible en alguna variante
     */
    public function getHayStockAttribute()
    {
        return $this->variantes()->where('stock', '>', 0)->exists();
    }

    /**
     * Accesor para obtener el stock total de todas las variantes
     */
    public function getStockTotalAttribute()
    {
        return $this->variantes()->sum('stock');
    }
}