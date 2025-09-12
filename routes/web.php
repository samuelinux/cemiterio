<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmpresaController as AdminEmpresaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmpresaLoginController;
use App\Http\Controllers\Empresa\CausaMorteController;
use App\Http\Controllers\Empresa\DashboardController;
use App\Http\Controllers\Empresa\SepultamentoController;
use App\Models\Empresa;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota inicial → login admin
// Route::get('/', fn () => redirect()->route('admin.login'));

// Atalho /admin → dashboard ou login (dependendo do auth)
Route::get('/admin', fn() => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'admin'])
    ->name('admin.home');

// Rotas de autenticação/Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Protegidas
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestão de empresas
        Route::resource('empresas', AdminEmpresaController::class);

        // Gestão de utilizadores
        Route::resource('users', AdminUserController::class);
        Route::get('users/{user}/permissions', [AdminUserController::class, 'permissions'])
            ->name('users.permissions');
        Route::put('users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])
            ->name('users.permissions.update');
    });
});

// Rotas de autenticação/Empresa
Route::prefix('{empresa:slug}')
    ->where(['empresa' => '^(?!admin$)(?!home$)(?!api$)[a-z0-9-]+$']) // evita colisões
    ->name('empresa.')
    ->group(function () {
        Route::get('/', fn(Empresa $empresa) => redirect()->route('empresa.login', $empresa))
            ->name('home');

        // Login empresa
        Route::get('login', [EmpresaLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [EmpresaLoginController::class, 'login']);
        Route::post('logout', [EmpresaLoginController::class, 'logout'])->name('logout');

        // Protegidas
        Route::middleware(['auth', 'empresa'])->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::resource('sepultamentos', SepultamentoController::class);
            Route::resource('causas-morte', CausaMorteController::class)
                ->parameters(['causas-morte' => 'causa']);
        });
    });

// Fallback global → 404
Route::fallback(fn() => abort(404, 'Página não encontrada.'));
