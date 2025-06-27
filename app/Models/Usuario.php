<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory;

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

    //Verifica si el usuario es administrador
    public function isAdmin()
    {
        return $this->rol === 'Administrador';
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