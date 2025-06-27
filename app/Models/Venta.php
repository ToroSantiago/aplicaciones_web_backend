<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $fillable = [
        'usuario_id',
        'total',
        'estado',
        'metodo_pago',
        'observaciones'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario (cliente)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación con los detalles de la venta
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    /**
     * Accesor para obtener el nombre completo del cliente
     */
    public function getClienteNombreCompletoAttribute(): string
    {
        return $this->usuario->nombre . ' ' . $this->usuario->apellido;
    }

    /**
     * Accesor para obtener la cantidad total de items
     */
    public function getCantidadTotalItemsAttribute(): int
    {
        return $this->detalles->sum('cantidad');
    }

    /**
     * Accesor para obtener la fecha formateada
     */
    public function getFechaFormateadaAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Scope para filtrar por cliente
     */
    public function scopeDelCliente($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeConEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para ordenar por fecha más reciente
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}