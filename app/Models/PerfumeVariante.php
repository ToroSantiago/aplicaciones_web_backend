<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Descuentos asignados a esta variante (puede haber varios; vigentes o no).
     */
    public function descuentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Descuento::class,
            'descuento_perfume_variante',
            'perfume_variante_id',
            'descuento_id'
        );
    }

    /**
     * Devuelve el descuento vigente que más conviene al cliente (mayor %).
     * Null si no hay ninguno vigente.
     *
     * Si la relación `descuentos` está eager-loaded, filtra en memoria para
     * evitar N+1; si no, va a la DB.
     */
    public function descuentoVigente(): ?Descuento
    {
        if ($this->relationLoaded('descuentos')) {
            return $this->descuentos
                ->filter(fn (Descuento $d) => $d->esta_vigente)
                ->sortByDesc('porcentaje')
                ->first();
        }

        return $this->descuentos()
            ->vigentes()
            ->orderByDesc('porcentaje')
            ->first();
    }

    /**
     * Accesor: indica si la variante tiene un descuento vigente.
     */
    public function getTieneDescuentoAttribute(): bool
    {
        return $this->descuentoVigente() !== null;
    }

    /**
     * Accesor: porcentaje del descuento vigente, o 0 si no hay.
     */
    public function getDescuentoPorcentajeAttribute(): float
    {
        return (float) ($this->descuentoVigente()?->porcentaje ?? 0);
    }

    /**
     * Accesor: precio efectivo después de aplicar el mejor descuento vigente.
     * Redondea a 2 decimales para evitar arrastre de fracciones.
     */
    public function getPrecioFinalAttribute(): float
    {
        $descuento = $this->descuentoVigente();
        if (! $descuento) {
            return (float) $this->precio;
        }

        $precio = (float) $this->precio;
        $final  = $precio * (1 - ((float) $descuento->porcentaje / 100));

        return round($final, 2);
    }
}
