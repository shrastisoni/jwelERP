<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PartyController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\StockLedgerController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProfitController;
use App\Http\Controllers\Api\OpeningStockController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerLedgerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StockValuationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CategoryValuationController; 
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockAdjustmentController;
use App\Http\Controllers\Api\AccountingController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\TrialBalanceController;     

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
    Route::get('/sales/{id}', [SaleController::class, 'show']);
    
    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
    
    // Route::apiResource('purchases', PurchaseController::class);
    // Route::get('/stock-ledger', [StockLedgerController::class, 'index']);
    Route::get('/stock-ledger', [StockLedgerController::class, 'index']);
    Route::get('/stock-ledger/{productId}', [StockLedgerController::class, 'productLedger']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);   // 👈
    Route::get('/products/category/{id}', [ProductController::class, 'byCategory']);   // 👈
    
    Route::put('/products/{id}', [ProductController::class, 'update']); 
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::patch('/products/{id}/toggle', [ProductController::class, 'toggleStatus']);
    Route::get('/getallproducts', [ProductController::class,'getAllProducts']);
    //Routes for profit controller
    Route::get('/reports/product-profit', [ ProfitController::class, 'productWise']);
    Route::get('/reports/profit/purchase-cost', [ ProfitController::class, 'productWiseNew']);
    Route::get('/reports/category-stock', [ ReportController::class, 'categoryStock']);
    Route::get('/profit/products', [ProfitController::class, 'productProfit']);
    Route::get('/profit/purchase-cost', [ProfitController::class, 'purchaseCostProfit']);
    Route::get('/profit/fifo', [ProfitController::class, 'fifoProfit']);

    
    Route::post('/opening-stock', [ OpeningStockController::class, 'store']);
     
    // Route for dashboard
    Route::get('/dashboard', [ DashboardController::class, 'index']);
    Route::get('/dashboard/charts', [ DashboardController::class, 'charts']);
    //Route for customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);
    Route::get('/customers/{id}/ledger', [CustomerLedgerController::class, 'ledger']);
    //Payments
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

    Route::get('/dashboard/stock-value', [DashboardController::class, 'stockValue']);
    Route::get('/dashboard/total-sales', [DashboardController::class, 'totalSales']);
    Route::get('/dashboard/profit', [DashboardController::class, 'totalProfit']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/recent-sales', [DashboardController::class, 'recentSales']);
    Route::get('/dashboard/low-stock', [DashboardController::class, 'lowStock']);

    Route::get('/parties', [PartyController::class, 'index']);
    Route::post('/parties', [PartyController::class, 'store']);
    Route::put('/parties/{id}', [PartyController::class, 'update']);
    Route::delete('/parties/{id}', [PartyController::class, 'destroy']);
    Route::get('/parties/{id}/ledger', [PartyController::class, 'ledger']);
    Route::get('/stock-valuation', [StockValuationController::class, 'index']);
    Route::get('/category-valuation', [CategoryValuationController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/change-password', [ProfileController::class, 'changePassword']);
     Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/ledger/{id}', [InventoryController::class, 'ledger']);
    Route::get('/inventory/category', [InventoryController::class, 'categorySummary']);

    Route::post('/stock-adjustment', [StockAdjustmentController::class, 'store']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
    Route::get('/ledger/party/{id}', [AccountingController::class, 'partyLedger']);
    Route::get('/outstanding', [AccountingController::class, 'outstanding']);
    Route::get('/day-book', [AccountingController::class, 'dayBook']);

    Route::post('/journal', [JournalController::class, 'store']);
    Route::get('/trial-balance', [TrialBalanceController::class, 'index']);
    });

