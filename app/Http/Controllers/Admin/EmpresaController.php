<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
   public function index( )
    {
        // Aqui você pode carregar as empresas e passá-las para a view
        // Por enquanto, vamos apenas retornar uma view simples
        return view("admin.empresas.index");
    }

    public function create()
{
    // Retorna a view de criação de empresa
    return view('admin.empresas.create');
}

}
