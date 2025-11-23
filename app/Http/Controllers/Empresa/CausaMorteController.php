<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\CausaMorte;

class CausaMorteController extends Controller
{
    public function index(Empresa $empresa)
    {
        return view('empresa.causas-morte.index');
    }
}
