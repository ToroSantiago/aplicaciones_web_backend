<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfumeVariante extends Model
{
    protected $table = 'perfume_variantes';
    
    protected $fillable = [
        'perfume_id',
        'volumen',
        'precio',
        'stock'
    ];

    protected $casts = [
        'volumen' => 'integer',
        'precio' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Relación con el perfume
     */
    public function perfume()
    {
        return $this->belongsTo(Perfume::class);
    }
}