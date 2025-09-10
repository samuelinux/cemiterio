<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class CausaMorteController extends Controller
{
    public function index()
    {
        return view('empresa.causas-morte.index');
    }
}
