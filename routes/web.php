<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ChartOfInventoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FundTransferVoucherController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\OthersOutletSaleController;
use App\Http\Controllers\OutletAccountController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReceiveVoucherController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierPaymentVoucherController;
use App\Http\Controllers\SystemSettingController;
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
    return redirect()->route('login');
//    return view('welcome');
});
//register start
Route::resource('registers', \App\Http\Controllers\RegisterController::class);
//register end


Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth','prevent_duplicate_submission'])->group(function () {

    Route::get('/admin-dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/factory-dashboard', [\App\Http\Controllers\FactoryDashboardController::class, 'factoryDashboard'])->name('factory.dashboard');
    Route::get('/outlet-dashboard', [\App\Http\Controllers\OutletDashboardController::class, 'outletDashboard'])->name('outlet.dashboard');


    //lock Screen Start
    Route::get('lock-screen', [App\Http\Controllers\LockScreenController::class, 'lockScreen']);

    //lock Screen Start


    Route::resource('chart-of-accounts', ChartOfAccountController::class);
    Route::resource('stores', \App\Http\Controllers\StoreController::class);
    Route::resource('chart-of-inventories', ChartOfInventoryController::class);

    Route::get('/update-profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('profile.update-admin');


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
    Route::resource('fg-purchases', \App\Http\Controllers\FGPurchaseController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::get('purchase-return-pdf/{id}', [App\Http\Controllers\PurchaseReturnController::class, 'pdfDownload'])->name('purchase_return.pdf');
    Route::resource('purchase-returns', \App\Http\Controllers\PurchaseReturnController::class);
    Route::resource('sales', SaleController::class);

    Route::resource('others-outlet-sales', OthersOutletSaleController::class);
    Route::get('others-outlet-sale-pdf/{id}', [App\Http\Controllers\OthersOutletSaleController::class, 'pdfDownload'])->name('others.outlet.pdf-download');


    Route::put('requisitions.status-update/{id}', [\App\Http\Controllers\RequisitionController::class, 'updateStatus'])->name('requisitions.status-update');
    Route::resource('requisitions', \App\Http\Controllers\RequisitionController::class);
    Route::get('requisition-pdf/{id}', [App\Http\Controllers\RequisitionController::class, 'pdfDownload'])->name('requisition.pdf');
    Route::resource('productions', \App\Http\Controllers\ProductionController::class);
    Route::get('production-pdf/{id}', [App\Http\Controllers\ProductionController::class, 'productionPdf'])->name('production.pdf');
    Route::resource('outlets', \App\Http\Controllers\OutletController::class);
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);

    Route::get('payment-voucher-pdf/{id}', [App\Http\Controllers\PaymentVoucherController::class, 'Pdf'])->name('payment-voucher.pdf');
    Route::resource('payment-vouchers', PaymentVoucherController::class);
    Route::get('receive-voucher-pdf/{id}', [App\Http\Controllers\ReceiveVoucherController::class, 'Pdf'])->name('receive-voucher.pdf');
    Route::resource('receive-vouchers', ReceiveVoucherController::class);
    Route::get('journal-voucher-pdf/{id}', [App\Http\Controllers\JournalVoucherController::class, 'Pdf'])->name('journal-voucher.pdf');
    Route::resource('journal-vouchers', JournalVoucherController::class);
    Route::get('fund-transfer-voucher-pdf/{id}', [App\Http\Controllers\FundTransferVoucherController::class, 'Pdf'])->name('fund-transfer-voucher.pdf');
    Route::get('fund-transfer-voucher-receive/{id}', [App\Http\Controllers\FundTransferVoucherController::class, 'receive'])->name('fund-transfer-vouchers.receive');
    Route::post('fund-transfer-voucher-receive-report', [App\Http\Controllers\FundTransferVoucherController::class, 'receiveReport'])->name('fund-transfer-vouchers.receive.report');
    Route::resource('fund-transfer-vouchers', FundTransferVoucherController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('factories', FactoryController::class);

    Route::resource('earn-points', \App\Http\Controllers\EarnPointController::class);
    Route::resource('redeem-points', \App\Http\Controllers\RedeemPointController::class);
    Route::resource('member-types', \App\Http\Controllers\MemberTypeController::class);
    Route::resource('member-points', \App\Http\Controllers\MemberPointController::class);
    Route::resource('memberships', \App\Http\Controllers\MembershipController::class);
    Route::resource('outlet-configs', \App\Http\Controllers\OuteletConfigController::class);
    Route::resource('system-settings', SystemSettingController::class);

    Route::resource('outlet-accounts', OutletAccountController::class);
    Route::patch('outlet-account-active-or-inactive/{id}', [\App\Http\Controllers\OutletAccountController::class, 'changeStatus'])->name('outlet-account.change');


    //Promo Code
    Route::get('get-customer-by-type', [\App\Http\Controllers\PromoCodeController::class, 'getCustomers'])->name('promo-codes.customers');
    Route::resource('promo-codes', \App\Http\Controllers\PromoCodeController::class);

    //-----Start Supplier Payment Voucher---------
    Route::get('supplier-voucher-pdf/{id}', [App\Http\Controllers\SupplierPaymentVoucherController::class, 'Pdf'])->name('supplier-voucher.pdf');
    Route::resource('supplier-vouchers', SupplierPaymentVoucherController::class);
    Route::get('fetch-due-by-supplier-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchSupplierDueById'])->name('supplier.due');
    //-----End Supplier Payment Voucher---------
    //
    // //-----Start Pre Order---------
    Route::put('pre-orders.status-update/{id}', [\App\Http\Controllers\PreOrderController::class, 'updateStatus'])->name('pre-orders.status-update');
    Route::resource('pre-orders', \App\Http\Controllers\PreOrderController::class);
    Route::get('pre-order-pdf/{id}', [App\Http\Controllers\PreOrderController::class, 'Pdf'])->name('pre-order.pdf');

    //-----End Pre Order---------

    Route::resource('consumptions', \App\Http\Controllers\ConsumptionController::class);
    Route::get('consumption-pdf/{id}', [App\Http\Controllers\ConsumptionController::class, 'consumptionPdf'])->name('consumptions.pdf');
    //-----start Pos---------
    Route::get('pos-pre-orders', [POSController::class, 'getAllPreOrders'])->name('pos.pre.orders');
    Route::post('pos-pre-order', [POSController::class, 'storePreOrder'])->name('pos.pre.order');
    Route::get('pos-invoice-print/{id}', [POSController::class, 'printInvoice'])->name('pos.invoice.print');
    Route::post('pos-add-customer', [POSController::class, 'addCustomer'])->name('pos.add.customer');
    Route::post('pos-update-customer/{id}', [POSController::class, 'updateCustomer'])->name('pos.update.customer');
    Route::get('pos-coupon-code-discount', [POSController::class, 'getCouponDiscountValue'])->name('pos.coupon.discount');
    Route::get('pos--return-number-amount', [POSController::class, 'getReturnNumberValue'])->name('pos.exchange.discount');
    Route::get('pos-product-by-name-sku-bar-code', [POSController::class, 'getProductByNameSkuBarCode'])->name('pos.product.barcode');
    Route::get('pos-categories', [POSController::class, 'getAllProductCategories'])->name('pos.categories');
    Route::get('pos-products', [POSController::class, 'getAllProducts'])->name('pos.products');
    Route::get('pos-customers', [POSController::class, 'getAllCustomers'])->name('pos.customers');
    Route::get('pos-customer-by-number', [POSController::class, 'getCustomerByNumber'])->name('pos.customer.by.number');
    Route::get('pos-orders', [POSController::class, 'getAllOrders'])->name('pos.orders');
    Route::get('pos-view', [POSController::class, 'getView'])->name('pos.get-view');
    Route::resource('pos', POSController::class);

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
    Route::get('fetch-items-by-group-id-rm-consumption/{group_id}/{store_id?}', [App\Http\Controllers\ApiController::class, 'fetchItemsByGroupIdRMConsumption']);
    Route::get('/fetch-requisitions-by-store-id/{id}', [App\Http\Controllers\ApiController::class, 'fetch_req_by_store_id']);


    Route::get('/fetch-suppliers-by-group-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchSuppliersByGroupId']);
    Route::get('fetch-purchase-by-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchPurchaseById']);
    Route::get('/fetch-product-by-category-id/{id}', [App\Http\Controllers\ProductController::class, 'fetch_products_by_cat_id']);

    //as soon as possible Remove
    Route::get('/fetch-product-info-for-sale/{id}', [App\Http\Controllers\ApiController::class, 'fetch_product_sale'])->name('fetch-product-info-for-sale');
