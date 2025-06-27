<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';

    protected $fillable = [
        'venta_id',
        'perfume_variante_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    /**
     * Relación con la venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con la variante del perfume
     */
    public function perfumeVariante(): BelongsTo
    {
        return $this->belongsTo(PerfumeVariante::class);
    }

    /**
     * Relación con el perfume a través de la variante
     */
    public function perfume()
    {
        return $this->hasOneThrough(
            Perfume::class,
            PerfumeVariante::class,
            'id', // Foreign key on PerfumeVariante
            'id', // Foreign key on Perfume
            'perfume_variante_id', // Local key on VentaDetalle
            'perfume_id' // Local key on PerfumeVariante
        );
    }

    /**
     * Accesor para obtener la descripción completa del producto
     */
    public function getDescripcionCompletaAttribute(): string
    {
        $variante = $this->perfumeVariante;
        $perfume = $variante->perfume;
        
        return "{$perfume->nombre} - {$perfume->marca} ({$variante->volumen}ml)";
    }

    /**
     * Boot method para calcular el subtotal automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            if (!$detalle->subtotal) {
                $detalle->subtotal = $detalle->precio_unitario * $detalle->cantidad;
            }
        });

        static::updating(function ($detalle) {
            $detalle->subtotal = $detalle->precio_unitario * $detalle->cantidad;
        });
    }
}