<?php

use App\Http\Controllers\LeverancierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlantenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoorraadController;
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
    Route::get('/leveranciers', [LeverancierController::class, 'index'])
        ->middleware('role:directie,magazijn_medewerker,vrijwilliger')
        ->name('leveranciers.index');
    Route::get('/voorraad', [VoorraadController::class, 'index'])
        ->middleware('role:directie,magazijn_medewerker,vrijwilliger')
        ->name('voorraad');
    Route::get('/leveranciers/nieuw', [LeverancierController::class, 'create'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('leveranciers.create');
    Route::post('/leveranciers', [LeverancierController::class, 'store'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('leveranciers.store');
    Route::delete('/leveranciers/{leverancierId}', [LeverancierController::class, 'destroy'])
        ->middleware('role:directie,magazijn_medewerker,vrijwilliger')
        ->name('leveranciers.destroy');

    Route::get('/profiel', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profiel', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:directie')->group(function (): void {
        Route::get('/directie/dashboard', [DashboardController::class, 'directie'])->name('directie.dashboard');
        Route::get('/directie/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('/directie/klanten', [KlantenController::class, 'index'])->name('klanten.index');
        Route::get('/directie/klanten/nieuw', [KlantenController::class, 'create'])->name('klanten.create');
        Route::post('/directie/klanten', [KlantenController::class, 'store'])->name('klanten.store');
        Route::get('/directie/klanten/{klantId}/wijzig', [KlantenController::class, 'edit'])->name('klanten.edit');
        Route::put('/directie/klanten/{klantId}', [KlantenController::class, 'update'])->name('klanten.update');
        Route::get('/directie/accounts', fn () => redirect()->route('klanten.index'))->name('accounts.index');
        });

    Route::middleware('role:magazijn_medewerker')->group(function (): void {
        Route::get('/magazijn/dashboard', [DashboardController::class, 'magazijn'])->name('magazijn.dashboard');
    });

    Route::middleware('role:vrijwilliger')->group(function (): void {
        Route::get('/vrijwilliger/dashboard', [DashboardController::class, 'vrijwilliger'])->name('vrijwilliger.dashboard');
    });
});
