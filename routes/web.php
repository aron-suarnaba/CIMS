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

Route::post('/login', [UserController::class, 'store'])
    ->name('login.store');

Route::post('/logout', [UserController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/Home', function () {
        return Inertia::render('Home');
    })->name('dashboard');

    Route::get('/AssetAndInventoryManagement', function () {
        return Inertia::render('AssetAndInventoryManagement');
    })->name('AssetAndInventoryManagement');

    Route::get('/AssetAndInventoryManagement/Phone', [PhoneController::class, 'index'])
        ->name('phone.index');

    Route::get('/AssetAndInventoryManagement/Phone/AddPhone', [PhoneController::class, 'create'])
        ->name('phone.create');

    Route::get('/AssetAndInventoryManagement/Phone/{phone}', [PhoneController::class, 'show'])
        ->name('phone.show');

});
