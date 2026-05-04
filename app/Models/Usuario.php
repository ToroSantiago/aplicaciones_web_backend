<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * Nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'email',
        'password', // Aún mantenemos el campo password
        'rol',
        'genero',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /** Roles válidos del sistema. */
    public const ROL_CLIENTE = 'Cliente';
    public const ROL_EMPLEADO = 'Empleado';
    public const ROL_ADMIN = 'Administrador';

    public const ROLES_BACKOFFICE = [self::ROL_EMPLEADO, self::ROL_ADMIN];

    //Verifica si el usuario es administrador
    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    /** Verifica si el usuario es empleado (rol básico de backoffice). */
    public function isEmpleado(): bool
    {
        return $this->rol === self::ROL_EMPLEADO;
    }

    /**
     * Indica si el usuario puede acceder al backoffice (panel Blade).
     * Los 'Cliente' (compradores del SPA) quedan excluidos.
     */
    public function canAccessBackoffice(): bool
    {
        return in_array($this->rol, self::ROLES_BACKOFFICE, true);
    }


        /**
     * Relación con las ventas del usuario
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Accesor para obtener el total gastado por el cliente
     */
    public function getTotalGastadoAttribute()
    {
        return $this->ventas()
                    ->where('estado', 'completada')
                    ->sum('total');
    }

    /**
     * Accesor para obtener la cantidad de compras realizadas
     */
    public function getCantidadComprasAttribute()
    {
        return $this->ventas()
                    ->where('estado', 'completada')
                    ->count();
    }
}