<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
//        $tables=$this->allTables(); //DB::select('SHOW TABLES');
//        for($i=0;$i<count($tables);$i++){
//            if (Schema::hasColumn($tables[$i], 'company_id'))
//            {
//                Schema::table($tables[$i], function (Blueprint $table)
//                {
//                    $table->dropColumn('company_id');
//                });
//            }
//            Schema::table($tables[$i], function (Blueprint $table) {
//                $table->foreignId('company_id')->nullable()->references('id')->on('companies')->onDelete('cascade');
//            });
//        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        $tables=$this->allTables(); //DB::select('SHOW TABLES');
//        for($i=0;$i<count($tables);$i++){
//            if (Schema::hasColumn($tables[$i], 'company_id'))
//            {
//                Schema::table($tables[$i], function (Blueprint $table)
//                {
//                    $table->dropColumn('company_id');
//                });
//            }
//        }
    }

    public function allTables()
    {
        return [

            app(\App\Models\AccountTransaction::class)->getTable(),
            app(\App\Models\Attribute::class)->getTable(),
            app(\App\Models\AttributeOption::class)->getTable(),
            app(\App\Models\Batch::class)->getTable(),
            app(\App\Models\Brand::class)->getTable(),
            app(\App\Models\Category::class)->getTable(),
            app(\App\Models\ChartOfInventory::class)->getTable(),
            app(\App\Models\ChartOfAccount::class)->getTable(),
            app(\App\Models\Consumption::class)->getTable(),
            app(\App\Models\ConsumptionItem::class)->getTable(),
            app(\App\Models\Customer::class)->getTable(),
            app(\App\Models\CustomerOpeningBalance::class)->getTable(),
            app(\App\Models\CustomerPromoCode::class)->getTable(),
            app(\App\Models\CustomerReceiveVoucher::class)->getTable(),
            app(\App\Models\CustomerTransaction::class)->getTable(),
            app(\App\Models\DeliveryCashTransfer::class)->getTable(),
            app(\App\Models\DeliveryReceive::class)->getTable(),
            app(\App\Models\DeliveryReceiveItem::class)->getTable(),
            app(\App\Models\Department::class)->getTable(),
            app(\App\Models\Designation::class)->getTable(),
            app(\App\Models\EarnPoint::class)->getTable(),
            app(\App\Models\Employee::class)->getTable(),
            app(\App\Models\ExpenseCategory::class)->getTable(),
            app(\App\Models\Expense::class)->getTable(),
            app(\App\Models\Factory::class)->getTable(),
            app(\App\Models\FinishGoodsOpeningBalance::class)->getTable(),
            app(\App\Models\FundTransferVoucher::class)->getTable(),
            app(\App\Models\GeneralLedgerOpeningBalance::class)->getTable(),
            app(\App\Models\InventoryAdjustment::class)->getTable(),
            app(\App\Models\InventoryAdjustmentItem::class)->getTable(),
            app(\App\Models\InventoryTransaction::class)->getTable(),
            app(\App\Models\InventoryTransfer::class)->getTable(),
            app(\App\Models\InventoryTransferItem::class)->getTable(),
            app(\App\Models\JournalVoucher::class)->getTable(),
            app(\App\Models\Membership::class)->getTable(),
            app(\App\Models\MembershipPointHistory::class)->getTable(),
            app(\App\Models\MemberPoint::class)->getTable(),
            app(\App\Models\MemberType::class)->getTable(),
            app(\App\Models\OthersOutletSale::class)->getTable(),
            app(\App\Models\OthersOutletSaleItem::class)->getTable(),
            app(\App\Models\OutletTransactionConfig::class)->getTable(),
            app(\App\Models\Outlet::class)->getTable(),
            app(\App\Models\PaymentVoucher::class)->getTable(),
            app(\App\Models\Payment::class)->getTable(),
            app(\App\Models\PreOrder::class)->getTable(),
            app(\App\Models\PreOrderItem::class)->getTable(),
            app(\App\Models\PreOrderAttachment::class)->getTable(),
            app(\App\Models\Production::class)->getTable(),
            app(\App\Models\ProductionItem::class)->getTable(),
            app(\App\Models\PromoCode::class)->getTable(),
            app(\App\Models\Purchase::class)->getTable(),
            app(\App\Models\PurchaseItem::class)->getTable(),
            app(\App\Models\PurchaseReturn::class)->getTable(),
            app(\App\Models\PurchaseReturnItem::class)->getTable(),
            app(\App\Models\Product::class)->getTable(),
            app(\App\Models\Requisition::class)->getTable(),
            app(\App\Models\RequisitionDelivery::class)->getTable(),
            app(\App\Models\RequisitionDeliveryItem::class)->getTable(),
            app(\App\Models\RequisitionItem::class)->getTable(),
            app(\App\Models\RawMaterialOpeningBalance::class)->getTable(),
            app(\App\Models\ReceiveVoucher::class)->getTable(),
            app(\App\Models\RedeemPoint::class)->getTable(),
            app(\App\Models\Sale::class)->getTable(),
            app(\App\Models\SaleItem::class)->getTable(),
            app(\App\Models\SalesExchange::class)->getTable(),
            app(\App\Models\SalesReturn::class)->getTable(),
            app(\App\Models\SalesReturnItem::class)->getTable(),
            app(\App\Models\Stock::class)->getTable(),
            app(\App\Models\StockIn::class)->getTable(),
            app(\App\Models\StockOut::class)->getTable(),
            app(\App\Models\Store::class)->getTable(),
            app(\App\Models\SupplierGroup::class)->getTable(),
            app(\App\Models\Supplier::class)->getTable(),
            app(\App\Models\SupplierOpeningBalance::class)->getTable(),
            app(\App\Models\SupplierPaymentVoucher::class)->getTable(),
            app(\App\Models\SupplierTransaction::class)->getTable(),
            app(\App\Models\SystemConfig::class)->getTable(),
            app(\App\Models\TransferReceive::class)->getTable(),
            app(\App\Models\TransferReceiveItem::class)->getTable(),
            app(\App\Models\Unit::class)->getTable(),
            app(\App\Models\User::class)->getTable(),

        ];

//        // ALL TABLE NAME
//        $tables = DB::select('SHOW TABLES');
//        $tb_array = [];
//        foreach($tables as $table)
//        {
//            $tb_array[]= $table->Tables_in_sal;
//        }
//        return $tb_array;
    }
};
