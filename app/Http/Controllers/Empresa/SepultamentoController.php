<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;


class SepultamentoController extends Controller
{
    public function index()
    {
        return view('empresa.sepultamentos.index');
    }
}
