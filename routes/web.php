<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MetaAuthController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\PlanningGoalController;
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
    Route::get('/clientes/{client}/configuracoes', [ClientController::class, 'settings'])->name('clients.settings');
    Route::put('/clientes/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clientes/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Meta OAuth
    Route::get('/clientes/{client}/meta/conectar', [MetaAuthController::class, 'redirect'])->name('meta.redirect');
    Route::get('/meta/callback', [MetaAuthController::class, 'callback'])->name('meta.callback');
    Route::get('/clientes/{client}/meta/ad-accounts', [MetaAuthController::class, 'listAdAccounts'])->name('meta.list-ad-accounts');
    Route::post('/clientes/{client}/meta/ad-account', [MetaAuthController::class, 'selectAdAccount'])->name('meta.ad-account');
    Route::delete('/clientes/{client}/meta', [MetaAuthController::class, 'disconnect'])->name('meta.disconnect');

    // Campanhas (read-only + sync)
    Route::get('/clientes/{client}/campanhas', [CampaignController::class, 'index'])->name('clients.campaigns');
    Route::post('/clientes/{client}/campanhas/sync', [CampaignController::class, 'sync'])->name('campaigns.sync');
    Route::get('/clientes/{client}/campanhas/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/clientes/{client}/campanhas/{campaign}/insights', [CampaignController::class, 'insights'])->name('campaigns.insights');

    // Planejamentos
    Route::get('/clientes/{client}/planejamentos', [PlanningController::class, 'index'])->name('plannings.index');
    Route::get('/clientes/{client}/planejamentos/criar', [PlanningController::class, 'create'])->name('plannings.create');
    Route::post('/clientes/{client}/planejamentos', [PlanningController::class, 'store'])->name('plannings.store');
    Route::get('/clientes/{client}/planejamentos/{planning}', [PlanningController::class, 'show'])->name('plannings.show');
    Route::get('/clientes/{client}/planejamentos/{planning}/editar', [PlanningController::class, 'edit'])->name('plannings.edit');
    Route::put('/clientes/{client}/planejamentos/{planning}', [PlanningController::class, 'update'])->name('plannings.update');
    Route::delete('/clientes/{client}/planejamentos/{planning}', [PlanningController::class, 'destroy'])->name('plannings.destroy');

    // Vínculo de campanhas em planejamento
    Route::post('/clientes/{client}/planejamentos/{planning}/campanhas', [PlanningController::class, 'attachCampaign'])->name('plannings.attach-campaign');
    Route::delete('/clientes/{client}/planejamentos/{planning}/campanhas/{campaign}', [PlanningController::class, 'detachCampaign'])->name('plannings.detach-campaign');
    Route::patch('/clientes/{client}/planejamentos/{planning}/campanhas/{campaign}', [PlanningController::class, 'updateCampaignStatus'])->name('plannings.campaign-status');

    // Metas (goals)
    Route::post('/clientes/{client}/planejamentos/{planning}/metas', [PlanningGoalController::class, 'store'])->name('goals.store');
    Route::put('/clientes/{client}/planejamentos/{planning}/metas/{goal}', [PlanningGoalController::class, 'update'])->name('goals.update');
    Route::delete('/clientes/{client}/planejamentos/{planning}/metas/{goal}', [PlanningGoalController::class, 'destroy'])->name('goals.destroy');

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
