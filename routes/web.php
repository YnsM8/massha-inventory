<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

// ============================================
// RUTAS DE AUTENTICACIÓN
// ============================================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperación de contraseña
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Inventario
    Route::resource('inventory', ItemController::class);
    
    // Movimientos
    Route::get('/movements/incoming', [MovementController::class, 'incoming'])->name('movements.incoming');
    Route::get('/movements/outgoing', [MovementController::class, 'outgoing'])->name('movements.outgoing');
    Route::resource('movements', MovementController::class)->only(['index', 'store', 'show', 'destroy']);
    
    // Solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::post('/solicitudes/{solicitud}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
    
    // Proveedores
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
    
    // Reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
    Route::get('/reports/critical-items', [ReportController::class, 'criticalItems'])->name('reports.critical-items');
    
    // Usuarios (solo admin)
    Route::middleware(['check.admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});

// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\ItemController;
// use App\Http\Controllers\SupplierController;
// use App\Http\Controllers\MovementController;
// use App\Http\Controllers\ReportController;
// use App\Http\Controllers\SolicitudController;
// use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\UserController;

// // Rutas de autenticación
// Auth::routes();

// // Rutas protegidas por autenticación
// Route::middleware(['auth'])->group(function () {
    
//     // Dashboard
//     Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//     Route::get('/dashboard', [DashboardController::class, 'index']);
//     Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

//     // Gestión de Items/Inventario
//     Route::resource('inventory', ItemController::class)->parameters([
//         'inventory' => 'item'
//     ]);

//     // Gestión de Proveedores
//     Route::resource('suppliers', SupplierController::class);
//     Route::patch('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

//     // Gestión de Movimientos
//     Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
//     Route::get('/movements/incoming', [MovementController::class, 'incoming'])->name('movements.incoming');
//     Route::get('/movements/outgoing', [MovementController::class, 'outgoing'])->name('movements.outgoing');
//     Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
//     Route::get('/movements/{movement}', [MovementController::class, 'show'])->name('movements.show');
//     Route::delete('/inventory/{item}', [ItemController::class, 'destroy'])->name('inventory.destroy');

//     // Reportes
//     Route::prefix('reports')->name('reports.')->group(function () {
//         Route::get('/', [ReportController::class, 'index'])->name('index');
//         Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
//         Route::get('/movements', [ReportController::class, 'movements'])->name('movements');
//         Route::get('/critical-items', [ReportController::class, 'criticalItems'])->name('critical-items');
//     });

//     // Gestión de Solicitudes de Insumos
//     Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
//         Route::get('/', [SolicitudController::class, 'index'])->name('index');
//         Route::get('/create', [SolicitudController::class, 'create'])->name('create');
//         Route::post('/', [SolicitudController::class, 'store'])->name('store');
//         Route::get('/{solicitud}', [SolicitudController::class, 'show'])->name('show');
//         Route::post('/{solicitud}/aprobar', [SolicitudController::class, 'aprobar'])->name('aprobar');
//         Route::post('/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
//     });

//     // Gestión de Usuarios (solo Admin)
//     Route::middleware(['role:admin'])->group(function () {
//         Route::resource('users', UserController::class);
//     });
// });