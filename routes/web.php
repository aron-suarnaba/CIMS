<?php

use App\Http\Controllers\ComputersController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FortigateController;
use App\Http\Controllers\NetworkMonitoringController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/refresh-session', function () {
    return response()->json(['status' => 'alive']);
})->middleware(['auth']);

Route::get('/login', [UserController::class, 'showLogin'])->name('login');

Route::post('/login', [UserController::class, 'store'])->name('login.store');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/user/{userid}', [UserController::class, 'index'])->name('user.index');
    Route::patch('/user/update', [UserController::class, 'update'])->name('user.update');

    Route::get('/Home', function () {
        return Inertia::render('Home');
    })->name('dashboard');

    Route::prefix('AssetAndInventoryManagement')->group(function () {

        Route::get('/', function () {
            return Inertia::render('AssetAndInventoryManagement');
        })->name('AssetAndInventoryManagement');

        // Phone Routes
        Route::prefix('Phone')->group(function () {
            Route::get('/', [PhoneController::class, 'index'])->name('phone.index');
            Route::get('/create', [PhoneController::class, 'create'])->name('phone.create');
            Route::post('/', [PhoneController::class, 'store'])->name('phone.store');
            Route::get('/{phone}', [PhoneController::class, 'show'])->name('phone.show');
            Route::put('/{phone}', [PhoneController::class, 'update'])->name('phone.update');

            // Asset Actions
            Route::post('/{phone}/issue', [PhoneController::class, 'issue'])->name('phone.issue');
            Route::post('/{phone}/return', [PhoneController::class, 'return'])->name('phone.return');
            Route::delete('/{phone}', [PhoneController::class, 'destroy'])->name('phone.destroy');

            //Generate Report
            Route::get('/{phone}/logsheet', [PhoneController::class, 'generateLogsheetReport'])->name('phone.logsheet');
        });

        // Your existing transaction store (if used for logging)
        // Route::post('/Phone/Transaction', [PhoneController::class, 'phoneTransStore'])
        //     ->name('phone.trans.store');

        Route::prefix('Computer')->group(function () {

            Route::get('/', [ComputersController::class, 'index'])
                ->name('computer.index');
            Route::post('/', [ComputersController::class, 'store'])
                ->name('computer.store');

            Route::get('/{computer:host_name}', [ComputersController::class, 'show'])->name('computer.show');

            Route::post('/{computer:host_name}/issue', [ComputersController::class, 'issue'])->name('computer.issue');
            Route::post('/{computer:host_name}/return', [ComputersController::class, 'return'])->name('computer.return');
            Route::delete('/{computer:host_name}', [ComputersController::class, 'destroy'])->name('computer.destroy');

        });
    });

    Route::get('/NetworkMonitoringAndManagement', [NetworkMonitoringController::class, 'index'])->name('network.index');

});
