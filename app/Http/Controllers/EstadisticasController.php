<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function index()
    {
        // WIP: cálculos de ventas, productos, usuarios, etc.
        return view('layouts.estadisticas');
    }
}