<?php

use App\Http\Controllers\ComputersController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\SoftwareLicenseController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FortigateController;
use App\Http\Controllers\NetworkMonitoringController;
use App\Http\Controllers\NetworkingAssetController;
use App\Http\Controllers\PeripheralAssetController;
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

        Route::prefix('SoftwareLicense')->group(function () {
            Route::get('/', [SoftwareLicenseController::class, 'index'])->name('software-license.index');
            Route::post('/', [SoftwareLicenseController::class, 'store'])->name('software-license.store');
            Route::get('/{softwareLicense}', [SoftwareLicenseController::class, 'show'])->whereNumber('softwareLicense')->name('software-license.show');
            Route::put('/{softwareLicense}', [SoftwareLicenseController::class, 'update'])->whereNumber('softwareLicense')->name('software-license.update');
            Route::delete('/{softwareLicense}', [SoftwareLicenseController::class, 'destroy'])->whereNumber('softwareLicense')->name('software-license.destroy');
        });

        Route::prefix('Automation')->group(function () {
            Route::get('/', [AutomationController::class, 'index'])->name('automation.index');
            Route::post('/run', [AutomationController::class, 'run'])->name('automation.run');
        });

        // Credentials management
        Route::prefix('Credentials')->group(function () {
            Route::get('/', [\App\Http\Controllers\CredentialController::class, 'index'])->name('credentials.index');
            Route::get('/create', [\App\Http\Controllers\CredentialController::class, 'create'])->name('credentials.create');
            Route::post('/', [\App\Http\Controllers\CredentialController::class, 'store'])->name('credentials.store');
            Route::get('/{credential}', [\App\Http\Controllers\CredentialController::class, 'show'])->whereNumber('credential')->name('credentials.show');
            Route::post('/{credential}/reveal', [\App\Http\Controllers\CredentialController::class, 'reveal'])->whereNumber('credential')->name('credentials.reveal');
            Route::put('/{credential}', [\App\Http\Controllers\CredentialController::class, 'update'])->whereNumber('credential')->name('credentials.update');
            Route::delete('/{credential}', [\App\Http\Controllers\CredentialController::class, 'destroy'])->whereNumber('credential')->name('credentials.destroy');
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

        Route::prefix('Networking')->group(function () {
            Route::get('/', [NetworkingAssetController::class, 'index'])->name('networking.index');
            Route::post('/', [NetworkingAssetController::class, 'store'])->name('networking.store');
            Route::get('/{networking}', [NetworkingAssetController::class, 'show'])->whereNumber('networking')->name('networking.show');
            Route::put('/{networking}', [NetworkingAssetController::class, 'update'])->whereNumber('networking')->name('networking.update');
            Route::delete('/{networking}', [NetworkingAssetController::class, 'destroy'])->whereNumber('networking')->name('networking.destroy');
            Route::post('/{networking}/issue', [NetworkingAssetController::class, 'issue'])->whereNumber('networking')->name('networking.issue');
            Route::post('/{networking}/return', [NetworkingAssetController::class, 'return'])->whereNumber('networking')->name('networking.return');
            Route::get('/{networking}/logsheet', [NetworkingAssetController::class, 'generateLogsheetReport'])->whereNumber('networking')->name('networking.logsheet');
        });

        Route::prefix('Peripherals')->group(function () {
            Route::get('/', [PeripheralAssetController::class, 'index'])->name('peripherals.index');
            Route::post('/', [PeripheralAssetController::class, 'store'])->name('peripherals.store');
            Route::get('/{peripheral}', [PeripheralAssetController::class, 'show'])->whereNumber('peripheral')->name('peripherals.show');
            Route::put('/{peripheral}', [PeripheralAssetController::class, 'update'])->whereNumber('peripheral')->name('peripherals.update');
            Route::delete('/{peripheral}', [PeripheralAssetController::class, 'destroy'])->whereNumber('peripheral')->name('peripherals.destroy');
            Route::post('/{peripheral}/issue', [PeripheralAssetController::class, 'issue'])->whereNumber('peripheral')->name('peripherals.issue');
            Route::post('/{peripheral}/return', [PeripheralAssetController::class, 'return'])->whereNumber('peripheral')->name('peripherals.return');
            Route::get('/{peripheral}/logsheet', [PeripheralAssetController::class, 'generateLogsheetReport'])->whereNumber('peripheral')->name('peripherals.logsheet');
        });
    });

    Route::get('/NetworkMonitoringAndManagement', [NetworkMonitoringController::class, 'index'])->name('network.index');

});

require __DIR__.'/auth.php';
