<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class EmpresaController extends Controller
{
    public function index()
    {
        return view('admin.empresas.index');
    }

    public function create()
    {
        // Retorna a view de criação de empresa
        return view('admin.empresas.create');
    }
}
