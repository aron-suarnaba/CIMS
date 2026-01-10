<?php

use App\Http\Controllers\ComputersController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FortigateController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [UserController::class, 'store'])->name('login.store');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/Home', function () {
        return Inertia::render('Home');
    })->name('dashboard');

    Route::prefix('AssetAndInventoryManagement')->group(function () {

        Route::get('/', function () {
            return Inertia::render('AssetAndInventoryManagement');
        })->name('AssetAndInventoryManagement');

        // Phone Routes
        Route::prefix('phone')->group(function () {
            Route::get('/', [PhoneController::class, 'index'])->name('phone.index');
            Route::get('/create', [PhoneController::class, 'create'])->name('phone.create');
            Route::post('/', [PhoneController::class, 'store'])->name('phone.store');
            Route::get('/{phone}', [PhoneController::class, 'show'])->name('phone.show');

            // Asset Actions
            Route::post('/{phone}/issue', [PhoneController::class, 'issue'])->name('phone.issue');
            Route::post('/{phone}/return', [PhoneController::class, 'return'])->name('phone.return');
            Route::delete('/{phone:serial_num}', [PhoneController::class, 'destroy'])->name('phone.destroy');
        });

        // Your existing transaction store (if used for logging)
        // Route::post('/Phone/Transaction', [PhoneController::class, 'phoneTransStore'])
        //     ->name('phone.trans.store');

        Route::prefix('Computer')->group(function () {

            Route::get('/', [ComputersController::class, 'index'])
                ->name('computer.index');
            Route::get('/{computer}', [ComputersController::class, 'show'])->name('computer.show');
            Route::post('/{computer}/issue', [ComputersController::class, 'issue'])->name('computer.issue');
        });
    });

    Route::get('/NetworkMonitoringAndManagement', function () {
        return Inertia::render('NetworkMonitoringManagement');
    })->name('NetworkMonitoringAndManagement');

});
