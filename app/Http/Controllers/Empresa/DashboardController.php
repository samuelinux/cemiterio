<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Sepultamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $empresa = Auth::user()->empresa;
        $empresaId = $empresa->id;

        $totalSepultamentos = Sepultamento::porEmpresa($empresaId)->count();
        $sepultamentosHoje = Sepultamento::porEmpresa($empresaId)
            ->whereDate('data_sepultamento', today())->count();
        $sepultamentosEsteMes = Sepultamento::porEmpresa($empresaId)
            ->whereMonth('data_sepultamento', now()->month)
            ->whereYear('data_sepultamento', now()->year)
            ->count();
        
        $ultimosSepultamentos = Sepultamento::porEmpresa($empresaId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('empresa.dashboard', compact(
            'empresa',
            'totalSepultamentos',
            'sepultamentosHoje',
            'sepultamentosEsteMes',
            'ultimosSepultamentos'
        ));
    }
}
