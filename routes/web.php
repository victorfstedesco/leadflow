<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('clients.index') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('clients.index'))->name('dashboard');

    // Clientes
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/criar', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clientes/{client}/postagens', [ClientController::class, 'posts'])->name('clients.posts');
    Route::get('/clientes/{client}/analytics', [AnalyticsController::class, 'show'])->name('clients.analytics');
    Route::get('/clientes/{client}/configuracoes', [ClientController::class, 'settings'])->name('clients.settings');
    Route::put('/clientes/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clientes/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Leads
    Route::get('/clientes/{client}/leads/criar', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/clientes/{client}/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('/leads/{lead}/mover', [LeadController::class, 'move'])->name('leads.move');

    // Interações
    Route::post('/leads/{lead}/interacoes', [InteractionController::class, 'store'])->name('interactions.store');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
