<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Sepultamento;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEmpresas = Empresa::count();
        $empresasAtivas = Empresa::ativo()->count();
        $totalUsuarios = User::user()->count();
        $usuariosAtivos = User::user()->ativo()->count();
        $totalSepultamentos = Sepultamento::count();
        $sepultamentosHoje = Sepultamento::whereDate('data_sepultamento', today())->count();

        return view('admin.dashboard', compact(
            'totalEmpresas',
            'empresasAtivas',
            'totalUsuarios',
            'usuariosAtivos',
            'totalSepultamentos',
            'sepultamentosHoje'
        ));
    }
}
