<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FortigateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function() {
    return response()->json(['message' => 'API is reachable']);
});

Route::get('/fortigate/status', [FortigateController::class, 'getSystemStatus']);

Route::get('/fortigate/devices', [FortigateController::class, 'getNetworkDevices']);
