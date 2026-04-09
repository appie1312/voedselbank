<?php

use App\Http\Controllers\LeverancierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlantenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoedselpakketController;
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
    Route::get('/voorraad/nieuw', [VoorraadController::class, 'create'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('voorraad.create');
    Route::post('/voorraad', [VoorraadController::class, 'store'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('voorraad.store');
    Route::get('/voorraad/{productId}/wijzig', [VoorraadController::class, 'edit'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('voorraad.edit');
    Route::put('/voorraad/{productId}', [VoorraadController::class, 'update'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('voorraad.update');
    Route::delete('/voorraad/{productId}', [VoorraadController::class, 'destroy'])
        ->middleware('role:directie,magazijn_medewerker')
        ->name('voorraad.destroy');
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
        // Volledige voedselpakketflow voor directie.
        Route::get('/directie/voedselpakketten', [VoedselpakketController::class, 'index'])->name('directie.voedselpakketten.index');
        Route::get('/directie/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('directie.voedselpakketten.create');
        Route::post('/directie/voedselpakketten', [VoedselpakketController::class, 'store'])->name('directie.voedselpakketten.store');
        Route::get('/directie/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('directie.voedselpakketten.edit');
        Route::get('/directie/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'samenstellen'])->name('directie.voedselpakketten.samenstellen');
        Route::post('/directie/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'opslaanSamenstelling'])->name('directie.voedselpakketten.samenstellen.opslaan');
        Route::put('/directie/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('directie.voedselpakketten.update');
        Route::delete('/directie/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('directie.voedselpakketten.destroy');
        Route::get('/directie/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('/directie/klanten', [KlantenController::class, 'index'])->name('klanten.index');
        Route::get('/directie/klanten/nieuw', [KlantenController::class, 'create'])->name('klanten.create');
        Route::post('/directie/klanten', [KlantenController::class, 'store'])->name('klanten.store');
        Route::get('/directie/klanten/{klantId}/wijzig', [KlantenController::class, 'edit'])->name('klanten.edit');
        Route::put('/directie/klanten/{klantId}', [KlantenController::class, 'update'])->name('klanten.update');
        Route::delete('/directie/klanten/{klantId}', [KlantenController::class, 'destroy'])->name('klanten.destroy');
        Route::get('/directie/accounts', fn () => redirect()->route('klanten.index'))->name('accounts.index');
        });

    Route::middleware('role:magazijn_medewerker')->group(function (): void {
        Route::get('/magazijn/dashboard', [DashboardController::class, 'magazijn'])->name('magazijn.dashboard');
        // Volledige voedselpakketflow voor magazijn.
        Route::get('/magazijn/voedselpakketten', [VoedselpakketController::class, 'index'])->name('magazijn_medewerker.voedselpakketten.index');
        Route::get('/magazijn/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('magazijn_medewerker.voedselpakketten.create');
        Route::post('/magazijn/voedselpakketten', [VoedselpakketController::class, 'store'])->name('magazijn_medewerker.voedselpakketten.store');
        Route::get('/magazijn/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('magazijn_medewerker.voedselpakketten.edit');
        Route::get('/magazijn/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'samenstellen'])->name('magazijn_medewerker.voedselpakketten.samenstellen');
        Route::post('/magazijn/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'opslaanSamenstelling'])->name('magazijn_medewerker.voedselpakketten.samenstellen.opslaan');
        Route::put('/magazijn/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('magazijn_medewerker.voedselpakketten.update');
        Route::delete('/magazijn/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('magazijn_medewerker.voedselpakketten.destroy');
    

    });

    Route::middleware('role:vrijwilliger')->group(function (): void {
        Route::get('/vrijwilliger/dashboard', [DashboardController::class, 'vrijwilliger'])->name('vrijwilliger.dashboard');
        // Volledige voedselpakketflow voor vrijwilligers.
        Route::get('/vrijwilliger/voedselpakketten', [VoedselpakketController::class, 'index'])->name('vrijwilliger.voedselpakketten.index');
        Route::get('/vrijwilliger/voedselpakketten/create', [VoedselpakketController::class, 'create'])->name('vrijwilliger.voedselpakketten.create');
        Route::post('/vrijwilliger/voedselpakketten', [VoedselpakketController::class, 'store'])->name('vrijwilliger.voedselpakketten.store');
        Route::get('/vrijwilliger/voedselpakketten/{id}/edit', [VoedselpakketController::class, 'edit'])->name('vrijwilliger.voedselpakketten.edit');
        Route::get('/vrijwilliger/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'samenstellen'])->name('vrijwilliger.voedselpakketten.samenstellen');
        Route::post('/vrijwilliger/voedselpakketten/{id}/samenstellen', [VoedselpakketController::class, 'opslaanSamenstelling'])->name('vrijwilliger.voedselpakketten.samenstellen.opslaan');
        Route::put('/vrijwilliger/voedselpakketten/{id}', [VoedselpakketController::class, 'update'])->name('vrijwilliger.voedselpakketten.update');
        Route::delete('/vrijwilliger/voedselpakketten/{id}', [VoedselpakketController::class, 'destroy'])->name('vrijwilliger.voedselpakketten.destroy');

        


    });
});