//    new
    Route::get('/fetch-item-by-id-for-pre-order/{id}', [App\Http\Controllers\ApiController::class, 'fetchItemByIdForPreOrder'])->name('fetch-item-by-id-for-pre-order');
    Route::get('/fetch-item-by-id-for-sale/{id}', [App\Http\Controllers\ApiController::class, 'fetchItemByIdForSale'])->name('fetch-item-by-id-for-sale');
    Route::get('/fetch-item-by-id-for-rm-requisition/{id}', [App\Http\Controllers\ApiController::class, 'fetchItemByIdForRMRequisition']);

    Route::resource('stocks', App\Http\Controllers\StockController::class);
    Route::resource('label', App\Http\Controllers\LabelController::class);
    Route::get('vuejs/autocomplete/search', [App\Http\Controllers\LabelController::class, 'autocompleteSearch'])->name('vuejs.autocomplete.search');
    Route::get('vuejs/autocomplete/sales-invoice-search', [App\Http\Controllers\SalesReturnController::class, 'autocompleteSearch']);
    Route::get('fetch-product-info-for-gatepass/{id}', [App\Http\Controllers\LabelController::class, 'fetch_product_info']);
    Route::get('fetch-sale-info/{id}', [App\Http\Controllers\SalesReturnController::class, 'fetchSaleInfo']);

    Route::get('invoice-by-store/{store_id}', [SaleController::class, 'getInvoiceByOutlet']);
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
    Route::get('raw-materials-opening-balances-excel-sample', [\App\Http\Controllers\Api\Web\RMOpeningBalanceController::class, 'sampleDownload'])->name('rmob.sample');
    Route::post('raw-materials-opening-balances-excel-upload', [\App\Http\Controllers\Api\Web\RMOpeningBalanceController::class, 'sampleUpload'])->name('rmob.sample.upload');
    Route::resource('raw-materials-opening-balances', \App\Http\Controllers\Api\Web\RMOpeningBalanceController::class);
    /*=========== Raw Materials Opening Balance Api Ends ===========*/

    /*=========== Raw Materials Inventory Report Api Starts ===========*/
    Route::resource('raw-materials-inventory-report', \App\Http\Controllers\Api\Web\RMInventoryReportController::class);

    /*=========== Raw Materials Inventory Report Api Ends ===========*/

    /*=========== Finish Goods Opening Balance Api Starts ===========*/
    Route::get('finish-goods-opening-balances-list', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'list']);
    Route::get('finish-goods-opening-balances-initial-info', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'initialInfo']);
    Route::get('finish-goods-opening-balances-excel-sample', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'sampleDownload'])->name('fgob.sample');
    Route::post('finish-goods-opening-balances-excel-upload', [\App\Http\Controllers\Api\Web\FGOpeningBalanceController::class, 'sampleUpload'])->name('fgob.sample.upload');
    Route::resource('finish-goods-opening-balances', \App\Http\Controllers\Api\Web\FGOpeningBalanceController::class);
    /*=========== Finish Goods Opening Balance Api Ends ===========*/

    /*=========== Finish Goods Inventory Report Api Starts ===========*/
    Route::resource('finish-goods-inventory-report', \App\Http\Controllers\Api\Web\FGInventoryReportController::class);
    /*=========== Finish Goods Inventory Report Api Ends ===========*/


    /*=========== Finish Goods Inventory Transfer Starts ===========*/
    Route::resource('fg-inventory-transfers', \App\Http\Controllers\FGInventoryTransferController::class);
    Route::get('fg-inventory-transfers-pdf/{id}', [App\Http\Controllers\FGInventoryTransferController::class, 'pdfDownload'])->name('fg-inventory-transfers.pdf');
    /*=========== Finish Goods Inventory Transfer Ends ===========*/

    /*=========== Finish Goods Delivery Receive Starts ===========*/
    Route::resource('fg-transfer-receives', \App\Http\Controllers\FGTransferReceiveController::class);
    Route::get('fg-transfer-receive-pdf/{id}', [App\Http\Controllers\FGTransferReceiveController::class, 'pdf'])->name('fg-transfer-receive.pdf');

    /*=========== Finish Goods Delivery Receive Ends ===========*/

    /*=========== Finish Goods Inventory Adjustment Starts ===========*/
    Route::resource('fg-inventory-adjustments', \App\Http\Controllers\FGInventoryAdjustmentController::class);
    /*=========== Finish Goods Inventory Adjustment Ends ===========*/
    /*=========== Raw Material Inventory Adjustment Starts ===========*/
    Route::resource('rm-inventory-adjustments', \App\Http\Controllers\RMInventoryAdjustmentController::class);
    /*=========== Raw Material Inventory Adjustment Ends ===========*/
    /*=========== Finish Goods wastage Report Api Starts ===========*/
    Route::resource('finish-goods-wastage-report', \App\Http\Controllers\Api\Web\FGWastegeReportController::class);
    /*=========== Finish Goods wastage Report Api Ends ===========*/
    /*=========== RM Inventory Transfer Starts ===========*/
    Route::resource('rm-inventory-transfers', \App\Http\Controllers\RMInventoryTransferController::class);
    Route::get('rm-inventory-transfers-pdf/{id}', [App\Http\Controllers\RMInventoryTransferController::class, 'pdfDownload'])->name('rm-inventory-transfers.pdf');
    /*=========== RM Inventory Transfer Ends ===========*/

    /*=========== RM Transfer Receive Starts ===========*/
    Route::resource('rm-transfer-receives', \App\Http\Controllers\RMTransferReceiveController::class);
    Route::get('rm-transfer-receive-pdf/{id}', [App\Http\Controllers\RMTransferReceiveController::class, 'pdf'])->name('rm-transfer-receive.pdf');
    /*=========== RM Transfer Receive Ends ===========*/

    /*=========== Raw Materials Requisition Starts ===========*/
    Route::resource('rm-requisitions', \App\Http\Controllers\RMRequisitionController::class);
    Route::get('rm-requisition-pdf/{id}', [App\Http\Controllers\RMRequisitionController::class, 'pdfDownload'])->name('rm-requisition.pdf');
    Route::get('fetch-requisition-by-id/{id}/{store_id?}', [App\Http\Controllers\ApiController::class, 'fetchRequisitionById']);
    Route::get('fetch-requisition-delivery-by-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchRequisitionDeliveryById']);
    Route::get('fetch-inventory-transfer-by-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchInventoryTransferById']);
    /*=========== Raw Materials Requisition Ends ===========*/

    /*=========== Raw Materials Requisition Delivery Starts ===========*/
    Route::resource('rm-requisition-deliveries', \App\Http\Controllers\RMRequisitionDeliveryController::class);
    Route::get('rm-requisition-delivery-pdf/{id}', [App\Http\Controllers\RMRequisitionDeliveryController::class, 'pdfDownload'])->name('rm-requisition-delivery.pdf');

    /*=========== Raw Materials Requisition Delivery Ends ===========*/

    /*=========== Finish Goods Requisition Delivery Starts ===========*/
    Route::resource('fg-requisition-deliveries', \App\Http\Controllers\FGRequisitionDeliveryController::class);
    Route::get('fg-requisition-delivery-pdf/{id}', [App\Http\Controllers\FGRequisitionDeliveryController::class, 'pdfDownload'])->name('fg-requisition-delivery.pdf');

    /*=========== Finish Goods Requisition Delivery Ends ===========*/

    /*=========== Finish Goods Delivery Receive Starts ===========*/
    Route::resource('fg-delivery-receives', \App\Http\Controllers\FGDeliveryReceiveController::class);
    Route::get('fg-delivery-receive-pdf/{id}', [App\Http\Controllers\FGDeliveryReceiveController::class, 'pdf'])->name('fg-delivery-receive.pdf');

    /*=========== Finish Goods Delivery Receive Ends ===========*/

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

    Route::resource('system-config', \App\Http\Controllers\SystemConfigController::class);

    /*=========== Sale Report Api Starts ===========*/
    Route::get('get-all-customers', [\App\Http\Controllers\Api\Web\SaleReportController::class, 'getAllCustomers']);
    Route::get('get-all-fg-stores', [\App\Http\Controllers\Api\Web\SaleReportController::class, 'getAllFGStores']);
    Route::resource('sale-reports', \App\Http\Controllers\Api\Web\SaleReportController::class);
    /*=========== Sale Report Api Ends ===========*/

    /*=========== Purchase Report Api Starts ===========*/
    Route::resource('purchase-reports', \App\Http\Controllers\Api\Web\PurchaseReportController::class);
    /*=========== Purchase Report Api Ends ===========*/


    Route::get('fetch-data-by-sale-id-for-sale/{id}', [\App\Http\Controllers\SalesDeliveryController::class, 'getItemData']);
    Route::resource('sales-deliveries', \App\Http\Controllers\SalesDeliveryController::class);


    //delivery cash transfer
    Route::resource('delivery-cash-transfers', \App\Http\Controllers\DeliveryCashTransferController::class);
    Route::resource('delivery-cash-receives', \App\Http\Controllers\DeliveryCashReceiveController::class);
    Route::resource('sales-exchanges', \App\Http\Controllers\SalesExchangeController::class);
    Route::resource('sales-returns', \App\Http\Controllers\SalesReturnController::class);

    Route::get('get-uuid/{store_id}', [\App\Http\Controllers\ApiController::class, 'getUUIDbyStore']);
    //-----Start Customer Receive Voucher---------
    Route::resource('customer-receive-vouchers', \App\Http\Controllers\CustomerReceiveVoucherController::class);
    Route::get('fetch-due-by-customer-id/{id}', [App\Http\Controllers\ApiController::class, 'fetchSupplierDueById'])->name('customer.due');
    //-----End Customer Receive Voucher---------
    Route::get('fetch-account-info/{id}', [App\Http\Controllers\ApiController::class, 'fetchAccountDetailsById']);
    Route::get('fetch-from-account-balance/{id}', [App\Http\Controllers\ApiController::class, 'fetchFromAccountBalanceById']);
});
Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {


    Route::get('ledger-reports', [App\Http\Controllers\LedgerReportController::class, 'index'])->name('ledger.reports');
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
Route::group(['middleware' => 'auth'], function () {
    Route::get('today-requisitions/{type}', [App\Http\Controllers\RequisitionController::class, 'exportRequisition'])->name('today.requisitions.export');
    Route::get('today-requisitions', [App\Http\Controllers\RequisitionController::class, 'todayRequisition'])->name('today.requisitions');

});


require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
