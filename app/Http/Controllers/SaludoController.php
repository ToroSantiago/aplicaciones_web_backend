<?php

namespace App\Http\Controllers;
use App\Models\Saludo;

use Illuminate\Http\Request;

class SaludoController extends Controller
{
    public function saludo(){
        $mensaje = new Saludo();
        $texto = $mensaje->obtenerMensaje();
        return view('Saludo', compact('texto'));
    }


}
