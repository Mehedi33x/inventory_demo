<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/', [DashboardController::class, 'index']);
Route::get('/dashboard-stats', [DashboardController::class, 'dashboard']);
Route::get('/product', [ProductController::class, 'index']);
Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
Route::get('/product/show/{id}', [ProductController::class, 'show']);
Route::get('/product/edit/{id}', [ProductController::class, 'edit']);
Route::post('/product/update/{id}', [ProductController::class, 'update']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);

Route::get('/user', [UserController::class, 'index'])->name('user.list');
Route::post('user/create', [UserController::class, 'store'])->name('user.store');

Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::post('/stock/store', [StockController::class, 'store'])->name('stock.store');
Route::get('/stock/edit/{id}', [StockController::class, 'edit']);
Route::post('/stock/update/{id}', [StockController::class, 'update']);
Route::delete('stock/{id}', [StockController::class, 'destroy']);

Route::get('sale', [SaleController::class, 'index'])->name('sale.index');
Route::post('sale/store', [SaleController::class, 'store'])->name('sale.store');
Route::get('/sale/edit/{id}', [SaleController::class, 'edit']);
Route::post('/sale/update/{id}', [SaleController::class, 'update']);
Route::delete('sale/{id}', [SaleController::class, 'destroy']);
Route::get('generate-sale-invoice', [SaleController::class, 'generateSalesInvoice']);

Route::post('/registraion', [AuthController::class, 'registraion']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('get-products', [ProductController::class, 'getProducts'])->name('get.products');

Route::get('report', [ReportController::class, 'index'])->name('reports.index');
Route::get('/journals', [SaleController::class, 'getJournalEntries']);
