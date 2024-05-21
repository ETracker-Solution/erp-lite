<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ChartOfInventoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FundTransferVoucherController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReceiveVoucherController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierPaymentVoucherController;
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
    Route::resource('requisitions', \App\Http\Controllers\RequisitionController::class);
    Route::resource('productions', \App\Http\Controllers\ProductionController::class);
    Route::get('production-pdf/{id}', [App\Http\Controllers\ProductionController::class, 'productionPdf'])->name('production.pdf');
    Route::resource('outlets', \App\Http\Controllers\OutletController::class);
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    Route::resource('payment-vouchers', PaymentVoucherController::class);
    Route::resource('receive-vouchers', ReceiveVoucherController::class);
    Route::resource('journal-vouchers', JournalVoucherController::class);
    Route::resource('fund-transfer-vouchers', FundTransferVoucherController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('factories', FactoryController::class);

    Route::resource('earn-points', \App\Http\Controllers\EarnPointController::class);
    Route::resource('redeem-points', \App\Http\Controllers\RedeemPointController::class);
    Route::resource('member-types', \App\Http\Controllers\MemberTypeController::class);
    Route::resource('member-points', \App\Http\Controllers\MemberPointController::class);
    Route::resource('memberships', \App\Http\Controllers\MembershipController::class);

    //Promo Code
    Route::get('get-customer-by-type', [\App\Http\Controllers\PromoCodeController::class, 'getCustomers'])->name('promo-codes.customers');
    Route::resource('promo-codes', \App\Http\Controllers\PromoCodeController::class);

    //-----Supplier Payment Voucher---------
    Route::resource('supplier-vouchers', SupplierPaymentVoucherController::class);
    Route::get('fetch-due-by-supplier-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchSupplierDueById'])->name('supplier.due');
    //-----Supplier Payment Voucher---------

    Route::resource('consumptions', \App\Http\Controllers\ConsumptionController::class);
    Route::get('consumption-pdf/{id}', [App\Http\Controllers\ConsumptionController::class, 'consumptionPdf'])->name('consumptions.pdf');
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

    Route::get('/fetch-production-by-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchProductionById']);
    Route::get('/fetch-consumption-by-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchConsumptionById']);
    Route::get('/fetch-items-by-group-id/{id}', [App\Http\Controllers\ApiController::class, 'fetch_products_by_cat_id']);
    Route::get('/fetch-item-available-balance/{item_id}/{store_id?}', [App\Http\Controllers\ApiController::class, 'fetchItemAvailableBalance']);
    Route::get('fetch-item-info-rm-consumption/{item_id}/{store_id?}', [App\Http\Controllers\ApiController::class, 'fetchItemInfoRMConsumption']);


    Route::get('/fetch-suppliers-by-group-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchSuppliersByGroupId']);
    Route::get('fetch-purchase-products-info/{id}', [App\Http\Controllers\ApiController::class, 'fetchPurchaseProductInfo']);
    Route::get('/fetch-product-by-category-id/{id}', [App\Http\Controllers\ProductController::class, 'fetch_products_by_cat_id']);

    //as soon as possible Remove
    Route::get('/fetch-product-info-for-sale/{id}', [App\Http\Controllers\ApiController::class, 'fetch_product_sale'])->name('fetch-product-info-for-sale');
//    new
    Route::get('/fetch-item-by-id-for-sale/{id}', [App\Http\Controllers\ApiController::class, 'fetchItemByIdForSale'])->name('fetch-item-by-id-for-sale');

    Route::resource('stocks', App\Http\Controllers\StockController::class);
    Route::resource('label', App\Http\Controllers\LabelController::class);
    Route::get('vuejs/autocomplete/search', [App\Http\Controllers\LabelController::class, 'autocompleteSearch'])->name('vuejs.autocomplete.search');
    Route::get('fetch-product-info-for-gatepass/{id}', [App\Http\Controllers\LabelController::class, 'fetch_product_info']);


    Route::get('sale-pdf/{id}', [App\Http\Controllers\SaleController::class, 'pdf'])->name('sale.pdf');
    Route::get('sale-pdf-download/{id}', [App\Http\Controllers\SaleController::class, 'pdfDownload'])->name('sale.pdf-download');

