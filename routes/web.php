<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlantenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoedselpakketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/inloggen', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/inloggen', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/registreren', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/registreren', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/uitloggen', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'redirectByRole'])->name('dashboard.redirect');

    Route::get('/profiel', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profiel', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:directie')->group(function (): void {
        Route::get('/directie/dashboard', [DashboardController::class, 'directie'])->name('directie.dashboard');
        Route::get('/directie/klanten', [KlantenController::class, 'index'])->name('klanten.index');
        Route::get('/directie/accounts', fn () => redirect()->route('klanten.index'))->name('accounts.index');
        Route::get('/directie/voedselpakketten', [VoedselpakketController::class, 'index'])->name('Directie.voedselpakketten.index');
        Route::get('/directie/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('Directie.voedselpakketten.create');
        Route::post('/directie/voedselpakketten', [VoedselpakketController::class, 'store'])->name('Directie.voedselpakketten.store');
        Route::get('/directie/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('Directie.voedselpakketten.edit');
        Route::put('/directie/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('Directie.voedselpakketten.update');
        Route::delete('/directie/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('Directie.voedselpakketten.destroy');

    });

    Route::middleware('role:magazijn_medewerker')->group(function (): void {
        Route::get('/magazijn/dashboard', [DashboardController::class, 'magazijn'])->name('magazijn.dashboard');
        Route::get('/magazijn/voedselpakketten', [VoedselpakketController::class, 'index'])->name('Magazijn.voedselpakketten.index');
        Route::get('/magazijn/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('Magazijn.voedselpakketten.create');
        Route::post('/magazijn/voedselpakketten', [VoedselpakketController::class, 'store'])->name('Magazijn.voedselpakketten.store');
        Route::get('/magazijn/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('Magazijn.voedselpakketten.edit');
        Route::put('/magazijn/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('Magazijn.voedselpakketten.update');
        Route::delete('/magazijn/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('Magazijn.voedselpakketten.destroy');
    

    });

    Route::middleware('role:vrijwilliger')->group(function (): void {
        Route::get('/vrijwilliger/dashboard', [DashboardController::class, 'vrijwilliger'])->name('vrijwilliger.dashboard');
        Route::get('/vrijwilliger/voedselpakketten', [VoedselpakketController::class, 'index'])->name('Vrijwilliger.voedselpakketten.index');
        Route::get('/vrijwilliger/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('Vrijwilliger.voedselpakketten.create');
        Route::post('/vrijwilliger/voedselpakketten', [VoedselpakketController::class, 'store'])->name('Vrijwilliger.voedselpakketten.store');
        Route::get('/vrijwilliger/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('Vrijwilliger.voedselpakketten.edit');
        Route::put('/vrijwilliger/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('Vrijwilliger.voedselpakketten.update');
        Route::delete('/vrijwilliger/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('Vrijwilliger.voedselpakketten.destroy');

        


    });
});
