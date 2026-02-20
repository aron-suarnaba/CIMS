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
})->middleware(['auth'])->name('session.refresh');

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
            Route::get('/{phone}', [PhoneController::class, 'show'])->whereNumber('phone')->name('phone.show');
            Route::put('/{phone}', [PhoneController::class, 'update'])->whereNumber('phone')->name('phone.update');

            // Asset Actions
            Route::post('/{phone}/issue', [PhoneController::class, 'issue'])->whereNumber('phone')->name('phone.issue');
            Route::post('/{phone}/return', [PhoneController::class, 'return'])->whereNumber('phone')->name('phone.return');
            Route::delete('/{phone}', [PhoneController::class, 'destroy'])->whereNumber('phone')->name('phone.destroy');

            //Generate Report
            Route::get('/{phone}/logsheet', [PhoneController::class, 'generateLogsheetReport'])->whereNumber('phone')->name('phone.logsheet');

            // mini PC routes
            Route::get('/minipc', [App\Http\Controllers\MiniPcController::class, 'index'])->name('minipc.index');
            Route::get('/minipc/create', [App\Http\Controllers\MiniPcController::class, 'create'])->name('minipc.create');
            Route::post('/minipc', [App\Http\Controllers\MiniPcController::class, 'store'])->name('minipc.store');
            Route::get('/minipc/{minipc}', [App\Http\Controllers\MiniPcController::class, 'show'])->whereNumber('minipc')->name('minipc.show');
            Route::put('/minipc/{minipc}', [App\Http\Controllers\MiniPcController::class, 'update'])->whereNumber('minipc')->name('minipc.update');
            Route::delete('/minipc/{minipc}', [App\Http\Controllers\MiniPcController::class, 'destroy'])->whereNumber('minipc')->name('minipc.destroy');
            Route::post('/minipc/{minipc}/issue', [App\Http\Controllers\MiniPcController::class, 'issue'])->whereNumber('minipc')->name('minipc.issue');
            Route::post('/minipc/{minipc}/pullout', [App\Http\Controllers\MiniPcController::class, 'pullout'])->whereNumber('minipc')->name('minipc.pullout');
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

require __DIR__.'/auth.php';
