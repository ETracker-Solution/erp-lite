<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ChartOfInventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //lock Screen Start
    Route::get('lock-screen', [App\Http\Controllers\LockScreenController::class, 'lockScreen']);

    //lock Screen Start

    Route::resource('chart-of-accounts', ChartOfAccountController::class);
    Route::resource('stores', \App\Http\Controllers\StoreController::class);
    Route::resource('chart-of-inventories', ChartOfInventoryController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('brands', App\Http\Controllers\BrandController::class);
    Route::resource('units', App\Http\Controllers\UnitController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('supplier-groups', App\Http\Controllers\SupplierGroupController::class);
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);

    //-----start Pos---------
    Route::resource('pos', App\Http\Controllers\POSController::class);

    Route::get('fetch-sub-category-product-info/{id}', [App\Http\Controllers\POSController::class, 'fetchSubCategoryProductInfo']);
    Route::get('fetch-sub-category-wise-product-info/{catId}/{subCatId}', [App\Http\Controllers\POSController::class, 'fetch_sub_category_wise_product_info']);
    Route::get('fetch-item-info/{id}', [App\Http\Controllers\ApiController::class, 'fetchItemById']);
    Route::get('fetch-product-info/{id}', [App\Http\Controllers\POSController::class, 'fetch_product_info']);
    Route::get('fetch-all-product', [App\Http\Controllers\POSController::class, 'fetch_all_product']);

    Route::get('fetch-item-by-search-keyword/{keyword}', [App\Http\Controllers\POSController::class, 'getItemBySearchKeyword']);
    //--------------

    Route::resource('attributes', AttributeController::class);
    Route::get('/fetch-items-by-group-id/{id}', [App\Http\Controllers\ApiController::class, 'fetch_products_by_cat_id']);
    Route::get('/fetch-product-by-category-id/{id}', [App\Http\Controllers\ProductController::class, 'fetch_products_by_cat_id']);
    Route::get('/fetch-product-info-for-sale/{id}', [App\Http\Controllers\ApiController::class, 'fetch_product_sale'])->name('fetch-product-info-for-sale');
    Route::resource('stocks', App\Http\Controllers\StockController::class);
    Route::resource('label', App\Http\Controllers\LabelController::class);
    Route::get('vuejs/autocomplete/search', [App\Http\Controllers\LabelController::class, 'autocompleteSearch'])->name('vuejs.autocomplete.search');
    Route::get('fetch-product-info-for-gatepass/{id}', [App\Http\Controllers\LabelController::class, 'fetch_product_info']);


    Route::get('sale-pdf/{id}', [App\Http\Controllers\SaleController::class, 'pdf'])->name('sale.pdf');
    Route::get('sale-pdf-download/{id}', [App\Http\Controllers\SaleController::class, 'pdfDownload'])->name('sale.pdf-download');

    Route::get('order-pdf/{id}', [App\Http\Controllers\OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('order-pdf-download/{id}', [App\Http\Controllers\OrderController::class, 'pdfDownload'])->name('order.pdf-download');

    Route::get('purchase-pdf/{id}', [App\Http\Controllers\PurchaseController::class, 'pdf'])->name('purchase.pdf');
    Route::get('purchase-pdf-download/{id}', [App\Http\Controllers\PurchaseController::class, 'pdfDownload'])->name('purchase.pdf-download');

    Route::resource('users', \App\Http\Controllers\UserController::class);

    Route::resource('batches', \App\Http\Controllers\BatchController::class);

});
Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {
    //Report Route
    Route::get('daily-report', [App\Http\Controllers\ReportController::class, 'dailyReport'])->name('daily.report');
    Route::get('profit-loss', [App\Http\Controllers\ReportController::class, 'profitLoss'])->name('profit.loss');
    Route::get('balance-sheet', [App\Http\Controllers\ReportController::class, 'balanceSheet'])->name('balance.sheet');
    Route::get('balance-sheet-data', [App\Http\Controllers\ReportController::class, 'balanceSheetData'])->name('balance.sheet.data');
    Route::get('stock-report', [App\Http\Controllers\ReportController::class, 'stockReport'])->name('stock.report');
    Route::get('fetch-today-summery', [App\Http\Controllers\ReportController::class, 'fetchTodaySummery'])->name('today.summery.report');
    Route::get('fetch-month-summery', [App\Http\Controllers\ReportController::class, 'fetchMonthSummery'])->name('month.summery.report');
    Route::get('fetch-year-summery', [App\Http\Controllers\ReportController::class, 'fetchYearSummery'])->name('year.summery.report');

    Route::get('daterange-report', [App\Http\Controllers\ReportController::class, 'daterange'])->name('daterange.report');
    Route::get('daterange-data', [App\Http\Controllers\ReportController::class, 'fetchByDaterange'])->name('fetchbydaterange.report');

    Route::get('daterangesummary-report', [App\Http\Controllers\ReportController::class, 'daterangeSummery'])->name('daterangesummery.report');
    Route::get('daterangesummary-data', [App\Http\Controllers\ReportController::class, 'fetchDaterangeSummary'])->name('fetchdaterangesummary.report');

});


require __DIR__ . '/auth.php';