//    Route::get('order-pdf/{id}', [App\Http\Controllers\OrderController::class, 'pdf'])->name('order.pdf');
//    Route::get('order-pdf-download/{id}', [App\Http\Controllers\OrderController::class, 'pdfDownload'])->name('order.pdf-download');

    Route::get('purchase-pdf/{id}', [App\Http\Controllers\PurchaseController::class, 'pdf'])->name('purchase.pdf');
    Route::get('purchase-pdf-download/{id}', [App\Http\Controllers\PurchaseController::class, 'pdfDownload'])->name('purchase.pdf-download');

    Route::resource('users', \App\Http\Controllers\UserController::class);

    Route::resource('batches', \App\Http\Controllers\BatchController::class);

    /*=========== Chart Of Inventory Api Starts ===========*/
    Route::get('inventory-items', [\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryItems']);
    Route::get('inventory-details/{id}', [\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryDetails']);
    Route::post('inventory-update/{id}', [\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryUpdate']);
    Route::post('inventory-store/{id}', [\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryStore']);
    Route::delete('inventory-delete/{id}', [\App\Http\Controllers\Api\Web\InventoryController::class, 'inventoryDelete']);
    /*=========== Chart Of Inventory Api Ends ===========*/

    /*=========== Chart Of Account Api Starts ===========*/
    Route::get('coa-items', [\App\Http\Controllers\Api\Web\COAccountController::class, 'items']);
    Route::get('coa-details/{id}', [\App\Http\Controllers\Api\Web\COAccountController::class, 'details']);
    Route::post('coa-update/{id}', [\App\Http\Controllers\Api\Web\COAccountController::class, 'update']);
    Route::post('coa-store/{id}', [\App\Http\Controllers\Api\Web\COAccountController::class, 'store']);
    Route::delete('coa-delete/{id}', [\App\Http\Controllers\Api\Web\COAccountController::class, 'delete']);
    /*=========== Chart Of Account Api Ends ===========*/

    /*=========== Raw Materials Opening Balance Api Starts ===========*/
    Route::get('raw-materials-opening-balances-list', [\App\Http\Controllers\Api\Web\RMOpeningBalanceController::class, 'list']);
    Route::get('raw-materials-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\RMOpeningBalanceController::class, 'initialInfo']);
    Route::resource('raw-materials-opening-balances', \App\Http\Controllers\Api\Web\RMOpeningBalanceController::class);
    /*=========== Raw Materials Opening Balance Api Ends ===========*/

    /*=========== Raw Materials Inventory Report Api Starts ===========*/
    Route::resource('raw-materials-inventory-report', \App\Http\Controllers\Api\Web\RMInventoryReportController::class);
    /*=========== Raw Materials Inventory Report Api Ends ===========*/

    /*=========== Finish Goods Opening Balance Api Starts ===========*/
    Route::get('finish-goods-opening-balances-list', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'list']);
    Route::get('finish-goods-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'initialInfo']);
    Route::resource('finish-goods-opening-balances', \App\Http\Controllers\Api\Web\FGOpeningBalanceController::class);
    /*=========== Finish Goods Opening Balance Api Ends ===========*/

    /*=========== Finish Goods Inventory Report Api Starts ===========*/
    Route::resource('finish-goods-inventory-report', \App\Http\Controllers\Api\Web\FGInventoryReportController::class);
    /*=========== Finish Goods Inventory Report Api Ends ===========*/


    /*=========== Finish Goods Inventory Transfer Starts ===========*/
    Route::resource('finish-goods-inventory-transfers', \App\Http\Controllers\FGInventoryTransferController::class);
    /*=========== Finish Goods Inventory Transfer Ends ===========*/

    /*=========== Finish Goods Inventory Adjustment Starts ===========*/
    Route::resource('fg-inventory-adjustments', \App\Http\Controllers\FGInventoryAdjustmentController::class);
    /*=========== Finish Goods Inventory Adjustment Ends ===========*/


    /*=========== General ledger Opening Balance Api Starts ===========*/
    Route::get('general-ledger-opening-balances-list', [\App\Http\Controllers\Api\Web\GLOpeningBalanceController::class, 'list']);
    Route::get('general-ledger-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\GLOpeningBalanceController::class, 'initialInfo']);
    Route::resource('general-ledger-opening-balances', \App\Http\Controllers\Api\Web\GLOpeningBalanceController::class);
    /*=========== General ledger Opening Balance Api Ends ===========*/

    /*=========== Customer Opening Balance Api Starts ===========*/
    Route::get('customer-opening-balances-list', [\App\Http\Controllers\Api\Web\CustomerOpeningBalanceController::class, 'list']);
    Route::get('customer-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\CustomerOpeningBalanceController::class, 'initialInfo']);
    Route::resource('customer-opening-balances', \App\Http\Controllers\Api\Web\CustomerOpeningBalanceController::class);
    /*=========== Customer Opening Balance Api Ends ===========*/

    /*=========== Supplier Opening Balance Api Starts ===========*/
    Route::get('supplier-opening-balances-list', [\App\Http\Controllers\Api\Web\SupplierOpeningBalanceController::class, 'list']);
    Route::get('supplier-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\SupplierOpeningBalanceController::class, 'initialInfo']);
    Route::resource('supplier-opening-balances', \App\Http\Controllers\Api\Web\SupplierOpeningBalanceController::class);
    /*=========== Supplier Opening Balance Api Ends ===========*/


    /*=========== Raw Materials Opening Balance Api Starts ===========*/
    Route::get('ledger-reports-initial-info', [\App\Http\Controllers\Api\Web\LedgerReportController::class, 'initialInfo']);
    Route::resource('ledger-reports', \App\Http\Controllers\Api\Web\LedgerReportController::class);
    /*=========== Raw Materials Opening Balance Api Ends ===========*/

    /*=========== Raw Materials Opening Balance Api Starts ===========*/
    Route::get('financial-statements-initial-info', [\App\Http\Controllers\Api\Web\FinancialStatementReportController::class, 'initialInfo']);
    Route::resource('financial-statements', \App\Http\Controllers\Api\Web\FinancialStatementReportController::class);
    /*=========== Raw Materials Opening Balance Api Ends ===========*/
});
Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {


    Route::get('ledger-reports', [App\Http\Controllers\LedgerReportController::class, 'index']);
//    Route::
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
