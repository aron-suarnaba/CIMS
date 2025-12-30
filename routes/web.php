<?php

use App\Http\Controllers\PhoneController;
use App\Http\Controllers\UserController;
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

        // Main Index
        Route::get('/Phone', [PhoneController::class, 'index'])
            ->name('phone.index');

        // Create & Store Phone (The Asset itself)
        Route::get('/Phone/AddPhone', [PhoneController::class, 'create'])
            ->name('phone.create');

        Route::post('/Phone/AddPhone', [PhoneController::class, 'store'])
            ->name('phone.store');

        // Show Single Phone Detail
        Route::get('/Phone/{phone}', [PhoneController::class, 'show'])
            ->name('phone.show');

        // Store Phone Issuance Data
        Route::post('/Phone/{phone}/issue', [PhoneController::class, 'phoneTransStore'])
            ->name('phone.issue');

        // unsigned
        Route::post('/Phone/{phone}/return', [PhoneController::class, 'returnPhone'])
            ->name('phone.return');

        // unsigned
        Route::delete('/Phone/{phone}', [PhoneController::class, 'destroy'])
            ->name('phone.destroy');

        // Your existing transaction store (if used for logging)
        // Route::post('/Phone/Transaction', [PhoneController::class, 'phoneTransStore'])
        //     ->name('phone.trans.store');
    });
});