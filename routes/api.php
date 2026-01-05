<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PartyController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\StockLedgerController;
use App\Http\Controllers\Api\PurchaseController;
/*
|--------------------------------------------------------------------------
| API Routes (Laravel 12)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/parties', [PartyController::class, 'index']);
    // Route::get('/party', [PartyController::class, 'getPartyRecord']);
    // Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::post('/purchases', [PurchaseController::class, 'store']);
    // Route::apiResource('purchases', PurchaseController::class);
    Route::get('/stock-ledger', [StockLedgerController::class, 'index']);
});

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\API\AuthController;
// use App\Http\Controllers\API\CategoryController;
// use App\Http\Controllers\API\ProductController;
// use App\Http\Controllers\API\PartyController;
// use App\Http\Controllers\API\SaleController;
// use App\Http\Controllers\API\StockLedgerController;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// */

// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {

//     Route::get('/categories', [CategoryController::class, 'index']);
//     Route::get('/products', [ProductController::class, 'index']);
//     Route::get('/parties', [PartyController::class, 'index']);

//     Route::post('/sales', [SaleController::class, 'store']);

//     Route::get('/stock-ledger', [StockLedgerController::class, 'index']);
// });

// use App\Http\Controllers\SaleController;
// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\PartyController;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\API\{
//     AuthController,
//     CategoryController,
//     ProductController,
//     PartyController,
//     SaleController,
//     StockLedgerController
// };

// Route::post('login',[AuthController::class,'login']);

// Route::middleware('auth:sanctum')->group(function () {

//     Route::get('categories',[CategoryController::class,'index']);
//     Route::get('products',[ProductController::class,'index']);
//     Route::get('parties',[PartyController::class,'index']);

//     Route::post('sales',[SaleController::class,'store']);

//     Route::get('stock-ledger',[StockLedgerController::class,'index']);
// });

// // Route::post('/login', [AuthController::class, 'login']);

// // Route::middleware('auth:sanctum')->group(function () {
// //     Route::apiResource('sales', SaleController::class);
// // });

// // Route::post('/login', [AuthController::class, 'login']);

// // Route::middleware('auth:sanctum')->group(function () {
// //     Route::apiResource('categories', CategoryController::class);
// //     Route::apiResource('products', ProductController::class);
// //     Route::apiResource('parties', PartyController::class);
// //     Route::apiResource('sales', SaleController::class);
// // });
