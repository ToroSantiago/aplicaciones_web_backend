<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Descuento extends Model
{
    protected $table = 'descuentos';

    protected $fillable = [
        'nombre',
        'porcentaje',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'porcentaje'   => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    /**
     * Variantes de perfumes a las que se aplica este descuento.
     */
    public function variantes(): BelongsToMany
    {
        return $this->belongsToMany(
            PerfumeVariante::class,
            'descuento_perfume_variante',
            'descuento_id',
            'perfume_variante_id'
        );
    }

    /**
     * Indica si el descuento está vigente hoy (activo + dentro del rango).
     */
    public function getEstaVigenteAttribute(): bool
    {
        if (! $this->activo) {
            return false;
        }

        $hoy = Carbon::today();
        return $hoy->betweenIncluded($this->fecha_inicio, $this->fecha_fin);
    }

    /**
     * Estado legible: vigente / futuro / expirado / inactivo.
     */
    public function getEstadoAttribute(): string
    {
        if (! $this->activo) {
            return 'inactivo';
        }

        $hoy = Carbon::today();
        if ($hoy->lt($this->fecha_inicio)) {
            return 'futuro';
        }
        if ($hoy->gt($this->fecha_fin)) {
            return 'expirado';
        }
        return 'vigente';
    }

    /**
     * Scope: solo descuentos vigentes hoy (activo + en rango).
     */
    public function scopeVigentes($query)
    {
        $hoy = Carbon::today()->toDateString();
        return $query->where('activo', true)
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy);
    }
}
