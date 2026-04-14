<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/criar', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clientes/{client}/postagens', [ClientController::class, 'posts'])->name('clients.posts');
    Route::get('/clientes/{client}/campanhas', [ClientController::class, 'campaigns'])->name('clients.campaigns');
    Route::get('/clientes/{client}/insights', [ClientController::class, 'insights'])->name('clients.insights');
    Route::get('/clientes/{client}/configuracoes', [ClientController::class, 'settings'])->name('clients.settings');
    Route::put('/clientes/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clientes/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Postagens (CRUD)
    Route::get('/clientes/{client}/postagens/criar', [PostController::class, 'create'])->name('posts.create');
    Route::post('/clientes/{client}/postagens', [PostController::class, 'store'])->name('posts.store');
    Route::get('/clientes/{client}/postagens/{post}/editar', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/clientes/{client}/postagens/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/clientes/{client}/postagens/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/clientes/{client}/postagens/{post}/campanha', [PostController::class, 'linkCampaign'])->name('posts.link-campaign');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
