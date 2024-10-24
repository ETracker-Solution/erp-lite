<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChartOfAccount::create( [
            'id'=>1,
            'parent_id'=>NULL,
            'name'=>'Assets',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>2,
            'parent_id'=>NULL,
            'name'=>'Liability',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>3,
            'parent_id'=>NULL,
            'name'=>'Income',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'in',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>4,
            'parent_id'=>NULL,
            'name'=>'Expense',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>5,
            'parent_id'=>1,
            'name'=>'Current Assets',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>6,
            'parent_id'=>1,
            'name'=>'Fixed Assets',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>7,
            'parent_id'=>1,
            'name'=>'Other Assets',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>8,
            'parent_id'=>5,
            'name'=>'Bank',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'yes',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>9,
            'parent_id'=>5,
            'name'=>'Cash',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'yes',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>10,
            'parent_id'=>5,
            'name'=>'Inventory',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'yes',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>11,
            'parent_id'=>5,
            'name'=>'Accounts Receivable',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'accounts_receivable',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>12,
            'parent_id'=>8,
            'name'=>'Bank-123456',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'yes',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>13,
            'parent_id'=>9,
            'name'=>'Cash in hand',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'yes',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>14,
            'parent_id'=>9,
            'name'=>'Petty Cash',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>15,
            'parent_id'=>10,
            'name'=>'RM Inventory GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>16,
            'parent_id'=>10,
            'name'=>'FG Inventory GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>17,
            'parent_id'=>10,
            'name'=>'Cost of Production',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>18,
            'parent_id'=>11,
            'name'=>'Accounts Receivable GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>19,
            'parent_id'=>6,
            'name'=>'Office Equipment',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'office_equipment',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>20,
            'parent_id'=>2,
            'name'=>'Current Liabilities',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>21,
            'parent_id'=>2,
            'name'=>'Long Term Liabilities',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>22,
            'parent_id'=>20,
            'name'=>'Accounts Payable',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'accounts_payable',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>24,
            'parent_id'=>20,
            'name'=>'Short-Term Debt',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>33,
            'parent_id'=>3,
            'name'=>'Sales',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'in',
            'default_type'=>'sales',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>34,
            'parent_id'=>3,
            'name'=>'Others',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'in',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>35,
            'parent_id'=>33,
            'name'=>'Revenue from sales',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'in',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>36,
            'parent_id'=>4,
            'name'=>'Direct Expenses',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>37,
            'parent_id'=>4,
            'name'=>'Sales and Marketing Expense',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>1,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>'2024-05-23 04:13:19'
        ] );

        ChartOfAccount::create( [
            'id'=>39,
            'parent_id'=>4,
            'name'=>'Cost of Goods Sold',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>40,
            'parent_id'=>36,
            'name'=>'Utility',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>41,
            'parent_id'=>40,
            'name'=>'Water Bill',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>42,
            'parent_id'=>40,
            'name'=>'Electricity Bill',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>43,
            'parent_id'=>39,
            'name'=>'Cost of Goods Sold GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>44,
            'parent_id'=>2,
            'name'=>'External Liabilities',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>45,
            'parent_id'=>2,
            'name'=>'Internal Liabilities',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>46,
            'parent_id'=>45,
            'name'=>'Owners Equity',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'accounts_payable',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>47,
            'parent_id'=>45,
            'name'=>'Opening balance of Equity',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>'accounts_payable',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );

        ChartOfAccount::create( [
            'id'=>48,
            'parent_id'=>5,
            'name'=>'Miscellaneous',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:54:32',
            'updated_at'=>'2024-05-23 03:54:32'
        ] );

        ChartOfAccount::create( [
            'id'=>49,
            'parent_id'=>48,
            'name'=>'VAT Current Account GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:54:55',
            'updated_at'=>'2024-05-23 03:54:55'
        ] );

        ChartOfAccount::create( [
            'id'=>50,
            'parent_id'=>39,
            'name'=>'Discount Expense',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:55:23',
            'updated_at'=>'2024-05-23 03:55:23'
        ] );

        ChartOfAccount::create( [
            'id'=>51,
            'parent_id'=>39,
            'name'=>'Reward Point Redeem Expense',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:55:49',
            'updated_at'=>'2024-05-23 03:55:49'
        ] );

        ChartOfAccount::create( [
            'id'=>52,
            'parent_id'=>39,
            'name'=>'Inventory Adjustment',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:56:22',
            'updated_at'=>'2024-05-23 03:56:22'
        ] );

        ChartOfAccount::create( [
            'id'=>53,
            'parent_id'=>45,
            'name'=>'Retained Earning',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'credit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'li',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 04:10:37',
            'updated_at'=>'2024-05-23 04:10:37'
        ] );

        ChartOfAccount::create( [
            'id'=>54,
            'parent_id'=>4,
            'name'=>'Office and Administrative',
            'status'=>'active',
            'type'=>'group',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 04:13:39',
            'updated_at'=>'2024-05-23 04:13:39'
        ] );

        ChartOfAccount::create( [
            'id'=>55,
            'parent_id'=>54,
            'name'=>'Office Rent',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 04:13:52',
            'updated_at'=>'2024-05-23 04:13:52'
        ] );

        ChartOfAccount::create( [
            'id'=>56,
            'parent_id'=>54,
            'name'=>'Entertainment',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 04:14:12',
            'updated_at'=>'2024-05-23 04:14:12'
        ] );

        ChartOfAccount::create( [
            'id'=>57,
            'parent_id'=>54,
            'name'=>'Staff Salary',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'ex',
            'default_type'=>NULL,
            'created_by'=>1,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 04:14:24',
            'updated_at'=>'2024-05-23 04:14:24'
        ] );
        ChartOfAccount::create( [
            'id'=>58,
            'parent_id'=>11,
            'name'=>'Customers Receivable GL',
            'status'=>'active',
            'type'=>'ledger',
            'account_type'=>'debit',
            'is_bank_cash'=>'no',
            'root_account_type'=>'as',
            'default_type'=>'',
            'created_by'=>NULL,
            'updated_by'=>NULL,
            'deleted_at'=>NULL,
            'created_at'=>'2024-05-23 03:29:36',
            'updated_at'=>NULL
        ] );
    }
}
