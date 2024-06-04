<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemConfig::create( [
            'id'=>1,
            'key'=>'goods_purchase_bill_debit_account',
            'value'=>'15',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:49',
            'updated_at'=>'2024-05-21 05:06:49'
            ] );
                        
            Systemconfig::create( [
            'id'=>2,
            'key'=>'goods_purchase_bill_credit_account',
            'value'=>'22',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:49',
            'updated_at'=>'2024-05-21 05:06:49'
            ] );
                        
            Systemconfig::create( [
            'id'=>3,
            'key'=>'gl_account_liability_credit_account',
            'value'=>'31',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>4,
            'key'=>'gl_account_asset_credit_account',
            'value'=>'31',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>5,
            'key'=>'rm_opening_balance_debit_account',
            'value'=>'15',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>6,
            'key'=>'rm_opening_balance_credit_account',
            'value'=>'31',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>7,
            'key'=>'fg_opening_balance_debit_account',
            'value'=>'16',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>8,
            'key'=>'fg_opening_balance_credit_account',
            'value'=>'31',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>9,
            'key'=>'rm_consumption_debit_account',
            'value'=>'17',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>10,
            'key'=>'rm_consumption_credit_account',
            'value'=>'15',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>11,
            'key'=>'fg_production_debit_account',
            'value'=>'16',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>12,
            'key'=>'fg_production_credit_account',
            'value'=>'17',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>13,
            'key'=>'sales_account_receivable_account',
            'value'=>'18',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>14,
            'key'=>'income_from_sales_account',
            'value'=>'35',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>15,
            'key'=>'cogs_account',
            'value'=>'43',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>16,
            'key'=>'sales_fg_inventory_account',
            'value'=>'16',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>17,
            'key'=>'sales_cash_account',
            'value'=>'13',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>18,
            'key'=>'retained_earning',
            'value'=>'32',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );
                        
            Systemconfig::create( [
            'id'=>19,
            'key'=>'minimum_purchase_amount',
            'value'=>'1000',
            'is_active'=>1,
            'metadata'=>NULL,
            'created_at'=>'2024-05-21 05:06:50',
            'updated_at'=>'2024-05-21 05:06:50'
            ] );

            Systemconfig::create( [
                'id'=>19,
                'key'=>'company_name',
                'value'=>'Cake Town',
                'is_active'=>1,
                'metadata'=>NULL,
                'created_at'=>'2024-05-21 05:06:50',
                'updated_at'=>'2024-05-21 05:06:50'
            ] );

            Systemconfig::create( [
                'id'=>19,
                'key'=>'company_address',
                'value'=>'Maniknagar,Dhaka',
                'is_active'=>1,
                'metadata'=>NULL,
                'created_at'=>'2024-05-21 05:06:50',
                'updated_at'=>'2024-05-21 05:06:50'
            ] );

            Systemconfig::create( [
                'id'=>19,
                'key'=>'company_phone',
                'value'=>'01700000000',
                'is_active'=>1,
                'metadata'=>NULL,
                'created_at'=>'2024-05-21 05:06:50',
                'updated_at'=>'2024-05-21 05:06:50'
            ] );
            
            Systemconfig::create( [
                'id'=>19,
                'key'=>'company_email',
                'value'=>'caketown@gmailcom',
                'is_active'=>1,
                'metadata'=>NULL,
                'created_at'=>'2024-05-21 05:06:50',
                'updated_at'=>'2024-05-21 05:06:50'
            ] );
    }
}
