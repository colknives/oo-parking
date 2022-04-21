<?php

use App\Http\Controllers\ParkingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('map/{parkingLot}', [ParkingController::class, 'map'])->name('parking.map');
Route::post('park/{parkingLot}', [ParkingController::class, 'park'])->name('parking.park');
Route::post('unpark/{parkingHistory}', [ParkingController::class, 'unpark'])->name('parking.unpark');
