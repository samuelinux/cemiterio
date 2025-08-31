<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmpresaLoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmpresaController as AdminEmpresaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Empresa\DashboardController;
use App\Http\Controllers\Empresa\SepultamentoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rota inicial - redireciona para admin
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Rotas de autenticação para Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    
    // Rotas protegidas por middleware admin
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestão de empresas
        Route::resource('empresas', AdminEmpresaController::class);
        
        // Gestão de utilizadores
        Route::resource('users', AdminUserController::class);
        Route::get('users/{user}/permissions', [AdminUserController::class, 'permissions'])->name('users.permissions');
        Route::put('users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])->name('users.permissions.update');
    });
});

// Rotas de autenticação para Empresa
Route::prefix('{empresa}')->name('empresa.')->group(function () {
    // Login
    Route::get('login', [EmpresaLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EmpresaLoginController::class, 'login']);
    Route::post('logout', [EmpresaLoginController::class, 'logout'])->name('logout');
    
    // Rotas protegidas por middleware empresa
    Route::middleware(['auth', 'empresa'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Gestão de sepultamentos
        Route::resource('sepultamentos', SepultamentoController::class);
    });
});
