<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->group(function (){
    /*=========== Chart Of Inventory Api Starts ===========*/
    Route::get('inventory-items',[\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryItems']);
    Route::get('inventory-details/{id}',[\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryDetails']);
    Route::post('inventory-update/{id}',[\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryUpdate']);
    Route::post('inventory-store/{id}',[\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryStore']);
    Route::delete('inventory-delete/{id}',[\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryDelete']);
    /*=========== Chart Of Inventory Api Ends ===========*/
});
