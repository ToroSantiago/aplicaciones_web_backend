<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saludo extends Model
{
    public function obtenerMensaje(): string{
        return 'HOLAAA';
    }
}
